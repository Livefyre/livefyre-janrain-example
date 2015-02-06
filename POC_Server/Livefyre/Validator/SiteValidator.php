<?php

namespace Livefyre\Validator;


use Livefyre\Model\SiteData;

class SiteValidator {
    public static function validate(SiteData $data) {
        $reason = "";

        $id = $data->getId();
        if (empty($id)) {
            $reason .= "\n ID is null or blank.";
        }

        $key = $data->getKey();
        if (empty($key)) {
            $reason .= "\n Key is null or blank.";
        }

        if (strlen($reason) > 0) {
            throw new \InvalidArgumentException("Problems with your site input:" . $reason);
        }

        return $data;
    }
}