<?php

namespace Livefyre\Model;


use Livefyre\Type\CollectionType;

class CollectionData {
    private $type;
    private $id;
    private $articleId;
    private $title;
    private $url;

    private $tags;
    private $topics;
    private $extensions;

    public function __construct($type, $title, $articleId, $url) {
        $this->type = $type;
        $this->title = $title;
        $this->articleId = $articleId;
        $this->url = $url;
    }

    public function asArray() {
        $array = array_filter(get_object_vars($this));
        if (array_key_exists("topics", $array)) {
            $topics = array();
            foreach($array["topics"] as $topic) {
                $topics[] = $topic->serializeToJson();
            }
            $array["topics"] = $topics;
        }
        return $array;
    }

    public function getArticleId() {
        return $this->articleId;
    }

    public function setArticleId($articleId) {
        $this->articleId = $articleId;
        return $this;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getType() {
        return $this->type;
    }

    public function setType(CollectionType $type) {
        $this->type = $type;
        return $this;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    public function getExtensions() {
        return $this->extensions;
    }

    public function setExtensions($extensions) {
        $this->extensions = $extensions;
        return $this;
    }

    public function getTags() {
        return $this->tags;
    }

    public function setTags($tags) {
        $this->tags = $tags;
        return $this;
    }

    public function getTopics() {
        return $this->topics;
    }

    public function setTopics($topics) {
        $this->topics = $topics;
        return $this;
    }
}