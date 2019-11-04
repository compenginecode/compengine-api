<?php

namespace UnitTests\PresentationLayer\Routes\BulkUploadRequests\Upload\Post;

use DomainLayer\ORM\BulkUploadRequest\BulkUploadRequest;
use DomainLayer\ORM\TimeSeries\BulkUploadedTimeSeries\BulkUploadedTimeSeries;
use Guzzle\Http\Client;

/**
 * Class PostTest
 * @package UnitTests\PresentationLayer\Routes\BulkUploadRequests\Upload\Post
 */
class PostTest extends \PHPUnit_Framework_TestCase
{
    private $goodFile = "LineFeedExample.dat";
    private $badFile = "bad.docx";

    /** test_uploading_a_valid_file
     *
     *
     *
     * @param string $file
     * @param string|null $expectErrorMessage
     * @param int $expectStatusCode
     * @dataProvider filesProvider
     */
    public function test_uploading_a_file($file, $expectStatusCode = 200, $expectErrorMessage = null) {
        /**
         * Get a valid bulk upload request / approval code to test with
         */
        global $entityManager;

        $approvalToken = "ubgi753qyr3...2hgoq8790923";
        $exchangeToken = "p98347n5v9...1c2y45brndkhfwexgrew";
        $dummyBulkUploadRequest = new BulkUploadRequest("Jim", "jim@example.org", "", "", $approvalToken);
        $dummyBulkUploadRequest->setApprovedAt(new \DateTime());
        $dummyBulkUploadRequest->setExchangeToken($exchangeToken);
        $entityManager->persist($dummyBulkUploadRequest);
        $entityManager->flush();

        $client = new Client('http://localhost:8082', [
            'request.options' => [
                'exceptions' => false,
            ]
        ]);

        $data = [
            "file" => "@" . $file,
            "approvalToken" => $approvalToken,
            "exchangeToken" => $exchangeToken,
        ];

        $request = $client->post('/bulk-upload-requests/upload', null, $data);
        $response = $request->send();
        $responseBody = json_decode($response->getBody(true), true);

        $this->assertEquals($expectStatusCode, $response->getStatusCode());

        if (null !== $expectErrorMessage) {
            $this->assertEquals(["message" => $expectErrorMessage], $responseBody);
        } else {
            $this->assertArrayHasKey("timeSeriesId", $responseBody);
        }

        /**
         * clean up
         */
        if (isset($responseBody["timeSeriesId"])) {
            $timeSeries = $entityManager->find(BulkUploadedTimeSeries::class, $responseBody["timeSeriesId"]);
            $entityManager->remove($timeSeries);
        }
        $entityManager->remove($dummyBulkUploadRequest);
        $entityManager->flush();
    }

    public function filesProvider() {
        return [
            [ __DIR__ . "/" . $this->goodFile],
            [ __DIR__ . "/" . $this->badFile, 422, "File format is not accepted"],
        ];
    }
}
