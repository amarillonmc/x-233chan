<div class="postarea">
<a id="postbox"></a>
<form name="postform" id="postform" action="{%KU_CGIPATH}/board.php" method="post" enctype="multipart/form-data"
{if $board.enablecaptcha eq 1}
	onsubmit="return checkcaptcha('postform');"
{/if}
>
<script type="text/javascript">
    var oldonload = window.onload;
    window.onload=function(){
    oldonload();
    var ds_textarea = document.getElementById("fcom");
    var faceList = ["|∀ﾟ", "(´ﾟДﾟ`)", "(;´Д`)", "(｀･ω･)", "(=ﾟωﾟ)=", "| ω・´)", "|-` )", "|д` )", "|ー` )", "|∀` )", "(つд⊂)", "(ﾟДﾟ≡ﾟДﾟ)", "(＾o＾)ﾉ", "(|||ﾟДﾟ)", "( ﾟ∀ﾟ)", "( ´∀`)", "(*´∀`)", "(*ﾟ∇ﾟ)", "(*ﾟーﾟ)", "(　ﾟ 3ﾟ)", "( ´ー`)", "( ・_ゝ・)", "( ´_ゝ`)", "(*´д`)", "(・ー・)", "(・∀・)", "(ゝ∀･)", "(〃∀〃)", "(*ﾟ∀ﾟ*)", "( ﾟ∀。)", "( `д´)", "(`ε´ )", "(`ヮ´ )", "σ`∀´)", " ﾟ∀ﾟ)σ", "ﾟ ∀ﾟ)ノ", "(╬ﾟдﾟ)", "(|||ﾟдﾟ)", "( ﾟдﾟ)", "Σ( ﾟдﾟ)", "( ;ﾟдﾟ)", "( ;´д`)", "(　д ) ﾟ ﾟ", "( ☉д⊙)", "(((　ﾟдﾟ)))", "( ` ・´)", "( ´д`)", "( -д-)", "(>д<)", "･ﾟ( ﾉд`ﾟ)", "( TдT)", "(￣∇￣)", "(￣3￣)", "(￣ｰ￣)", "(￣ . ￣)", "(￣皿￣)", "(￣艸￣)", "(￣︿￣)", "(￣︶￣)", "ヾ(´ωﾟ｀)", "(*´ω`*)", "(・ω・)", "( ´・ω)", "(｀・ω)", "(´・ω・`)", "(`・ω・´)", "( `_っ´)", "( `ー´)", "( ´_っ`)", "( ´ρ`)", "( ﾟωﾟ)", "(oﾟωﾟo)", "(　^ω^)", "(｡◕∀◕｡)", "/( ◕‿‿◕ )\\", "ヾ(´ε`ヾ)", "(ノﾟ∀ﾟ)ノ", "(σﾟдﾟ)σ", "(σﾟ∀ﾟ)σ", "|дﾟ )", "┃電柱┃", "ﾟ(つд`ﾟ)", "ﾟÅﾟ )　", "⊂彡☆))д`)", "⊂彡☆))д´)", "⊂彡☆))∀`)", "(´∀((☆ミつ"];
    var optionsList = document.getElementById("emotion").options;
    for (var i = 0; i < faceList.length; i++) {
        optionsList[1 + i] = new Option(faceList[i], faceList[i]);
    }
    document.getElementById("emotion").onchange = function (i) { 
        if (this.selectedIndex != 0) { 
            ds_textarea.value += this.value; 
            //alert(this.value);
            var l = ds_textarea.value.length; 
            ds_textarea.focus(); 
            ds_textarea.setSelectionRange(l, l); 
        } 
    }
    }
// <![CDATA[
//error handling
empty_form = "You must write something!";
 
// Helpline messages
var help_line = {
b: 'Bold: [b]Text[/b]',
i: 'Cursive: [i]Text[/i]',
u: 'Underline: [u]Text[/u]',
c: 'Strike: [s]Text[/s]',
l: 'List: [list]Text[/list]',
o: 'Ordened List: [list=]Text[/list]',
p: 'Insert Remote Image: [img]http://url_imagen[/img]',
w: 'Insert URL: [url]http://url[/url] o [url=http://url]Text URL[/url]',
a: 'Close All BBCode Tags Opened',
s: 'Color: [color=red]text[/color] You can also use color=#FF0000',
f: 'Size: [size=x-small]small text[/size]',
spoiler: '[spoiler]Text[/spoiler]'
}
 
// ]]>
</script>
<input type="hidden" name="board" value="{$board.name}" />
<input type="hidden" name="replythread" value="<!sm_threadid>" />
{if $board.maximagesize > 0}
	<input type="hidden" name="MAX_FILE_SIZE" value="{$board.maximagesize}" />
{/if}
<input type="text" name="email" size="28" maxlength="75" value="" style="display: none;" />
<table class="postform">
	<tbody>
	{if $board.forcedanon neq 1}
		<tr>
			<td class="postblock">
				{t}Name{/t}</td>
			<td>
				<input type="text" name="name" size="28" maxlength="75" accesskey="n" />
			</td>
		</tr>
	{/if}
	<tr>
		<td class="postblock">
			{t}Email{/t}</td>
		<td>
			<input type="text" name="em" size="28" maxlength="75" accesskey="e" />
		</td>
	</tr>
	<tr>
		<td class="postblock">
			{t}Subject{/t}
		</td>
		<td>
			{strip}<input type="text" name="subject" size="35" maxlength="75" accesskey="s" />&nbsp;<input type="submit" value="
			{if %KU_QUICKREPLY && $replythread eq 0}
				{t}Submit{/t}" accesskey="z" />&nbsp;(<span id="posttypeindicator">{t}new thread{/t}</span>)
			{elseif %KU_QUICKREPLY && $replythread neq 0}
				{t}Reply{/t}" accesskey="z" />&nbsp;(<span id="posttypeindicator">{t}reply to{/t} <!sm_threadid></span>)
			{else}
				{t}Submit{/t}" accesskey="z" />
			{/if}{/strip}
		</td>
	</tr>
        <tr>
		<td class="postblock">
			<b>颜文字</b>
		</td>
        	<td>
			<select id='emotion'><option value='' selected='selected'>Emoicon</option><option value='|∀ﾟ'>|∀ﾟ</option><option value='(´ﾟДﾟ`)'>(´ﾟДﾟ`)</option><option value='(;´Д`)'>(;´Д`)</option><option value='(｀･ω･)'>(｀･ω･)</option><option value='(=ﾟωﾟ)='>(=ﾟωﾟ)=</option><option value='| ω・´)'>| ω・´)</option><option value='|-` )'>|-` )</option><option value='|д` )'>|д` )</option><option value='|ー` )'>|ー` )</option><option value='|∀` )'>|∀` )</option><option value='(つд⊂)'>(つд⊂)</option><option value='(ﾟДﾟ≡ﾟДﾟ)'>(ﾟДﾟ≡ﾟДﾟ)</option><option value='(＾o＾)ﾉ'>(＾o＾)ﾉ</option><option value='(|||ﾟДﾟ)'>(|||ﾟДﾟ)</option><option value='( ﾟ∀ﾟ)'>( ﾟ∀ﾟ)</option><option value='( ´∀`)'>( ´∀`)</option><option value='(*´∀`)'>(*´∀`)</option><option value='(*ﾟ∇ﾟ)'>(*ﾟ∇ﾟ)</option><option value='(*ﾟーﾟ)'>(*ﾟーﾟ)</option><option value='(　ﾟ 3ﾟ)'>(　ﾟ 3ﾟ)</option><option value='( ´ー`)'>( ´ー`)</option><option value='( ・_ゝ・)'>( ・_ゝ・)</option><option value='( ´_ゝ`)'>( ´_ゝ`)</option><option value='(*´д`)'>(*´д`)</option><option value='(・ー・)'>(・ー・)</option><option value='(・∀・)'>(・∀・)</option><option value='(ゝ∀･)'>(ゝ∀･)</option><option value='(〃∀〃)'>(〃∀〃)</option><option value='(*ﾟ∀ﾟ*)'>(*ﾟ∀ﾟ*)</option><option value='( ﾟ∀。)'>( ﾟ∀。)</option><option value='( `д´)'>( `д´)</option><option value='(`ε´ )'>(`ε´ )</option><option value='(`ヮ´ )'>(`ヮ´ )</option><option value='σ`∀´)'>σ`∀´)</option><option value=' ﾟ∀ﾟ)σ'> ﾟ∀ﾟ)σ</option><option value='ﾟ ∀ﾟ)ノ'>ﾟ ∀ﾟ)ノ</option><option value='(╬ﾟдﾟ)'>(╬ﾟдﾟ)</option><option value='(|||ﾟдﾟ)'>(|||ﾟдﾟ)</option><option value='( ﾟдﾟ)'>( ﾟдﾟ)</option><option value='Σ( ﾟдﾟ)'>Σ( ﾟдﾟ)</option><option value='( ;ﾟдﾟ)'>( ;ﾟдﾟ)</option><option value='( ;´д`)'>( ;´д`)</option><option value='(　д ) ﾟ ﾟ'>(　д ) ﾟ ﾟ</option><option value='( ☉д⊙)'>( ☉д⊙)</option><option value='(((　ﾟдﾟ)))'>(((　ﾟдﾟ)))</option><option value='( ` ・´)'>( ` ・´)</option><option value='( ´д`)'>( ´д`)</option><option value='( -д-)'>( -д-)</option><option value='(&gt;д&lt;)'>(&gt;д&lt;)</option><option value='･ﾟ( ﾉд`ﾟ)'>･ﾟ( ﾉд`ﾟ)</option><option value='( TдT)'>( TдT)</option><option value='(￣∇￣)'>(￣∇￣)</option><option value='(￣3￣)'>(￣3￣)</option><option value='(￣ｰ￣)'>(￣ｰ￣)</option><option value='(￣ . ￣)'>(￣ . ￣)</option><option value='(￣皿￣)'>(￣皿￣)</option><option value='(￣艸￣)'>(￣艸￣)</option><option value='(￣︿￣)'>(￣︿￣)</option><option value='(￣︶￣)'>(￣︶￣)</option><option value='ヾ(´ωﾟ｀)'>ヾ(´ωﾟ｀)</option><option value='(*´ω`*)'>(*´ω`*)</option><option value='(・ω・)'>(・ω・)</option><option value='( ´・ω)'>( ´・ω)</option><option value='(｀・ω)'>(｀・ω)</option><option value='(´・ω・`)'>(´・ω・`)</option><option value='(`・ω・´)'>(`・ω・´)</option><option value='( `_っ´)'>( `_っ´)</option><option value='( `ー´)'>( `ー´)</option><option value='( ´_っ`)'>( ´_っ`)</option><option value='( ´ρ`)'>( ´ρ`)</option><option value='( ﾟωﾟ)'>( ﾟωﾟ)</option><option value='(oﾟωﾟo)'>(oﾟωﾟo)</option><option value='(　^ω^)'>(　^ω^)</option><option value='(｡◕∀◕｡)'>(｡◕∀◕｡)</option><option value='/( ◕‿‿◕ )\'>/( ◕‿‿◕ )\</option><option value='ヾ(´ε`ヾ)'>ヾ(´ε`ヾ)</option><option value='(ノﾟ∀ﾟ)ノ'>(ノﾟ∀ﾟ)ノ</option><option value='(σﾟдﾟ)σ'>(σﾟдﾟ)σ</option><option value='(σﾟ∀ﾟ)σ'>(σﾟ∀ﾟ)σ</option><option value='|дﾟ )'>|дﾟ )</option><option value='┃電柱┃'>┃電柱┃</option><option value='ﾟ(つд`ﾟ)'>ﾟ(つд`ﾟ)</option><option value='ﾟÅﾟ )　'>ﾟÅﾟ )　</option><option value='⊂彡☆))д`)'>⊂彡☆))д`)</option><option value='⊂彡☆))д´)'>⊂彡☆))д´)</option><option value='⊂彡☆))∀`)'>⊂彡☆))∀`)</option><option value='(´∀((☆ミつ'>(´∀((☆ミつ</option></select></td>
        </tr>
	<tr>
		<td class="postblock">
			{t}Message{/t}
		</td>
		<td>
			<textarea name="message" id="fcom" cols="48" rows="4" accesskey="m"></textarea>
		</td>
	</tr>
	{if $board.enablecaptcha eq 1}
		<tr>
			<td class="postblock">{t}Captcha{/t}</td>
			<td colspan="2">{$recaptcha}</td>
		</tr>
	{/if}
	{if $board.uploadtype eq 0 || $board.uploadtype eq 1}
		<tr>
			<td class="postblock">
				{t}File{/t}
			</td>
			<td>
			<input type="file" name="imagefile" size="35" accesskey="f" />
			{if $replythread eq 0 && $board.enablenofile eq 1 }
				[<input type="checkbox" name="nofile" id="nofile" accesskey="q" checked="checked"/><label for="nofile"> {t}No File{/t}</label>]
			{/if}
			</td>
		</tr>
	{/if}
	{if ($board.uploadtype eq 1 || $board.uploadtype eq 2) && $board.embeds_allowed neq ''}
		<tr>
			<td class="postblock">
				{t}Embed{/t}
			</td>
			<td>
				<input type="text" name="embed" size="28" maxlength="75" accesskey="e" />&nbsp;<select name="embedtype">
				{foreach name=embed from=$embeds item=embed}
					{if in_array($embed.filetype,explode(',' $board.embeds_allowed))}
						<option value="{$embed.name|lower}">{$embed.name}</option>
					{/if}
				{/foreach}
				</select>
				<a class="rules" href="#postbox" onclick="window.open('{%KU_WEBPATH}/embedhelp.php','embedhelp','toolbar=0,location=0,status=0,menubar=0,scrollbars=0,resizable=0,width=300,height=210');return false;">Help</a>
			</td>
		</tr>
	{/if}
		<tr>
			<td class="postblock">
				{t}Password{/t}
			</td>
			<td>
				<input type="password" name="postpassword" size="8" accesskey="p" />&nbsp;{t}(for post and file deletion){/t}
			</td>
		</tr>
		<tr id="passwordbox"><td></td><td></td></tr>
		<tr>
			<td colspan="2" class="rules">
<div id="bbcode">
<input class="sub_btn" accesskey="b" name="addbbcode0" value=" B " style="font-weight:bold; width:30px" onclick="bbstyle(0)" onmouseover="helpline('b')" type="button">
<input class="sub_btn" accesskey="i" name="addbbcode2" value=" i " style="font-style:italic; width:30px" onclick="bbstyle(2)" onmouseover="helpline('i')" type="button">
<input class="sub_btn" accesskey="u" name="addbbcode4" value=" u " style="width:30px" onclick="bbstyle(4)" onmouseover="helpline('u')" type="button">
<input class="sub_btn" accesskey="q" name="addbbcode6" value="Quote" style="width:50px" onclick="bbstyle(6)" onmouseover="helpline('q')" type="button">
<input class="sub_btn" accesskey="c" name="addbbcode8" value="Strike" style="width:40px;" onclick="bbstyle(8)" onmouseover="helpline('c')" type="button">
<input class="sub_btn" name="addbbcode18" value="Spoiler" style="width:60px" onclick="bbstyle(18)" onmouseover="helpline('spoiler')" type="button">
</div>
<div id="tip"><input name="helpbox" id="helpbox" value="" disabled="" type="text" style="color: #000600; background-color: #ffffff; border: 1px solid #ffffff;"></div>
				<ul style="margin-left: 0; margin-top: 0; margin-bottom: 0; padding-left: 0;">
					<li>{t}Supported file types are{/t}:
					{if $board.filetypes_allowed neq ''}
						{foreach name=files item=filetype from=$board.filetypes_allowed}
							{$filetype.0|upper}{if $.foreach.files.last}{else}, {/if}
						{/foreach}
					{else}
						{t}None{/t}
					{/if}
					</li>
					<li>{t}Maximum file size allowed is{/t} {math "round(x/1024)" x=$board.maximagesize} KB.</li>
					<li>{t 1=%KU_THUMBWIDTH 2=%KU_THUMBHEIGHT}Images greater than %1x%2 pixels will be thumbnailed.{/t}</li>
					<li>{t 1=$board.uniqueposts}Currently %1 unique user posts.{/t}
					{if $board.enablecatalog eq 1} 
						<a href="{%KU_BOARDSFOLDER}{$board.name}/catalog.html">{t}View catalog{/t}</a>
					{/if}
					</li>
				</ul>
			{if %KU_BLOTTER && $blotter}
				<br />
				<ul style="margin-left: 0; margin-top: 0; margin-bottom: 0; padding-left: 0;">
				<li style="position: relative;">
					<span style="color: red;">
				{t}Blotter updated{/t}: {$blotter_updated|date_format:"%Y-%m-%d"}
				</span>
					<span style="color: red;text-align: right;position: absolute;right: 0px;">
						<a href="#" onclick="javascript:toggleblotter(true);return false;">{t}Show/Hide{/t}</a> <a href="{%KU_WEBPATH}/blotter.php">{t}Show All{/t}</a>
					</span>
				</li>
				{$blotter}
				</ul>
				<script type="text/javascript"><!--
				if (getCookie('ku_showblotter') == '1') {
					toggleblotter(false);
				}
				--></script>
			{/if}
			</td>
		</tr>
	</tbody>

</table>
</form>
<hr />
{if $topads neq ''}
	<div class="content ads">
		<center> 
			{$topads}
		</center>
	</div>
	<hr />
{/if}
</div>
<script type="text/javascript"><!--
				set_inputs("postform");
				//--></script>