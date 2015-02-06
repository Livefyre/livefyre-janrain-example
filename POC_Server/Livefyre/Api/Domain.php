<?php

namespace Livefyre\Api;


use Livefyre\Core\Core;
use Livefyre\Utils\LivefyreUtils;

class Domain {
	public static function quill(Core $core) {
        $network = LivefyreUtils::getNetworkFromCore($core);

		return $network->isSsl() ? sprintf("https://%s.quill.fyre.co", $network->getNetworkName()) : sprintf("http://quill.%s.fyre.co", $network->getNetworkName());
	}

	public static function bootstrap(Core $core) {
        $network = LivefyreUtils::getNetworkFromCore($core);

		return $network->isSsl() ? sprintf("https://%s.bootstrap.fyre.co", $network->getNetworkName()) : sprintf("http://bootstrap.%s.fyre.co", $network->getNetworkName());
	}
}
