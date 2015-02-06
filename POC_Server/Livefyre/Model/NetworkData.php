<?php

namespace Livefyre\Model;


class NetworkData {
    private $name;
    private $key;

    public function __construct($name, $key) {
        $this->name = $name;
        $this->key = $key;
    }

    public function getKey() {
        return $this->key;
    }

    public function setKey($key) {
        $this->key = $key;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }
}