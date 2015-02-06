<?php

namespace Livefyre\Api;


use Livefyre\Core\Collection;
use Livefyre\Core\Core;
use Livefyre\Core\Network;
use Livefyre\Cursor\TimelineCursor;
use Livefyre\Routing\Client;
use Livefyre\Dto\Topic;
use Livefyre\Dto\Subscription;
use Livefyre\Type\SubscriptionType;
use Livefyre\Utils\JWT;
use Livefyre\Utils\LivefyreUtils;

class PersonalizedStream {

	const BASE_URL = "%s/api/v4/";
	const STREAM_URL = "%s/api/v4/";

	const NETWORK_TOPICS_URL_PATH = ":topics/";
	const SUBSCRIPTION_URL_PATH = ":subscriptions/";
	const SUBSCRIBER_URL_PATH = ":subscribers/";
	const TIMELINE_PATH = "timeline/";

	/* Topic API */
	public static function getTopic(Core $core, $id) {
		$url = self::getUrl($core) . Topic::generateUrn($core, $id);

		$response = Client::GET($url, self::getHeaders($core));
		
		$body = self::getDataFromResponse($response);
		if (!property_exists($body, "topic")) {
			return null;
		}

		return Topic::serializeFromJson($body->{"topic"});
	}

	public static function createOrUpdateTopic(Core $core, $id, $label) {
		$topics = self::createOrUpdateTopics($core, array($id => $label));
		if (count($topics) > 0) {
			return $topics[0];
		}
		return NULL;
	}

	public static function deleteTopic(Core $core, $topic) {
		return self::deleteTopics($core, array($topic)) == 1;
	}

	/* Multiple Topic API */
	public static function getTopics(Core $core, $limit = 100, $offset = 0) {
		$url = self::getTopicsUrl($core) . "?limit=" . $limit . "&offset=" . $offset; 

		$response = Client::GET($url, self::getHeaders($core));
		
		$body = self::getDataFromResponse($response);
		if (!property_exists($body, "topics")) {
			return null;
		}

		$topics = array();
		foreach ($body->{"topics"} as &$topic) {
			$topics[] = Topic::serializeFromJson($topic);
		}

		return $topics;
	}

	public static function createOrUpdateTopics(Core $core, $topicMap) {
		$topics = array();
		$json = array();
		foreach ($topicMap as $id => $label) {
			if (empty($label) || strlen($label) > 128) {
				throw new \InvalidArgumentException("topic label should be 128 char or under and not empty");
			}

		    $topic = Topic::create($core, $id, $label);
			$topics[] = $topic;
			$json[] = $topic->serializeToJson();
		}

		$data = json_encode(array("topics" => $json));
		$url = self::getTopicsUrl($core);

		Client::POST($url, self::getHeaders($core), $data);
		return $topics;
	}

	public static function deleteTopics(Core $core, $topics) {
		$data = json_encode(array("delete" => self::getTopicIds($topics)));
		$url =  self::getTopicsUrl($core);

		$response = Client::PATCH($url, self::getHeaders($core), $data);

		$body = self::getDataFromResponse($response);
		if (!property_exists($body, "deleted")) {
			return 0;
		}

		return $body->{"deleted"};
	}

	/* Collection Topic API */
	public static function getCollectionTopics(Collection $collection) {
		$url = self::getTopicsUrl($collection);

		$response = Client::GET($url, self::getHeaders($collection));

		$body = self::getDataFromResponse($response);
		if (!property_exists($body, "topicIds")) {
			return null;
		}

		return $body->{"topicIds"};
	}

	public static function addCollectionTopics(Collection $collection, $topics) {
		$data = json_encode(array("topicIds" => self::getTopicIds($topics)));
		$url = self::getTopicsUrl($collection);

		$response = Client::POST($url, self::getHeaders($collection), $data);

		$body = self::getDataFromResponse($response);
		if (!property_exists($body, "added")) {
			return 0;
		}

		return $body->{"added"};
	}

	public static function replaceCollectionTopics(Collection $collection, $topics) {
		$data = json_encode(array("topicIds" => self::getTopicIds($topics)));
		$url = self::getTopicsUrl($collection);

		$response = Client::PUT($url, self::getHeaders($collection), $data);

		$body = self::getDataFromResponse($response);
		return (!((property_exists($body, "added") && $body->{"added"} > 0)
			|| (property_exists($body, "removed") && $body->{"removed"} > 0)));
	}

	public static function removeCollectionTopics(Collection $collection, $topics) {
		$data = json_encode(array("delete" => self::getTopicIds($topics)));
		$url = self::getTopicsUrl($collection);

		$response = Client::PATCH($url, self::getHeaders($collection), $data);
		
		$body = self::getDataFromResponse($response);
		if (!property_exists($body, "removed")) {
			return 0;
		}

		return $body->{"removed"};
	}

	/* UserSubscription API */
	public static function getSubscriptions(Network $network, $userId) {
		$url = self::getSubscriptionUrl($network, $network->getUrnForUser($userId));

		$response = Client::GET($url, self::getHeaders($network));

		$body = self::getDataFromResponse($response);
		if (!property_exists($body, "subscriptions")) {
			return null;
		}

		$subscriptions = array();
		foreach ($body->{"subscriptions"} as &$sub) {
			$subscriptions[] = Subscription::serializeFromJson($sub);
		}

		return $subscriptions;
	}

	public static function addSubscriptions(Network $network, $userToken, $topics) {
		$userId = JWT::decode($userToken, $network->getData()->getKey())->user_id;
		$userUrn = $network->getUrnForUser($userId);
		$data = json_encode(array("subscriptions" => self::buildSubscriptions($topics, $userUrn)));
		$url = self::getSubscriptionUrl($network, $userUrn);

		$response = Client::POST($url, self::getHeaders($network, $userToken), $data);

		$body = self::getDataFromResponse($response);
		if (!property_exists($body, "added")) {
			return 0;
		}

		return $body->{"added"};
	}

	public static function replaceSubscriptions(Network $network, $userToken, $topics) {
		$userId = JWT::decode($userToken, $network->getData()->getKey())->user_id;
		$userUrn = $network->getUrnForUser($userId);
		$data = json_encode(array("subscriptions" => self::buildSubscriptions($topics, $userUrn)));
		$url = self::getSubscriptionUrl($network, $userUrn);

		$response = Client::PUT($url, self::getHeaders($network, $userToken), $data);

		$body = self::getDataFromResponse($response);
		return (!((property_exists($body, "added") && $body->{"added"} > 0)
			|| (property_exists($body, "removed") && $body->{"removed"} > 0)));
	}

	public static function removeSubscriptions(Network $network, $userToken, $topics) {
		$userId = JWT::decode($userToken, $network->getData()->getKey())->user_id;
		$userUrn = $network->getUrnForUser($userId);
		$data = json_encode(array("delete" => self::buildSubscriptions($topics, $userUrn)));
		$url = self::getSubscriptionUrl($network, $userUrn);

		$response = Client::PATCH($url, self::getHeaders($network, $userToken), $data);
		
		$body = self::getDataFromResponse($response);
		if (!property_exists($body, "removed")) {
			return 0;
		}

		return self::getDataFromResponse($response)->{"removed"};
	}

	public static function getSubscribers(Network $network, $topic, $limit = 100, $offset = 0) {
		$url = self::getUrl($network) . $topic->getId() . self::SUBSCRIBER_URL_PATH  . "?limit=" . $limit . "&offset=" . $offset; ;

		$response = Client::GET($url, self::getHeaders($network));
		
		$body = self::getDataFromResponse($response);
		if (!property_exists($body, "subscriptions")) {
			return null;
		}

		$subscriptions = array();
		foreach ($body->{"subscriptions"} as &$sub) {
			$subscriptions[] = Subscription::serializeFromJson($sub);
		}

		return $subscriptions;
	}

	public static function getTimelineStream(TimelineCursor $cursor, $isNext) {
		$url = self::getTimelineUrl($cursor->getCore()) . "?resource=" . $cursor->getData()->getResource() . "&limit=" . $cursor->getData()->getLimit();

		if ($isNext) {
			$url .= "&since=" . $cursor->getData()->getCursorTime();
		} else {
			$url .= "&until=" . $cursor->getData()->getCursorTime();
		}

		$response = Client::GET($url, self::getHeaders($cursor->getCore()));

		return json_decode($response);
	}

	/* Helper Methods */
	private static function getHeaders(Core $core, $userToken = null) {
        $network = LivefyreUtils::getNetworkFromCore($core);
		$token = ($userToken === null) ? $network->buildLivefyreToken() : $userToken;
		return array(
			"Authorization" => "lftoken " . $token,
			"Content-Type" => "application/json"
		);
	}

	private static function getUrl(Core $core) {
		return sprintf(self::BASE_URL, Domain::quill($core));
	}

	private static function getTopicsUrl(Core $core) {
		return self::getUrl($core) . $core->getUrn() . self::NETWORK_TOPICS_URL_PATH;
	}

	private static function getSubscriptionUrl(Network $network, $userUrn) {
		return self::getUrl($network) . $userUrn . self::SUBSCRIPTION_URL_PATH;
	}

	private static function getTimelineUrl(Core $core) {
		return sprintf(self::STREAM_URL, Domain::bootstrap($core)) . self::TIMELINE_PATH;
	}

	private static function getTopicIds($topics) {
		$topicIds = array();
		foreach ($topics as &$topic) {
			$topicIds[] = $topic->getId();
		}
		return $topicIds;
	}

	private static function buildSubscriptions($topics, $userUrn) {
		$subscriptions = array();
		foreach($topics as &$topic) {
			$sub = new Subscription($topic->getId(), $userUrn, SubscriptionType::personalStream);
			$subscriptions[] = $sub->serializeToJson();
		}
		return $subscriptions;
	}

	private static function getDataFromResponse($response) {
		return json_decode($response)->{"data"};
	}
}
