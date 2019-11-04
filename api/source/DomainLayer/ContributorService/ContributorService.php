<?php

namespace DomainLayer\ContributorService;

use ConfigurationLayer\ApplicationConfig\ApplicationConfig;
use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\Contributor\Contributor;
use DomainLayer\ORM\Notification\Notification;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Repository\DatabaseTimeSeriesRepository;
use InfrastructureLayer\Crypto\TokenGenerator\ITokenGenerator;
use PresentationLayer\Routes\EInvalidInputs;

/**
 * Class ContributorService
 * @package DomainLayer\ContributorService
 */
class ContributorService
{
    /** entityManager
     *
     *
     *
     * @var EntityManager
     */
    private $entityManager;

    /** tokenGenerator
     *
     *
     *
     * @var ITokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var ApplicationConfig
     */
    private $applicationConfig;

    /**
     * @var DatabaseTimeSeriesRepository
     */
    private $timeSeriesRepository;

    /**
     * ContributorService constructor.
     * @param EntityManager $entityManager
     * @param ITokenGenerator $tokenGenerator
     * @param ApplicationConfig $applicationConfig
     * @param DatabaseTimeSeriesRepository $timeSeriesRepository
     */
    public function __construct(
        EntityManager $entityManager,
        ITokenGenerator $tokenGenerator,
        ApplicationConfig $applicationConfig,
        DatabaseTimeSeriesRepository $timeSeriesRepository
    ) {
        $this->entityManager = $entityManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->applicationConfig = $applicationConfig;
        $this->timeSeriesRepository = $timeSeriesRepository;
    }

    /** newContactContributorMessage
     *
     *
     *
     * @param IContactContributorRequest $request
     */
    public function newContactContributorMessage(IContactContributorRequest $request) {
        $this->ensureContributorHasUnsubscribeToken($request->getContributor());

        $notification = new Notification(Notification::WEEKLY, $request->getContributor()->getName(), $request->getContributor()->getEmailAddress(), Notification::NEW_MESSAGE, [
            "name" => $request->getName(),
            "replyEmailAddress" => $request->getEmailAddress(),
            "message" => $request->getMessage(),
        ], $this->getUnsubscribeLink($request->getContributor()));

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }

    /** newSimilarUploadNotification
     *
     *
     *
     * @param Contributor $contributor
     * @param PersistedTimeSeries $timeSeries
     */
    public function newSimilarUploadNotification(Contributor $contributor, PersistedTimeSeries $timeSeries)
    {
        $this->ensureContributorHasUnsubscribeToken($contributor);

        $notification = new Notification(Notification::WEEKLY, $contributor->getName(), $contributor->getEmailAddress(), Notification::SIMILAR_UPLOAD, [
            "timeSeriesUrl" => $this->getTimeSeriesLink($timeSeries),
            "timeSeriesName" => $timeSeries->getName(),
        ], $this->getUnsubscribeLink($contributor));

        $this->entityManager->persist($notification);
        $this->entityManager->flush($notification);
    }

    /** ensureContributorHasUnsubscribeToken
     *
     *
     *
     * @param Contributor $contributor
     */
    public function ensureContributorHasUnsubscribeToken(Contributor $contributor) {
        if (empty($contributor->getUnsubscribeToken())) {
            $contributor->setUnsubscribeToken($this->tokenGenerator->generateToken(64));
        }
    }

    /**
     * @param PersistedTimeSeries $timeSeries
     * @return string
     */
    public function getTimeSeriesLink(PersistedTimeSeries $timeSeries)
    {
        return $this->applicationConfig->get("frontend_url") . "/#!visualize/" . $timeSeries->getId();
    }

    /** getUnsubscribeLink
     *
     *
     *
     * @param Contributor $contributor
     * @return string
     */
    public function getUnsubscribeLink(Contributor $contributor) {
        return $this->applicationConfig->get("server_domain_name") . "/contributors/" . $contributor->getId() . "/unsubscribe?token=" . $contributor->getUnsubscribeToken();
    }

    /** unsubscribeContributor
     *
     *  Remove all queued daily email notifications for contributors email address
     *  Set wants_aggregation_email to false
     *
     * @param IUnsubscribeContributorRequest $request
     * @throws EInvalidInputs
     */
    public function unsubscribeContributor(IUnsubscribeContributorRequest $request) {
        if ($request->getContributor()->getUnsubscribeToken() !== $request->getToken()) {
            throw new EInvalidInputs("token is invalid");
        }

        $notificationsToClear = $this->entityManager->getRepository(Notification::class)->createQueryBuilder("n")
            ->where("n.emailAddress = :emailAddress")
            ->andWhere("n.frequency = :frequency")
            ->getQuery()->execute([
                "emailAddress" => $request->getContributor()->getEmailAddress(),
                "frequency" => Notification::WEEKLY,
            ]);
        array_walk($notificationsToClear, function (Notification $notification) {
            $this->entityManager->remove($notification);
        });

        /** Unset the contributor on any time series belonging to it */
        $timeSeries = $this->timeSeriesRepository->createQueryBuilder("ts")
            ->where("ts.contributor = :contributor")->setParameter("contributor", $request->getContributor())->getQuery()->execute();

        array_walk($timeSeries, function (PersistedTimeSeries $timeSeries) {
            $timeSeries->setContributor(null);
        });

        $contributor = $request->getContributor();
        $this->entityManager->remove($contributor);
        $this->entityManager->flush();
    }
}
