<?php

namespace IntegrationTests\PresentationLayer\Routes\Contributors\Id\Contact\Post;

use DomainLayer\ORM\Contributor\Contributor;
use DomainLayer\ORM\Notification\Notification;
use Guzzle\Http\Client;

/**
 * Class PostTest
 * @package IntegrationTests\PresentationLayer\Routes\Contributors\Id\Contact\Post
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

        $request = $client->post('/contributors/' . $contributorId . '/contact', null, json_encode($data));
        $response = $request->send();
        $responseBody = json_decode($response->getBody(true), true);

        /**
         * Remove dummy contributor before test potentially fails
         */
        if ($contributor = $entityManager->find(Contributor::class, $contributorId)) {
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
            /**
             * Check notification has been queued for this message
             */
            /** @var Contributor $contributor */
            $result = $entityManager->getRepository(Notification::class)->createQueryBuilder("n")
                ->where("n.body LIKE :replyEmailAddress")
                ->andWhere("n.emailAddress = :emailAddress")
                ->andWhere("n.type = 'New Messages'")
                ->setParameter("emailAddress", $contributor->getEmailAddress())
                ->setParameter("replyEmailAddress", "%" . $data["emailAddress"] . "%")
                ->getQuery()->execute();
            $this->assertEquals(1, count($result));

            /**
             * clean up the test notification
             */
            $entityManager->remove($result[0]);
            $entityManager->flush();
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
        $testContributors = $this->makeMultipleContributors(10);
        extract($testContributors);
        $entityManager->remove($C2); // test 2 does not need one
        $C3->setWantsAggregationEmail(false); // test 3 tests an uncontactable contributor
        $entityManager->flush();

        return [
            /**
             * Success
             */
            [$C1->getId(), ["name" => "Jim", "emailAddress" => "jim@example.org", "message" => "Hey bro, cool data!"]],
            /**
             * Contributor not found
             */
            ["fakeId", ["name" => "Jim", "emailAddress" => "jim@example.org", "message" => "Hey bro, cool data!"], 422, "Contributor not found"],
            /**
             * Contributor is uncontactable
             */
            [$C3->getId(), ["name" => "Jim", "emailAddress" => "jim@example.org", "message" => "Hey bro, cool data!"], 422, "Contributor is uncontactable"],
            /**
             * name, emailAddress and message are required
             */
            [$C4->getId(), ["emailAddress" => "jim@example.org", "message" => "Hey bro, cool data!"], 422, "name, emailAddress and message are required"],
            [$C5->getId(), ["name" => "Jim", "message" => "Hey bro, cool data!"], 422, "name, emailAddress and message are required"],
            [$C6->getId(), ["name" => "Jim", "emailAddress" => "jim@example.org"], 422, "name, emailAddress and message are required"],
            [$C7->getId(), ["name" => "", "emailAddress" => "jim@example.org", "message" => "Hey bro, cool data!"], 422, "name, emailAddress and message are required"],
            [$C8->getId(), ["name" => "Jim", "emailAddress" => "", "message" => "Hey bro, cool data!"], 422, "name, emailAddress and message are required"],
            [$C9->getId(), ["name" => "Jim", "emailAddress" => "jim@example.org", "message" => ""], 422, "name, emailAddress and message are required"],
            /**
             * emailAddress is invalid
             */
            [$C10->getId(), ["name" => "Jim", "emailAddress" => "notAnEmailAddress", "message" => "Hey bro, cool data!"], 422, "emailAddress is invalid"],
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
            $contributors[$key . $i]->setUnsubscribeToken("97thq3o8eruaghrleq9vh7p9842fj0q");
        }
        return $contributors;
    }
}
