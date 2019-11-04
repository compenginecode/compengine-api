<?php

namespace UnitTests\PresentationLayer\Routes\Admin\BulkUploadRequests\Get;

use Guzzle\Http\Client;

/**
 * Class GetTest
 * @package UnitTests\PresentationLayer\Routes\Admin\BulkUploadRequests\Get
 */
class GetTest extends \PHPUnit_Framework_TestCase
{
    public function test_listing_new_bulk_upload_requests() {
        $client = new Client('http://localhost:8082', [
            'request.options' => [
                'exceptions' => false,
            ]
        ]);

        $request = $client->get('/admin/bulk-upload-requests');
        $response = $request->send();
        $responseBody = json_decode($response->getBody(true), true);

        $this->assertArrayHasKey("newRequests", $responseBody);
    }
}
