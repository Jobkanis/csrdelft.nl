@extends('maaltijden.base')

@section('titel', 'Beheer corveefuncties en kwalificaties')

@section('content')
	@parent
	<p>
		Op deze pagina kunt u corveefuncties aanmaken, wijzigen en verwijderen.
		Onderstaande tabel toont alle functies in het systeem.
		Ook kunt u aangeven of er een kwalificatie benodigd is en een kwalificatie toewijzen of intrekken.
	</p>
	<p>
		N.B. Voordat een corveefunctie verwijderd kan worden moeten eerst alle bijbehorende corveetaken en alle bijbehorende
		corveerepetities definitief zijn verwijderd.
	</p>
	<div class="float-right">
		<a href="/corvee/functies/toevoegen" class="btn post popup">
			@icon("add") Nieuwe functie
		</a>
	</div>
	<table id="maalcie-tabel" class="maalcie-tabel">
		<thead>
		<tr>
			<th>Wijzig</th>
			<th title="Afkorting">Afk</th>
			<th>Naam</th>
			<th>Standaard<br/>punten</th>
			<th title="Email bericht">@icon("email")</th>
			<th>Gekwalificeerden</th>
			<th title="Mag maaltijden sluiten">@icon("lock_add")</th>
			<th title="Definitief verwijderen" class="text-center">@icon("cross")</th>
		</tr>
		</thead>
		<tbody>
		@foreach($functies as $functie)
			@include('maaltijden.functie.beheer_functie_lijst', ['functie' => $functie])
		@endforeach
		</tbody>
	</table>
@endsection
