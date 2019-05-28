{include(file='front_header.tpl')}

{foreach $faq faq}
	<div class="box last-faq">
		<div class="box-title">{$faq.subject}</div>
		<div class="box-content">
			<p>{$faq.message}</p>
		</div>
	</div>
{/foreach}

{include(file='front_footer.tpl')}