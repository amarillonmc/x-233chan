&#91;<a href="{%KU_WEBFOLDER}{$board.name}/">{t}Return{/t}</a>&#93;
{if %KU_FIRSTLAST && ( count($posts) > 50 || $replycount > 50)}
	&#91;<a href="{%KU_WEBFOLDER}{$board.name}/res/{$posts.0.id}.html">{t}Entire Thread{/t}</a>&#93; 
	&#91;<a href="{%KU_WEBFOLDER}{$board.name}/res/{$posts.0.id}+50.html">{t}Last 50 posts{/t}</a>&#93;
	{if ( count($posts) > 100 || $replycount > 100) }
		&#91;<a href="{%KU_WEBFOLDER}{$board.name}/res/{$posts.0.id}-100.html">{t}First 100 posts{/t}</a>&#93;
	{/if}
{/if}
{if not $isread}
	<div class="replymode">{t}Posting mode: Reply{/t}
	{if $modifier eq 'first100'}
		[{t}First 100 posts{/t}]
	{elseif $modifier eq 'last50'}
		[{t}Last 50 posts{/t}]
	{/if}
{else}
	&#91;<a href="{%KU_WEBFOLDER}{$board.name}/res/{$posts.0.id}.html">{t}Entire Thread{/t}</a>&#93; 
{/if}
</div>