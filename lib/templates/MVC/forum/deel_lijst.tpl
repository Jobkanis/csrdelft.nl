<tr class="forumdeel kleur{cycle values="0,1"}">
	<td class="titel hoverIntent">
		<a href="/forum/deel/{$deel->forum_id}">{$deel->titel}</a>
		<br />{$deel->omschrijving}
		{if LoginLid::mag('P_FORUM_ADMIN')}
			<div class="hoverIntentContent" style="float: right;">
				<a href="/forum/hertellen/{$deel->forum_id}" class="knop post ReloadPage" title="Hertellen">{icon get="calculator"}</a>
			</div>
		{/if}
	</td>
	<td class="reacties">{$deel->aantal_draden}</td>
	<td class="reacties">{$deel->aantal_posts}</td>
	<td class="reactiemoment">
		{if $deel->laatst_gewijzigd}
			{if LoginLid::instelling('forum_datumWeergave') === 'relatief'}
				{$deel->laatst_gewijzigd|reldate}
			{else}
				{$deel->laatst_gewijzigd}
			{/if}
			<br /><a href="/forum/reactie/{$deel->laatste_post_id}#{$deel->laatste_post_id}">bericht</a> 
			door {$deel->laatste_lid_id|csrnaam:'user'}
		{/if}
	</td>
</tr>