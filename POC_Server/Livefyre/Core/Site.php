<?php

namespace Livefyre\Core;


use Livefyre\Model\SiteData;
use Livefyre\Type\CollectionType;
use Livefyre\Validator\SiteValidator;

class Site extends Core {
	private $_network;
    private $_data;

	public function __construct(Network $network, SiteData $data) {
		$this->_network = $network;
		$this->_data = $data;
	}

    public static function init(Network $network, $name, $key) {
        $data = new SiteData($name, $key);
        return new Site($network, SiteValidator::validate($data));
    }

    public function buildCommentsCollection($title, $articleId, $url) {
        return $this->buildCollection(CollectionType::COMMENTS, $title, $articleId, $url);
    }

    public function buildBlogCollection($title, $articleId, $url) {
        return $this->buildCollection(CollectionType::BLOG, $title, $articleId, $url);
    }

    public function buildChatCollection($title, $articleId, $url) {
        return $this->buildCollection(CollectionType::CHAT, $title, $articleId, $url);
    }

    public function buildCountingCollection($title, $articleId, $url) {
        return $this->buildCollection(CollectionType::COUNTING, $title, $articleId, $url);
    }

    public function buildRatingsCollection($title, $articleId, $url) {
        return $this->buildCollection(CollectionType::RATINGS, $title, $articleId, $url);
    }

    public function buildReviewsCollection($title, $articleId, $url) {
        return $this->buildCollection(CollectionType::REVIEWS, $title, $articleId, $url);
    }

    public function buildSidenotesCollection($title, $articleId, $url) {
        return $this->buildCollection(CollectionType::SIDENOTES, $title, $articleId, $url);
    }

    public function buildCollection($type, $title, $articleId, $url) {
        return Collection::init($this, $type, $title, $articleId, $url);
    }

    public function getUrn() {
        return $this->_network->getUrn() . ":site=" . $this->getData()->getId();
    }

    public function getNetwork() {
        return $this->_network;
    }

    public function setNetwork($network) {
        $this->_network = $network;
    }

    public function getData() {
        return $this->_data;
    }

    public function setData($data) {
        $this->_data = $data;
    }
}
