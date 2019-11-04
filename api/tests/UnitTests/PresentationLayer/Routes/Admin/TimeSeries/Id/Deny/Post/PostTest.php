<?php

namespace UnitTests\PresentationLayer\Routes\Admin\TimeSeries\Id\Deny\Post;

use DomainLayer\ORM\TimeSeries\BulkUploadedTimeSeries\BulkUploadedTimeSeries;
use Guzzle\Http\Client;

/**
 * Class PostTest
 * @package UnitTests\PresentationLayer\Routes\Admin\BulkUploadRequests\Id\Deny\Post
 */
class PostTest extends \PHPUnit_Framework_TestCase
{
    public function test_denying_a_bulk_uploaded_time_series() {
        global $entityManager;
        $client = new Client('http://localhost:8082', [
            'request.options' => [
                'exceptions' => false,
            ]
        ]);

        $bulkUploadedTimeSeries = $this->makeTimeSeries();

        $request = $client->post('/admin/time-series/' . $bulkUploadedTimeSeries->getId() . '/deny');
        $response = $request->send();
        $responseBody = json_decode($response->getBody(true), true);

        $this->assertEquals(["message" => "success"], $responseBody);

        $entityManager->refresh($bulkUploadedTimeSeries);
        $this->assertTrue($bulkUploadedTimeSeries->isDenied());

        $entityManager->remove($bulkUploadedTimeSeries);
        $entityManager->flush();
    }

    public function makeTimeSeries() {
        global $entityManager;
        $bulkUploadedTimeSeries = new BulkUploadedTimeSeries([], 0);
        $entityManager->persist($bulkUploadedTimeSeries);
        $entityManager->flush();
        return $bulkUploadedTimeSeries;
    }
}
