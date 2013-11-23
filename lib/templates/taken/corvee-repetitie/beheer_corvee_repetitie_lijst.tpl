{*
	beheer_corvee_repetitie_lijst.tpl	|	P.W.G. Brussee (brussee@live.nl)
*}
<tr id="repetitie-row-{$repetitie->getCorveeRepetitieId()}">
	<td>
		<a href="{$module}/bewerk/{$repetitie->getCorveeRepetitieId()}" title="Corveerepetitie wijzigen" class="knop post popup">{icon get="pencil"}</a>
		<a href="/actueel/taken/functies/beheer/{$repetitie->getFunctieId()}" title="Wijzig onderliggende functie" class="knop get">{icon get="cog_edit"}</a>
{if !isset($maaltijdrepetitie) and $repetitie->getMaaltijdRepetitieId()}
		<a href="{$module}/maaltijd/{$repetitie->getMaaltijdRepetitieId()}" title="Corveebeheer maaltijdrepetitie" class="knop get">{icon get="calendar_link"}</a>
{/if}
	</td>
	<td>{$repetitie->getCorveeFunctie()->getNaam()}</td>
	<td>{$repetitie->getDagVanDeWeekText()}</td>
	<td>{$repetitie->getPeriodeInDagenText()}</td>
	<td>{if $repetitie->getIsVoorkeurbaar()}{icon get="tick" title="Voorkeurbaar"}{/if}</td>
	<td>{$repetitie->getStandaardAantal()}</td>
	<td class="col-del"><a href="{$module}/verwijder/{$repetitie->getCorveeRepetitieId()}" title="Corveerepetitie definitief verwijderen" class="knop post confirm">{icon get="cross"}</a></td>
</tr>