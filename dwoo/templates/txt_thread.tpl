{foreach name=pst key=postkey item=post from=$posts}

		
		{if $post.parentid eq 0 || isread}
			<form id="delform" action="{%KU_CGIPATH}/board.php" method="post">
			<input type="hidden" name="board" value="{$board.name}" />

			{if $post.parentid eq 0 }
			<h2>
				{$post.subject}
				<span class="replies">({$posts|count - 1})</span></h2>
			{/if}
		{/if}
		
		<div class="post even">
		<h3>
			<span class="postnum">
			{if $post.parentid eq 0}
				<a href="javascript:quote(1, 'post{$post.id}');">1</a>
				<a href="{%KU_BOARDSPATH}/{$board.name}/res/{$post.id}.html#1">.</a>
			{else}
				<a href="javascript:quote({if not $postnum}{$.foreach.pst.iteration}{else}{$postnum}{/if}, 'post{$post.id}');">{if not $postnum}{$.foreach.pst.iteration}{else}{$postnum}{/if}</a>
				<a href="{%KU_BOARDSPATH}/{$board.name}/res/{$post.parentid}.html#{if not $postnum}{$.foreach.pst.iteration}{else}{$postnum}{/if}">.</a>
			{/if}
			</span>
			<span class="postinfo">
			{t}Name{/t}: 
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
			@ {$post.timestamp|date_format:"%Y-%m-%d %H:%M"}
			{if $board.showid}
				{t}ID{/t}: {$post.ipmd5|substr:0:6}
			{/if}
			<input type="checkbox" name="post[]" value="{$post.id}" />
			<span id="dnb-{$board.name}-{$post_id}-{if $post.parentid eq 0}y{else}n{/if}"></span>
			<span class="id"></span>
			</span>
			</h3>
			<blockquote>
				{$post.message}
			</blockquote>
			</div>
			{if not $post.stickied && $post.parentid eq 0 && (($board.maxage > 0 && ($post + ($board.maxage * 3600)) < (time() + 7200 ) ) || ($post.deleted_timestamp > 0 && $post.deleted_timestamp <= (time() + 7200)))}
				<span class="oldpost">
					{t}Marked for deletion (old){/t}
				</span>
				<br />
			{/if}
			{if $post.parentid eq 0}
				<div id="replies{$post.id}{$board.name}">
				{if $modifier}
					<span class="abbrev">
						{if $modifier eq 'last50'}
							{$replycount-50}
							{if $replycount-50 eq 1}
								{t lower="yes"}Post{/t}
							{else}
								{t lower="yes"}Posts{/t}
							{/if}
						{t}omitted{/t}. {t}Last 50 posts shown{/t}.
						{elseif $modifier eq 'first100'}
							{$replycount-100}
							{if $replycount-100 eq 1}
								{t lower="yes"}Post{/t}
							{else}
								{t lower="yes"}Posts{/t}
							{/if}
							{t}omitted{/t}. {t}First 100 posts shown{/t}.
						{/if}
						</span>
					{/if}
			{/if}
			{if $.foreach.pst.last && not $isread}
			<table class="hborder">
			<tbody>
			<tr>
			<td>
			{t}Delete Post{/t}: <input type="password" name="postpassword" size="8" />&nbsp;<input name="deletepost" value="{t}Delete{/t}" type="submit" />
			</td>
			</tr>
			</tbody>
			</table>
			</form>
			<script type="text/javascript"><!--
				set_delpass("delform");
			//--></script>
			
			{if !$posts.0.locked}
				<form name="post{$posts.0.id}" id="post{$posts.0.id}" action="{%KU_CGIPATH}/board.php" method="post" {if $board.enablecaptcha eq 1}onsubmit="return checkcaptcha('post{$posts.parentid}');"{/if}>
				<input type="hidden" name="board" value="{$board.name}" />
				<input type="hidden" name="replythread" value="{$posts.0.id}" />
				<input name="email" size="25" value="" style="display: none;" />
				<table class="postform">
				<tr>
					{if $board.forcedanon neq 1}
						<td class="label">
							<label>{t}Name{/t}:</label>
						</td>
						<td>
							<input name="name" size="25" maxlength="75" />
						</td>
					{/if}
					<td class="label">
						<label>{t}Email{/t}:</label>
					</td>
					<td>
						<input name="em" size="25" maxlength="75" />
					</td>
					{if $board.forcedanon neq 1}
						<td>
							<input type="submit" name="submit" value="{t}Reply{/t}" class="submit" />
							<a href="#" onclick="toggleOptions('{$threadid}', 'post{$threadid}', '{$board.name}');return false;">{t}More{/t}...</a>
						</td>
					</tr>
					<tr>
					{/if}

					{if $board.enablecaptcha eq 1}
						<td class="label"><label for="captcha">{t}Captcha{/t}:</label></td>
						<td>
							<a href="#" onclick="javascript:document.getElementById('captchaimage').src = '{%KU_CGIPATH}/captcha.php?' + Math.random();return false;">
							<img id="captchaimage" src="{%KU_CGIPATH}/captcha.php" border="0" width="90" height="30" alt="Captcha image" />
							</a>&nbsp;
							<input type="text" id="captcha" name="captcha" size="8" maxlength="6" />
						</td>
					{/if}
					{if ($board.forcedanon eq 1 && $board.enablecaptcha neq 1) || $board.forcedanon neq 1}
						<td class="label">
							<label>{t}Password{/t}:</label>
						</td>
						<td>
							<input type="password" name="postpassword" size="8" accesskey="p" maxlength="75" />
						</td>
					{/if}
					{if $board.forcedanon eq 1}
						<td>
							<input type="submit" name="submit" value="{t}Reply{/t}" class="submit" />
							<a href="#" onclick="toggleOptions('{$threadid}', 'post{$threadid}', '{$board.name}');return false;">{t}More{/t}...</a>
						</td>
						{if $board.enablecaptcha eq 1}
						</tr>
							<tr>
								<td class="label">
									<label>{t}Password{/t}:</label>
								</td>
								<td>
									<input type="password" name="postpassword" size="8" accesskey="p" maxlength="75" />
								</td>
						{/if}
					{/if}
				</tr>
				<tr style="display: none;" id="opt{$threadid}"><td></td></tr>
				<tr>
					<td class="postfieldleft">
						<span class="postnum">
							{$posts|count+1}
						</span>
					</td>
					<td colspan="4">
						<textarea name="message" rows="8" cols="64"></textarea>
					</td>
				</tr>
			</table>
			<div id="preview{$threadid}"></div>
				</form>
				<script type="text/javascript"><!--
					set_inputs('post{$posts.parentid}');
				//--></script>
		{elseif $isread}
			</form>
		{/if}
		{/if}
	{/foreach}
