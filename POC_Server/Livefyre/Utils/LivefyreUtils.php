<?php

namespace Livefyre\Utils;


use Livefyre\Core\Core;
use Livefyre\Core\Network;
use Livefyre\Core\Site;

class LivefyreUtils {
    public static function getNetworkFromCore(Core $core) {
        if (get_class($core) == Network::getClassName()) {
            return $core;
        } elseif (get_class($core) == Site::getClassName()) {
            return $core->getNetwork();
        } else {
            return $core->getSite()->getNetwork();
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public static function startsWith($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * @codeCoverageIgnore
     */
    public static function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    public static function isValidUrl($url) {
        $IDNA = new IDNA(array('idn_version' => 2008));

        return filter_var($IDNA->encode($url), FILTER_VALIDATE_URL) !== false;
    }
}