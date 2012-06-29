<?php
require_once 'configuratie.include.php';
require_once 'mededelingen/mededeling.class.php';

define('MEDEDELINGEN_ROOT','actueel/mededelingen/');

if(!Mededeling::isModerator()){
	header('location: '.CSR_ROOT.'/actueel/mededelingen');
	setMelding('U heeft daar niets te zoeken.', -1);
	exit;
}

if(isset($_GET['mededelingId']) AND is_numeric($_GET['mededelingId']) AND $_GET['mededelingId']>0){
	try{
		$mededeling=new Mededeling((int)$_GET['mededelingId']);
	} catch (Exception $e) {
		header('location: '.CSR_ROOT.MEDEDELINGEN_ROOT);
		setMelding('Mededeling met id '.(int)$_GET['mededelingId'].' bestaat niet.', -1);
		exit;
	}
	header('location: '.CSR_ROOT.MEDEDELINGEN_ROOT.$mededeling->getId());
	$mededeling->keurGoed();
	setMelding('Mededeling is nu goedgekeurd.', 1);
}else{
	header('location: '.CSR_ROOT.MEDEDELINGEN_ROOT);
	setMelding('Geen mededelingId gezet.', -1);
}

?>
