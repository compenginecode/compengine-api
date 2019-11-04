<?php

namespace PresentationLayer\Routes\ContactUs\Post;

use DomainLayer\ContactEmailService\ContactEmailService;
use DomainLayer\ContactEmailService\ContactEmailWebRequest\ContactEmailWebRequest;
use PresentationLayer\Routes\EBadRequest;
use PresentationLayer\Routes\EInvalidInputs;
use PresentationLayer\Routing\StatusCode\UnprocessableEntity;
use Yam\Route\AbstractRoute;
use Yam\Route\Response\ReturnBody\JSONBody\JSONBody;
use Yam\Route\Response\StatusCode\StatusBadRequest;

/**
 * Class Post
 * @package PresentationLayer\Routes\ContactUs\Post
 */
class Post extends AbstractRoute {

    private $webRequest;

    private $service;

    public function __construct(ContactEmailWebRequest $webRequest, ContactEmailService $service) {
        $this->webRequest = $webRequest;
        $this->service = $service;
    }

    public function execute() {
        try {
            $payload = $this->request->getBodyAsArray();
            if (empty($payload)) {
                throw new EBadRequest("A post body is required.");
            }

            $this->webRequest->injectPayload($payload);
            $this->service->sendEmails(
                $this->webRequest->getName(),
                $this->webRequest->getEmailAddress(),
                $this->webRequest->getMessage(),
                $this->webRequest->getSendCopy()
            );

            $response = ["message" => "success"];
        } catch (EInvalidInputs $e) {
            $this->response->setStatusCode(new UnprocessableEntity());
            $response = [
                "errorCode" => "BadRequest",
                "showOnFront" => TRUE,
                "message" => $e->getMessage()
            ];
        } catch (EBadRequest $e) {
            $this->response->setStatusCode(new StatusBadRequest());
            $response = [
                "errorCode" => "BadRequest",
                "message" => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $this->response->setStatusCode(new StatusBadRequest());
            $response = [
                "message" => $e->getMessage()
            ];
        } finally {
            $this->response->setReturnBody(new JSONBody($response));
        }
    }

}