{include(file='front_header.tpl')}

<table border="1">
	<thead>
		<th>IP</th>
		<th>Motivo</th>
		<th>Boards</th>
		<th>Banido em</th>
		<th>Expira em</th>
		<th>Moderador</th>
	</thead>
	<tbody>
		{foreach $bans ban}
			<tr {if $.server.REMOTE_ADDR eq md5_decrypt($ban.ip, $seed)} style="background: #FFE4E1"{/if}>
				<td><a href="http://www.geoiptool.com/pt/?IP={md5_decrypt($ban.ip, $seed)}">{md5_decrypt($ban.ip, $seed)}</a></td>
				<td>{$ban.reason}</td>
				<td>{if $ban.boards eq ''}Todas{else}{replace $ban.boards "|" " / "}{/if}</td>
				<td nowrap>{date_format $ban.at "%d/%m @ %H:%M"}</td>
				<td nowrap>{if $ban.until eq 0}Nunca{else}{date_format $ban.until "%d/%m @ %H:%M"}{/if}</td>
				<td>{$ban.by}</td>
			</tr>
		{/foreach}
	</tbody>
</table>

{include(file='front_footer.tpl')}