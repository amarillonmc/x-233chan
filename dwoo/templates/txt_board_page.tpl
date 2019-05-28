{foreach name=thread item=postsa from=$posts}
	{foreach name=pst key=postkey item=post from=$postsa}
		{if $post.parentid eq 0}
			<div class="border">
				<div class="thread">
				<a name="{$.foreach.thread.iteration-1}"></a>
		{/if}
		{if $post.parentid eq 0}
			{if $.foreach.thread.last}
							<span class="navlinks">
								<a href="#{$posts|count - 1}">&uarr;</a>&nbsp;
								<a href="#0">&darr;</a>&nbsp;
								<a href="#menu">&#9632;</a>
							</span>
			{else}
							<span class="navlinks">
								<a href="#{$posts|count - 1}">&uarr;</a>&nbsp;
								<a href="#{$.foreach.thread.iteration}">&darr;</a>&nbsp;
								<a href="#menu">&#9632;</a>
							</span>
			{/if}
		{/if}
		
		{if $post.parentid eq 0}
		<h2>
			<a href="res/{$post.id}.html">{$post.subject}</a>
			<span class="replies">({$postsa.0.replies})</span></h2>
		{/if}
		
		{if $.foreach.thread.iteration % 2 eq 0}
			<div class="post even">
		{else}
			<div class="post odd">
		{/if}
		<h3>
		
			<span class="postnum">
			{if $post.parentid eq 0}
				<a href="javascript:quote(1, 'post{$post.id}');">1</a>
				<a href="{%KU_BOARDSPATH}/{$board.name}/res/{$post.id}.html#1">.</a>
			{else}
				<a href="javascript:quote({math "replies-postcount+iteration+x" replies=$postsa.0.replies postcount=$postsa|count iteration=$.foreach.pst.iteration x=1}, 'post{$post.id}');">{math "replies-postcount+iteration+x" replies=$postsa.0.replies postcount=$postsa|count iteration=$.foreach.pst.iteration x=1}</a>
				<a href="{%KU_BOARDSPATH}/{$board.name}/res/{$post.parentid}.html#{math "replies-postcount+iteration+x" replies=$postsa.0.replies postcount=$postsa|count iteration=$.foreach.pst.iteration x=1}">.</a>
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
			<span id="dnb-{$board.name}-{$post.id}-{if $post.parentid eq 0}y{else}n{/if}"></span>
			<span class="id"></span>
			</span>
			</h3>
			<blockquote>
				{$post.message}
			</blockquote>
			</div>
			{if $.foreach.pst.last}
				<form name="post{if $post.parentid eq 0}{$post.id}{else}{$post.parentid}{/if}" id="post{if $post.parentid eq 0}{$post.id}{else}{$post.parentid}{/if}" action="{%KU_CGIPATH}/board.php" method="post" {if $board.enablecaptcha eq 1}onsubmit="return checkcaptcha('post{if $post.parentid eq 0}{$post.id}{else}{$post.parentid}{/if}');"{/if}>
				<input type="hidden" name="board" value="{$board.name}" />
				<input type="hidden" name="replythread" value="{if $post.parentid eq 0}{$post.id}{else}{$post.parentid}{/if}" />
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
							<a href="#" onclick="toggleOptions('{if $post.parentid eq 0}{$post.id}{else}{$post.parentid}{/if}', 'post{if $post.parentid eq 0}{$post.id}{else}{$post.parentid}{/if}', '{$board.name}');return false;">{t}More{/t}...</a>
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
							<a href="#" onclick="toggleOptions('{if $post.parentid eq 0}{$post.id}{else}{$post.parentid}{/if}', 'post{if $post.parentid eq 0}{$post.id}{else}{$post.parentid}{/if}', '{$board.name}');return false;">{t}More{/t}...</a>
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
				<tr style="display: none;" id="opt{if $post.parentid eq 0}{$post.id}{else}{$post.parentid}{/if}"><td></td></tr>
				<tr>
					<td class="postfieldleft">
						<span class="postnum">
							{$postsa.0.replies+2}
						</span>
					</td>
					<td colspan="4">
						<textarea name="message" rows="8" cols="64"></textarea>
					</td>
				</tr>
			</table>
			<div id="preview{if $post.parentid eq 0}{$post.id}{else}{$post.parentid}{/if}"></div>
				</form>
				<script type="text/javascript"><!--
					set_inputs('post{if $post.parentid eq 0}{$post.id}{else}{$post.parentid}{/if}');
				//--></script>
				</div></div>
			{/if}
	{/foreach}

{/foreach}

