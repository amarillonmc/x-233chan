<div class="hborder head newthread">
	<a id="newthread"></a><h2>{t}New Thread{/t}</h2>
	<a id="postbox"></a>
	<form name="postform" id="postform" action="{%KU_CGIPATH}/board.php" method="post" enctype="multipart/form-data"{if $board.enablecaptcha eq 1} onsubmit="return checkcaptcha('postform');"{/if}>
	<input type="hidden" name="board" value="{$board.name}" />
	<input type="hidden" name="replythread" value="<!sm_threadid>" />
	{if $board.maximagesize > 0}
		<input type="hidden" name="MAX_FILE_SIZE" value="{$board.maximagesize}" />
	{/if}
	<input type="text" name="email" size="28" maxlength="75" value="" style="display: none;" />
	<table class="postform">
		<tr>
			<td class="label">
				<label>{t}Subject{/t}:</label>
			</td>

			<td colspan="4">
				<input name="subject" maxlength="75" size="50" style="width: 70%;" />
			</td>
		</tr>
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
					<input type="submit" name="submit" value="{t}Submit{/t}" class="submit" />
					<a href="#" onclick="toggleOptions('0', 'postform', '{$board.name}');return false;">{t}More{/t}...</a>
				</td>
			</tr>
			<tr>
			{/if}

			{if $board.enablecaptcha eq 1}
				<tr>
					<td class="postblock">{t}Captcha{/t}</td>
					<td colspan="2">{$recaptcha}</td>
				</tr>
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
					<input type="submit" name="submit" value="{t}Submit{/t}" class="submit" />
					<a href="#" onclick="toggleOptions('0', 'postform', '{$board.name}');return false;">{t}More{/t}...</a>
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
		<tr style="display: none;" id="opt0"><td></td></tr>
		<tr>
			<td class="postfieldleft">
				<span class="postnum">
					1
				</span>
			</td>
			<td colspan="4">
				<textarea name="message" rows="8" cols="64"></textarea>
			</td>
		</tr>
	</table>
	<div id="preview0"></div>
	</form>
</div>
<script type="text/javascript"><!--
	set_inputs("postform");
//--></script>