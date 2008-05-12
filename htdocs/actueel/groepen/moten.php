<?php

# instellingen & rommeltjes
require_once('include.config.php');

#moten toevoegen
if($lid->hasPermission('P_LEDEN_MOD') AND isset($_POST['moot'], $_POST['naam']) AND is_array($_POST['naam'])){
	$iKringGetal=$lid->getMaxKringen($_POST['moot'])+1;
	foreach($_POST['naam'] as $sKringLid){
		//echo 'uid: '.$sKringLid.' moot: '.$_POST['moot'].' kring: '.$iKringGetal;
		$lid->addUid2kring($sKringLid, $iKringGetal, $_POST['moot']);
	}
	header('location: '.CSR_ROOT.'actueel/groepen/moten.php');
}

if ($lid->hasPermission('P_LEDEN_READ')) {
	# Het middenstuk
	require_once('class.motencontent.php');
	$midden = new MotenContent($lid);
} else {
	# geen rechten
	require_once 'class.paginacontent.php';
	$pagina=new Pagina('geentoegang');
	$midden = new PaginaContent($pagina);
}	
	
## zijkolom in elkaar jetzen
	$zijkolom=new kolom();

# pagina weergeven
$pagina=new csrdelft($midden, $lid, $db);
$pagina->setZijkolom($zijkolom);
$pagina->addStylesheet('groepen.css');
$pagina->view();


?>
