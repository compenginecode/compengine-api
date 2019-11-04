<?php

namespace IntegrationTests\PresentationLayer\Routes\Contributors\Id\Unsubscribe\Post;

use DomainLayer\ORM\Contributor\Contributor;
use DomainLayer\ORM\Notification\Notification;
use Guzzle\Http\Client;

/**
 * Class PostTest
 * @package IntegrationTests\PresentationLayer\Routes\Contributors\Id\Unsubscribe\Post
 */
class PostTest extends \PHPUnit_Framework_TestCase
{
    /** test_contacting_contributor
     *
     *
     *
     * @param string $contributorId
     * @param array|null $data
     * @param int $expectedStatusCode
     * @param string|null $expectedErrorMessage
     * @dataProvider dataProvider
     */
    public function test_contacting_contributor($contributorId, $data, $expectedStatusCode = 200, $expectedErrorMessage = null) {
        global $entityManager;
        $client = new Client('http://localhost:8082', [
            'request.options' => [
                'exceptions' => false,
            ]
        ]);

        $request = $client->post('/contributors/' . $contributorId . '/unsubscribe', null, json_encode($data));
        $response = $request->send();
        $responseBody = json_decode($response->getBody(true), true);

        /**
         * Remove dummy contributor before test potentially fails
         *
         * @var Contributor $contributor
         */
        $wantsEmailCheck = null;
        if ($contributor = $entityManager->find(Contributor::class, $contributorId)) {
            $entityManager->refresh($contributor);
            $wantsEmailCheck = $contributor->wantsAggregationEmail();
            $entityManager->remove($contributor);
            $entityManager->flush();
        }

        /**
         * Test assertions
         */
        $this->assertEquals($expectedStatusCode, $response->getStatusCode());

        if (null !== $expectedErrorMessage) {
            $this->assertEquals(["message" => $expectedErrorMessage], $responseBody);
        } else {
            $this->assertEquals(["message" => "success"], $responseBody);
            if (null !== $wantsEmailCheck) {
                $this->assertFalse($wantsEmailCheck);
            }
        }
    }

    /** dataProvider
     *
     *
     *
     * @return array
     */
    public function dataProvider() {
        global $entityManager;
        $testContributors = $this->makeMultipleContributors(6);
        extract($testContributors);
        $entityManager->remove($C2); // test 2 does not need one
        $C3->setWantsAggregationEmail(false); // test 3 tests already unsubscribed contributor
        $entityManager->flush();

        return [
            /**
             * Success
             */
            [$C1->getId(), ["token" => $C1->getUnsubscribeToken()]],
            /**
             * Contributor not found
             */
            ["fakeId", ["token" => $C2->getUnsubscribeToken()], 422, "Contributor not found"],
            /**
             * Contributor is already unsubscribed, should succeed
             */
            [$C3->getId(), ["token" => $C2->getUnsubscribeToken()]],
            /**
             * token is required
             */
            [$C4->getId(), ["foo" => "bar"], 422, "token is required"],
            [$C5->getId(), [], 422, "token is required"],
            /**
             * emailAddress is invalid
             */
            [$C6->getId(), ["token" => "foo"], 422, "token is invalid"],
        ];
    }

    /** makeContributor
     *
     *
     *
     * @param string $name
     * @param string $emailAddress
     * @return Contributor
     */
    public function makeContributor($name = "Johnny", $emailAddress = "johnny@example.org") {
        global $entityManager;
        $contributor = new Contributor($name, $emailAddress);
        $contributor->setUnsubscribeToken("97thq3o8eruaghrleq9vh7p9842fj0q");
        $entityManager->persist($contributor);
        return $contributor;
    }

    /** makeMultipleContributors
     *
     *
     *
     * @param int $amount
     * @param string $key
     * @return array
     */
    public function makeMultipleContributors($amount, $key = "C") {
        $contributors = [];
        for ($i = 1; $i <= $amount; $i++) {
            $contributors[$key . $i] = $this->makeContributor("Johnny", "johnny{$i}@example.org");
            $contributors[$key . $i]->setWantsAggregationEmail(true);
        }
        return $contributors;
    }
}
