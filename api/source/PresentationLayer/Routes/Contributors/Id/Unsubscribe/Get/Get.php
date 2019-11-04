<?php

namespace PresentationLayer\Routes\Contributors\Id\Unsubscribe\Get;

use ConfigurationLayer\ApplicationConfig\ApplicationConfig;
use Doctrine\ORM\EntityManager;
use DomainLayer\ContributorService\ContributorService;
use DomainLayer\ORM\Contributor\Contributor;
use PresentationLayer\Routes\Contributors\Id\Unsubscribe\Requests\UnsubscribeContributorWebRequest;
use PresentationLayer\Routes\EInvalidInputs;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Post
 * @package PresentationLayer\Routes\Contributors\Id\Unsubscribe\Post
 */
class Get extends AbstractRoute
{
    /** contributorService
     *
     *
     *
     * @var ContributorService
     */
    private $contributorService;

    /** unsubscribeContributorWebRequest
     *
     *
     *
     * @var UnsubscribeContributorWebRequest
     */
    private $unsubscribeContributorWebRequest;

    /** entityManager
     *
     *
     *
     * @var EntityManager
     */
    private $entityManager;

    /** applicationConfig
     *
     *
     *
     * @var ApplicationConfig
     */
    private $applicationConfig;

    /** __construct
     *
     *  Constructor
     *
     * @param ContributorService $contributorService
     * @param UnsubscribeContributorWebRequest $unsubscribeContributorWebRequest
     * @param EntityManager $entityManager
     * @param ApplicationConfig $applicationConfig
     */
    public function __construct(ContributorService $contributorService, UnsubscribeContributorWebRequest $unsubscribeContributorWebRequest, EntityManager $entityManager, ApplicationConfig $applicationConfig) {
        $this->contributorService = $contributorService;
        $this->unsubscribeContributorWebRequest = $unsubscribeContributorWebRequest;
        $this->entityManager = $entityManager;
        $this->applicationConfig = $applicationConfig;
    }

    public function execute() {
        /** @var Contributor $contributor */
        $contributor = $this->entityManager->find(Contributor::class, $this->queryParams[0]);

        try {
            if (!$contributor) {
                throw new EInvalidInputs("Contributor not found");
            }

            $this->unsubscribeContributorWebRequest->populate($contributor, ["token" => $_GET["token"]]);
            $this->contributorService->unsubscribeContributor($this->unsubscribeContributorWebRequest);
        } catch (\Exception $e) {
            header("Location: " . $this->applicationConfig->get("frontend_url") . "#!oh-no?message=Unable to unsubscribe right now. You might already be unsubscribed.");
            die();
        }

        header("Location: " . $this->applicationConfig->get("frontend_url") . "#!success?message=You have been unsubscribed.");
        die();
    }
}
