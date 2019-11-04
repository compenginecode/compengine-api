<?php

namespace PresentationLayer\Routes\Contributors\Id\Contact\Post;

use Doctrine\ORM\EntityManager;
use DomainLayer\ContributorService\ContributorService;
use DomainLayer\ORM\Contributor\Contributor;
use PresentationLayer\Routes\Contributors\Id\Contact\Post\Requests\ContactContributorWebRequest;
use PresentationLayer\Routes\EInvalidInputs;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Post
 * @package PresentationLayer\Routes\Contributors\Id\Contact\Post
 */
class Post extends AbstractRoute
{
    /** contactContributorWebRequest
     *
     *
     *
     * @var ContactContributorWebRequest
     */
    private $contactContributorWebRequest;

    /** contributorService
     *
     *
     *
     * @var ContributorService
     */
    private $contributorService;

    /** entityManager
     *
     *
     *
     * @var EntityManager
     */
    private $entityManager;

    /** __construct
     *
     *  Constructor
     *
     * @param ContactContributorWebRequest $contactContributorWebRequest
     * @param ContributorService $contributorService
     * @param EntityManager $entityManager
     */
    public function __construct(ContactContributorWebRequest $contactContributorWebRequest, ContributorService $contributorService, EntityManager $entityManager) {
        $this->contactContributorWebRequest = $contactContributorWebRequest;
        $this->contributorService = $contributorService;
        $this->entityManager = $entityManager;
    }

    public function execute() {
        /** @var Contributor $contributor */
        $contributor = $this->entityManager->find(Contributor::class, $this->queryParams[0]);

        if (!$contributor) {
            throw new EInvalidInputs("Contributor not found");
        }

        $this->contactContributorWebRequest->populate($contributor, $this->request->getBodyAsArray());
        $this->contributorService->newContactContributorMessage($this->contactContributorWebRequest);

        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }
}
