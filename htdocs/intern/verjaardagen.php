<?php

# instellingen & rommeltjes
require_once('include.config.php');


require_once('class.kolom.php');

if ($lid->hasPermission('P_LEDEN_READ')) {
	# Het middenstuk
	require_once('class.verjaardagcontent.php');
	$midden = new VerjaardagContent('alleverjaardagen');
} else {
	# geen rechten
	$midden = new Includer('', 'geentoegang.html');
}	
## zijkolom in elkaar jetzen
	$zijkolom=new kolom();

# pagina weergeven
$pagina=new csrdelft($midden);
if(!isset($_GET['print'])){ $pagina->setZijkolom($zijkolom); }
$pagina->view();


?>
