<?php

namespace PresentationLayer\Routes\Admin\Tags\Id\Deny\Post;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\Tag\Tag;
use DomainLayer\ORM\Tag\Repository\ITagRepository;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\Repository\ITimeSeriesRepository;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Post
 * @package PresentationLayer\Routes\Admin\Tags\Id\Deny\Post
 */
class Post extends UserInferredRoute
{
    /** tagRepository
     *
     *
     *
     * @var ITagRepository
     */
    private $tagRepository;

    /** timeSeriesRepository
     *
     *
     *
     * @var ITimeSeriesRepository
     */
    private $timeSeriesRepository;

    /** __construct
     *
     *  Constructor
     *
     * @param SessionService $sessionService
     * @param EntityManager $entityManager
     * @param ITagRepository $tagRepository
     * @param ITimeSeriesRepository $timeSeriesRepository
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager,
                                ITagRepository $tagRepository, ITimeSeriesRepository $timeSeriesRepository) {

        parent::__construct($sessionService, $entityManager);
        $this->tagRepository = $tagRepository;
        $this->timeSeriesRepository = $timeSeriesRepository;
    }

    public function execute() {
        parent::execute();

        /** @var Tag $tag */
        $tag = $this->tagRepository->find($this->queryParams[0]);

        /** Check tag exists */
        if (null === $tag) {
            Throw new EInvalidInputs("Tag not found");
        }

        $webRequest = $this->request->getBodyAsArray();

        /** Replacement tag is optional */
        if (! empty($webRequest["replacementTagId"])) {
            /** @var Tag $replacementTag */
            $replacementTag = $this->tagRepository->find($webRequest["replacementTagId"]);

            /** Check replacement tag exists */
            if (null === $replacementTag) {
                Throw new EInvalidInputs("Replacement tag not found");
            }

            /** Check replacement tag is different to denied tag */
            if ($replacementTag === $tag) {
                Throw new EInvalidInputs("Replacement tag must be different to denied tag");
            }
        } else {
            $replacementTag = null;
        }

        /** Get time series that belongs to the denied tag */
        $timeSeries = $this->timeSeriesRepository->createQueryBuilder("ts")
            ->innerJoin("ts.tags", "t")->where("t = :tag")->setParameter("tag", $tag)->getQuery()->execute();

        /** Iterate through each time series and set the tag to the replacement tag */
        array_walk($timeSeries, function (PersistedTimeSeries $timeSeries) use ($replacementTag, $tag) {
            $tags = $timeSeries->getTags();
            $tags->remove($tags->indexOf($tag));
            if ($replacementTag) {
                $tags->add($replacementTag);
            }
            $timeSeries->setTags($tags);
        });

        /** Delete the denied tag and persist time series updates */
        $this->entityManager->remove($tag);
        $this->entityManager->flush();

        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }
}
