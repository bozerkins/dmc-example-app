<?php

require_once __DIR__ . '/../bootstrap.php';

$user = initializeUser();
if ($user === null) {
    $_SESSION['message_bad'] = 'Not authenticated';
    header('Location: index.php');
    exit;
}
?>
<!doctype html>
<html lang="en" data-framework="jquery">
	<head>
		<meta charset="utf-8">
		<title>Todo App | Integrated with Data Management Components</title>
		<link rel="stylesheet" href="../node_modules/todomvc-common/base.css">
		<link rel="stylesheet" href="../node_modules/todomvc-app-css/index.css">
		<link rel="stylesheet" href="../css/app.css">
	</head>
	<body>
		<section id="todoapp">
			<header id="header">
				<h1>todos</h1>
				<input id="new-todo" placeholder="What needs to be done?" autofocus>
			</header>
			<section id="main">
				<input id="toggle-all" type="checkbox">
				<label for="toggle-all">Mark all as complete</label>
				<ul id="todo-list"></ul>
			</section>
			<footer id="footer"></footer>
		</section>
		<footer id="info">
            <p>Logged in as <b><?=$user['Username']; ?></b></p>
			<p>Double-click to edit a todo</p>
			<p><a href="../logout.php">logout</a></p>
		</footer>
		<script id="todo-template" type="text/x-handlebars-template">
			{{#this}}
			<li {{#eq Completed 1 }}class="completed"{{/eq}} data-id="{{ID}}">
				<div class="view">
					<input class="toggle" type="checkbox" {{#eq Completed 1 }}checked{{/eq}}>
					<label>{{Title}}</label>
					<button class="destroy"></button>
				</div>
				<input class="edit" value="{{Title}}">
			</li>
		{{/this}}
		</script>
		<script id="footer-template" type="text/x-handlebars-template">
			<span id="todo-count"><strong>{{activeTodoCount}}</strong> {{activeTodoWord}} left</span>
			<ul id="filters">
				<li>
					<a {{#eq filter 'all'}}class="selected"{{/eq}} href="#/all">All</a>
				</li>
				<li>
					<a {{#eq filter 'active'}}class="selected"{{/eq}}href="#/active">Active</a>
				</li>
				<li>
					<a {{#eq filter 'completed'}}class="selected"{{/eq}}href="#/completed">Completed</a>
				</li>
			</ul>
			{{#if completedTodos}}<button id="clear-completed">Clear completed</button>{{/if}}
		</script>
		<script src="../node_modules/todomvc-common/base.js"></script>
		<script src="../node_modules/jquery/dist/jquery.js"></script>
		<script src="../node_modules/handlebars/dist/handlebars.js"></script>
		<script src="../node_modules/director/build/director.js"></script>
		<script src="../js/app.js"></script>
	</body>
</html>
