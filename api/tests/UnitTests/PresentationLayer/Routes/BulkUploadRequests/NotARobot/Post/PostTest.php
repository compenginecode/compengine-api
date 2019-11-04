<?php

namespace UnitTests\PresentationLayer\Routes\BulkUploadRequests\NotARobot\Post;

use DomainLayer\ORM\BulkUploadRequest\BulkUploadRequest;
use Guzzle\Http\Client;

/**
 * Class PostTest
 * @package UnitTests\PresentationLayer\Routes\BulkUploadRequests\NotARobot\Post
 */
class PostTest extends \PHPUnit_Framework_TestCase
{
    public function test_not_a_robot_stops_bot_aka_this_test() {
        /**
         * Get a valid bulk upload request / approval code to test with
         */
        global $entityManager;

        $approvalToken = "ubgi753qyr3...2hgoq8790923";
        $dummyBulkUploadRequest = new BulkUploadRequest("Jim", "jim@example.org", "", "", $approvalToken);
        $dummyBulkUploadRequest->setApprovedAt(new \DateTime());
        $entityManager->persist($dummyBulkUploadRequest);
        $entityManager->flush();

        $client = new Client('http://localhost:8082', [
            'request.options' => [
                'exceptions' => false,
            ]
        ]);

        $data = [
            "recaptchaResponseCode" => "ubgi753qyr3...2hgoq8790923",
            "approvalToken" => $approvalToken,
        ];

        $request = $client->post('/bulk-upload-requests/not-a-robot', null, json_encode($data));
        $response = $request->send();
        $responseBody = json_decode($response->getBody(true), true);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(["message" => "recaptchaResponseCode invalid"], $responseBody);

        /**
         * clean up
         */
        $entityManager->remove($dummyBulkUploadRequest);
        $entityManager->flush();
    }
}
