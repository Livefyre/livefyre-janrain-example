<?php

namespace Livefyre\Routing;


use Livefyre\Exceptions\ApiException;
use Requests;

/**
 * @codeCoverageIgnore
 */
class Client {
	public static function GET($url, $headers = array()) {
		if (function_exists("wp_remote_get")) {
			$response = wp_remote_get($url, array("headers" => $headers));

			self::examineResponse($response["response"]["code"]);
			return $response["body"];
		} else {
			$response = Requests::get($url, $headers);

			self::examineResponse($response->status_code);
			return $response->body;
		}
	}

	public static function POST($url, $headers = array(), $data = array(), $handle = true) {
		if (function_exists("wp_remote_post")) {
			$response = wp_remote_post($url, array("headers"=>$headers, "body"=>$data));

			self::examineResponse($response["response"]["code"]);
			return $response["body"];
		} else {
			$response = Requests::post($url, $headers, $data);

			if ($handle) {
				self::examineResponse($response->status_code);
				return $response->body;
			}
			return $response;
		}
	}

	public static function PUT($url, $headers = array(), $data = array()) {
		if (function_exists("wp_remote_request")) {
			$response = wp_remote_request($url, array("headers"=>$headers, "body"=>$data));

			self::examineResponse($response["response"]["code"]);
			return $response["body"];
		} else {
			$response = Requests::put($url, $headers, $data);

			self::examineResponse($response->status_code);
			return $response->body;
		}
	}

	public static function PATCH($url, $headers = array(), $data = array()) {
		if (function_exists("wp_remote_request")) {
			$response = wp_remote_request($url, array("headers"=>$headers, "body"=>$data));

			self::examineResponse($response["response"]["code"]);
			return $response["body"];
		} else {
			$response = Requests::patch($url, $headers, $data);

			self::examineResponse($response->status_code);
			return $response->body;
		}
	}

	public static function examineResponse($code) {
		if ($code >= 400) {
			throw new ApiException($code);
		}
	}
}