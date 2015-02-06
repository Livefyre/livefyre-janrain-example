<?php

namespace Livefyre\Model;


class SiteData {
    private $id;
    private $key;

    public function __construct($id, $key) {
        $this->id = $id;
        $this->key = $key;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getKey() {
        return $this->key;
    }

    public function setKey($key) {
        $this->key = $key;
        return $this;
    }
}