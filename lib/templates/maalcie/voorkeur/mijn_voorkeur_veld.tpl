{*
	mijn_voorkeur_veld.tpl	|	P.W.G. Brussee (brussee@live.nl)
*}
<td id="voorkeur-row-{$crid}" {if isset($uid)}class="voorkeur-ingeschakeld">
	<a href="{Instellingen::get('taken', 'url')}/uitschakelen/{$crid}" class="knop rounded post voorkeur-ingeschakeld"><input type="checkbox" checked="checked" /> Ja</a>
{else}class="voorkeur-uitgeschakeld">
	<a href="{Instellingen::get('taken', 'url')}/inschakelen/{$crid}" class="knop rounded post voorkeur-uitgeschakeld"><input type="checkbox" /> Nee</a>	
{/if}
</td>