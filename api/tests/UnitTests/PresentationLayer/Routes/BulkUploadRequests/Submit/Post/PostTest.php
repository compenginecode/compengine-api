<?php

namespace UnitTests\PresentationLayer\Routes\BulkUploadRequests\Submit\Post;

use DomainLayer\ORM\BulkUploadRequest\BulkUploadRequest;
use DomainLayer\ORM\TimeSeries\BulkUploadedTimeSeries\BulkUploadedTimeSeries;
use Guzzle\Http\Client;

/**
 * Class PostTest
 * @package UnitTests\PresentationLayer\Routes\BulkUploadRequests\Submit\Post
 */
class PostTest extends \PHPUnit_Framework_TestCase
{
    /** test_uploading_a_valid_file
     *
     *
     *
     * @param string $data
     * @param string|null $expectErrorMessage
     * @param int $expectStatusCode
     * @dataProvider dataProvider
     */
    public function test_uploading_a_file($data, $expectStatusCode = 200, $expectErrorMessage = null) {
        /**
         * Get a valid bulk upload request / approval code to test with
         */
        global $entityManager;

        $client = new Client('http://localhost:8082', [
            'request.options' => [
                'exceptions' => false,
            ]
        ]);

        $request = $client->post('/bulk-upload-requests/submit', null, json_encode($data));
        $response = $request->send();
        $responseBody = json_decode($response->getBody(true), true);

        $this->assertEquals($expectStatusCode, $response->getStatusCode());

        if (null !== $expectErrorMessage) {
            $this->assertEquals(["message" => $expectErrorMessage], $responseBody);
        } else {
            $this->assertEquals(["message" => "success"], $responseBody);
        }

        /**
         * clean up
         */
        if (isset($data["timeSeries"])) {
            array_walk($data["timeSeries"], function ($timeSeriesId) use ($entityManager) {
                $timeSeries = $entityManager->find(BulkUploadedTimeSeries::class, $timeSeriesId);
                if ($timeSeries) {
                    $entityManager->remove($timeSeries);
                }
            });
        }

        if (isset($data["exchangeToken"])) {
            $bulkUploadRequest = $entityManager->getRepository(BulkUploadRequest::class)->findOneBy([
                "exchangeToken" => $data["exchangeToken"],
            ]);
            if ($bulkUploadRequest) {
                $entityManager->remove($bulkUploadRequest);
            }
        }

        $entityManager->flush();
    }

    public function dataProvider() {
        $BUR1 = $this->makeBulkUploadRequest();
        $TS1 = $this->makeBulkUploadedTimeSeries($BUR1);
        $BUR2 = $this->makeBulkUploadRequest();
        $BUR3 = $this->makeBulkUploadRequest();

        return [
            [array_merge($this->getPayload($BUR1), ["timeSeries" => [$TS1->getId()]])],
            [array_merge($this->getPayload($BUR2), ["timeSeries" => []]), 422, "timeSeries(array), approvalToken, exchangeToken, metadata.category, metadata.samplingRate, metadata.samplingUnit, metadata.tags(array) fields are required"],
            [array_merge($this->getPayload($BUR3), ["timeSeries" => ["123"]]), 422, "Time series id 123 not found for this bulk upload"],
        ];
    }

    public function getPayload(BulkUploadRequest $bulkUploadRequest) {
        return [
            "timeSeries" => [],
            "approvalToken" => $bulkUploadRequest->getApprovalToken(),
            "exchangeToken" => $bulkUploadRequest->getExchangeToken(),
            "metadata" => [
                "category" => 1,
                "samplingRate" => "idk",
                "samplingUnit" => "idk",
                "tags" => ["Shape"],
            ],
        ];
    }

    public function makeBulkUploadRequest() {
        global $entityManager;
        $approvalToken = "ubgi753qyr3...2hgoq8790923";
        $exchangeToken = "p98347n5v9...1c2y45brndkhfwexgrew";
        $bulkUploadRequest = new BulkUploadRequest("Jim", "jim@example.org", "", "", $approvalToken);
        $bulkUploadRequest->setApprovedAt(new \DateTime());
        $bulkUploadRequest->setExchangeToken($exchangeToken);
        $entityManager->persist($bulkUploadRequest);
        $entityManager->flush();
        return $bulkUploadRequest;
    }

    public function makeBulkUploadedTimeSeries($bulkUploadRequest) {
        global $entityManager;
        $bulkUploadedTimeSeries = new BulkUploadedTimeSeries([], 0);
        $bulkUploadedTimeSeries->setBulkUploadRequest($bulkUploadRequest);
        $entityManager->persist($bulkUploadedTimeSeries);
        $entityManager->flush();
        return $bulkUploadedTimeSeries;
    }
}
