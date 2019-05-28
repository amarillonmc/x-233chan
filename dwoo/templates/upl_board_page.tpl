{if count($posts) > 0}
	<center>
		<table width="98%">
		<tr>
			<td class="postblock" align="center" width="1%">
				No.
			</td>
			<td class="postblock" style="text-align:center;width:25%;">
				{t}Name{/t}
			</td>
			<td class="postblock" align="center" width="1%">
				{t}File{/t}
			</td>
			<td class="postblock" align="center" width="1%">
				{t}Tag{/t}
			</td>
			<td class="postblock" style="text-align:center;width:40%;">
				{t}Subject{/t}
			</td>
			<td class="postblock" align="center" width="1%">
				{t}Size{/t}
			</td>
			<td class="postblock" align="center" width="1%">
				{t}Date{/t}
			</td>
			<td class="postblock" style="text-align:center;width:1px;">
				Rep.
			</td>
			<td class="postblock" style="width:1px;">
				&nbsp;
			</td>
		</tr>
	{foreach key=postkey item=post from=$posts}
		<tr>
		<td align="center">
			{$post.id}
		</td>
		<td>
			<span class="postername">
			{if $post.email neq '' && $board.anonymous neq ''}
				<a href="mailto:{$post.email}">
			{/if}
			{if $post.name eq '' && $post.tripcode eq ''}
				{$board.anonymous}
			{elseif $post.name eq '' && $post.tripcode neq ''}
			{else}
				{$post.name}
			{/if}
			{if $post.email neq '' && $board.anonymous neq ''}
				</a>
			{/if}

			</span>

			{if $post.tripcode neq ''}
				<span class="postertrip">!{$post.tripcode}</span>
			{/if}
			{if $post.posterauthority eq 1}
				<span class="admin">
					&#35;&#35;&nbsp;{t}Admin{/t}&nbsp;&#35;&#35;
				</span>
			{elseif $post.posterauthority eq 4}
				<span class="mod">
					&#35;&#35;&nbsp;{t}Super Mod{/t}&nbsp;&#35;&#35;
				</span>
			{elseif $post.posterauthority eq 2}
				<span class="mod">
					&#35;&#35;&nbsp;{t}Mod{/t}&nbsp;&#35;&#35;
				</span>
			{/if}

		</td>
		<td align="center">
			[<a href="{%KU_BOARDSFOLDER}{$board.name}/src/{$post.file}.{$post.file_type}" target="_blank">{$post.file}.{$post.file_type}</a>]
		</td>
		<td align="center">
			[{$post.tag}]
		</td>
		<td>
			{$post.subject}
		</td>
		<td align="center">
			{$post.file_size_formatted}
		</td>
		<td>
			<span style="white-space: nowrap;">{$post.timestamp|date_format:"%y/%m/%d(%a)%H:%M"}</span>
		</td>
		<td align="center">
			{$post.replies}
		</td>
		<td align="center">
			[<a href="{%KU_BOARDSFOLDER}{$board.name}/res/{$post.id}.html">Reply</a>]
		</td>
	</tr>
	{/foreach}
	</table></center><br /><hr />
{/if}