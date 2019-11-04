<?php

namespace UnitTests\PresentationLayer\Routes\Admin\TimeSeries\Id\Download\Get;

use DomainLayer\ORM\TimeSeries\BulkUploadedTimeSeries\BulkUploadedTimeSeries;
use Guzzle\Http\Client;

/**
 * Class Get
 * @package UnitTests\PresentationLayer\Routes\Admin\TimeSeries\Id\Download\Get
 */
class GetTest extends \PHPUnit_Framework_TestCase
{
    public function test_downloading_a_bulk_uploaded_time_series() {
        global $entityManager;
        $client = new Client('http://localhost:8082', [
            'request.options' => [
                'exceptions' => false,
            ]
        ]);

        $bulkUploadedTimeSeries = $this->makeTimeSeries();

        $request = $client->get('/admin/time-series/' . $bulkUploadedTimeSeries->getId() . '/download');
        $response = $request->send();
        $responseBody = $response->getBody(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/csv", $response->getHeader("Content-Type"));
        $this->assertEquals("1\n2\n3", $responseBody);

        $entityManager->remove($bulkUploadedTimeSeries);
        $entityManager->flush();
    }

    public function makeTimeSeries() {
        global $entityManager;
        $bulkUploadedTimeSeries = new BulkUploadedTimeSeries([1,2,3], 0);
        $entityManager->persist($bulkUploadedTimeSeries);
        $entityManager->flush();
        return $bulkUploadedTimeSeries;
    }
}
