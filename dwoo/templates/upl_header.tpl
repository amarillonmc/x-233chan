<script type="text/javascript" src="{$cwebpath}lib/javascript/protoaculous-compressed.js"></script>
<link rel="stylesheet" type="text/css" href="{$cwebpath}css/img_global.css" />
{loop $ku_styles}
	<link rel="{if $ neq $__.ku_defaultstyle}alternate {/if}stylesheet" type="text/css" href="{$__.cwebpath}css/{$}.css" title="{$|capitalize}" />
{/loop}
{if $locale eq 'ja'}
	{literal}
	<style type="text/css">
		*{
			font-family: IPAMonaPGothic, Mona, 'MS PGothic', YOzFontAA97 !important;
			font-size: 1em;
		}
	</style>
	{/literal}
{/if}
{if %KU_RSS neq ''}
	<link rel="alternate" type="application/rss+xml" title="RSS" href="{%KU_BOARDSPATH}/{$board.name}/rss.xml" />
{/if}
<script type="text/javascript"><!--
		var ku_boardspath = '{%KU_BOARDSPATH}';
		var ku_cgipath = '{%KU_CGIPATH}';
		var style_cookie = "kustyle";
{if $replythread > 0}
		var ispage = false;
{else}
		var ispage = true;
{/if}
//--></script>
<script type="text/javascript" src="{%KU_WEBPATH}/lib/javascript/kusaba.js"></script>
<script type="text/javascript"><!--
	var hiddenthreads = getCookie('hiddenthreads').split('!');
//--></script>
{if $board.enablecaptcha eq 1}
	{literal}
		<script type="text/javascript"> var RecaptchaOptions = { theme : 'clean' }; </script>
	{/literal}
{/if}
</head>
<body>
<div class="adminbar">
{if %KU_STYLESWITCHER}
	{if %KU_DROPSWITCHER}
		<select onchange="javascript:if(selectedIndex != 0)set_stylesheet(options[selectedIndex].value);return false;">
			<option>{t}Styles{/t}</option>
		{loop $ku_styles}
			<option value="{$|capitalize}">{$|capitalize}</option>;
		{/loop}
		</select>
	{else}
		{loop $ku_styles}
			[<a href="#" onclick="javascript:set_stylesheet('{$|capitalize}');return false;">{$|capitalize}</a>]&nbsp;
		{/loop}
	{/if}
	{if count($ku_styles) > 0}
		-&nbsp;
	{/if}
{/if}
{if %KU_WATCHTHREADS}
	[<a href="#" onclick="javascript:showwatchedthreads();return false" title="{t}Watched Threads{/t}">WT</a>]&nbsp;
{/if}
[<a href="{%KU_WEBPATH}" target="_top">{t}Home{/t}</a>]&nbsp;[<a href="{%KU_CGIPATH}/manage.php" target="_top">{t}Manage{/t}</a>]
</div>
<div class="navbar">
{if %KU_GENERATEBOARDLIST}
	{foreach name=sections item=sect from=$boardlist}
		[
		{foreach name=brds item=brd from=$sect}
			<a title="{$brd.desc}" href="{%KU_BOARDSFOLDER}{$brd.name}/">{$brd.name}</a>{if $.foreach.brds.last}{else} / {/if}
		{/foreach}
		 ]
	{/foreach}
{else}
	{if is_file($boardlist)}
		{include $boardlist}
	{/if}
{/if}
</div>
{if %KU_WATCHTHREADS && not $isoekaki && not $hidewatchedthreads}
				<script type="text/javascript"><!--
				if (getCookie('showwatchedthreads') == '1') {
				document.write('<div id="watchedthreads" style="top: {$ad_top}px; left: 25px;" class="watchedthreads"><div class="postblock" id="watchedthreadsdraghandle" style="width: 100%;">{t}Watched Threads{/t}<\/div><span id="watchedthreadlist"><\/span><div id="watchedthreadsbuttons"><a href="#" onclick="javascript:hidewatchedthreads();return false;" title="{t}Hide the watched threads box{/t}"><img src="{$cwebpath}css/icons/blank.gif" border="0" class="hidewatchedthreads" alt="hide" /><\/a>&nbsp;<a href="#" onclick="javascript:getwatchedthreads(\'0\', \'{$board.name}\');return false;" title="{t}Refresh watched threads{/t}"><img src="{$cwebpath}css/icons/blank.gif" border="0" class="refreshwatchedthreads" alt="refresh" /><\/a><\/div><\/div>');
				watchedthreadselement = document.getElementById('watchedthreads');
				watchedthreadselement.style.top = getCookie('watchedthreadstop');
				watchedthreadselement.style.left = getCookie('watchedthreadsleft');
				watchedthreadselement.style.width = Math.max(250,getCookie('watchedthreadswidth')) + 'px';
				watchedthreadselement.style.height = Math.max(75,getCookie('watchedthreadsheight')) + 'px';
				getwatchedthreads('<!sm_threadid>', '{$board.name}');
			}
			//--></script>
{/if}

<div class="logo">
{if %KU_HEADERURL neq '' && $board.image eq ''}
	<img src="{%KU_HEADERURL}" alt="{t}Logo{/t}" /><br />
{elseif $board.image neq '' && $board.image neq "none"}
	<img src="{$board.image}" alt="{t}Logo{/t}" /><br />
{/if}
{if %KU_DIRTITLE}
	/{$board.name}/ - 
{/if}
{$board.desc}</div>
{$board.includeheader}
<hr />