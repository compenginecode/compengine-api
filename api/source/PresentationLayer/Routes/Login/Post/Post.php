<?php

namespace PresentationLayer\Routes\Login\Post;

use Doctrine\ORM\EntityManager;
use DomainLayer\ORM\Authenticator\Authenticator;
use DomainLayer\ORM\SiteAttribute\Repository\ISiteAttributeRepository;
use DomainLayer\TimeSeriesManagement\TimeSeriesDownloadService\TimeSeriesDownloadService;
use InfrastructureLayer\Sessions\SessionService\SessionService;
use PresentationLayer\ReturnBody\TextReturnBody;
use PresentationLayer\Routes\EInvalidInputs;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;

/**
 * Class Post
 * @package PresentationLayer\Routes\Login\Post
 */
class Post extends AbstractRoute{

	private $entityManager;

	private $sessionService;

	public function __construct(SessionService $sessionService, EntityManager $entityManager){
		$this->entityManager = $entityManager;
		$this->sessionService = $sessionService;
	}

	/** execute
     *
     *  Route execution.
     *
     */
    public function execute(){
		$payload = $this->request->getBodyAsArray();
    	if (!isset($payload["emailAddress"])){
    		throw new EInvalidInputs("emailAddress field must be supplied");
		}

		if (!isset($payload["password"])){
			throw new EInvalidInputs("password field must be supplied");
		}

		$authenticator = $this->entityManager->getRepository(Authenticator::class)->findOneBy([
			"emailAddress" => $payload["emailAddress"]
		]);

		if (NULL !== $authenticator){
			/** @var $authenticator Authenticator */
			if ($authenticator->authenticateAgainst($payload["password"])){
				$sessionToken = $this->sessionService->createSession($authenticator);
				$response = array(
					"token" => $sessionToken->token,
					"expiry_time" => $sessionToken->expiryInSeconds(),
				);

				$this->response->setReturnBody(new JSONBody($response));
			}else{
				throw new EInvalidInputs("Invalid email address or password.");
			}
		}else{
			throw new EInvalidInputs("Invalid email address or password.");
		}
    }

}