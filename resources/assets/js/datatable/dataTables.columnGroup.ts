/*! ColumnGroup 1.0.0
 */

import $ from 'jquery';

interface ColumnGroupConfig {
	column: any;
}

interface ColumnGroupSettings {
	dt: DataTables.Api;
	collapsedGroups: any[];
	regrouping: boolean;
	lastDraw: number | null;
}

/**
 * ColumnGroup allows grouping by column for DataTables
 * @class ColumnGroup
 * @constructor
 * @param {object} settings DataTables settings object
 * @param {object} config ColumnGroup options
 */
class ColumnGroup {
	public static defaults: ColumnGroupConfig = {
		column: null,
	};

	public static version = '1.0.0';

	private c: ColumnGroupConfig;
	private s: ColumnGroupSettings;

	constructor(settings: any, config: ColumnGroupConfig) {
		// Sanity check - you just know it will happen
		if (!(this instanceof ColumnGroup)) {
			throw new Error('ColumnGroup must be initialised with the \'new\' keyword.');
		}

		const dt = new $.fn.dataTable.Api(settings);

		this.c = $.extend(true, {}, ColumnGroup.defaults, config);

		this.s = {
			dt,
			collapsedGroups: [],
			regrouping: false,
			lastDraw: null,
		};

		const dtSettings = dt.settings()[0];
		if (dtSettings._columnGroup) {
			throw new Error('ColumnGroup already initialized on table ' + dtSettings.nTable.id);
		}

		dtSettings._columnGroup = this;

		this._fnConstruct();
	}

	public _fnConstruct() {
		const dt = this.s.dt;
		const table = dt.table(0);
		const tableNode = $(table.node());
		const that = this;

		$.fn.dataTable.ext.search.push((settings: any, data: any) => {
			return that._fnGroupExpandCollapseDraw(settings, data);
		});

		// Group by column
		tableNode.find('tbody')
			.on('click', 'tr.group', function (event) {
				if (!event.shiftKey && !event.ctrlKey) {
					that._fnGroupExpandCollapse($(this));
				}
			});
		tableNode.find('thead')
			.on('click', 'th.toggle-group:first', function (event) {
				that._fnGroupExpandCollapseAll($(this));
			});
		tableNode.on('draw.dt', () => this._fnGroupByColumnDraw());
		tableNode.find('thead tr th').first().addClass('toggle-group toggle-group-expanded');
	}

	public _fnGroupByColumnDraw() {
		const dt = this.s.dt;
		const table = dt.table(0);
		const tableNode = $(table.node());

		const collapsedGroups = this.s.collapsedGroups;
		const lastDraw = this.s.lastDraw;

		const column = this.c.column;

		if (lastDraw === Date.now()) {
			return; // workaround childrow
		}

		const collapse = collapsedGroups.slice(); // copy by value
		let colspan = '';
		const j = tableNode.find('thead tr th').length - 2;
		for (let i = 0; i < j; i++) {
			colspan += '<td></td>';
		}
		let groupRow;
		// Create group rows for visible rows
		const rows = $(dt.rows({page: 'current'}).nodes());
		tableNode.find('tr.group').remove();
		let last: any;
		// Iterate over data in the group by column
		dt.column(column, {page: 'current'}).data().each((group: any, i: any) => {
			if (last !== group) {
				// Create group rows for collapsed groups
				while (collapse.length > 0 && collapse[0].localeCompare(group) < 0) {
					groupRow = $(`<tr class="group">
<td class="toggle-group"></td>
<td class="group-label">${collapse[0]}</td>
${colspan}
</tr>`)
						.data('groupData', collapse[0]);
					rows.eq(i).before(groupRow);
					collapse.shift();
				}
				groupRow = $(`<tr class="group">
<td class="toggle-group toggle-group-expanded"></td>
<td class="group-label">${group}</td>
${colspan}
</tr>`)
					.data('groupData', group);
				rows.eq(i).before(groupRow);
				last = group;
			}
		});
		// Create group rows for collapsed groups
		const tbody = tableNode.children('tbody:first');
		collapse.forEach((group: any) => {
			groupRow = $(`<tr class="group">
<td class="toggle-group"></td>
<td class="group-label">${group}</td>
${colspan}
</tr>`)
				.data('groupData', group);
			tbody.append(groupRow);
		});
		this.s.lastDraw = Date.now();
	}

	public _fnGroupExpandCollapse($tr: JQuery) {
		const dt = this.s.dt;
		const table = dt.table(0);
		const tableNode = $(table.node());

		let collapsedGroups = this.s.collapsedGroups;
		const td = $('td:first', $tr);
		td.toggleClass('toggle-group-expanded');
		const group = $tr.data('groupData');
		if (td.hasClass('toggle-group-expanded')) {
			collapsedGroups = $.grep(collapsedGroups, (value) => value !== group);
		} else {
			collapsedGroups.push(group);
		}
		this.s.collapsedGroups = collapsedGroups.sort();
		dt.draw(false);
		this._fnHideEmptyCollapsedAll(tableNode.find('thead tr th:first'));
	}

	public _fnHideEmptyCollapsedAll($th: JQuery<Node>) {
		const dt = this.s.dt;
		const table = dt.table(0);
		const tableNode = $(table.node());

		const collapsedGroups = this.s.collapsedGroups;

		if (tableNode.find('tr.group').length === collapsedGroups.length) {
			tableNode.find('td.dataTables_empty').parent().remove();
			$th.removeClass('toggle-group-expanded');
		} else {
			$th.addClass('toggle-group-expanded');
		}
	}

	public _fnGroupExpandCollapseAll($th: JQuery) {
		const dt = this.s.dt;

		const column = this.c.column;
		const collapsedGroups: any[] = [];

		if ($th.hasClass('toggle-group-expanded')) {
			let last: any;
			dt.column(column).data().each((group: any) => {
				if (last !== group) {
					collapsedGroups.push(group);
					last = group;
				}
			});
		}
		this.s.collapsedGroups = collapsedGroups;
		dt.draw(false);
		this._fnHideEmptyCollapsedAll($th);
	}

	public _fnGroupExpandCollapseDraw(settings: any, data: any) {
		const column = this.c.column;
		const collapsedGroups = this.s.collapsedGroups;

		const group = data[column];

		return $.inArray(group, collapsedGroups) <= -1;
	}
}

declare global {
	namespace DataTables {
		// noinspection JSUnusedGlobalSymbols
		interface StaticFunctions {
			ColumnGroup: typeof ColumnGroup;
		}

		interface Settings {
			columnGroup: any;
		}
	}
}
// Expose
$.fn.dataTable.ColumnGroup = ColumnGroup;
// @ts-ignore
$.fn.DataTable.ColumnGroup = ColumnGroup;

// Attach a listener to the document which listens for DataTables initialisation
// events so we can automatically initialise
$(document).on('preInit.dt.columnGroup', (e, settings) => {
	if (e.namespace !== 'dt') {
		return;
	}

	const init = settings.oInit.columnGroup;
	const defaults = $.fn.dataTable.defaults.columnGroup;

	if (init || defaults) {
		const opts = $.extend({}, init, defaults);

		if (init !== false) {
			// tslint:disable-next-line:no-unused-expression
			new ColumnGroup(settings, opts);
		}
	}
});
