<?php

namespace Livefyre\Validator;


use Livefyre\Model\CursorData;

class CursorValidator {
    public static function validate(CursorData $data) {
        $reason = "";

        $resource = $data->getResource();
        if (empty($resource)) {
            $reason .= "\n Resource is null or blank.";
        }

        $limit = $data->getLimit();
        if (empty($limit)) {
            $reason .= "\n Limit is null or blank.";
        }

        $cursorTime = $data->getCursorTime();
        if (empty($cursorTime)) {
            $reason .= "\n Cursor time is null or blank.";
        }

        if (strlen($reason) > 0) {
            throw new \InvalidArgumentException("Problems with your cursor input:" . $reason);
        }

        return $data;
    }
}