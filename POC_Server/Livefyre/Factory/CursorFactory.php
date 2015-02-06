<?php

namespace Livefyre\Factory;


use Livefyre\Core\Core;
use Livefyre\Core\Network;
use Livefyre\Cursor\TimelineCursor;

class CursorFactory {
	public static function getTopicStreamCursor(Core $core, $topic, $limit = 50, $date = null) {
    	if (is_null($date)) {
    		$date = time();
    	}
		$resource = $topic->getId() . ":topicStream";
		return TimelineCursor::init($core, $resource, $limit, $date);
	}

	public static function getPersonalStreamCursor(Network $network, $user, $limit = 50, $date = null) {
    	if (is_null($date)) {
    		$date = time();
    	}
		$resource = $network->getUrnForUser($user) . ":personalStream";
		return TimelineCursor::init($network, $resource, $limit, $date);
	}
}
