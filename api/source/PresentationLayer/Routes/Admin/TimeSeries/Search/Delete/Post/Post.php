<?php

namespace PresentationLayer\Routes\Admin\TimeSeries\Search\Delete\Post;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use DomainLayer\ORM\Contributor\Contributor;
use DomainLayer\ORM\Tag\Tag;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Routes\Admin\AdminRoute;
use PresentationLayer\Routes\UserInferredRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;
use Carbon\Carbon;

/**
 * Class Post
 * @package PresentationLayer\Routes\Admin\TimeSeries\Search\Delete\Post
 */
class Post extends UserInferredRoute
{

    /** __construct
     *
     *  Constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(SessionService $sessionService, EntityManager $entityManager) {
        parent::__construct($sessionService, $entityManager);
    }

    public function execute() {
        parent::execute();

        $timeSeriesRepository = $this->entityManager->getRepository(PersistedTimeSeries::class);

        $query = $timeSeries = $timeSeriesRepository->createQueryBuilder("ts");
        $query = $this->filterQuery($query);
        $items = $query->getQuery()->execute();



        array_walk($items, function (PersistedTimeSeries $timeSeries) {
            $this->entityManager->remove($timeSeries);
        });

        $this->entityManager->flush();

        $this->response->setReturnBody(new JSONBody(["message" => "success"]));
    }

    /**
     * filterQuery
     *
     *
     *
     * @param QueryBuilder $query
     * @return QueryBuilder
     */
    public function filterQuery(QueryBuilder $query) {
        $request = $this->request->getBodyAsArray();

        if (isset($request["name"])) {
            $query->andWhere("ts.name LIKE :name")
                ->setParameter("name", "%$request[name]%");
        }

        if (isset($request["description"])) {
            $query->andWhere("ts.description LIKE :description")
                ->setParameter("description", "%$request[description]%");
        }

        if (isset($request["samplingRate"])) {
            $query->andWhere("ts.samplingInformation.samplingRate = :samplingRate")
                ->setParameter("samplingRate", $request["samplingRate"]);
        }

        if (isset($request["samplingUnit"])) {
            $query->andWhere("ts.samplingInformation.samplingUnit = :samplingUnit")
                ->setParameter("samplingUnit", $request["samplingUnit"]);
        }

        if (isset($request["source"]) && !empty($request["source"])) {
            $query->andWhere("ts.source = :source")
                ->setParameter("source", $request["source"]);
        }

        if (isset($request["category"]) && !empty($request["category"])) {
            $query->andWhere("ts.category = :category")
                ->setParameter("category", $request["category"]);
        }

        if (isset($request["contributor"]) && !empty($request["contributor"])) {
            $contributors = $this->entityManager->getRepository(Contributor::class)->createQueryBuilder("c")->where("c.name LIKE :name")
                ->setParameter("name", "%$request[contributor]%")->getQuery()->execute();

            $contributorExpressions = array_map(function (Contributor $contributor) use ($query) {
                $query->setParameter("contributor{$contributor->getId()}", $contributor);
                return "ts.contributor = :contributor{$contributor->getId()}";
            }, $contributors);
            if ($contributorExpressions) {
                $query->andWhere(implode(" OR ", $contributorExpressions));
            } else {
                // user entered a contributor name but it didn't match any known contributors so no results should be returned
                $query->andWhere("1=0");
            }
        }

        if (isset($request["startDate"]) && !empty($request["startDate"])) {
            $query->andWhere(":startDate <= ts.timestampCreated")
                ->setParameter("startDate", $request["startDate"]);
        }

        if (isset($request["endDate"]) && !empty($request["endDate"])) {
            $query->andWhere("ts.timestampCreated <= :endDate")
                ->setParameter("endDate", $request["endDate"] . " 23:59:59");
        }

        if (isset($request["tags"]) && is_array($request["tags"])) {
            $tags = array_map(function ($tagName) {
                return $this->entityManager->getRepository(Tag::class)->findBy(["name" => $tagName]);
            }, $request["tags"]);
            array_walk($tags, function ($tag, $index) use ($query) {
                if (null !== $tag) {
                    $query->andWhere(":tag$index MEMBER OF ts.tags")
                        ->setParameter("tag$index", $tag);
                }
            });
        }

        return $query;
    }
}
