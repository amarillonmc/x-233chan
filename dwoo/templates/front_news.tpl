{include(file='front_header.tpl')}

{foreach $news new}
	<div class="box last-new">
		<div class="box-title">{$new.subject} - {date_format $new.timestamp "%d/%m/%Y @ %H:%M:%S"} - by {$new.poster}</div>
		<div class="box-content">
			<p>{$new.message}</p>
		</div>
	</div>
{/foreach}

{include(file='front_footer.tpl')}