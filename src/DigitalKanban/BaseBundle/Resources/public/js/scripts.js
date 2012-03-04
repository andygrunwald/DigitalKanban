var DigitalKanbanBaseBundle = {

	/**
	 * Sortable object
	 *
	 * @var object JQuery UI Sortable
	 */
	sortableObj: null,

	/**
	 * Initialize method to bootstrap the application
	 *
	 * @return void
	 */
	init: function() {

			// Set focus on username field in login form
		this.initLoginFormular();

			// Board selector is available on every page
		this.initBoardSelector();

			// Available on action 'Board show'
		this.initNewIssueItems();
		this.initKanbanBoard();

			// Available on management sites like 'User management' or 'Board management'
		this.initDeleteLinks();

			// Available at 'Edit board columns'
		this.initBoardColumnSortable();
		this.initNewColumnItems();
	},

	/**
	 * If the login site is called, set the focus (mouse cursor) on the username field.
	 * This is just a small but useful usability improvement
	 *
	 * @return void
	 */
	initLoginFormular: function() {
		$('#loginbox form input#username').focus();
	},

	/**
	 * Initialize the onChange-Event of the kanban board selector
	 *
	 * @return void
	 */
	initBoardSelector: function() {
		$('#boardToShow').change(function(event){
			window.location.replace($(this).val());
		});
	},

	/**
	 * Initialize the kanban board with all the functionality (move, issue delete, and so on ...)
	 *
	 * @return void
	 */
	initKanbanBoard: function() {
		this.initBoardIssueSortable();
		this.handleColumnLimitsDuringDragAndDrop();
		this.initIssueAndColumnDeleteFunction();
		this.initColumnWidth();
		this.initLastColumnOnKanbanBoard();
	},

	/**
	 * Initialize the last column of a kanban board.
	 * Every column has a css border-right. But the last column shouldn`t have one.
	 *
	 * @return void
	 */
	initLastColumnOnKanbanBoard: function(){
		$('.kanban-board .board-columns ul li.column.last').removeClass('last');
		$('.kanban-board .board-columns ul li:last-child').addClass('last');
	},

	/**
	 * Initialize the delete links on User or Board management pages.
	 * The user will be asked if he want to delete one record via javascript confirm function.
	 *
	 * @return void
	 */
	initDeleteLinks: function() {
		$('.delete-link a').click(function() {
			return confirm($(this).prev().text());
		});
	},

	/**
	 * Initialize the delete functions for issues and columns.
	 * It sets various mouse and click-events.
	 *
	 * The mousevents are to handle the toggle of the delete icons.
	 * The clickevents are to delete the item.
	 *
	 * @return void
	 */
	initIssueAndColumnDeleteFunction: function() {
			// Set event handler to show the delete links on issues and columns
			// We have to unbind the vents before, because this method is called
			// more than one time. For example if you create a new issue or column
			// the method will be call again.
		$('.show-board .kanban-board .issues li.issue, .board-columns ul li.column')
			.unbind('mouseenter')
			.mouseenter(function(){
				$('a.delete', this).toggle();
			})
			.unbind('mouseleave')
			.mouseleave(function(){
				$('a.delete', this).toggle();
			});

			// Set click delete event handler on issue
		$('.show-board .kanban-board .issues li.issue a.delete')
			.unbind('click')
			.click(this.deleteIssueFromKanbanBoard);

			// Set click delete event handler on column
		$('.board-columns ul li.column a.delete')
			.unbind('click')
			.click(this.deleteColumnFromKanbanBoard);
	},

	/**
	 * Initialize the drag and drop functionality of issues on a kanban board
	 *
	 * @return	void
	 */
	initBoardIssueSortable: function() {
		this.sortableObj = $('.show-board .issues ul');
		this.sortableObj.sortable({
			connectWith: '.show-board .column.draganddrop-ok .issues ul',
			placeholder: 'issue-state-highlight',
				// Ajax request to database to update the issue
			update: this.updateIssueInKanbanBoard,
				// Handling of limits of a sortable column
			start: this.handleColumnLimitsDuringDragAndDrop,
			stop: this.handleColumnLimitsDuringDragAndDrop
		}).disableSelection();
	},

	/**
	 * Initialize the drag and drop functionality of column of a kanban board
	 *
	 * @return void
	 */
	initBoardColumnSortable: function() {
		this.sortableObj = $('.edit-columns ul.editable-column-board');
		this.sortableObj.sortable({
				// Ajax request to database to update the issue
			update: this.updateColumnInKanbanBoard,
			placeholder: 'column-state-highlight'
		}).disableSelection();
	},

	/**
	 * Initialize the 'create new issue' functionality
	 *
	 * @return void
	 */
	initNewIssueItems: function() {
			// Set click event on Reset button
		$('#link-issue-reset').click(function() {
			$('#new-issue textarea').val('');
		});

			// Set click event on save button
		$('#link-issue-save').click($.proxy(this.addNewIssueToKanbanBoard, this));
	},

	/**
	 * Initialize the 'create new column' functionality
	 *
	 * @return void
	 */
	initNewColumnItems: function() {
			// Set click event on Reset button
		$('#link-column-reset').click(function() {
			$('#BoardColumnFormType_name, #BoardColumnFormType_max_issues,#BoardColumnFormType_user_group').val('');
		});

			// Set click event on save button
		$('#link-column-save').click($.proxy(this.addNewColumnToKanbanBoard, this));
	},

	/**
	 * Javascript method to delete an issue from kanban board
	 *
	 * @param event
	 * @return void
	 */
	deleteIssueFromKanbanBoard: function(event) {
		var issueId,
			issue = $(this).parents('li.issue'),
			options = {};

			// If the user confirms to delete the issue make an ajax call to talk to the database :)
		if(confirm($(this).prev().text()) === true) {
			issueId = DigitalKanbanBaseBundle.getDatabaseIdFromCSSClass(issue, 'issue');
			options = {
				'url': "/application/issue/delete/" + parseInt(issueId),
				'data': {},
				'successCallback': $.proxy(DigitalKanbanBaseBundle.deleteIssueFromDOM, this)
			};
			DigitalKanbanBaseBundle.sendAjaxRequest(options);
		}
	},

	/**
	 * Method to delete a column from kanban board
	 *
	 * @param event
	 * @return void
	 */
	deleteColumnFromKanbanBoard: function(event) {
		var columnId,
			column = $(this).parents('li.column'),
			options = {};

			// If the user confirms to delete the column make an ajax call to talk to the database :)
		if(confirm($(this).prev().text()) === true) {
			issueId = DigitalKanbanBaseBundle.getDatabaseIdFromCSSClass(column, 'column');
			options = {
				'url': '/application/column/delete/' + parseInt(issueId),
				'data': {},
				'successCallback': $.proxy(DigitalKanbanBaseBundle.deleteColumnFromDOM, this)
			};
			DigitalKanbanBaseBundle.sendAjaxRequest(options);
		}
	},

	/**
	 * If the ajax request (aka deletion in database) was a success, remove the issue from DOM
	 *
	 * @return void
	 */
	deleteIssueFromDOM: function() {
			// Fade the issue out, after this remove this from DOM and renew the drag and drop functions
		$(this).parents('li.issue').fadeOut('fast', function() {
			$(this).remove();
			DigitalKanbanBaseBundle.handleColumnLimitsDuringDragAndDrop();
		});
	},

	/**
	 * If the ajax request (aka deletion in database) was a success, remove the column from DOM
	 *
	 * @return void
	 */
	deleteColumnFromDOM: function() {
			// Fade the column out, after this remove this from DOM and renew the column width
		$(this).parents('li.column').fadeOut('fast', function() {
			$(this).remove();
			DigitalKanbanBaseBundle.initColumnWidth();
		});
	},

	/**
	 * Method to add a new issue to the kanban board
	 *
	 * @param event
	 * @return void
	 */
	addNewIssueToKanbanBoard: function(event) {
		var newIssueTitle = $.trim($('#new-issue textarea').val()),
			firstColumn = null,
			numOfIssues = 0,
			nameOfFirstColumn = '',
			options = {};

			// If the textarea / issue-text is empty, throw an error
		if(newIssueTitle === '') {
			alert('Please add a title to your new issue.');
			return;
		}

		firstColumn = $('#main .board-columns .column:first');
		numOfIssues = $('div.issues ul li.issue', firstColumn).length;

			// If the limit of the first column is reached, throw an error
		if(this.checkLimitOfColumn(firstColumn, (numOfIssues + 1)) === false) {
			nameOfFirstColumn = $.trim($('.name', firstColumn).val());
			alert('The limit of column ' + nameOfFirstColumn + ' is reached. Please do the work before adding new issues ;)');
			return;
		}

			// Save new issue to the database
		options = {
			'url': '/application/issue/add',
			'data': {
				'column': this.getDatabaseIdFromCSSClass(firstColumn, 'column'),
				'issue': {title: newIssueTitle}
			},
			'successCallback': $.proxy(this.addNewInsertedIssueToDOM, this)
		};
		this.sendAjaxRequest(options);
	},

	/**
	 * If the ajax request (aka the insertion in the database) was successful, add the new issue to the DOM
	 *
	 * @param xhrData
	 * @param eventName
	 * @param xhrObject
	 * @return void
	 */
	addNewInsertedIssueToDOM: function(xhrData, eventName, xhrObject) {
		var firstColumn = $('#main .board-columns .column:first'),
			elements = {};

			// Add the issue to the first column
		elements.issue = $(document.createElement('li'))
			.addClass('issue rotate' + parseInt(xhrData.rotation) + ' issue-' + parseInt(xhrData.id))
			.text(xhrData.name)
			.appendTo($('ul', firstColumn));

			// If the user is an administrator, generate the delete link and insert this, too
		if(xhrData.userIsAdmin === true) {
			elements.deleteText = $(document.createElement('span'))
				.addClass('confirm-text visuallyhidden')
				.text('Wollen Sie das Kanban "' + xhrData.name + '" wirklich löschen?')
				.appendTo(elements.issue);

			elements.deleteLink = $(document.createElement('a'))
				.attr('href', 'javascript:void(0);')
				.addClass('delete')
				.css('display', 'none')
				.appendTo(elements.issue);

			elements.deleteImg = $(document.createElement('img'))
				.attr({
					'alt': 'Delete',
					'title': 'Delete',
					'src': '/bundles/digitalkanbanbase/images/no.png'
				})
				.appendTo(elements.deleteLink);
		}

			// Reset the textarea
		$('#new-issue textarea').val('');

			// Update column icons (drag and drop)
		this.handleColumnLimitsDuringDragAndDrop();

			// Refresh sortable objects and delete link events
		this.sortableObj.sortable('refresh');
		this.initIssueAndColumnDeleteFunction();
	},

	/**
	 * Method to add a new column to the kanban board
	 *
	 * @param event
	 * @return void
	 */
	addNewColumnToKanbanBoard: function(event) {
		var newColumn = {
				'name': $.trim($('#BoardColumnFormType_name').val()),
				'limit': parseInt($.trim($('#BoardColumnFormType_max_issues').val())),
                'usergroup': parseInt($.trim($('#BoardColumnFormType_user_group').val()))
			},
			boardId = 0,
			options = {};

			// If the name field is empty, throw an error
		if(newColumn.name === '') {
			alert('Please add a name to your new column.');
			return;
		}

			// Save new column to the database via ajax request
		options = {
			'url': '/app_dev.php/application/column/add',
			'data': {
				'board': this.getDatabaseIdFromCSSClass($('div.edit-columns div.kanban-board'), 'board'),
				'column': newColumn
			},
			'successCallback': $.proxy(this.addNewInsertedColumnToDOM, this)
		};
		this.sendAjaxRequest(options);
	},

	/**
	 * If the ajax request (aka the insertion into the database) was successful, add the new column to DOM
	 *
	 * @param xhrData
	 * @param eventName
	 * @param xhrObject
	 * @return void
	 */
	addNewInsertedColumnToDOM: function(xhrData, eventName, xhrObject) {
			// Clone the 'template'-node for one column
		var column = $('ul.hidden li.column').clone(true, true),
			tmpVal = '';

			// Add the missing 'id'-class
		column.addClass('column-' + xhrData.id);

			// Add name and limit
		$('div.name', column).text(xhrData.name);
		if(xhrData.limit > 0) {
			$('div.limit', column).text(xhrData.limit);
		}
        if(xhrData.usergroup) {
            $('div.usergroup', column).text(xhrData.usergroup);
        }
			// Replace Name marker in values and attributes
		tmpVal = $('span.confirm-text', column).text();
		$('span.confirm-text', column).text(tmpVal.replace(/###NAME###/, xhrData.name));

		tmpVal = $('a.delete img', column).attr('alt');
		$('a.delete img', column).attr('alt', tmpVal.replace(/###NAME###/, xhrData.name));

		tmpVal = $('a.delete img', column).attr('title');
		$('a.delete img', column).attr('title', tmpVal.replace(/###NAME###/, xhrData.name));

			// Add the new column to the right place in DOM
		column.prependTo('.kanban-board ul.editable-column-board');

			// Reset input fields
		$('#BoardColumnFormType_name, #BoardColumnFormType_max_issues,#BoardColumnFormType_user_group').val('');

			// Refresh and reinitialize sortable objects, events and css styles
		this.initColumnWidth();
		this.sortableObj.sortable('refresh');
		this.initIssueAndColumnDeleteFunction();
		this.initLastColumnOnKanbanBoard();
	},

	/**
	 * Main function to send an ajax request. Options is an object after JSON.
	 * Possible keys:
	 * 		url: The 'target' for this ajax request
	 * 		data: An object after JSON to submit the data to the target (url)
	 * 		successCallback: A function which will be executed, if the ajax request was a success
	 *
	 * @param options
	 * @return void
	 */
	sendAjaxRequest: function(options) {
		$.ajax({
			type: "POST",
			url: options.url,
			data: options.data,
			success: options.successCallback || function(){}
		}).fail(function(qXHR, textStatus, headerText) {
			alert('An error occued: ' + headerText);
		});
	},

	/**
	 * Initialize the width of a kanban board.
	 * If the board is to big to display it correctly, add the css attribute  overflow: scroll to the parent element.
	 * With this solution the board could be displayed correctly.
	 *
	 * This method will be called again and again. After every insertion / deletion of columns.
	 *
	 * @return void
	 */
	initColumnWidth: function() {
		var columns = $('#main .board-columns .column');
		var columnWrapperWidth = parseInt($('#main .board-columns').css('width'));
		var widthToSet = parseInt($(columns[1]).css('width')) * columns.length;

		$('#main .board-columns').css('width', widthToSet + 'px');
		if(columnWrapperWidth < widthToSet) {
			$('#main .kanban-board').css('overflow-x', 'scroll');
		} else {
			$('#main .kanban-board').css('overflow-x', 'auto');
		}
	},

	/**
	 * If an issue was moved to another column, this method updates all including issues in the database.
	 *
	 * This method is called as a callback from Sortable-library
	 *
	 * @param object event JQuery event object of callback event
	 * @param object ui Sortable object of callback event
	 * @return void
	 */
	updateIssueInKanbanBoard: function(event, ui) {
		var column = null,
			columnId = 0,
			tmpId = 0,
			issues = null,
			issueIdArray = new Array(),
			options = {};

			// If a ticket was moved to another column, the plugin Sortable is called twice.
			// This condition prevents the second call. To update all issue, one call is enough.
		if (this !== ui.item.parent()[0]) {
			return;
		}

			// Get the columns with issues
		column = $(ui.item).parents('div.column');
		issues = $('div.issues ul li.issue', column);

			// Get database ids from column and issues
		columnId = DigitalKanbanBaseBundle.getDatabaseIdFromCSSClass(column, 'column');
		issues.each(function(index, element){
			tmpId = DigitalKanbanBaseBundle.getDatabaseIdFromCSSClass(element, 'issue');
			issueIdArray.push(tmpId);
		});

			// Update affected issues in database
		options = {
			'url': '/application/board/update',
			'data': {
				'column': columnId,
				'issues': issueIdArray.join(',')
			}
		};
		DigitalKanbanBaseBundle.sendAjaxRequest(options);
	},

	/**
	 * This methods update the column after the column was moved to another place (sorting).
	 *
	 * @param event JQuery event object of callback event
	 * @param ui Sortable object of callback event
	 * @return void
	 */
	updateColumnInKanbanBoard: function(event, ui) {
		var board = null,
			boardId = 0,
			tmpId = 0,
			columns = null,
			columnIdArray = new Array(),
			options = {};

			// Get the board with columns
		board = $(ui.item).parents('div.kanban-board');
		columns = $('ul.editable-column-board li.column');

			// Get database ids from board and columns
		boardId = DigitalKanbanBaseBundle.getDatabaseIdFromCSSClass(board, 'board');
		columns.each(function(index, element){
			tmpId = DigitalKanbanBaseBundle.getDatabaseIdFromCSSClass(element, 'column');
			columnIdArray.push(tmpId);
		});

			// Update affected columns in database
		options = {
			'url': '/application/column/update',
			'data': {
				'board': boardId,
				'columns': columnIdArray.join(',')
			},
			'successCallback': function() {
				DigitalKanbanBaseBundle.initLastColumnOnKanbanBoard();
			}
		};
		DigitalKanbanBaseBundle.sendAjaxRequest(options);
	},

	/**
	 * Method to handle the drag and drop action of an kanban board.
	 * This is very important, because one kanban board column has limits.
	 * If the limits are reached, it isn`t possible to move an issue to this column.
	 *
	 * This method take care of it this behaviour.
	 * This method is called as a callback from Sortable-library
	 *
	 * @param object event JQuery event object of callback event
	 * @param object ui Sortable object of callback event
	 * @return void
	 */
	handleColumnLimitsDuringDragAndDrop: function(event, ui) {
			// Get all columns
		var columns = $('#main .board-columns .column');
		columns.removeClass('draganddrop-ok draganddrop-fail');

			// Loop over every column and renew the drag and drop state in base of the 'kanban limits'
		columns.each($.proxy(function(index, singleColumn) {
			var issues = $('div.issues ul li.issue', singleColumn);
			if(this.checkLimitOfColumn(singleColumn, (issues.length + 1)) === false) {
				$(singleColumn).addClass('draganddrop-fail');
			} else {
				$(singleColumn).addClass('draganddrop-ok');
			}
			this.sortableObj.sortable('refresh');
		}, DigitalKanbanBaseBundle));
	},

	/**
	 * Detect the database id from a given DOM-element with prefix.
	 * For example the id 5 from element column (css class: column-5).
	 * For more information have a look at the HTML-Source of a single kanban board.
	 *
	 * @param object/string elementWithIdClass DOM-Element or selector
	 * @param string classPrefix CSS-Class-Prefix
	 * @return integer Database id of record
	 */
	getDatabaseIdFromCSSClass: function(elementWithIdClass, classPrefix) {
		var tmpIdClass = $(elementWithIdClass).attr('class').split(' ').filter(function(element) {
			return (element.indexOf(classPrefix + '-') >= 0);
		});
		return parseInt(tmpIdClass[0].split('-')[1]);
	},

	/**
	 * Gets the Kanban limit of a specified column.
	 *
	 * @param object column DOM-Element of column
	 * @return integer limit
	 */
	getLimitOfColumn: function(column) {
		var limit = parseInt($('div.limit', column).text());

			// If there is no limit (empty value), set it to 0
		if(isNaN(limit) === true) {
			limit = 0;
		}

		return limit;
	},

	/**
	 * Checks if the limit of a specified column is reached
	 *
	 * @param object column DOM-Element of column to check
	 * @param integer numOfIssues Number of issues in the column
	 * @return boolean FALSE if the limit is reached, TRUE otherwise
	 */
	checkLimitOfColumn: function(column, numOfIssues) {
		var returnVal = true,
			limit = this.getLimitOfColumn(column);

		if(limit > 0 && limit < (numOfIssues)) {
			returnVal = false;
		}

		return returnVal;
	}
};

/**
 * Bootstrap / Startup functionality
 * If the document is ready and fully loaded, execute the javascript magic
 *
 */
$(document).ready(function() {
	DigitalKanbanBaseBundle.init();
});