<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
<channel>
<title>{%KU_NAME} - {$boardname}</title>
<link>{%KU_BOARDSPATH}/{$boardname}</link>
<description>Live RSS feed for {%KU_BOARDSPATH}/{$boardname}</description>
<language>{%KU_LOCALE}</language>';
{foreach name=rss from=$posts item=item}
	<item>
	<title>{$item.id}</title>
	<link>
	{if $item.parentid neq 0}
		{%KU_BOARDSPATH}/{$boardname}/res/{$item.parentid}.html#{$item.id}</link>
	{else}
		{%KU_BOARDSPATH}/{$boardname}/res/{$item.id}.html</link>
	{/if}
	<description><![CDATA[
	{if $item.file neq ''}
		{if $item.file_type eq 'jpg' || $item.file_type eq 'png' || $item.file_type eq 'gif'}
			<a href="{%KU_BOARDSPATH}/{$boardname}/src/{$item.file}.{$item.file_type}"><img src="{%KU_BOARDSPATH}/{$boardname}/thumb/{$item.file}s.{$item.file_type}" /></a><br /><br />
		{else}
			[{%KU_BOARDSPATH}/{$boardname}/src/{$item.file}.{$item.file_type}] <br /><br />
		{/if}
	{/if}
	{if trim($item.message) neq ''}
		{$item.message|stripslashes}<br />
	{/if}
	]]></description>
	</item>
{/foreach}
</channel>
</rss>
