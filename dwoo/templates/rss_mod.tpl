<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
<channel>
<title>{%KU_NAME} - Modlog</title>
<link>{%KU_WEBPATH}</link>
<description>Live view of all moderative actions on {%KU_WEBPATH}</description>
<language>{%KU_LOCALE}</language>
{foreach from=$entries item=item}
	<item>
	<title>{$item.timestamp|date_format:"%a %m/%d %H:%M"}</title>
	<description><![CDATA[{$item.user} - {$item.entry}]]></description>
	</item>
{/foreach}
</channel>
</rss>
