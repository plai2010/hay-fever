<!DOCTYPE html>
<!--
 * CCHS Gift Registry: donation page
 * Copyright (c) 2015,2016 Patrick Lai
 -->
<?php
use PLai2010\CCHS\GiftRegistry;

// TODO: configurable default user, etc.
//
$ANON_USER = 'cchs-friend';
$TOKEN_TTL = 3600*8;

$cfg = require dirname(__DIR__).'/etc/config.php';
$registry = new GiftRegistry($cfg['pdo']);

// generate an access token for default donor
//
$anon = $registry->getUserByName($ANON_USER);
$atoken = !empty($anon['id'])
	? $registry->getUserDelegateToken($TOKEN_TTL, $anon['name'])
	: null;

//--------------------------------------------------------------
?>
<html>
<head> <!--{{{-->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>California Crosspoint Gift Registry</title>
	<link rel="stylesheet" href="css/normalize.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="css/style.css">

	<style>
		div.navbar > div.nav-title {
			background-color: #880015;
		}
		div.nav-title > a {
			text-align: center;
			color: #ffffff;
			/*
			color: #337ab7;
			*/
			font-weight: bold;
			margin: auto;
			width: 100%;
		}
		div.gold-divider {
			background-color: #fff200;
			height: 10px;
		}
		div.gold-border-bot {
			border-style: solid;
			border-color: #fff200;
			border-width: 0px 0px 10px 0px;
		}
		div.home-msg > p {
			text-align: center;
		}
		img.home-logo {
			display: block;
			margin-left: auto;
			margin-right: auto;
			max-width: 320px;
			max-height: 320px;
		}
		img.trans-bg {
			display: block;
			left: 0;
			top: 0;
			width: 100%;
			height: auto;
			opacity: 0.05;
			z-index: -1;
			position: absolute;
		}
	</style>
</head> <!--}}}-->
<body>
	<!-- Javascript resources {{{-->
	<script src="js/lib/jquery-1.11.2.min.js"></script>
	<script src="js/lib/bootstrap.min.js"></script>
	<script src="js/lib/handlebars-v2.0.0.js"></script>
	<script src="js/lib/ember.prod.js"></script>
	<script src="js/lib/ember-data.js"></script>

	<script src="js/gift-registry.prod.js"></script>
	<!--
	<script src="js/gift-registry.js"></script>
	-->
	<!--}}}-->

	<!-- index {{{-->
	<script type="text/x-handlebars" id="index">
		<div class="container-fluid gold-border-bot">
			<div class="row-fluid home-msg">
				{{#each msg in model}}
				<p>{{{msg}}}</p>
				{{/each}}
				<p>
					Welcome to our donation page!
					Here are some items that our school is in need of.
				</p>
				<p>As always, thank you for your support!</p>
				<p><strong>GO RAMS!</strong></p>
				<img class="home-logo" src="images/ram-logo.jpg"/>
			</div>
		</div>
	</script>
	<!--}}}-->

	<!-- navigation tabs {{{-->
	<script type="text/x-handlebars">
		<div class="navbar">
			<div class="nav nav-title">
				<a href="" class="navbar-brand">
					California Crosspoint Gift Registry
				</a>
			</div>
			<div class="nav gold-divider">&nbsp;</div>
		</div>
		<div class="navbar">
			<ul class="nav nav-tabs">
				{{#link-to 'index' tagName="li" href=false}}
					<a {{bind-attr href="view.href"}}
						 {{bind-attr class="isRouteIndex:active:"}}>
						Home
					</a>
				{{/link-to}}
				{{#link-to 'items' tagName="li" href=false}}
					<a {{bind-attr href="view.href"}}
						 {{bind-attr class="isRouteItems:active:"}}>
						Items
					</a>
				{{/link-to}}
			</ul>
		</div>
		{{outlet}}
		{{outlet 'modal'}}
		<div class="panel-footer small">
			<p>
				This site is for authorized users only.
				Information on this site is confidential of the
				California Crosspoint Middle &amp; High School (CCHS).
			</p>
		</div>
	</script>
	<!--}}}-->

<!--*********************************************************-->

	<!-- components/modal-dialog {{{-->
	<script type="text/x-handlebars" id="components/modal-dialog">
		<div class="modal fade"><div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">{{title}}</h4>
				</div>
				<div class="modal-body">
					{{yield}}
				</div>
				<div class="modal-footer">
					<button class="btn btn-default" data-dismiss="modal">
						{{other}}
					</button>
					<button class="btn btn-primary" {{action 'ok'}}>
						{{main}}
					</button>
				</div>
			</div>
		</div></div>
	</script>
	<!--}}}-->

	<!-- confirmation {{{-->
	<script type="text/x-handlebars" data-template-name="confirmation">
		{{#modal-dialog title=title main=main other=other ok=ok close=close}}
			{{msg}}
		{{/modal-dialog}}
	</script>
	<!--}}}-->

	<!-- pledge-form {{{-->
	<script type="text/x-handlebars" data-template-name="pledge-form">
		{{#modal-dialog title=title main=main other=other ok=ok close=close}}
			<table class="table">
				<thead>
					<tr>
						<th>Item #{{item.id}}: {{item.name}}</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Amount:
						${{item.price}} &times; {{input type="text" size="3" value=count}}
						= ${{amount}}
						</td>
					</tr>
				</tbody>
			</table>
		{{/modal-dialog}}
	</script>
	<!--}}}-->

	<!-- entity-picker {{{-->
	<script type="text/x-handlebars" data-template-name="entity-picker">
		{{#modal-dialog title=title main=main other=other ok=ok close=close}}
			<button class="btn btn-xs" {{action 'pageFirst'}}>|&lsaquo;</button>
			<button class="btn btn-xs" {{action 'pagePrev'}}>&laquo;</button>
			<!--&hellip;-->
			{{pgno}}
			<!--&hellip;-->
			<button class="btn btn-xs" {{action 'pageNext'}}>&raquo;</button>
			&nbsp;&nbsp;
			Name:
			{{input type="text" size="12" value=searchStr insert-newline="search"}}
			<button class="btn btn-sm" {{action 'search'}}>Search</button>

			<table class="table">
				<thead>
					<tr>
						<th>
							Selected:
								{{#unless picked.id}}(none){{else}}
									<button class="btn btn-xs" {{action 'unpick'}}>
										&times;
									</button>
									{{picked.name}} [#{{picked.id}}]
								{{/unless}}
						</th>
					</tr>
				</thead>
				<tbody>
					{{#each model.entities}}
					<tr>
						<td>
							<button class="btn btn-xs" {{action 'pick' this}}>
								&#10003;
							</button>
							{{name}}
						</td>
					</tr>
					{{/each}}
				</tbody>
			</table>
		{{/modal-dialog}}
	</script>
	<!--}}}-->

<!--*********************************************************-->

	<!-- items/_control {{{-->
	<script type="text/x-handlebars" id="items/_control">
		<table class="table-condensed">
			<tbody>
			<tr>
				<td>
					<button class="btn btn-xs" {{action 'pageFirst'}}>|&lsaquo;</button>
					<button class="btn btn-xs" {{action 'pagePrev'}}>&laquo;</button>
					<!--&hellip;-->
					{{page}} / {{total}}
					<!--&hellip;-->
					<button class="btn btn-xs" {{action 'pageNext'}}>&raquo;</button>
					<button class="btn btn-xs" {{action 'pageLast'}}>&rsaquo;|</button>
				</td>
				<td>
					Name:
					{{input type="text" size="12"
						value=nameFilter insert-newline="applyItemFilters"}}
					<button class="btn btn-xs" {{action 'clearNameFilter'}}>
						&times;
					</button>
				</td>
				<td>
					Category:
					{{view "select" value=deptFilter
						optionLabelPath="content.name"
						content=deptChoices
					}}
				</td>
			</tr>
			</tbody>
		</table>
	</script>
	<!--}}}-->

	<!-- items {{{-->
	<script type="text/x-handlebars" id="items">
		<div class="container-fluid gold-border-bot">
			<div class="row-fluid">
				<div class="col-md-10">
					{{partial "items/control"}}
					<table class="table">
						<thead>
							<tr>
								<th>Name</th>
								<th>&nbsp;</th>
								<th class="number-cell">$Needed</th>
								<th>Description</th>
								<th class="number-cell">Unit Cost</th>
								<th class="number-cell">Quantity</th>
								<th class="number-cell">%Pledged</th>
							</tr>
						</thead>
						<tbody>
							{{#each it in model}}
							<tr>
								<td>
									{{#if it.detail_url}}
									<a title="For information only. Don't purchase there!"
										onclick="return confirm('Go to external information page?')"
										{{bind-attr href=it.detail_url}}>
										{{it.name}}
									</a>
									{{else}}
									{{it.name}}
									{{/if}}
								</td>
								<td>
									{{#if it.image_url}}
									<img class="item-img-small" {{bind-attr src=it.image_url}}>
									{{/if}}
								</td>
								<td class="number-cell">
									{{#if it.remain}}
									<button class="btn btn-xs" title="Purchase here!"
										{{action 'contribToItem' it}}>
										${{it.remain}}
									</button>
									{{else}}
									<button class="btn btn-xs" disabled>
										${{it.remain}}
									</button>
									{{/if}}
								</td>
								<td>{{it.description}}</td>
								<td class="number-cell">${{it.price}}</td>
								<td class="number-cell">{{it.quantity}}</td>
								<td class="number-cell">{{it.progress}}%</td>
							</tr>
							{{/each}}
						</tbody>
					</table>
				</div>
				<img class="trans-bg" src="images/ram-logo.jpg"/>
			</div>
		</div>
	</script>
	<!--}}}-->

<!--*********************************************************-->
</body>
	<script type="text/javascript">
		$(document).ready(function() {
			<?php if (!empty($atoken)) { ?>
			CchsApp.setAccessToken(<?php echo json_encode($atoken); ?>);
			<?php } ?>
			CchsApp.setDefaultGiver(1);

			// go to item list
			//
			window.location.replace('#/items');
		});
	</script>
</html>

<!-- vim: set ts=2 noexpandtab fileformat=unix fdm=marker syntax=php: -->
