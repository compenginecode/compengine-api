<?php

namespace PresentationLayer\Routes;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\Authenticator\Authenticator;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\Operators\AuthenticationRequired\EAuthenticationRequiredUnauthorized;
use Yam\Route\AbstractRoute;
use Yam\Route\Request\ERequestHeaderMissing;

/**
 * Class UserInferredRoute
 * @package PresentationLayer\Routes\UserInferredRoute
 */
class UserInferredRoute extends AbstractRoute{

    /**
     * @var \InfrastructureLayer\Sessions\SessionService\SessionService
     */
	protected $sessionService;

	/**
	 * @var EntityManager
	 */
	protected $entityManager;

    /**
     * @var string
     */
    protected $sessionToken;

	/**
	 * UserInferredRoute constructor.
	 * @param SessionService $sessionService
	 * @param EntityManager $entityManager
	 */
    public function __construct(SessionService $sessionService, EntityManager $entityManager){
        $this->sessionService = $sessionService;
        $this->entityManager = $entityManager;
    }

    /**
     * @throws \PresentationLayer\Operators\AuthenticationRequired\EAuthenticationRequiredUnauthorized
     */
    public function execute(){
        $this->response->setHeader("Content-type", "text/json");

        try{
            $token = $this->request->getAuthorization()->getTokenAsBearer();
            if (empty($token)){
				throw new EAuthenticationRequiredUnauthorized();
			}
        }catch(ERequestHeaderMissing $e){
            throw new EAuthenticationRequiredUnauthorized();
        }

        $authenticatorId = $this->sessionService->getSession($token);

        $this->sessionToken = $token;
        if (NULL !== $authenticatorId){
            /** @var \DomainLayer\ORM\Authenticator\Authenticator $authenticator */
            $authenticator = $this->entityManager->getRepository(Authenticator::class)->findOneBy([
            	"id" => $authenticatorId
			]);

            if (NULL == $authenticator){
				throw new EAuthenticationRequiredUnauthorized();
            }
        }else{
            throw new EAuthenticationRequiredUnauthorized();
        }
    }

}