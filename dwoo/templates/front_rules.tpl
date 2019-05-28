{include(file='front_header.tpl')}

<div class="box last-new">
	<div class="box-title">Regras</div>
	<div class="box-content">
		<ul>
			{foreach $rules rule}
				<li>{$rule.message}</li>
			{/foreach}
		</ul>
	</div>
</div>

{include(file='front_footer.tpl')}