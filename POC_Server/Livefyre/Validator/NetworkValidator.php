<?php

namespace Livefyre\Validator;


use Livefyre\Model\NetworkData;
use Livefyre\Utils\LivefyreUtils;

class NetworkValidator {
    public static function validate(NetworkData $data) {
        $reason = "";

        $name = $data->getName();
        if (empty($name)) {
            $reason .= "\n Name is null or blank.";
        } elseif (!LivefyreUtils::endsWith($name, "fyre.co")) {
            $reason .= "\n Network name should end with '.fyre.co'.";
        }

        $key = $data->getKey();
        if (empty($key)) {
            $reason .= "\n Key is null or blank.";
        }

        if (strlen($reason) > 0) {
            throw new \InvalidArgumentException("Problems with your network input:" . $reason);
        }

        return $data;
    }
} 