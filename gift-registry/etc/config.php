<?php
/**
 * CCHS Gift Registry - configuration script
 * Copyright (c) 2014-2016 Patrick Lai
 */
return call_user_func_array(function($libdir) {
	static $CONFIG;
	if (!isset($CONFIG)) {
		$settings = array(
			'dbuser' => 'cchs',
			'dbpass' => 'cchs',
			'dbname' => 'cchs',
			'dbhost' => 'localhost',
			'tzone' => 'America/Los_Angeles',
		);
		$custom = __DIR__.'/../../etc/local-settings.php';
		if (file_exists($custom))
			$settings = array_merge($settings, include $custom);

		$pdoName = 'mysql:'
			. "host={$settings['dbhost']}"
			. ';'
			. "dbname={$settings['dbuser']}"
			;
		$pdoUser = $settings['dbuser'];
		$pdoPass = $settings['dbpass'];

		$tzone = $settings['tzone'];

		$CONFIG = array(
			'pdo' => new PDO($pdoName, $pdoUser, $pdoPass),
			'tz' => new DateTimeZone($tzone),
		);

		spl_autoload_register(function($cls) use($libdir) {
			$cls = strtolower($cls);

			if (substr($cls, 0, 9) == 'plai2010\\') {
				$path = explode('\\', $cls, 3);
				switch (isset($path[1]) ? $path[1] : '') {
				case 'cchs':
					require_once "$libdir/gift-registry.php";
					break;
				case 'apiframework':
					require_once "$libdir/api-provider.php";
					break;
				case 'database':
					require_once "$libdir/db-util.php";
					break;
				}
			}
		});
	}

	return $CONFIG;
}, array(
	__DIR__.'/../lib',
));

// vim: set ts=4 noexpandtab fileformat=unix fdm=marker syntax=php:
