<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>{t}YOU ARE BANNED{/t}!</title>
<link rel="stylesheet" type="text/css" href="{%KU_BOARDSPATH}/css/site_futaba.css" title="Futaba">
<link rel="shortcut icon" href="{%KU_WEBPATH}/favicon.ico">
</head>
<body>
<h1>{%KU_NAME}</h1>
<h3>{%KU_SLOGAN}</h3>
<div style="margin: 3em;">
	<h2>&nbsp;{t}YOU ARE BANNED{/t}! :\</h2>
	<img src="{%KU_BOARDSPATH}/youarebanned.jpg" style="float: right;" alt=":'(">
	{foreach name=bans item=ban from=$bans}
		{if not $.foreach.bans.first}
			{t}Additionally{/t},
		{/if}
		{if $ban.expired eq 1}
			{t}You were banned from posting on{/t}
		{else}
			{t}You have been banned from posting on{/t}
		{/if} 
		<strong>{if $ban.globalban eq 1}{t}All boards{/t}{else}/{implode('/</strong>, <strong>/', explode('|', $ban.boards))}/{/if}</strong> {t}for the following reason{/t}:<br /><br />
		<strong>{$ban.reason}</strong><br /><br />
		{t}Your ban was placed on{/t} <strong>{$ban.at|date_format:"%B %e, %Y, %I:%M %P %Z"}</strong>, {t}and{/t}
		{if $ban.expired eq 1}
			{t}expired on{/t} <strong>{$ban.until|date_format:"%B %e, %Y, %I:%M %P"}</strong><br  />
			<strong>{t}This ban has already expired, this message is for your information only and will not be displayed again{/t}</strong>
		{else}
			{if $ban.until > 0}{t}will expire on{/t} <strong>{$ban.until|date_format:"%B %e, %Y, %I:%M %P"}</strong>{else}{t}will not expire{/t}</strong>{/if}
		{/if}
		<br /><br />
		{if %KU_APPEAL neq '' && $ban.expired eq 0}
			{if $ban.appealat eq 0}
				{t}You may <strong>not</strong> appeal this ban.{/t}
			{elseif $ban.appealat eq -1}
				{t}Your appeal is currently pending review.{/t}
				{t}For reference, your appeal message is{/t}:<br />
				<strong>{$ban.appeal}</strong>
			{elseif $ban.appealat eq -2}
				{t}Your appeal was reviewed and denied. You may <strong>not</strong> appeal this ban again.{/t}
				{t}For reference, your appeal message was{/t}:<br />
				<strong>{$ban.appeal}</strong>
			{else}
				{if $ban.appealat < $.now}
					{t}You may now appeal this ban.{/t}
					<br /><br />
					<form action="{%KU_BOARDSPATH}/banned.php" method="post">
						<input type="hidden" name="banid" value="{$ban.id}" />
						<label for="appealmessage">{t}Appeal Message{/t}:</label>
						<br />
						<textarea name="appealmessage" rows="10" cols="50"></textarea>
						<br /><input type="submit" value="{t}Send Appeal{/t}" />
					</form>
				{else}
					{t}You may appeal this ban in{/t} <strong>{$ban.appealin}</strong>.
				{/if}
			{/if}
			<br />
		{/if}
		{if $.foreach.bans.last}
			<br />{t}Your IP address is{/t} <strong>{$.server.REMOTE_ADDR}</strong>.<br /><br />
		{/if}
		{if count($bans) > 1 && not $.foreach.bans.last}
			<hr />
		{/if}

	{/foreach}
</div>
</body>
</html>
