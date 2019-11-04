<?php

namespace PresentationLayer\Routes\Admin\TimeSeries\Id\Moderate\Post;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\BulkUploadRequest\BulkUploadRequest;
use DomainLayer\ORM\Notification\Notification;
use DomainLayer\ORM\TimeSeries\BulkUploadedTimeSeries\BulkUploadedTimeSeries;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
* Class Post
* @package PresentationLayer\Routes\Admin\TimeSeries\Id\Moderate\Post
*/
class Post extends UserInferredRoute
{

    /** __construct
     *
     *  Constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager)
    {
        parent::__construct($sessionService, $entityManager);
    }

    public function execute()
    {
        parent::execute();

        /** @var PersistedTimeSeries $timeSeries */
        $timeSeries = $this->entityManager->find(PersistedTimeSeries::class, $this->queryParams[0]);

        if (is_null($timeSeries)) {
            Throw new EInvalidInputs("Time series does not exist");
        }

        if ($timeSeries->isApproved()) {
            Throw new EInvalidInputs("Time series is already approved");
        }

        $request = $this->request->getBodyAsArray();

        if (! isset($request['action']) || ! in_array($request['action'], ['keep', 'remove'])) {
            Throw new EInvalidInputs("Action parameter must be provided and one of 'keep' or 'remove'");
        }

        if ('keep' === $request['action']) {
            $timeSeries->approve();
        } else {
            $timeSeries->reject();
            $this->notifyContributorOfRemoval();
        }

        $this->entityManager->persist($timeSeries);
        $this->entityManager->flush();

        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }

    private function notifyContributorOfRemoval() {
        /** @var PersistedTimeSeries $timeSeries */
        $timeSeries = $this->entityManager->find(PersistedTimeSeries::class, $this->queryParams[0]);
        if (is_null($timeSeries)) {
            Throw new EInvalidInputs("Time series does not exist");
        }

        $contributor = $timeSeries->getContributor();

        if (! $contributor) {
            return;
        }

        $request = $this->request->getBodyAsArray();
        $reason = ! empty($request['reason']) ? $request['reason'] : 'Reason not provided';

        $notification = new Notification(
            Notification::DAILY,
            $contributor->getName(),
            $contributor->getEmailAddress(),
            Notification::TIME_SERIES_DENIED,
            [
                "fileName" => $timeSeries->getName(),
                "count" => 1,
                "reason" => $reason,
            ]
        );

        $this->entityManager->persist($notification);
    }
}
