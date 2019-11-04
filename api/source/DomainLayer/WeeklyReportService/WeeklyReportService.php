<?php

namespace DomainLayer\WeeklyReportService;

use ConfigurationLayer\ApplicationConfig\ApplicationConfig;
use Doctrine\ORM\EntityManager;
use InfrastructureLayer\EmailGateway\IEmailGateway;

/**
 * Class WeeklyReportService
 * @package DomainLayer\WeeklyReportService
 */
class WeeklyReportService {

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var IEmailGateway
	 */
	private $emailGateway;

	/**
	 * @var ApplicationConfig
	 */
	private $applicationConfig;

	/**
	 * @param \DateTime $start
	 * @param \DateTime $end
	 * @return mixed
	 * @throws \Doctrine\DBAL\DBALException
	 */
	protected function getTotalContributions(\DateTime $start, \DateTime $end){
		$sql = 'SELECT COUNT(id) FROM timeseries WHERE timestamp_created BETWEEN :pStart AND :pEnd';
		$statement = $this->entityManager->getConnection()->prepare($sql);
		$statement->bindValue('pStart', $start->format('Y-m-d ') . '00:00:00');
		$statement->bindValue('pEnd', $end->format('Y-m-d ') . '00:00:00');

		$statement->execute();
		return (int)$statement->fetchAll()[0]['COUNT(id)'];
	}

	/**
	 * @param \DateTime $start
	 * @param \DateTime $end
	 * @return mixed
	 * @throws \Doctrine\DBAL\DBALException
	 */
	protected function getTotalTimeseriesRequiringApproval(\DateTime $start, \DateTime $end){
		$sql = 'SELECT COUNT(id) FROM timeseries WHERE timestamp_created BETWEEN :pStart AND :pEnd AND is_approved = 0 AND is_rejected = 0';
		$statement = $this->entityManager->getConnection()->prepare($sql);
		$statement->bindValue('pStart', $start->format('Y-m-d ') . '00:00:00');
		$statement->bindValue('pEnd', $end->format('Y-m-d ') . '00:00:00');

		$statement->execute();
		return (int)$statement->fetchAll()[0]['COUNT(id)'];
	}

	/**
	 * @param \DateTime $start
	 * @param \DateTime $end
	 * @return mixed
	 * @throws \Doctrine\DBAL\DBALException
	 */
	protected function getTotalContributors(\DateTime $start, \DateTime $end){
		$sql = 'SELECT COUNT(*) FROM (SELECT contributor_id FROM timeseries WHERE timestamp_created 
			BETWEEN :pStart AND :pEnd AND contributor_id IS NOT NULL 
			GROUP BY contributor_id) X';

		$statement = $this->entityManager->getConnection()->prepare($sql);
		$statement->bindValue('pStart', $start->format('Y-m-d ') . '00:00:00');
		$statement->bindValue('pEnd', $end->format('Y-m-d ') . '00:00:00');

		$statement->execute();
		return (int)$statement->fetchAll()[0]['COUNT(*)'];
	}

	/**
	 * WeeklyReportService constructor.
	 * @param EntityManager $entityManager
	 * @param IEmailGateway $emailGateway
	 * @param ApplicationConfig $applicationConfig
	 */
	public function __construct(EntityManager $entityManager, IEmailGateway $emailGateway,
		ApplicationConfig $applicationConfig){

		$this->entityManager = $entityManager;
		$this->emailGateway = $emailGateway;
		$this->applicationConfig = $applicationConfig;
	}

	/**
	 * @param \DateTime $dateTime
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function sendWeeklyReport(\DateTime $dateTime){
		$day = $dateTime->format("w");
		$weekStart = \DateTime::createFromFormat('m-d-Y', date("m-d-Y", strtotime("-" . ($day - 1) . " days")));
		$weekEnd = \DateTime::createFromFormat('m-d-Y', date("m-d-Y", strtotime("+" . (7 - $day) . " days")));

		$x = $this->getTotalContributions($weekStart, $weekEnd);
		$y = $this->getTotalContributors($weekStart, $weekEnd);
		$z = $this->getTotalTimeseriesRequiringApproval($weekStart, $weekEnd);

		$message = "There have been $x new contributions by $y known contributors this week. " .
			"There are currently $z contributions awaiting moderation.";

		$this->emailGateway->sendEmail(
			$this->applicationConfig->get('default_sender_email_address'),
			"Weekly CompEngine report",
			$message
		);
	}
}