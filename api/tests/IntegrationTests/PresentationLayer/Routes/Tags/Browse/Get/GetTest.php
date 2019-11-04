<?php

namespace IntegrationTests\PresentationLayer\Routes\Tags\Browse\Get;

use Guzzle\Http\Client;

/**
 * Class GetTest
 * @package IntegrationTests\PresentationLayer\Routes\Tags\Browse\Get
 */
class GetTest extends \PHPUnit_Framework_TestCase
{
    /** test_browsing_tags
     *
     *
     *
     * @param int $expectedStatusCode
     * @param string|null $expectedErrorMessage
     * @dataProvider dataProvider
     */
    public function test_browsing_tags($expectedStatusCode = 200, $expectedErrorMessage = null) {
        // global $entityManager;
        $client = new Client('http://localhost:8082', [
            'request.options' => [
                'exceptions' => false,
            ]
        ]);

        $request = $client->get("/tags/browse");
        $response = $request->send();
        $responseBody = json_decode($response->getBody(true), true);

        /**
         * Test assertions
         */
        $this->assertEquals($expectedStatusCode, $response->getStatusCode());

        if (null !== $expectedErrorMessage) {
            $this->assertEquals(["message" => $expectedErrorMessage], $responseBody);
        } else {
            $this->assertArrayHasKey("tags", $responseBody);
            $this->assertArrayHasKey("total", $responseBody);
            $this->assertArrayHasKey("time", $responseBody);
        }
    }

    /** dataProvider
     *
     *
     *
     * @return array
     */
    public function dataProvider() {
        return [
            [],
        ];
    }
}
