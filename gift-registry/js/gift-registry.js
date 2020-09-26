/**
 * CCHS Gift Registry - application JS
 * @license Copyright (c) 2015,2016 Patrick Lai
 */

var CchsAppConfig = CchsAppConfig || {};

var CchsApp = (function(Ember, DS, app, cfg) {
	var INFLECTOR = Ember.Inflector.inflector;
	var ROUTE = Ember.Route;
	var MODEL = DS.Model;
	var ATTR = DS.attr;

	app = app || Ember.Application.create();

	var STATE = {
		api_ns: cfg.api_ns || (function(path) {
			var defaultNS = 'gifts/api';

			if (!path)
				return defaultNS;
			if (path.substr(0,1) == '/')
				path = path.substr(1);
			if (path.substr(-1) == '/')
				path = path.substr(0, path.length-1);

			return (path == '' || path == '/') ? defaultNS : path+'/api';
		})(window.location.pathname),

		atoken: null,
		giver_id: null,

		config: {
			pgsz: Math.max(5, Math.floor((window.innerHeight - 300) / 80)),
			donate_url: 'http://www.cchsrams.org/support-us/'
		}
	};

	var HELPERS = { /*{{{*/
		// prepare API URL
		//

		// get full name from last/first name
		//
		getFullName: function(lname, fname) {
			if (!fname && !lname)
				return '';
			if (!fname)
				return lname;
			if (!lname)
				return fname;
			return fname + ' ' + lname;
		},

		// pick department
		//
		pickDept: function(route) {
			var deptKey = 'controller.dept';
			var deptIdKey = 'controller.model.dept_id';

			var idx = 1;
			if (arguments.length > idx)
				deptKey = arguments[idx++];
			if (arguments.length > idx)
				deptIdKey = arguments[idx++];

			var dept = route.get(deptKey);
			if (dept && !dept.id)
				dept = null;

			var picker = app.EntityPicker.create({
				what: 'dept',
				title: 'Pick Category',
				picked: dept,
				entities: dept ? [ dept ] : [],
				trigger: function(dept, picker) {
					if (dept) {
						if (deptKey)
							route.set(deptKey, dept);
						if (deptIdKey)
							route.set(deptIdKey, dept.id);
					}
				}
			});
			route.send('showModal', 'entity-picker', picker);
		},

		// pick donor
		//
		pickDonor: function(route) {
			var donor = route.get('controller.donor');

			var picker = app.EntityPicker.create({
				what: 'donor',
				title: 'Pick Supporter',
				picked: donor,
				entities: donor ? [ donor ] : [],
				fixup: function(donor) {
					if (!donor)
						return;
					donor.name = HELPERS.getFullName(donor.l_name, donor.f_name);
				},
				trigger: function(donor, picker) {
					if (donor) {
						route.set('controller.donor', donor);
						route.set('controller.model.donor_id', donor.id);
					}
				}
			});
			route.send('showModal', 'entity-picker', picker);
		},

		// pick item
		//
		pickItem: function(route) {
			var item = route.get('controller.item');

			var picker = app.EntityPicker.create({
				what: 'item',
				title: 'Pick Item',
				picked: item,
				entities: item ? [ item ] : [],
				trigger: function(item, picker) {
					if (item) {
						route.set('controller.item', item);
						route.set('controller.model.item_id', item.id);
					}
				}
			});
			route.send('showModal', 'entity-picker', picker);
		},

		getEntity: function(type, id, receiver) {
			$.ajax({
				url: '/'+STATE.api_ns+'/'+type+'s/' + id,
				dataType: 'json'
			}).done(function(data) {
				data = data || {};

				if (data._err) {
						console.log('API failed: ' + JSON.stringify(data));
						return;
				}
				if (data[type]) {
					receiver(data[type]);
				}
			});
		},

		getWantingDepts: function(route, recv) {
			var max = (arguments.length > 2) ? arguments[2] : STATE.config.pgsz;

			$.ajax({
				url: '/'+STATE.api_ns+'/depts',
				dataType: 'json',
				data: {
					pgsz: max,
					items: 'count'
				}
			}).done(function(data) {
				data = data || {};

				// more departments than the max requested?
				//
				if (data.meta && data.meta.total > max) {
					HELPERS.getWantingDepts(route, recv, max*2);
					return;
				}

				if (!data.depts) {
					if (data._err)
						console.log('API failed: ' + JSON.stringify(data));
					return;
				}

				// only include departments with items
				//
				var result = [];
				data.depts.forEach(function(dept) {
					if (dept.items)
						result.push(dept);
				});
				recv(result);
			});
		},

		// edit extension for controller
		// - cancel - model rollback()
		// - finish - model save(), followed by 'refreshPage' action
		//
		makeEditableObjectController: function(base) /*{{{*/ {
			base = base || Ember.ObjectController;
			return base.extend({
				isEditing: false,
				isNotEditing: true,
				actions: {
					edit: function() {
						this.set('isEditing', true);
						this.set('isNotEditing', false);
					},
					cancelEdit: function() {
						this.set('isEditing', false);
						this.set('isNotEditing', true);
						this.get('model').rollback();
					},
					finishEdit: function() {
						this.set('isEditing', false);
						this.set('isNotEditing', true);

						var controller = this;
						this.get('model').save().then(function(model) {
							controller.send('refreshPage');
						});
					}
				}
			});
		} /*}}}*/
	}; /*}}}*/

	// access token and donor management {{{
	//
	app.setAccessToken = function(token) {
		STATE.atoken = token;
	};
	$.ajaxPrefilter(function(req) {
		if (STATE.atoken) {
			var sep = req.url.indexOf('?') < 0 ? '?' : '&';
			req.url = req.url + sep + '_atoken=' + encodeURIComponent(STATE.atoken);
		}
	});
	
	app.setDefaultGiver = function(id) {
		STATE.giver_id = id;
	};
	//}}}

	//----------------------------------------------------------
	// miscellaneous setup {{{
	//

	app.ApplicationAdapter = DS.RESTAdapter.extend({
		namespace: STATE.api_ns
	});

	// TODO: a no-op 'date' transform to work around DB date format problem
	//
	app.DateTransform = DS.Transform.extend({
		deserialize: function(serialized) {
			return serialized;
		},
		serialize: function(deserialized) {
			return deserialized;
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// routes setup
	//
	app.Router.map(function() {
		this.resource('donors', function() {
			this.route('add');
			this.resource('donor', { path: ':donor_id' });
		});
		this.resource('items', function() {
			this.route('add');
			this.resource('item', { path: ':item_id' });
		});
		this.resource('depts', function() {
			this.route('add');
			this.resource('dept', { path: ':dept_id' });
		});
		this.resource('pledges', function() {
			this.route('add');
			this.resource('pledge', { path: ':pledge_id' });
		});
	});

	//----------------------------------------------------------
	// ModalDialogComponent {{{
	//
	app.ModalDialogComponent = Ember.Component.extend({
		actions: {
			ok: function() {
				this.$('.modal').modal('hide');
				this.sendAction('ok');
			}
		},
		show: function() {
			this.$('.modal').modal().on('hidden.bs.modal', function() {
				this.sendAction('close');
			}.bind(this));
		}.on('didInsertElement')
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// ApplicationRoute {{{
	//
	app.ApplicationRoute = ROUTE.extend({
		actions: {
			showModal: function(name, model) {
				this.render(name, {
					into: 'application',
					outlet: 'modal',
					model: model
				});
			},
			removeModal: function() {
				this.disconnectOutlet({
					outlet: 'modal',
					parentView: 'application'
				});
			}
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// IndexRoute {{{
	//
	app.IndexRoute = ROUTE.extend({
		model: function() {
			var hour = (new Date()).getHours();
			var greeting = '<strong>' + (
				hour < 12
					? 'Good morning!' : (
				hour < 18
					? 'Good afternoon!'
					: 'Good evening!'
				)
			) + '</strong>';

			return [
				greeting
			];
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// DonorsRoute {{{
	//
	app.DonorsRoute = ROUTE.extend({
		pgsz: STATE.config.pgsz,
		pgno: 1,
		searchStr: '',
		model: function() {
			var params = {
				pgsz: this.pgsz,
				pgno: this.pgno
			};
			if (this.searchStr)
				params.search = this.searchStr;

			return this.store.find('donor', params);
		},
		setupController: function(controller, model) {
			this._super(controller, model);

			var pages = this.get('totalPages');
			if (this.pgno > pages)
				this.pgno = pages > 0 ? pages : 1;

			controller.set('page', this.pgno);
			controller.set('total', pages);

			controller.set('nameFilter', this.searchStr);
		},
		totalPages: function() {
			var meta = this.store.metadataFor('donor');
			var pages = Math.floor(((meta.total||0) + this.pgsz - 1) / this.pgsz);
			return pages;
		}.property(
			'pgsz',
			'searchStr',
			'controller.model'
		),
		actions: {
			addDonor: function() {
				this.controller.transitionToRoute('donors.add');
			},
			delDonor: function(donor) {
				// TODO
				alert('TODO: delete donor');
			},
			pageFirst: function() {
				this.set('pgno', 1);
				this.refresh();
			},
			pageNext: function() {
				var count = this.get('controller.model.length');
				if (count < this.get('pgsz'))
					return;

				var pg = this.get('pgno');
				if (pg >= this.get('totalPages'))
					return;

				this.set('pgno', pg + 1);
				this.refresh();
			},
			pagePrev: function() {
				var pg = this.get('pgno');
				if (pg <= 1)
					return;

				this.set('pgno', pg - 1);
				this.refresh();
			},
			pageLast: function() {
				var pg = this.get('totalPages');
				this.set('pgno', pg);
				this.refresh();
			},
			clearNameFilter: function() {
				this.controller.set('nameFilter', '');
				this.send('applyNameFilter');
			},
			applyNameFilter: function() {
				var filter = this.get('controller.nameFilter');
				if (this.get('searchStr') == filter)
					return;

				this.set('searchStr', filter);
				this.send('pageFirst');
			},
			refreshPage: function() {
				this.refresh();
			}
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// DonorsAddRoute {{{
	//
	app.DonorsAddRoute = ROUTE.extend({
		model: function() {
			var donor = this.store.createRecord('donor');
			donor.set('l_name', '');
			donor.set('f_name', '');
			donor.set('email', '');
			donor.set('tags', '');
			donor.set('notes', '');
			return donor;
		},
		actions: {
			add: function(model) {
				var route = this;
				model.save().then(function(donor) {
					route.send('refreshPage');
					route.controller.transitionToRoute('donor', donor.get('id'));
				});
			}
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// DonorRoute {{{
	//
	app.DonorRoute = ROUTE.extend({
		setupController: function(controller, model) {
			this._super(controller, model);
			controller.set('isEditing', false);
			controller.set('isNotEditing', true);
		},
		model: function(params) {
			return this.store.find('donor', params.donor_id);
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// ItemsRoute {{{
	//
	app.ItemsRoute = ROUTE.extend({
		pgsz: STATE.config.pgsz,
		pgno: 1,
		searchStr: '',
		deptMatch: null,
		model: function() {
			var params = {
				pgsz: this.pgsz,
				pgno: this.pgno
			};
			if (this.searchStr)
				params.search = this.searchStr;
			if (this.deptMatch && this.deptMatch.id)
				params.dept = this.deptMatch.id;

			return this.store.find('item', params);
		},
		setupController: function(controller, model) {
			this._super(controller, model);

			if (!this.deptMatch)
				this.deptMatch = controller.get('pseudoDeptAll');

			var pages = this.get('totalPages');
			if (this.pgno > pages)
				this.pgno = pages > 0 ? pages : 1;

			var me = this;

			controller.set('page', this.pgno);
			controller.set('total', pages);

			controller.set('nameFilter', this.searchStr);
			controller.set('deptFilter', this.deptMatch);

			controller.addObserver('deptFilter', function() {
				me.send('applyItemFilters');
			});

			// set up giver from global state if necessary
			//
			if (!controller.get('giver') && STATE.giver_id) {
				HELPERS.getEntity('donor', STATE.giver_id, function(data) {
					if (data)
						data.name = HELPERS.getFullName(data.l_name, data.f_name);
					controller.set('giver', data);
				});
			}

			// set up list of departments with needs
			//
			HELPERS.getWantingDepts(this, function(depts) {
				var allDept = controller.get('pseudoDeptAll');
				depts.splice(0, 0, allDept);

				var found = false;
				var filter = controller.get('deptFilter') || allDept;
				if (filter.id) {
					var i;
					for (i = 0; i < depts.length; ++i) {
						if (filter.id == depts[i].id) {
							for (prop in depts[i])
								controller.set('deptFilter.'+prop, depts[i][prop]);
							depts[i] = filter;
							found = true;
							break;
						}
					}
				}

				if (!found) {
					controller.set('deptFilter', allDept);
				}

				controller.set('deptChoices', depts);
			});
		},
		totalPages: function() {
			var meta = this.store.metadataFor('item');
			var pages = Math.floor(((meta.total||0) + this.pgsz - 1) / this.pgsz);
			return pages;
		}.property(
			'pgsz',
			'searchStr',
			'deptMatch.id',
			'controller.model'
		),
		actions: {
			addItem: function() {
				this.controller.transitionToRoute('items.add');
			},
			delItem: function(item) {
				if (!item || !confirm('Delete "' + item.get('name') + '"?'))
					return;

				var route = this;

				$.ajax({
					url: '/'+STATE.api_ns+'/items/'+item.id,
					type: 'DELETE'
				}).done(function(data) {
					data = data || {};

					if (data.okay) {
						alert('Item deleted.');
						route.store.find('item', item.id).then(function(item) {
							item.unloadRecord();
							route.send('refreshPage');
						});
					}
				});
			},
			contribToItem: function(item) {
				if (!item)
					return;

				var giver = this.get('controller.giver');
				if (!giver)
					return;

				// make a pledge
				//
				var picker = app.PledgeForm.create({
					donor: giver,
					item: item,
					trigger: 'submitPledge'
				});
				this.send('showModal', 'pledge-form', picker);
			},
			submitPledge: function(form) {
				var price = form.get('item.price');
				if (!price)
					return;

				var count = (form.get('count') + '').trim();
				if (!count || !count.match(/^[1-9][0-9]*$/)) {
					alert('Please enter count.');
					return;
				}
				var amount = count * price;

				var giver = form.get('donor.id');
				var item = form.get('item.id');
				if (!giver || !item)
					return;

				var me = this;
				$.ajax({
					url: '/'+STATE.api_ns+'/donors/'+giver+'/pledges',
					type: 'POST',
					dataType: 'json',
					data: {
						pledge: {
							item_id: item,
							amount: amount
						}
					}
				}).done(function(data) {
					data = data || {};

					if (!data.pledge) {
						if (data._err)
							console.log('API failed: ' + JSON.stringify(data));
						return;
					}
					var gift = data.pledge.id;

					me.refresh();

					var memo = 'item#' + item + ',gift#' + gift;

					alert('Thank you! Your gift # is ' + gift);

					var url = STATE.config.donate_url;
					if (url) {
						url = url + '#amount='+amount+'&memo='+encodeURIComponent(memo);
						window.location.assign(url);
					}
				});
			},
			pageFirst: function() {
				this.set('pgno', 1);
				this.refresh();
			},
			pageNext: function() {
				var count = this.get('controller.model.length');
				if (count < this.get('pgsz'))
					return;

				var pg = this.get('pgno');
				if (pg >= this.get('totalPages'))
					return;

				this.set('pgno', pg + 1);
				this.refresh();
			},
			pagePrev: function() {
				var pg = this.get('pgno');
				if (pg <= 1)
					return;

				this.set('pgno', pg - 1);
				this.refresh();
			},
			pageLast: function() {
				var pg = this.get('totalPages');
				this.set('pgno', pg);
				this.refresh();
			},
			clearNameFilter: function() {
				this.controller.set('nameFilter', '');
				this.send('applyItemFilters');
			},
			clearDeptFilter: function() {
				var allDept = this.controller.get('pseudoDeptAll');
				this.controller.set('deptFilter', allDept);
				this.send('applyItemFilters');
			},
			applyItemFilters: function() {
				var name = this.get('controller.nameFilter');
				var dept = this.get('controller.deptFilter')
					|| this.get('controller.pseudoDeptAll');
				if (this.get('searchStr') == name && this.get('deptMatch.id')==dept.id)
					return;

				this.set('searchStr', name);
				this.set('deptMatch', dept);
				this.send('pageFirst');
			},
			pickDept: function() {
				var me = this;
				var controller = this.get('controller');

				HELPERS.pickDept(this, 'controller.deptFilter', null);
			},
			refreshPage: function() {
				this.refresh();
			}
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// ItemsAddRoute {{{
	//
	app.ItemsAddRoute = ROUTE.extend({
		model: function() {
			var item = this.store.createRecord('item');
			item.set('name', '');
			return item;
		},
		actions: {
			pickDept: function() {
				HELPERS.pickDept(this);
			},
			add: function(model) {
				var route = this;
				model.save().then(function(item) {
					route.send('refreshPage');
					route.controller.transitionToRoute('item', item.get('id'));
				});
			}
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// ItemRoute {{{
	//
	app.ItemRoute = ROUTE.extend({
		setupController: function(controller, model) {
			this._super(controller, model);
			controller.set('isEditing', false);
			controller.set('isNotEditing', true);

			var did;
			if (did = model.get('dept_id'))
				controller.set('dept', this.store.find('dept', did));
		},
		model: function(params) {
			return this.store.find('item', params.item_id);
		},
		actions: {
			pickDept: function() {
				HELPERS.pickDept(this);
			}
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// DeptsRoute {{{
	//
	app.DeptsRoute = ROUTE.extend({
		pgsz: STATE.config.pgsz,
		pgno: 1,
		searchStr: '',
		model: function() {
			var params = {
				pgsz: this.pgsz,
				pgno: this.pgno
			};
			if (this.searchStr)
				params.search = this.searchStr;

			return this.store.find('dept', params);
		},
		setupController: function(controller, model) {
			this._super(controller, model);

			var pages = this.get('totalPages');
			if (this.pgno > pages)
				this.pgno = pages > 0 ? pages : 1;

			controller.set('page', this.pgno);
			controller.set('total', pages);

			controller.set('nameFilter', this.searchStr);
		},
		totalPages: function() {
			var meta = this.store.metadataFor('dept');
			var pages = Math.floor(((meta.total||0) + this.pgsz - 1) / this.pgsz);
			return pages;
		}.property(
			'pgsz',
			'searchStr',
			'controller.model'
		),
		actions: {
			addDept: function() {
				this.controller.transitionToRoute('depts.add');
			},
			delDept: function(dept) {
				// TODO
				alert('TODO: delete dept');
			},
			pageFirst: function() {
				this.set('pgno', 1);
				this.refresh();
			},
			pageNext: function() {
				var count = this.get('controller.model.length');
				if (count < this.get('pgsz'))
					return;

				var pg = this.get('pgno');
				if (pg >= this.get('totalPages'))
					return;

				this.set('pgno', pg + 1);
				this.refresh();
			},
			pagePrev: function() {
				var pg = this.get('pgno');
				if (pg <= 1)
					return;

				this.set('pgno', pg - 1);
				this.refresh();
			},
			pageLast: function() {
				var pg = this.get('totalPages');
				this.set('pgno', pg);
				this.refresh();
			},
			clearNameFilter: function() {
				this.controller.set('nameFilter', '');
				this.send('applyNameFilter');
			},
			applyNameFilter: function() {
				var filter = this.get('controller.nameFilter');
				if (this.get('searchStr') == filter)
					return;

				this.set('searchStr', filter);
				this.send('pageFirst');
			},
			refreshPage: function() {
				this.refresh();
			}
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// DeptsAddRoute {{{
	//
	app.DeptsAddRoute = ROUTE.extend({
		model: function() {
			var dept = this.store.createRecord('dept');
			dept.set('name', '');
			return dept;
		},
		actions: {
			add: function(model) {
				var route = this;
				model.save().then(function(dept) {
					route.send('refreshPage');
					route.controller.transitionToRoute('dept', dept.get('id'));
				});
			}
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// DeptRoute {{{
	//
	app.DeptRoute = ROUTE.extend({
		setupController: function(controller, model) {
			this._super(controller, model);
			controller.set('isEditing', false);
			controller.set('isNotEditing', true);
		},
		model: function(params) {
			return this.store.find('dept', params.dept_id);
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// PledgesRoute {{{
	//
	app.PledgesRoute = ROUTE.extend({
		pgsz: STATE.config.pgsz,
		pgno: 1,
		searchStr: '',
		model: function() {
			var params = {
				pgsz: this.pgsz,
				pgno: this.pgno
			};
			if (this.searchStr)
				params.search = this.searchStr;

			return this.store.find('pledge', params);
		},
		setupController: function(controller, model) {
			this._super(controller, model);

			var pages = this.get('totalPages');
			if (this.pgno > pages)
				this.pgno = pages > 0 ? pages : 1;

			controller.set('page', this.pgno);
			controller.set('total', pages);

			controller.set('nameFilter', this.searchStr);
		},
		totalPages: function() {
			var meta = this.store.metadataFor('pledge');
			var pages = Math.floor(((meta.total||0) + this.pgsz - 1) / this.pgsz);
			return pages;
		}.property(
			'pgsz',
			'searchStr',
			'controller.model'
		),
		actions: {
			addPledge: function() {
				this.controller.transitionToRoute('pledges.add');
			},
			delPledge: function(pledge) {
				// TODO
				alert('TODO: delete pledge');
			},
			pageFirst: function() {
				this.set('pgno', 1);
				this.refresh();
			},
			pageNext: function() {
				var count = this.get('controller.model.length');
				if (count < this.get('pgsz'))
					return;

				var pg = this.get('pgno');
				if (pg >= this.get('totalPages'))
					return;

				this.set('pgno', pg + 1);
				this.refresh();
			},
			pagePrev: function() {
				var pg = this.get('pgno');
				if (pg <= 1)
					return;

				this.set('pgno', pg - 1);
				this.refresh();
			},
			pageLast: function() {
				var pg = this.get('totalPages');
				this.set('pgno', pg);
				this.refresh();
			},
			clearNameFilter: function() {
				this.controller.set('nameFilter', '');
				this.send('applyNameFilter');
			},
			applyNameFilter: function() {
				var filter = this.get('controller.nameFilter');
				if (this.get('searchStr') == filter)
					return;

				this.set('searchStr', filter);
				this.send('pageFirst');
			},
			refreshPage: function() {
				this.refresh();
			}
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// PledgesAddRoute {{{
	//
	app.PledgesAddRoute = ROUTE.extend({
		model: function() {
			var pledge = this.store.createRecord('pledge');
			pledge.set('name', '');
			return pledge;
		},
		actions: {
			pickDonor: function() {
				HELPERS.pickDonor(this);
			},
			pickItem: function() {
				HELPERS.pickItem(this);
			},
			add: function(model) {
				var route = this;
				model.save().then(function(pledge) {
					route.send('refreshPage');
					route.controller.transitionToRoute('pledge', pledge.get('id'));
				});
			}
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// PledgeRoute {{{
	//
	app.PledgeRoute = ROUTE.extend({
		setupController: function(controller, model) {
			this._super(controller, model);
			controller.set('isEditing', false);
			controller.set('isNotEditing', true);

			var id;
			if (id = model.get('donor_id'))
				controller.set('donor', this.store.find('donor', id));
			if (id = model.get('item_id'))
				controller.set('item', this.store.find('item', id));
		},
		model: function(params) {
			return this.store.find('pledge', params.pledge_id);
		},
		actions: {
			pickDonor: function() {
				HELPERS.pickDonor(this);
			},
			pickItem: function() {
				HELPERS.pickItem(this);
			}
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// ModalDialog: {{{
	//
	app.ModalDialog = Ember.Object.extend({
		title: '',
		main: 'OK',
		other: 'Close',
		ok: 'done',
		close: 'removeModal'
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// Confirmation: {{{
	//
	app.Confirmation = app.ModalDialog.extend({
		title: 'Confirmation Request',
		main: 'No',
		other: 'Yes',
		ok: 'removeModal',
		close: 'confirmed',
		trigger: null
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// EntityPicker: {{{
	//
	app.EntityPicker = app.ModalDialog.extend({
		what: null,
		title: 'Pick Entity',
		route: null,
		picked: null,
		entities: null,
		fixup: null,
		trigger: null
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// PledgeForm: {{{
	//
	app.PledgeForm = app.ModalDialog.extend({
		title: 'Pledge Form',
		main: 'Confirm',
		other: 'Cancel',
		ok: 'confirmed',
		close: 'removeModal',
		trigger: null,
		donor: null,
		item: null,
		count: 1
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// Donor: {{{
	//
	app.Donor = MODEL.extend({
		f_name: ATTR('string'),
		l_name: ATTR('string'),
		email: ATTR('string'),
		tags: ATTR('string'),
		notes: ATTR('string'),

		name: function() {
			var fname = this.get('f_name');
			var lname = this.get('l_name');
			return HELPERS.getFullName(lname, fname);
		}.property('f_name', 'l_name')
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// Item: {{{
	//
	app.Item = MODEL.extend({
		notes: ATTR('string'),
		name: ATTR('string'),
		description: ATTR('string'),
		tags: ATTR('string'),
		quantity: ATTR('number'),
		price: ATTR('number'),
		image_url: ATTR('string'),
		detail_url: ATTR('string'),
		dept_id: ATTR('number'),
		pledged: ATTR('number'),
		remain: function() {
			var total = (this.get('total') || 0);
			var covered = (this.get('pledged') || 0);

			var remain = total > covered ? total - covered : 0;
			return remain;
		}.property('total', 'pledged'),
		progress: function() {
			var remain = (this.get('remain') || 0);
			if (remain <= 0)
				return 100;

			var total = (this.get('total') || 0);
			var progress = 100 - Math.floor(100 * remain / total);
			return progress;
		}.property('remain', 'total'),
		total: function() {
			var count = (this.get('quantity') || 0);
			var price = (this.get('price') || 0);

			var total = count * price;
			return total;
		}.property('price', 'quantity')
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// Dept: {{{
	//
	app.Dept = MODEL.extend({
		name: ATTR('string')
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// Pledge: {{{
	//
	app.Pledge = MODEL.extend({
		fulfilled: ATTR('date'),
		tstamp: ATTR('date'),
		lstamp: ATTR('string'),
		donor_id: ATTR('number'),
		item_id: ATTR('number'),
		amount: ATTR('number'),
		notes: ATTR('string')
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// ConfirmationController {{{
	//
	app.ConfirmationController = Ember.ObjectController.extend({
		actions: {
			confirmed: function() {
				var trigger = this.get('model.trigger');
				if (trigger)
					this.send(trigger, this.get('model'));
			}
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// EntityPickerController {{{
	//
	app.EntityPickerController = Ember.ObjectController.extend({
		searchStr: '',
		pgno: 1,
		pgsz: STATE.config.pgsz,

		actions: {
			pageFirst: function() {
				this.set('pgno', 1);
				this.send('loadEntities');
			},
			pageNext: function() {
				var pg = this.get('pgno');
				++pg;
				this.set('pgno', pg);
				this.send('loadEntities');
			},
			pagePrev: function() {
				var pg = this.get('pgno');
				if (pg <= 0)
					return;
				--pg;
				this.set('pgno', pg);
				this.send('loadEntities');
			},
			search: function() {
				this.send('loadEntities');
			},
			loadEntities: function() {
				var model = this.get('model');
				var key = model.what + 's';

				$.ajax({
					url: '/'+STATE.api_ns+'/'+key,
					dataType: 'json',
					data: {
						pgno: this.pgno,
						pgsz: this.pgsz,
						search: this.searchStr
					}
				}).done(function(data) {
					data = data || {};

					if (!data[key]) {
						if (data._err)
							console.log('API failed: ' + JSON.stringify(data));
						return;
					}

					var found = data[key];
					if (found) {
						var fixup = model.get('fixup');
						if (fixup)
							found.forEach(fixup);
					}
					model.set('picked', null);
					model.set('entities', found);
				});
			},
			pick: function(pick) {
				this.set('model.picked', pick);
			},
			unpick: function() {
				this.set('model.picked', null);
			},
			done: function() {
				var picker = this.get('model');
				var picked = this.get('model.picked');
				var trigger = this.get('model.trigger');
				if (trigger)
					trigger(picked, picker);
			}
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// PledgeFormController {{{
	//
	app.PledgeFormController = Ember.ObjectController.extend({
		amount: function() {
			return this.get('model.item.price') * this.get('model.count');
		}.property('model.item.price', 'model.count'),
		actions: {
			confirmed: function() {
				var trigger = this.get('model.trigger');
				if (trigger)
					this.send(trigger, this.get('model'));
			}
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// ApplicationController {{{
	//
	app.ApplicationController = Ember.Controller.extend({
		isRouteDonors: function() {
			var path = this.get('currentPath');
			return path.substr(0, 7) == 'donors.';
		}.property('currentPath'),
		isRoutePledges: function() {
			var path = this.get('currentPath');
			return path.substr(0, 8) == 'pledges.';
		}.property('currentPath'),
		isRouteItems: function() {
			var path = this.get('currentPath');
			return path.substr(0, 6) == 'items.';
		}.property('currentPath'),
		isRouteDepts: function() {
			var path = this.get('currentPath');
			return path.substr(0, 6) == 'depts.';
		}.property('currentPath'),
		isRouteIndex: function() {
			var path = this.get('currentPath');
			return path.substr(0, 5) == 'index';
		}.property('currentPath')
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// DonorsController {{{
	//
	app.DonorsController = Ember.ArrayController.extend({
		queryParams: [
			'pgno',
			'pgsz',
			'search'
		],
		page: 1,
		total: 0,
		nameFilter: '',
		showOptions: false,
		actions: {
			toggleShowOptions: function() {
				this.toggleProperty('showOptions');
			}
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// DonorsAddController {{{
	//
	app.DonorsAddController = Ember.ObjectController.extend({
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// DonorController {{{
	//
	app.DonorController = HELPERS.makeEditableObjectController(
		app.DonorsAddController
	);
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// ItemsController {{{
	//
	app.ItemsController = Ember.ArrayController.extend({
		queryParams: [
			'pgno',
			'pgsz',
			'dept',
			'search'
		],
		page: 1,
		total: 0,
		pseudoDeptAll: {
			id: 0,
			name: '(all)'
		},
		deptFilter: null,
		deptChoices: [],
		nameFilter: '',
		showOptions: false,
		giver: null,
		actions: {
			pickGiver: function() {
				var giver = this.get('giver');

				var me = this;
				var picker = app.EntityPicker.create({
					what: 'donor',
					title: 'Pick Giver',
					picked: giver,
					entities: giver ? [ giver ] : [],
					fixup: function(giver) {
						if (!giver)
							return;
						giver.name = HELPERS.getFullName(giver.l_name, giver.f_name);
					},
					trigger: function(giver, picker) {
						if (giver) {
							me.set('giver', giver);
						}
					}
				});
				this.send('showModal', 'entity-picker', picker);
			},
			clearGiver: function() {
				this.set('giver', null);
			},
			toggleShowOptions: function() {
				this.toggleProperty('showOptions');
			}
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// ItemsAddController {{{
	//
	app.ItemsAddController = Ember.ObjectController.extend({
		needs: [ 'dept' ],
		dept: null
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// ItemController {{{
	//
	app.ItemController = HELPERS.makeEditableObjectController(
		app.ItemsAddController
	);
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// DeptsController {{{
	//
	app.DeptsController = Ember.ArrayController.extend({
		queryParams: [
			'pgno',
			'pgsz',
			'search'
		],
		page: 1,
		total: 0,
		nameFilter: '',
		showOptions: false,
		actions: {
			toggleShowOptions: function() {
				this.toggleProperty('showOptions');
			}
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// DeptsAddController {{{
	//
	app.DeptsAddController = Ember.ObjectController.extend({
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// DeptController {{{
	//
	app.DeptController = HELPERS.makeEditableObjectController(
		app.DeptsAddController
	);
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// PledgesController {{{
	//
	app.PledgesController = Ember.ArrayController.extend({
		queryParams: [
			'pgno',
			'pgsz',
			'donor_id'
		],
		page: 1,
		total: 0,
		donor: 0,
		showOptions: false,
		actions: {
			toggleShowOptions: function() {
				this.toggleProperty('showOptions');
			}
		}
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// PledgesAddController {{{
	//
	app.PledgesAddController = Ember.ObjectController.extend({
		needs: [ 'donor', 'item' ],
		donor: null,
		item: null
	});
	//}}}-------------------------------------------------------

	//----------------------------------------------------------
	// PledgeController {{{
	//
	app.PledgeController = HELPERS.makeEditableObjectController(
		app.PledgesAddController
	);
	//}}}-------------------------------------------------------

	return app;
})(
	Ember,
	DS,
	CchsApp,
	CchsAppConfig
);

// vim: set ts=2 noexpandtab fileformat=unix fdm=marker syntax=javascript:
