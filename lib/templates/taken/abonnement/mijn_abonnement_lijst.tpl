{*
	mijn_abonnement_lijst.tpl	|	P.W.G. Brussee (brussee@live.nl)
*}
<tr>
	{include file='taken/abonnement/mijn_abonnement_veld.tpl' uid=$abonnement->getLidId() mrid=$abonnement->getMaaltijdRepetitieId()}
	<td>{$abonnement->getMaaltijdRepetitie()->getStandaardTitel()}</td>
	<td>{$abonnement->getMaaltijdRepetitie()->getDagVanDeWeekTimestamp()|date_format:"%A"}</td>
	<td>{$abonnement->getMaaltijdRepetitie()->getPeriodeInDagenText()}</td>
</tr>