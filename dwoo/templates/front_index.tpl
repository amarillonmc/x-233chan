{include(file='front_header.tpl')}

<div class="box boards">
	<div class="box-title">Boards</div>
	<div class="box-content">
		{foreach $sections section}
			<ul>
				{$section.name}
				{foreach $boards board}
					{if $section.id eq $board.section}
						<li>
							<a href="{$board.name}">/{$board.name}/ - {$board.desc}</a>
						</li>
					{/if}
				{/foreach} 
			</ul>
		{/foreach}
		<div class="clear"></div>
	</div>
</div>

<div class="box last-new">
	<div class="box-title">{$last_new.0.subject} - {date_format $last_new.0.timestamp "%d/%m/%Y @ %H:%M:%S"} - by {$last_new.0.poster}</div>
	<div class="box-content">
		<p>{$last_new.0.message}</p>
	</div>
</div>

<div class="box last-images">
	<div class="box-title">Ultimas Imagens</div>
	<div class="box-content">
		<ul>
			{foreach $last_images last_image}
				<li>
					<a href="{$last_image.board}/res/{$last_image.parentid}.html#{$last_image.id}">
						<img src="{$last_image.board}/thumb/{$last_image.file}s.{$last_image.file_type}" alt="" />
					</a>
				</li>
			{/foreach}
		</ul>
	</div>
</div>

<div class="box last-post">
	<div class="box-title">Ultimos Posts</div>
	<div class="box-content">
		<ul>
			{foreach $last_posts last_post}
				<li>
					{date_format $last_post.timestamp "%d/%m @ %H:%M"} - 
					<a href="{$last_post.board}/">/{$last_post.board}/</a> -
					<a href="{$last_post.board}/res/{$last_post.parentid}.html#{$last_post.id}">#{$last_post.id}</a> -
					{truncate strip_tags($last_post.message) 40}
				</li>	
			{/foreach}
		</ul>
	</div>
</div>

<div class="box popular-thread">
	<div class="box-title">Tópicos Populares</div>
	<div class="box-content">
		<ul>
			{foreach $popular_threads popular_thread}
				<li>
					{date_format $popular_thread.timestamp "%d/%m @ %H:%M"} - 
					<a href="{$popular_thread.board}/">/{$popular_thread.board}/</a> -
					<a href="{$popular_thread.board}/res/{$popular_thread.parentid}.html#{$popular_thread.id}">#{$popular_thread.id}</a> -
					{truncate strip_tags($popular_thread.message) 40}
				</li>	
			{/foreach}		
	</div>
</div>	

<div class="box popular-thread">
	<div class="box-title">Estatisticas</div>
	<div class="box-content">
		<ul>
			<li>Posts ativos: {$postcount}</li>
			<li>Imagens Ativas: {$imagecount.0.imagecount}</li>
			<li>Espaço em disco usado: {math "$imagecount.0.imagesize / 1000 / 1000" %.2f} MB</li>
	</div>
</div>

{include(file='front_footer.tpl')}
