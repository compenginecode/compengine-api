<?php

namespace PresentationLayer\Routes;

use Doctrine\ORM\EntityManager;
use Yam\Route\AbstractRoute;

/**
 * Class TransactionRoute
 * @package PresentationLayer\Routes
 */
abstract class TransactionRoute extends AbstractRoute{

    /** $entityManager
     *
     *  Access to the shared Unit of Work for this route, which encapsulates
     *  at least one single transaction.
     *
     * @var EntityManager
     */
    protected $entityManager;

    /** $exceptionMap
     *
     *  Maps internal exceptions to a naming convention acceptable by the front end.
     *
     * @var array
     */
    protected $exceptionMap = [];

    /** wrapExceptionForFront
     *
     *  Returns an array which can be used for error handling on the front end.
     *
     * @param \Exception $exception
     * @param $message
     * @return array
     */
    protected function wrapExceptionForFront(\Exception $exception, $message){
        $physicalClass = get_class($exception);
        $class = isset($this->exceptionMap[$physicalClass]) ? $this->exceptionMap[$physicalClass] : "EUnknown";
        return array(
            "class" => $class,
            "userMessage" => $message
        );
    }

    /** __construct
     *
     *  TransactionRoute constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager){
        $this->entityManager = $entityManager;
    }

}