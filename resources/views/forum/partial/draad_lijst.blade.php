<div class="alternate-row draad-titel">
	@if($draad->wacht_goedkeuring)
		<small class="niet-dik">[ter goedkeuring...]</small>
	@endif
	@if($draad->belangrijk)
		@icon($draad->belangrijk, null, 'dit onderwerp is door het bestuur aangemerkt als belangrijk')
	@elseif($draad->plakkerig)
		@icon('note', null, 'Dit onderwerp is plakkerig, het blijft bovenaan')
	@elseif($draad->gesloten)
		@icon('lock', null, 'Dit onderwerp is gesloten, u kunt niet meer reageren')
	@endif

	@inject('lidInstellingenModel', 'CsrDelft\model\LidInstellingenModel')
	@if($lidInstellingenModel::get('forum', 'open_draad_op_pagina') == 'ongelezen')
		@php($urlHash = "#ongelezen")
	@elseif($lidInstellingenModel::get('forum', 'open_draad_op_pagina') == 'laatste')
		@php($urlHash = "#reageren")
	@else
		@php($urlHash = "")
	@endif

	@php($ongelezenWeergave = $lidInstellingenModel::get('forum', 'ongelezenWeergave'))

	<a id="{{$draad->draad_id}}"
		 href="/forum/onderwerp/{{$draad->draad_id}}{{$urlHash}}"
		 @auth @if($draad->isOngelezen()) class="{{$ongelezenWeergave}}"@endif @endauth>{{$draad->titel}}</a>
	@auth
		@if($draad->getAantalOngelezenPosts() > 0)
			<span class="badge">{{$draad->getAantalOngelezenPosts()}}</span>
		@endif
	@endauth
	@if(!isset($deel->forum_id))
		<span class="lichtgrijs">[<a href="/forum/deel/{{$draad->getForumDeel()->forum_id}}"
																 class="lichtgrijs">{{$draad->getForumDeel()->titel}}</a>]</span>
	@endif
</div>
<div class="alternate-row draad-laatst-gewijzigd">
	@if(\CsrDelft\model\LidInstellingenModel::get('forum', 'datumWeergave') === 'relatief')
		{!! reldate($draad->laatst_gewijzigd) !!}
	@else
		{{$draad->laatst_gewijzigd}}
	@endif
</div>
<div class="alternate-row draad-laatste-post">
	{!! CsrDelft\model\ProfielModel::getLink($draad->laatste_wijziging_uid, 'user') !!}
</div>
