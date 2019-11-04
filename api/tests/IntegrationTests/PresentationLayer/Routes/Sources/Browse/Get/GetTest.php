<?php

namespace IntegrationTests\PresentationLayer\Routes\Sources\Browse\Get;

use Guzzle\Http\Client;

/**
 * Class GetTest
 * @package IntegrationTests\PresentationLayer\Routes\Sources\Browse\Get
 */
class GetTest extends \PHPUnit_Framework_TestCase
{
    /** test_browsing_sources
     *
     *
     *
     * @param int $expectedStatusCode
     * @param string|null $expectedErrorMessage
     * @dataProvider dataProvider
     */
    public function test_browsing_sources($expectedStatusCode = 200, $expectedErrorMessage = null) {
        // global $entityManager;
        $client = new Client('http://localhost:8082', [
            'request.options' => [
                'exceptions' => false,
            ]
        ]);

        $request = $client->get("/sources/browse");
        $response = $request->send();
        $responseBody = json_decode($response->getBody(true), true);

        /**
         * Test assertions
         */
        $this->assertEquals($expectedStatusCode, $response->getStatusCode());

        if (null !== $expectedErrorMessage) {
            $this->assertEquals(["message" => $expectedErrorMessage], $responseBody);
        } else {
            $this->assertArrayHasKey("sources", $responseBody);
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
