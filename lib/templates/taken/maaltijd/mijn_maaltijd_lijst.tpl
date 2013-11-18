{*
	mijn_maaltijd_lijst.tpl	|	P.W.G. Brussee (brussee@live.nl)
*}
<tr id="maaltijd-row-{$maaltijd->getMaaltijdId()}"{if !isset($aanmelding) and $maaltijd->getIsGesloten()} class="taak-grijs"{/if}>
	<td>{$maaltijd->getDatum()|date_format:"%a %e %b"} {$maaltijd->getTijd()|date_format:"%H:%M"}</td>
	<td>{$maaltijd->getTitel()}
		<span style="float: right;">&nbsp;
{assign var=prijs value=$maaltijd->getPrijs()|string_format:"%.2f"}
{if isset($aanmelding) and $aanmelding->getSaldoStatus() < 0}
		{icon get="money_delete" title="U staat rood bij de MaalCie!&#013;Maaltijdprijs: &euro; "|cat:$prijs}
{elseif isset($aanmelding) and $aanmelding->getSaldoStatus() < 2}
		{icon get="money_delete" title="Uw MaalCie saldo is te laag!&#013;Maaltijdprijs: &euro; "|cat:$prijs}
{else}
		{icon get="money_euro" title="Maaltijdprijs: &euro; "|cat:$prijs}
{/if}
		</span>
	</td>
	<td>
{if $loginlid->hasPermission('P_MAAL_MOD') or opConfide()}
		<a href="/actueel/taken/maaltijdenbeheer/lijst/{$maaltijd->getMaaltijdId()}" title="Toon maaltijdlijst" class="knop" style="margin-right:10px;">{icon get="table"}</a>
{/if}
		{$maaltijd->getAantalAanmeldingen()} ({$maaltijd->getAanmeldLimiet()})
{if $maaltijd->getAantalAanmeldingen() >= $maaltijd->getAanmeldLimiet()}
		<div style="float: right;">
			{icon get="stop" title="Maaltijd is vol"}
		</div>
{/if}
	</td>
{if isset($aanmelding)}
	{if $maaltijd->getIsGesloten()}
	<td class="maaltijd-aangemeld">
		Ja
		{if $aanmelding->getDoorAbonnement()}(abo){/if}
		<span style="float: right;">&nbsp;
			{assign var=date value=$maaltijd->getLaatstGesloten()|date_format:"%H:%M"}
			{icon get="lock" title="Maaltijd is gesloten om "|cat:$date}
		</span>
	{else}
	<td class="maaltijd-aangemeld">
		<a href="{$module}/afmelden/{$maaltijd->getMaaltijdId()}" class="knop post maaltijd-aangemeld"><input type="checkbox" checked="checked" /> Ja</a>
		{if $aanmelding->getDoorAbonnement()}(abo){/if}
	{/if}
	</td>
	<td>
	{if $maaltijd->getIsGesloten()}
		{$aanmelding->getAantalGasten()}
	{else}
		<div class="inline-edit" onclick="toggle_taken_hiddenform(this);">{$aanmelding->getAantalGasten()}</div>
		<form method="post" action="{$module}/gasten/{$maaltijd->getMaaltijdId()}" class="Formulier taken-hidden-form taken-subform">
			<input type="text" name="aantal_gasten" value="{$aanmelding->getAantalGasten()}" maxlength="4" size="4" />
			<a onclick="$(this).parent().submit();" title="Wijzigingen opslaan" class="knop">{icon get="accept"}</a>
			<a onclick="toggle_taken_hiddenform($(this).parent());" title="Annuleren" class="knop">{icon get="delete"}</a>
		</form>
	{/if}
	</td>
	<td>
	{if $maaltijd->getIsGesloten()}
		{$aanmelding->getGastenOpmerking()|truncate:20:"...":true}&nbsp;
	{else}
		<div class="inline-edit" onclick="toggle_taken_hiddenform(this);" title="{$aanmelding->getGastenOpmerking()}">{$aanmelding->getGastenOpmerking()|truncate:20:"...":true}&nbsp;</div>
		<form method="post" action="{$module}/opmerking/{$maaltijd->getMaaltijdId()}" class="Formulier taken-hidden-form taken-subform">
			<input type="text" name="gasten_opmerking" value="{$aanmelding->getGastenOpmerking()}" maxlength="255" size="20" />
			<a onclick="$(this).parent().submit();" title="Wijzigingen opslaan" class="knop">{icon get="accept"}</a>
			<a onclick="toggle_taken_hiddenform($(this).parent());" title="Annuleren" class="knop">{icon get="delete"}</a>
		</form>
	{/if}
	</td>
{else}
	{if $maaltijd->getIsGesloten() or $maaltijd->getAantalAanmeldingen() >= $maaltijd->getAanmeldLimiet()}
	<td class="maaltijd-afgemeld">
		Nee
		{if $maaltijd->getIsGesloten()}
		<span style="float: right;">&nbsp;
			{assign var=date value=$maaltijd->getLaatstGesloten()|date_format:"%H:%M"}
			{icon get="lock" title="Maaltijd is gesloten om "|cat:$date}
		</span>
		{/if}
	{else}
	<td class="maaltijd-afgemeld">
		<a href="{$module}/aanmelden/{$maaltijd->getMaaltijdId()}" class="knop post maaltijd-afgemeld"><input type="checkbox" /> Nee</a>
	{/if}
	</td>
	<td>-</td>
	<td></td>
{/if}
</tr>