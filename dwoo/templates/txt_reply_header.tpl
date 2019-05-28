<a href="{%KU_WEBFOLDER}{$board.name}/">{t}Return{/t}</a>
{if $replythread}
	<a href="{%KU_WEBFOLDER}{$board.name}/res/{$replythread}.html">{t}Entire Thread{/t}</a>
{/if}
{if %KU_FIRSTLAST && ( count($posts) > 50 || $replycount > 50)}
	<a href="{%KU_WEBFOLDER}{$board.name}/res/{$posts.0.id}+50.html">{t}Last 50 posts{/t}</a>
	{if ( count($posts) > 100 || $replycount > 100) }
		<a href="{%KU_WEBFOLDER}{$board.name}/res/{$posts.0.id}-100.html">{t}First 100 posts{/t}</a>
	{/if}
{/if}
<br />
<br />