@if(is_null($uid))
<td id="voorkeur-row-{{$crid}}" class="voorkeur-uitgeschakeld">
	<a href="/corvee/voorkeuren/inschakelen/{{$crid}}" class="btn post voorkeur-uitgeschakeld"><input type="checkbox" /> Nee</a>
</td>
@else
<td id="voorkeur-row-{{$crid}}" class="voorkeur-ingeschakeld">
	<a href="/corvee/voorkeuren/uitschakelen/{{$crid}}" class="btn post voorkeur-ingeschakeld"><input type="checkbox" checked="checked" /> Ja</a>
</td>
@endif
