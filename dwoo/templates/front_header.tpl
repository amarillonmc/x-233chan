<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" type="text/css" href="assets/front.css">
	<title>{$title}</title>
</head>	

<body>
	<div class="logo"></div>

	<div class="menu">
		<ul>
			<li><a href="?page=index" {if $page eq 'index'}class="active"{/if}>Home</a></li>
			<li><a href="?page=news" {if $page eq 'news'}class="active"{/if}>News</a></li>
			<li><a href="?page=faq" {if $page eq 'faq'}class="active"{/if}>F.A.Q.</a></li>
			<li><a href="?page=rules" {if $page eq 'rules'}class="active"{/if}>Rules</a></li>
			<li><a href="?page=banlist" {if $page eq 'banlist'}class="active"{/if}>Bans</a></li>
		</ul>
	</div>