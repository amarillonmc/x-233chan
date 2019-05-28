<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>{%KU_NAME} Navigation</title>
{if %KU_MENUTYPE eq 'normal'}
	<link rel="stylesheet" type="text/css" href="{$boardpath}css/menu_global.css" />
	{loop $styles}
			<link rel="{if $ neq %KU_DEFAULTMENUSTYLE}alternate {/if}stylesheet" type="text/css" href="{%KU_WEBFOLDER}css/site_{$}.css" title="{$|capitalize}" />
			<link rel="{if $ neq %KU_DEFAULTMENUSTYLE}alternate {/if}stylesheet" type="text/css" href="{%KU_WEBFOLDER}css/sitemenu_{$}.css" title="{$|capitalize}" />
	{/loop}
{else}
	{literal}<style type="text/css">body { margin: 0px; } h1 { font-size: 1.25em; } h2 { font-size: 0.8em; font-weight: bold; color: #CC3300; } ul { list-style-type: none; padding: 0px; margin: 0px; } li { font-size: 0.8em; padding: 0px; margin: 0px; }</style>{/literal}
{/if}

<script type="text/javascript"><!--
			var style_cookie_site = "kustyle_site";
		//--></script>
<link rel="shortcut icon" href="{%KU_WEBFOLDER}favicon.ico" />
<script type="text/javascript" src="{%KU_WEBFOLDER}lib/javascript/gettext.js"></script>
<script type="text/javascript" src="{%KU_WEBFOLDER}lib/javascript/menu.js"></script>
<script type="text/javascript" src="{%KU_WEBFOLDER}lib/javascript/kusaba.js"></script>
<script type="text/javascript"><!--

{if $showdirs eq 0 && $files.0 neq $files.1 }
	if (getCookie(tcshowdirs) == yes) {
		window.location = '{%KU_BOARDSPATH}/{$files.1}';
	}
{/if}

function showstyleswitcher() {
		var switcher = document.getElementById('sitestyles');
		switcher.innerHTML = '{strip}
		{if %KU_MENUSTYLESWITCHER && %KU_MENUTYPE eq 'normal'}
			{t}Styles{/t}:
			{loop $styles}
				[<a href="#" onclick="javascript:set_stylesheet(\'{$|capitalize}\', false, true);reloadmain();" style="display: inline;" target="_self">{$|substr:0:1|upper}</a>]{if !$dwoo.loop.default.last} {/if}
			{/loop}
		{/if}
		{/strip}';

}
{literal}
function toggle(button, area) {
	var tog=document.getElementById(area);
	if(tog.style.display)	{
		tog.style.display="";
	} else {
		tog.style.display="none";
	}
	button.innerHTML=(tog.style.display)?'+':'&minus;';
	set_cookie('nav_show_'+area, tog.style.display?'0':'1', 30);
}

function removeframes() {
	var boardlinks = document.getElementsByTagName("a");
	for(var i=0;i<boardlinks.length;i++) if(boardlinks[i].className == "boardlink") boardlinks[i].target = "_top";

	document.getElementById("removeframes").innerHTML = '{/literal}{t}Frames removed{/t}{literal}.';

	return false;
}
function reloadmain() {
	if (parent.main) {
		parent.main.location.reload();
	}
}
{/literal}
function hidedirs() {
	set_cookie('tcshowdirs', '', 30);
	{if $files.0 eq $files.1}
		location.reload(true)
	{else}
		window.location = '{%KU_WEBFOLDER}{$files.0}';
	{/if}
}
function showdirs() {
	set_cookie('tcshowdirs', 'yes', 30);
	{if $files.0 eq $files.1}
		location.reload(true)
	{else}
		window.location = '{%KU_WEBFOLDER}{$files.1}';
	{/if}
}
//--></script>
<base target="main" />
</head>
<body>
<h1>{%KU_NAME}</h1>
<ul>
<li><a href="{%KU_WEBFOLDER}" target="_top">{t}主页{/t}</a></li>
{if %KU_MENUSTYLESWITCHER && %KU_MENUTYPE eq 'normal'}
	<li id="sitestyles"><a onclick="javascript:showstyleswitcher();" href="#" target="_self">[{t}站点样式{/t}]</a></li>
{/if}
{if $showdirs eq 0}
	<li><a onclick="javascript:showdirs();" href="{$files.1}" target="_self">[{t}显示目录名{/t}]</a></li>
{else}
	<li><a onclick="javascript:hidedirs();" href="{$files.0}" target="_self">[{t}隐藏目录名{/t}]</a></li>
{/if}
{if %KU_MENUTYPE eq 'normal'}
	<li id="removeframes"><a href="#" onclick="javascript:return removeframes();" target="_self">[{t}隐藏此栏{/t}]</a></li>
{/if}
</ul>
{if empty($boards)}
	<ul>
		<li>{t}No visible boards{/t}</li>
	</ul>
{else}

	{foreach name=sections item=sect from=$boards}
	
		{if %KU_MENUTYPE eq 'normal'}
			<h2>
		{else}
			<h2 style="display: inline;"><br />
		{/if}
		{if %KU_MENUTYPE eq 'normal'}
			<span class="plus" onclick="toggle(this, '{$sect.abbreviation}');" title="{t}Click to show/hide{/t}">{if $sect.hidden eq 1}+{else}&minus;{/if}</span>&nbsp;
		{/if}
		{$sect.name}</h2>
		{if %KU_MENUTYPE eq 'normal'}
			<div id="{$sect.abbreviation}"{if $sect.hidden eq 1} style="display: none;"{/if}>
		{/if}
		<ul>
		{if count($sect.boards) > 0}
			{foreach name=brds item=brd from=$sect.boards}
				<li><a href="{%KU_BOARDSPATH}/{$brd.name}/" class="boardlink{if $brd.trial eq 1} trial{/if}{if $brd.popular eq 1} pop{/if}">
				{if $showdirs eq 1}
					/{$brd.name}/ - 
				{/if}
				{$brd.desc}
				{if $brd.locked eq 1}
					<img src="{%KU_BOARDSPATH}/css/locked.gif" border="0" alt="{t}Locked{/t}">
				{/if}
				</a></li>
			{/foreach}
		{else}
			<li>{t}No visible boards{/t}</li>
		{/if}
		</ul>
		{if %KU_MENUTYPE eq 'normal'}
			</div>
		{/if}
	{/foreach}
{/if}
{if %KU_IRC}
	{if %KU_MENUTYPE eq 'normal'}
		<h2>
	{else}
		<h2 style="display: inline;"><br />
	{/if}
	&nbsp;IRC</h2>
	<ul>
		<li>{%KU_IRC}</li>
	</ul>
{/if}
</body>
</html>


