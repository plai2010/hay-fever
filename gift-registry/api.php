<?php
/**
 * CCHS Gift Registry
 * Copyright (c) 2014-2016 Patrick Lai
 */

use PLai2010\ApiFramework\ApiProvider;
use PLai2010\CCHS\GiftRegistry;

call_user_func_array(function($cfg, $apiDefs, $user) {
	// API invocation details from request environment
	//
	$method = isset($_SERVER['REQUEST_METHOD'])
		? $_SERVER['REQUEST_METHOD'] : 'GET';
	$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : null;
	$inputs = isset($_REQUEST) ? $_REQUEST : null;
	$ctype = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : null;

	// access token as user
	//
	if (!isset($user) && isset($inputs['_atoken']))
		$user = $inputs['_atoken'];

	$result = null;

	$gr = new GiftRegistry($cfg['pdo'], $user);
	$api = new ApiProvider($apiDefs, $gr, function($spec,$who,$args) use($gr) {
		list($roles, $dynamic) = $spec;
		if ($dynamic)
			$roles = array_merge($roles, call_user_func($dynamic, $args));
		return $gr->hasRoleAny($roles, $who);
	});

	// match API and execute it
	//
	if (($ctx = $api->matchApi($method, $path)) !== null) {
		try {
			$body = array($ctype);
			$getBody = function() use(&$body) {
				// already retrieved?
				//
				if (count($body) > 1)
					return $body;

				// retrieve body and remember it
				//
				if (($data = file_get_contents('php://input')) === false)
					$body = array(null, null);
				else
					$body[] = $data;

				return $body;
			};

			$result = $api->executeApi($ctx, $user, $inputs, $getBody);
		}
		catch (Exception $ex) {
			$result = $api->decodeApiException($ex);
		}
	}
	else {
		$result = $api->makeApiError(ApiProvider::ERR_API_UNKNOWN, null, array(
			'path' => $path,
		));
	}

	// render result in JSON
	//
	header('Content-Type: application/json');
	header('Cache-Control: private, max-age=0, no-cache');

	echo json_encode($result);
}, array(
	require __DIR__.'/etc/config.php',
	require __DIR__.'/etc/gift-registry-api.php',
	isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : null,
));

// vim: set ts=4 noexpandtab fileformat=unix fdm=marker syntax=php:
