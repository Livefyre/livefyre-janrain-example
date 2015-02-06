<?php

namespace Livefyre\Cursor;


use Livefyre\Api\PersonalizedStream;
use Livefyre\Core\Core;
use Livefyre\Model\CursorData;
use Livefyre\Validator\CursorValidator;

class TimelineCursor {
	const DATE_FORMAT = "Y-m-d\\TH:i:s.z\\Z";

	private $_core;
	private $_data;

	public function __construct(Core $core, CursorData $data) {
		$this->_core = $core;
		$this->_data = $data;
	}

	public static function init($core, $resource, $limit, $startTime) {
		$data = new CursorData($resource, $limit, $startTime);
		return new TimelineCursor($core, CursorValidator::validate($data));
	}

	public function next() {
		$resp = PersonalizedStream::getTimelineStream($this, true);
		$cursor = $resp->{"meta"}->{"cursor"};

		$data = $this->getData();
		$data->setNext($cursor->{"hasNext"});
		$data->setPrevious($cursor->{"next"} !== null);
		if ($data->isPrevious()) {
			$data->setCursorTime($cursor->{"next"});
		}

		return $resp;
	}

	public function previous() {
		$resp = PersonalizedStream::getTimelineStream($this, false);
		$cursor = $resp->{"meta"}->{"cursor"};

		$data = $this->getData();
		$data->setPrevious($cursor->{"hasPrev"});
		$data->setNext($cursor->{"prev"} !== null);
		if ($data->isNext()) {
			$data->setCursorTime($cursor->{"prev"});
		}

		return $resp;
	}

	public function getCore() {
		return $this->_core;
	}
	public function setCore($core) {
		$this->_core = $core;
	}
	public function getData() {
		return $this->_data;
	}
	public function setData(CursorData $data) {
		$this->_data = $data;
	}
}