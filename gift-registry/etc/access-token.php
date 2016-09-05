<?php
/**
 * CCHS Gift Registry - API definitions.
 * Copyright (c) 2016 Patrick Lai
 */

use PLai2010\CCHS\GiftRegistry;

$cfg = require_once __DIR__.'/config.php';

global $PROG;
$PROG = pathinfo(__FILE__, PATHINFO_FILENAME);

$cmd = null;
$user = null;
$roles = null;
$ttl = 1;

$opts = getopt('', array(
	'create',
	'user:',
	'role:',
	'ttl:',
));

function usage($rc=1) {
	global $PROG;
	echo "usage: $PROG",
		" --create [--user=<user>|--role=<role>...] [--ttl=<days>]",
		"\n";
	exit($rc);
}

function abort($msg) {
	global $PROG;
	die("*** $PROG: $msg");
}

if (isset($opts['create']))
	$cmd = 'create';
if (!empty($opts['user']))
	$user = $opts['user'];
if (!empty($opts['role']))
	$roles = (array)$opts['role'];
if (!empty($opts['ttl']))
	$ttl = $opts['ttl'];

if (empty($cmd))
	usage();

$registry = new GiftRegistry($cfg['pdo'], GiftRegistry::SYSTEM_USER);

switch ($cmd) {
case 'create':
	if ($ttl <= 0)
		abort('invalid --ttl');
	$life = $ttl * 86400;

	if ($user)
		$token = $registry->getUserDelegateToken($life, $user);
	else if ($roles)
		$token = $registry->getRolesToken($life, $roles);
	else
		abort('must specify --user or --role');
		
	echo "$token\n";
	break;
default:
	usage();
	break;
}

// vim: set ts=4 noexpandtab fileformat=unix fdm=marker syntax=php:
