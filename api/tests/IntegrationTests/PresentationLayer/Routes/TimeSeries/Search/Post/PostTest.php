<?php

namespace IntegrationTests\PresentationLayer\Routes\TimeSeries\Search\Post;

use Guzzle\Http\Client;

/**
 * Class PostTest
 * @package IntegrationTests\PresentationLayer\Routes\TimeSeries\Search\Post
 */
class PostTest extends \PHPUnit_Framework_TestCase
{
    /** test_browsing_sources
     *
     *
     *
     * @param int $expectedStatusCode
     * @param string|null $expectedErrorMessage
     * @dataProvider dataProvider
     */
    public function test_browsing_sources($data = [], $expectedStatusCode = 200, $expectedErrorMessage = null) {
        // global $entityManager;
        $client = new Client('http://localhost:8082', [
            'request.options' => [
                'exceptions' => false,
            ]
        ]);

        $request = $client->post("/time-series/search", null, json_encode($data));
        $response = $request->send();
        $responseBody = json_decode($response->getBody(true), true);

        /**
         * Test assertions
         */
        $this->assertEquals($expectedStatusCode, $response->getStatusCode());

        if (null !== $expectedErrorMessage) {
            $this->assertEquals(["message" => $expectedErrorMessage], $responseBody);
        } else {
            $this->assertArrayHasKey("timeSeries", $responseBody);
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
            [["page" => "a"], 422, "Page number is invalid"],
            [["term" => "bla"]],
            [["source" => "bla"]],
            [["category" => "bla"]],
            [["tag" => "bla"]],
            [["term" => "bla", "tag" => "bla"], 422, "Only ONE (term, source, category, tag) filter may be used at a time"],
        ];
    }
}
