<?php

namespace Livefyre\Model;


class CursorData {
    const DATE_FORMAT = "Y-m-d\\TH:i:s.z\\Z";

    private $resource;
    private $cursorTime;
    private $next = false;
    private $previous = false;
    private $limit;

    public function __construct($resource, $limit, $startTime) {
        $this->resource = $resource;
        $this->cursorTime = gmdate(self::DATE_FORMAT, $startTime);
        $this->limit = $limit;
    }

    public function getResource() {
        return $this->resource;
    }

    public function setResource($resource) {
        $this->resource = $resource;
        return $this;
    }

    public function getCursorTime() {
        return $this->cursorTime;
    }

    public function setCursorTime($cursorTime) {
        $this->cursorTime = $cursorTime;
        return $this;
    }

    public function setCursorTimeWithDate($cursorTime) {
        $this->cursorTime = gmdate(self::DATE_FORMAT, $cursorTime);
        return $this;
    }

    public function isNext() {
        return $this->next;
    }

    public function setNext($next) {
        $this->next = $next;
        return $this;
    }

    public function isPrevious() {
        return $this->previous;
    }

    public function setPrevious($previous) {
        $this->previous = $previous;
        return $this;
    }

    public function getLimit() {
        return $this->limit;
    }

    public function setLimit($limit) {
        $this->limit = $limit;
        return $this;
    }
}