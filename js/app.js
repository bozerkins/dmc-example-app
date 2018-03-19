/*global jQuery, Handlebars, Router */
jQuery(function ($) {
	'use strict';

	Handlebars.registerHelper('eq', function (a, b, options) {
		return a === b ? options.fn(this) : options.inverse(this);
	});

	var ENTER_KEY = 13;
	var ESCAPE_KEY = 27;

	var util = {
		pluralize: function (count, word) {
			return count === 1 ? word : word + 's';
		}
	};

    $( document ).ajaxComplete(function(event, xhr, settings ) {
        var response = $.parseJSON(xhr.responseText);
        if (response.status === false && response.result === 'unauthorized access') {
            window.location = '../index.php';
            return;
        }
        if (response.status === false) {
            alert('Failed with message: ' + response.message);
            return;
        }
    });

	var App = {

		todos: null,

		init: function () {
			// this.todos = util.store('todos-jquery');
			this.todos = [];
			this.todoTemplate = Handlebars.compile($('#todo-template').html());
			this.footerTemplate = Handlebars.compile($('#footer-template').html());
			this.bindEvents();

			new Router({
				'/:filter': function (filter) {
					this.filter = filter;
					this.render();
				}.bind(this)
			}).init('/all');

            $.post('../server/list.php', (function(response) {
                this.todos = response.result;
                this.render();
            }).bind(this));
		},
		bindEvents: function () {
			$('#new-todo').on('keyup', this.create.bind(this));
			$('#toggle-all').on('change', this.toggleAll.bind(this));
			$('#footer').on('click', '#clear-completed', this.destroyCompleted.bind(this));
			$('#todo-list')
				.on('change', '.toggle', this.toggle.bind(this))
				.on('dblclick', 'label', this.editingMode.bind(this))
				.on('keyup', '.edit', this.editKeyup.bind(this))
				.on('focusout', '.edit', this.update.bind(this))
				.on('click', '.destroy', this.destroy.bind(this));
		},
		render: function () {
			var todos = this.getFilteredTodos();
			$('#todo-list').html(this.todoTemplate(todos));
			$('#main').toggle(todos.length > 0);
			$('#toggle-all').prop('checked', this.getActiveTodos().length === 0);
			this.renderFooter();
			$('#new-todo').focus();
		},
		renderFooter: function () {
			var todoCount = this.todos.length;
			var activeTodoCount = this.getActiveTodos().length;
			var template = this.footerTemplate({
				activeTodoCount: activeTodoCount,
				activeTodoWord: util.pluralize(activeTodoCount, 'item'),
				completedTodos: todoCount - activeTodoCount,
				filter: this.filter
			});

			$('#footer').toggle(todoCount > 0).html(template);
		},
		toggleAll: function (e) {
			var isChecked = $(e.target).prop('checked');

            $.post('../server/toggle_all.php', {'Completed' : isChecked ? 1 : 0 }, (function() {
                this.todos.forEach(function (todo) {
                    todo.Completed = isChecked ? 1 : 0;
                });
                this.render();
            }).bind(this));


			this.render();
		},
		getActiveTodos: function () {
			return this.todos.filter(function (todo) {
				return !todo.Completed;
			});
		},
		getCompletedTodos: function () {
			return this.todos.filter(function (todo) {
				return todo.Completed;
			});
		},
		getFilteredTodos: function () {
			if (this.filter === 'active') {
				return this.getActiveTodos();
			}

			if (this.filter === 'completed') {
				return this.getCompletedTodos();
			}

			return this.todos;
		},
		destroyCompleted: function () {
            $.post('../server/remove_completed.php', (function() {
                this.todos = this.getActiveTodos();
                this.filter = 'all';
                this.render();
            }).bind(this));
		},
		// accepts an element from inside the `.item` div and
		// returns the corresponding index in the `todos` array
		getIndexFromEl: function (el) {
			var id = $(el).closest('li').data('id');
			var todos = this.todos;
			var i = todos.length;

			while (i--) {
				if (todos[i].ID === id) {
					return i;
				}
			}
		},
		create: function (e) {
			var $input = $(e.target);
			var val = $input.val().trim();

			if (e.which !== ENTER_KEY || !val) {
				return;
			}

            $.post('../server/add.php', {
                Title: val,
                Completed: 0
            }, (function(response) {
            	this.todos.push(response.result);
                this.render();
            }).bind(this));

			$input.val('');
		},
		toggle: function (e) {
			var i = this.getIndexFromEl(e.target);
			this.todos[i].Completed = !this.todos[i].Completed ? 1 : 0;

            $.post('../server/update.php', this.todos[i], (function(response) {
                this.todos[i] = response.result;
                this.render();
            }).bind(this));

		},
		editingMode: function (e) {
			var $input = $(e.target).closest('li').addClass('editing').find('.edit');
			$input.val($input.val()).focus();
		},
		editKeyup: function (e) {
			if (e.which === ENTER_KEY) {
				e.target.blur();
			}

			if (e.which === ESCAPE_KEY) {
				$(e.target).data('abort', true).blur();
			}
		},
		update: function (e) {
			var el = e.target;
			var $el = $(el);
			var val = $el.val().trim();

			if (!val) {
				this.destroy(e);
				return;
			}

			if ($el.data('abort')) {
				$el.data('abort', false);
                this.render();
			} else {
				var i = this.getIndexFromEl(el);
				this.todos[i].Title = val;
                $.post('../server/update.php', this.todos[i], (function(response) {
                    this.todos[i] = response.result;
                    this.render();
                }).bind(this));
			}
		},
		destroy: function (e) {
			this.render();
            var i = this.getIndexFromEl(e.target);
            $.post('../server/remove.php', this.todos[i], (function() {
                this.todos.splice(i, 1);
                this.render();
            }).bind(this));
		}
	};

	App.init();
});
