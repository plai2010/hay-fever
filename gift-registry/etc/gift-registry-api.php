<?php
/**
 * CCHS Gift Registry - API definitions.
 * Copyright (c) 2014-2016 Patrick Lai
 */

use PLai2010\ApiFramework\ApiProvider;
use PLai2010\CCHS\GiftRegistry;

// authz requirement is a two-element tuple:
// - list of roles (match any)
// - callback (taking API arguments) to provide dynamic roles to match

return call_user_func(function() {
//**********************************************************************
$helpers = new stdClass;

$helpers->buildPaginationOpts = function($inputs, $opts=null) { /*{{{*/
	$max = 10;
	if (isset($inputs['pgsz']))
		$max = $opts['range']['max'] = $inputs['pgsz'];
	if (isset($inputs['pgno']))
		$opts['range']['off'] = $max * ($inputs['pgno'] - 1);

	return $opts;
}; /*}}}*/

$helpers->buildDonorListOpts = function($inputs) use($helpers) { /*{{{*/
	$opts = call_user_func($helpers->buildPaginationOpts, $inputs);

	if (isset($inputs['status']))
		$opts['filters']['sts_donor'] = $inputs['status'];
	if (isset($inputs['f_alumnus']))
		$opts['filters']['alumnus'] = (boolean)$inputs['f_alumnus'];
	if (isset($inputs['f_board']))
		$opts['filters']['board'] = (boolean)$inputs['f_board'];
	if (isset($inputs['f_others']))
		$opts['filters']['others'] = (boolean)$inputs['f_others'];
	if (isset($inputs['f_marked']))
		$opts['filters']['marked'] = (boolean)$inputs['f_marked'];
	if (isset($inputs['tag']))
		$opts['filters']['tags_like'] = $inputs['tag'];
	if (isset($inputs['search']))
		$opts['filters']['name_like'] = $inputs['search'];

	return $opts;
}; /*}}}*/

$helpers->buildDonorViewerRoles = function($args) { /*{{{*/
	// donor ID is first argument
	//
	list($donorId) = $args;

	$roles = array();

	if (!empty($donorId))
		$roles[] = GiftRegistry::getDonorViewerRole($donorId);

	return $roles;
}; /*}}}*/

$helpers->buildPledgeListOpts = function($inputs) use($helpers) { /*{{{*/
	$opts = call_user_func($helpers->buildPaginationOpts, $inputs);

	if (isset($inputs['status']))
		$opts['filters']['sts_pledge'] = $inputs['status'];

	return $opts;
}; /*}}}*/

$helpers->buildPledgeCreatorRoles = function($args) { /*{{{*/
	// donor ID is first argument
	//
	list($donorId) = $args;

	$roles = array();

	if (!empty($donorId))
		$roles[] = GiftRegistry::getPledgeCreatorRole($donorId);

	return $roles;
}; /*}}}*/

$helpers->buildPledgeViewerRoles = function($args) { /*{{{*/
	// donor ID is first argument
	//
	list($donorId) = $args;

	$roles = array();

	if (!empty($donorId))
		$roles[] = GiftRegistry::getPledgeViewerRole($donorId);

	return $roles;
}; /*}}}*/

$helpers->buildDeptListOpts = function($inputs) use($helpers) { /*{{{*/
	$opts = call_user_func($helpers->buildPaginationOpts, $inputs);

	if (isset($inputs['tag']))
		$opts['filters']['tags_like'] = $inputs['tag'];
	if (isset($inputs['search']))
		$opts['filters']['name_like'] = $inputs['search'];
	if (isset($inputs['items']) && $inputs['items'] == 'count')
		$opts['count_items'] = true;

	return $opts;
}; /*}}}*/

$helpers->buildItemListOpts = function($inputs) use($helpers) { /*{{{*/
	$opts = call_user_func($helpers->buildPaginationOpts, $inputs);

	if (isset($inputs['search']))
		$opts['filters']['name_like'] = $inputs['search'];
	if (isset($inputs['dept']) && is_numeric($inputs['dept']))
		$opts['filters']['dept_id'] = (int)$inputs['dept'];

	return $opts;
}; /*}}}*/

return array(
	//--------------------------------------------------------------
	// get list of items {{{
	//
	array(
		'pattern' => '%^GET:/items/?$%',
		'impl' => array(null, 'getItemList'),
		'params' => array(
			ApiProvider::PARAM_SRC_OPTS => $helpers->buildItemListOpts,
		),
		'args' => array(ApiProvider::PARAM_OPTIONS),
		'marshal' => function($list) {
			list($items, $meta) = $list;
			return array(
				'items' => $items,
				'meta' => $meta,
			);
		},
	),
	/*}}}*/

	//--------------------------------------------------------------
	// create item {{{
	//
	array(
		'pattern' => '%^POST:/items/?$%',
		'impl' => array(null, 'createItem'),
		'params' => array(
			ApiProvider::PARAM_SRC_BODY => array(
				'item' => 'dummy-0',
			),
		),
		'args' => array('dummy-0'),
		'marshal' => function($dept) {
			return array(
				'dept' => $dept,
			);
		},
		'authz' => array(array(
			GiftRegistry::ROLE_DBA,
			GiftRegistry::ROLE_CLERK,
		), null),
	),
	/*}}}*/

	//--------------------------------------------------------------
	// get item by ID {{{
	//
	array(
		'pattern' => '%^GET:/items/([0-9]+)/?$%',
		'impl' => array(null, 'getItemById'),
		'args' => array(0),
		'marshal' => function($item) {
			return array(
				'item' => $item,
			);
		},
	),
	/*}}}*/

	//--------------------------------------------------------------
	// update item by ID {{{
	//
	array(
		'pattern' => '%^PUT:/items/([0-9]+)/?$%',
		'impl' => array(null, 'updateItemById'),
		'params' => array(
			ApiProvider::PARAM_SRC_BODY => array(
				'item' => 'dummy-0',
			),
		),
		'args' => array(0, 'dummy-0'),
		'marshal' => function($item) {
			return array(
				'item' => $item,
			);
		},
		'authz' => array(array(
			GiftRegistry::ROLE_DBA,
			GiftRegistry::ROLE_CLERK,
		), null),
	),
	/*}}}*/

	//--------------------------------------------------------------
	// delete item by ID {{{
	//
	array(
		'pattern' => '%^DELETE:/items/([0-9]+)/?$%',
		'impl' => array(null, 'deleteItemById'),
		'args' => array(0),
		'marshal' => function($okay) {
			return array(
				'okay' => (boolean)$okay,
			);
		},
		'authz' => array(array(
			GiftRegistry::ROLE_DBA,
		), null),
	),
	/*}}}*/

	//--------------------------------------------------------------
	// get list of departments {{{
	//
	array(
		'pattern' => '%^GET:/depts/?$%',
		'impl' => array(null, 'getDeptList'),
		'params' => array(
			ApiProvider::PARAM_SRC_OPTS => $helpers->buildDeptListOpts,
		),
		'args' => array(ApiProvider::PARAM_OPTIONS),
		'marshal' => function($list) {
			list($depts, $meta) = $list;
			return array(
				'depts' => $depts,
				'meta' => $meta,
			);
		},
	),
	/*}}}*/

	//--------------------------------------------------------------
	// create department {{{
	//
	array(
		'pattern' => '%^POST:/depts/?$%',
		'impl' => array(null, 'createDept'),
		'params' => array(
			ApiProvider::PARAM_SRC_BODY => array(
				'dept' => 'dummy-0',
			),
		),
		'args' => array('dummy-0'),
		'marshal' => function($dept) {
			return array(
				'dept' => $dept,
			);
		},
		'authz' => array(array(
			GiftRegistry::ROLE_DBA,
			GiftRegistry::ROLE_CLERK,
		), null),
	),
	/*}}}*/

	//--------------------------------------------------------------
	// get department by ID {{{
	//
	array(
		'pattern' => '%^GET:/depts/([0-9]+)/?$%',
		'impl' => array(null, 'getDeptById'),
		'args' => array(0),
		'marshal' => function($dept) {
			return array(
				'dept' => $dept,
			);
		},
	),
	/*}}}*/

	//--------------------------------------------------------------
	// update department by ID {{{
	//
	array(
		'pattern' => '%^PUT:/depts/([0-9]+)/?$%',
		'impl' => array(null, 'updateDeptById'),
		'params' => array(
			ApiProvider::PARAM_SRC_BODY => array(
				'dept' => 'dummy-0',
			),
		),
		'args' => array(0, 'dummy-0'),
		'marshal' => function($dept) {
			return array(
				'dept' => $dept,
			);
		},
		'authz' => array(array(
			GiftRegistry::ROLE_DBA,
			GiftRegistry::ROLE_CLERK,
		), null),
	),
	/*}}}*/

	//--------------------------------------------------------------
	// delete department by ID {{{
	//
	array(
		'pattern' => '%^DELETE:/depts/([0-9]+)/?$%',
		'impl' => array(null, 'deleteDeptById'),
		'args' => array(0),
		'marshal' => function($okay) {
			return array(
				'okay' => (boolean)$okay,
			);
		},
		'authz' => array(array(
			GiftRegistry::ROLE_DBA,
		), null),
	),
	/*}}}*/

	//--------------------------------------------------------------
	// get list of donors {{{
	//
	array(
		'pattern' => '%^GET:/donors/?$%',
		'impl' => array(null, 'getDonorList'),
		'params' => array(
			ApiProvider::PARAM_SRC_OPTS => $helpers->buildDonorListOpts,
		),
		'args' => array(ApiProvider::PARAM_OPTIONS),
		'marshal' => function($list) {
			list($donors, $meta) = $list;
			return array(
				'donors' => $donors,
				'meta' => $meta,
			);
		},
		'authz' => array(array(
			GiftRegistry::ROLE_DBA,
			GiftRegistry::ROLE_CLERK,
		), null),
	),
	/*}}}*/

	//--------------------------------------------------------------
	// create donor {{{
	//
	array(
		'pattern' => '%^POST:/donors/?$%',
		'impl' => array(null, 'createDonor'),
		'params' => array(
			ApiProvider::PARAM_SRC_BODY => array(
				'donor' => 'dummy-0',
			),
		),
		'args' => array('dummy-0'),
		'marshal' => function($donor) {
			return array(
				'donor' => $donor,
			);
		},
		'authz' => array(array(
			GiftRegistry::ROLE_DBA,
			GiftRegistry::ROLE_CLERK,
		), null),
	),
	/*}}}*/

	//--------------------------------------------------------------
	// get donor by ID {{{
	//
	array(
		'pattern' => '%^GET:/donors/([0-9]+)/?$%',
		'impl' => array(null, 'getDonorById'),
		'args' => array(0),
		'marshal' => function($donor) {
			return array(
				'donor' => $donor,
			);
		},
		'authz' => array(array(
			GiftRegistry::ROLE_DBA,
			GiftRegistry::ROLE_CLERK,
		), $helpers->buildDonorViewerRoles),
	),
	/*}}}*/

	//--------------------------------------------------------------
	// update donor by ID {{{
	//
	array(
		'pattern' => '%^PUT:/donors/([0-9]+)/?$%',
		'impl' => array(null, 'updateDonorById'),
		'params' => array(
			ApiProvider::PARAM_SRC_BODY => array(
				'donor' => 'dummy-0',
			),
		),
		'args' => array(0, 'dummy-0'),
		'marshal' => function($donor) {
			return array(
				'donor' => $donor,
			);
		},
		'authz' => array(array(
			GiftRegistry::ROLE_DBA,
			GiftRegistry::ROLE_CLERK,
		), null),
	),
	/*}}}*/

	//--------------------------------------------------------------
	// delete donor by ID {{{
	//
	array(
		'pattern' => '%^DELETE:/donors/([0-9]+)/?$%',
		'impl' => array(null, 'deleteDonorById'),
		'args' => array(0),
		'marshal' => function($okay) {
			return array(
				'okay' => (boolean)$okay,
			);
		},
		'authz' => array(array(
			GiftRegistry::ROLE_DBA,
		), null),
	),
	/*}}}*/

	//--------------------------------------------------------------
	// get list of pledges of a donor {{{
	//
	array(
		'pattern' => '%^GET:/donors/([0-9]+)/pledges/?$%',
		'impl' => array(null, 'getDonorPledges'),
		'args' => array(0),
		'marshal' => function($list) {
			return array(
				'pledges' => $list,
			);
		},
		'authz' => array(array(
			GiftRegistry::ROLE_DBA,
			GiftRegistry::ROLE_CLERK,
		), $helpers->buildPledgeViewerRoles),
	),
	/*}}}*/

	//--------------------------------------------------------------
	// create a pledge for a donor {{{
	//
	array(
		'pattern' => '%^POST:/donors/([0-9]+)/pledges/?$%',
		'impl' => array(null, 'createDonorPledge'),
		'params' => array(
			ApiProvider::PARAM_SRC_BODY => array(
				'pledge' => 'dummy-0',
			),
			ApiProvider::PARAM_SRC_VALUES => array(
				'dummy-1' => isset($_SERVER['REMOTE_ADDR'])
					? "IP:{$_SERVER['REMOTE_ADDR']}" : null,
			),
		),
		'args' => array(0, 'dummy-0', 'dummy-1'),
		'marshal' => function($pledge) {
			return array(
				'pledge' => $pledge,
			);
		},
		'authz' => array(array(
			GiftRegistry::ROLE_DBA,
			GiftRegistry::ROLE_CLERK,
		), $helpers->buildPledgeCreatorRoles),
	),
	/*}}}*/

	//--------------------------------------------------------------
	// get list of pledges {{{
	//
	array(
		'pattern' => '%^GET:/pledges/?$%',
		'impl' => array(null, 'getPledgeList'),
		'params' => array(
			ApiProvider::PARAM_SRC_OPTS => $helpers->buildPledgeListOpts,
		),
		'args' => array(ApiProvider::PARAM_OPTIONS),
		'marshal' => function($list) {
			list($pledges, $meta) = $list;
			return array(
				'pledges' => $pledges,
				'meta' => $meta,
			);
		},
		'authz' => array(array(
			GiftRegistry::ROLE_DBA,
			GiftRegistry::ROLE_CLERK,
		), null),
	),
	/*}}}*/

	//--------------------------------------------------------------
	// create pledge {{{
	//
	array(
		'pattern' => '%^POST:/pledges/?$%',
		'impl' => array(null, 'createPledge'),
		'params' => array(
			ApiProvider::PARAM_SRC_BODY => array(
				'pledge' => 'dummy-0',
			),
			ApiProvider::PARAM_SRC_VALUES => array(
				'dummy-1' => isset($_SERVER['REMOTE_ADDR'])
					? "IP:{$_SERVER['REMOTE_ADDR']}" : null,
			),
		),
		'args' => array('dummy-0', 'dummy-1'),
		'marshal' => function($pledge) {
			return array(
				'pledge' => $pledge,
			);
		},
		'authz' => array(array(
			GiftRegistry::ROLE_DBA,
			GiftRegistry::ROLE_CLERK,
		), null),
	),
	/*}}}*/

	//--------------------------------------------------------------
	// get pledge by ID {{{
	//
	array(
		'pattern' => '%^GET:/pledges/([0-9]+)/?$%',
		'impl' => array(null, 'getPledgeById'),
		'args' => array(0),
		'marshal' => function($pledge) {
			return array(
				'pledge' => $pledge,
			);
		},
		'authz' => array(array(
			GiftRegistry::ROLE_DBA,
			GiftRegistry::ROLE_CLERK,
		), null),
	),
	/*}}}*/

	//--------------------------------------------------------------
	// update a pledge {{{
	//
	array(
		'pattern' => '%^PUT:/pledges/([0-9]+)/?$%',
		'impl' => array(null, 'updatePledgeById'),
		'params' => array(
			ApiProvider::PARAM_SRC_BODY => array(
				'pledge' => 'dummy-0',
			),
		),
		'args' => array(0, 'dummy-0'),
		'marshal' => function($pledge) {
			return array(
				'pledge' => $pledge,
			);
		},
		'authz' => array(array(
			GiftRegistry::ROLE_DBA,
			GiftRegistry::ROLE_CLERK,
		), null),
	),
	/*}}}*/

	//--------------------------------------------------------------
	// delete a pledge {{{
	//
	array(
		'pattern' => '%^DELETE:/pledges/([0-9]+)/?$%',
		'impl' => array(null, 'deletePledge'),
		'args' => array(0),
		'marshal' => function($okay) {
			return array(
				'okay' => (boolean)$okay,
			);
		},
		'authz' => array(array(
			GiftRegistry::ROLE_DBA,
			GiftRegistry::ROLE_CLERK,
		), null),
	),
	/*}}}*/
);
//**********************************************************************
});

// vim: set ts=4 noexpandtab fileformat=unix fdm=marker syntax=php:
