<script type="text/javascript" src="{$cwebpath}lib/javascript/protoaculous-compressed.js"></script>
<link rel="stylesheet" type="text/css" href="{$cwebpath}css/txt_global.css" />
{loop $ku_styles}
	<link rel="{if $ neq $__.ku_defaultstyle}alternate {/if}stylesheet" type="text/css" href="{$__.cwebpath}css/txt_{$}.css" title="{$|capitalize}" />
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
		var style_cookie_txt = "kustyle_txt";
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
{if $replythread eq 0}
		<body class="board">
{else}
		<body class="read">
{/if}
<div class="topbar">
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
{if not $isthread}
	<div class="fullhead">
		<div class="hborder">
			<div class="head">
				<a name="menu" rev="contents"></a>
				{if $isindex eq 1}
					<span class="navlinks"><a href="#0">&darr;</a>&nbsp;<a href="#menu">&#9632;</a></span>
				{/if}	
				<h1 align="center">{$board.desc}</h1>
				{$board.includeheader}
				</div>
			</div>
			{if %KU_TXTSTYLESWITCHER && $isindex}
				<div class="hborder">
					<div class="head midhead">
						<strong>{t}Styles{/t}:</strong> 
						{loop $ku_styles}
							<a href="#" onclick="javascript:set_stylesheet('{$|capitalize:true}',true);return false;">
							{strip}{if $ eq 'futatxt'}
								FutaTXT
							{elseif $ eq 'buritxt'}
								BuriTXT
							{else}
								{$|capitalize:true}
							{/if}
							{/strip}</a> 
						{/loop}
					</div>
				</div>
			{/if}
			{if $isindex eq 0}
				{t}Pages{/t}:&nbsp;<a href="{%KU_BOARDSPATH}/{$board.name}/">{t}Front{/t}</a>
				{section name=pages loop=$numpages}
					&nbsp;
					{if $.section.pages.iteration neq $thispage}
						<a href="list{if $.section.pages.iteration neq 1}{$.section.pages.iteration}{/if}.html">
					{/if}	
					{$.section.pages.iteration}
					{if $.section.pages.iteration neq $thispage}
					</a>
					{/if}
				{/section}
				<br />
			{/if}
{/if}
