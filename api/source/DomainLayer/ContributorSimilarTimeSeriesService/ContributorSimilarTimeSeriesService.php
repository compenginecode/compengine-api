<?php

namespace DomainLayer\ContributorSimilarTimeSeriesService;

use ConfigurationLayer\ApplicationConfig\ApplicationConfig;
use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\FeatureVector\FeatureVector;
use DomainLayer\ORM\SiteAttribute\Repository\DatabaseSiteAttributeRepository;
use DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\NearestNeighbourService;
use DomainLayer\TimeSeriesManagement\Comparison\NearestNeighbourService\SearchQuery\SearchQuery;
use InfrastructureLayer\EmailGateway\IEmailGateway;

/**
 * Class ContributorSimilarTimeSeriesService
 * @package DomainLayer\ContributorSimilarTimeSeriesService
 */
class ContributorSimilarTimeSeriesService {

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var NearestNeighbourService
	 */
	private $nearestNeighbourService;

	/**
	 * @var ISiteAttributeRepository
	 */
	private $siteAttributeRepository;

	/**
	 * @var IEmailGateway
	 */
	private $emailGateway;

	/**
	 * @var ApplicationConfig
	 */
	private $applicationConfig;

	/**
	 * 	We select all the time series that are associated to contributors who
	 * 	want the periodic comparison aggregation email. We ignore the first week
	 * 	so that data uploaded in batches is not considered.
	 *
	 * @throws \Exception
	 * @return array
	 */
	protected function getBaseTimeSeries(){
		$sql = 'SELECT timeseries.id FROM timeseries JOIN contributors ON contributors.id = timeseries.contributor_id
			WHERE contributors.wants_aggregation_email = 1 AND NOW() > (timeseries.timestamp_created + INTERVAL 7 DAY)';

		$statement = $this->entityManager->getConnection()->prepare($sql);
		$statement->execute();

		$entityManager = $this->entityManager;
		return array_map(function($array) use ($entityManager){
			return $this->entityManager->getRepository(PersistedTimeSeries::class)->findOneBy(
				['id' => $array['id']]
			);
		}, $statement->fetchAll());
	}

	/**
	 * 	Returns the dates for the start and end of the week given any date within that week.
	 *
	 * @param \DateTime $currentDateTime
	 * @return array
	 */
	protected function getWeekInterval(\DateTime $currentDateTime){
		$day = $currentDateTime->format("w");

		$weekStart = \DateTime::createFromFormat('m-d-Y',
			date("m-d-Y", strtotime("-" . ($day - 1) . " days")));

		$weekEnd = \DateTime::createFromFormat('m-d-Y',
			date("m-d-Y", strtotime("+" . (7 - $day) . " days")));

		return array($weekStart, $weekEnd);
	}

	protected function getSimilarTimeSeries(PersistedTimeSeries $persistedTimeSeries, \DateTime $currentDateTime){
		$buffer = [];
		$timeSeriesRepository = $this->entityManager->getRepository(PersistedTimeSeries::class);

		$searchQuery = new SearchQuery(
			$this->siteAttributeRepository->getCurrentFeatureVectorFamily()->getCommonIndex(),
			100
		);

		$neighbourhood = $this->nearestNeighbourService->findNearestNeighbours(
			$persistedTimeSeries->getNormalizedFeatureVector(),
			$searchQuery
		);

		list($startDate, $endDate) = $this->getWeekInterval($currentDateTime);
		foreach($neighbourhood->getNodes() as $aNeighbourCandidate){
			/** @var $aNeighbourCandidate PersistedTimeSeries */
			$aNeighbourCandidateId = $aNeighbourCandidate['id'];

			/** Skip the root node */
			if ('root' === $aNeighbourCandidateId){
				continue;
			}

			$aNeighbourCandidate = $timeSeriesRepository->findOneBy(['id' => $aNeighbourCandidateId]);
			$timestamp = $aNeighbourCandidate->timestampCreated();

			/** Skip rejected */
			if (!$aNeighbourCandidate->isApproved()){
				continue;
			}

			if ($timestamp >= $startDate && $timestamp <= $endDate){
				$buffer[] = $aNeighbourCandidate;
			}
		}

		$newBuffer = [];
		foreach($buffer as $x){
			/** @var $x PersistedTimeSeries */
			if (!in_array($x->getId(), $newBuffer)){
				$newBuffer[] = $x;
			}
		}

		return $newBuffer;
	}

	/**
	 * ContributorSimilarTimeSeriesService constructor.
	 * @param EntityManager $entityManager
	 * @param NearestNeighbourService $nearestNeighbourService
	 * @param ISiteAttributeRepository $siteAttributeRepository
	 * @param IEmailGateway $emailGateway
	 * @param ApplicationConfig $applicationConfig
	 */
	public function __construct(EntityManager $entityManager, NearestNeighbourService $nearestNeighbourService,
		ISiteAttributeRepository $siteAttributeRepository, IEmailGateway $emailGateway,
		ApplicationConfig $applicationConfig){

		$this->entityManager = $entityManager;
		$this->nearestNeighbourService = $nearestNeighbourService;
		$this->siteAttributeRepository = $siteAttributeRepository;
		$this->emailGateway = $emailGateway;
		$this->applicationConfig = $applicationConfig;
	}

	protected function dispatchEmails(array $buffer){
		/** The max number of links put in each email */
		$maxLinksPerEmail = 25;

		foreach($buffer as $aContributorEmail => $secondaryBuffers){
			/** No similar neighbours this week :( */
			if (count($secondaryBuffers) === 0){
				continue;
			}

			$message = "Hello there,<br><br>We found matches of your data to new data uploaded this week!<br><br>";

			$linkCount = 0;
			foreach($secondaryBuffers as $aSecondaryBuffer){
				/** Check if we should bail out - we only want 25 links in the email in total */
				if ($linkCount > $maxLinksPerEmail){
					break;
				}

				/** @var PersistedTimeSeries $timeSeries */
				$timeSeries = $aSecondaryBuffer['timeSeries'];
				$message .= "<b>Similar data to your time series '" . $timeSeries->getName() . "'</b><ul>";

				foreach($aSecondaryBuffer['similar'] as $aSimilarTimeSeries){
					/** Check if we should bail out - we only want 25 links in the email in total */
					if ($linkCount > $maxLinksPerEmail){
						break 2;
					}

					/** @var $aSimilarTimeSeries PersistedTimeSeries */
					$name = $aSimilarTimeSeries->getName();
					$url = rtrim($this->applicationConfig->get('bulk_contribution_service_notification_link_prefix'), '/')
						. '/' . $aSimilarTimeSeries->getId();

					$message .= "<li><a href='${url}'>${name}</a></li>";
					$linkCount++;
				}
				$message .= "</ul><br>";
			}

			$this->emailGateway->sendEmail(
				$aContributorEmail,
				"We've found some similar data you may be interested in!",
				$message
			);
		}
	}

	/**
	 * @param \DateTime $currentTime
	 * @param callable $logOutput
	 * @throws \Exception
	 */
	public function sendOutEmails(\DateTime $currentTime, callable $logOutput){
		$contributorBuffer = [];

		$logOutput('Finding all time series with contributor aggregation emails turned on.');
		foreach($this->getBaseTimeSeries() as $aBaseTimeSeries){
			/** @var $aBaseTimeSeries PersistedTimeSeries */
			$email = $aBaseTimeSeries->getContributor()->getEmailAddress();
			if (!isset($contributorBuffer[$email])){
				$contributorBuffer[$email] = [];
			}

			$logOutput('Finding applicable neighbours for time series ' . $aBaseTimeSeries->getId());
			$similarTimeSeries = $this->getSimilarTimeSeries($aBaseTimeSeries, $currentTime);

			/** Setup the empty secondary buffer */
			$secondaryBuffer = array('timeSeries' => $aBaseTimeSeries, 'similar' => []);
			foreach($similarTimeSeries as $aSimilarAndApplicableTimeSeries){
				/** @var $aSimilarAndApplicableTimeSeries PersistedTimeSeries */
				$logOutput(' - Found newly added similar time series ' . $aBaseTimeSeries->getId());
				/** Store in secondary buffer */
				$secondaryBuffer['similar'][] = $aSimilarAndApplicableTimeSeries;
			}

			if (count($secondaryBuffer['similar']) > 0){
				$contributorBuffer[$email][] = $secondaryBuffer;
			}
		}

		/** Now we have a full buffer we pump out an email */
		$logOutput('Dispatching all emails');
		$this->dispatchEmails($contributorBuffer);
		$logOutput('Done.');
	}

}