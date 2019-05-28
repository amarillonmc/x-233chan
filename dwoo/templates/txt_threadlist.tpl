<div class="hborder">
	<div class="head threadldiv"{if $board.compactlist} style="padding: 4px;"{/if}>
		<a name="menu"></a>
		{if not $board.compactlist || not $isindex}
			<table class="threads">
			<thead>
				<tr>
					<th width="10%">#</th>
					<th nowrap="nowrap" width="100%">{t}Subject{/t}</th>
					<th>{t}Posts{/t}</th>
				<th>{t}Last Post{/t}</th>
				</tr>
			</thead>
			<tbody>
		{/if}
		{if count($threads) > 0}
			{foreach key=threadkey name=list item=thread from=$threads}
				
				{if $board.compactlist && $isindex}
					<a href="{if $.foreach.list.iteration < %KU_THREADSTXT}#{$.foreach.list.iteration}">{$.foreach.list.iteration}: </a><a href="res/{$thread.id}.html">{else}res/{$thread.id}.html">{$.foreach.list.iteration}: {/if}{$thread.subject} ({$thread.replies + 1})</a>{if $.foreach.thread.last}{else} &nbsp;{/if}
				
				{else}
					<tr><td><a href="res/{$thread.id}.html">{$.foreach.list.iteration}</a></td><td><a href="{if $.foreach.list.iteration < %KU_THREADSTXT}#{$.foreach.list.iteration-1}{else}res/{$thread.id}.html{/if}">{$thread.subject}</a></td><td>{$thread.replies + 1}</td><td nowrap="nowrap"><small>{$thread.bumped|date_format:"%e %B %Y %H:%M"}</small></td></tr>
				{/if}
			{/foreach}
		{else}
			{if $board.compactlist && $isindex}
				{t}There are currently no threads to display.{/t}
			{else}
				<tr><td>N/A</td><td>{t}There are currently no threads to display.{/t}</td><td>N/A</td><td>N/A</td></tr>
			{/if}
		{/if}
		{if $isindex}
			{if $board.compactlist}
				<br /><div class="threadlinks">
			{else}
				<tr><td colspan="4" class="threadlinks">
			{/if}
			<a href="#newthread" style="display: inline;">{t}New Thread{/t}</a> | <a href="list.html" style="display: inline;">{t}All Threads{/t}</a>
			{if $board.compactlist}
				</div>
			{else}
				</td></tr>
			{/if}
		{/if}
		{if not $board.compactlist || not $isindex}
				</tbody>
			</table>
		{/if}

		</div>
	</div>
</div>
</div>
