<?php
/*
 * saldografiek.php	| 	Jan Pieter Waagmeester (jieter@jpwaag.com)
 * 
 * 
 */

require_once('include.config.php');
require_once('chart-0.8/chart.php');
require_once('class.saldi.php');


if(	isset($_GET['uid']) AND 
	($lid->isValidUid($_GET['uid']) OR $_GET['uid']=='0000') AND 
	($lid->hasPermission('P_ADMIN') OR $lid->getUid()==$_GET['uid'])){
	
	
	$maalcie=new Saldi($_GET['uid'], 'maalcie');
	$soccie=new Saldi($_GET['uid'], 'soccie');
	
	$chart = new chart(500, 200);
	if($_GET['uid']=='0000'){
		$chart->set_title('Totaal');
	}else{
		$chart->set_title('Saldo voor '.$lid->getCivitasName($_GET['uid']));
	}

	$chart->set_x_ticks($soccie->getKeys(), 'date');
	$chart->plot($soccie->getValues(), false, 'blue');
	
	
	$chart->add_legend('SocCie', 'blue');
	$chart->add_legend('MaalCie', 'red');

	$chart->set_margins(60, 10, 20, 23);
	$chart->set_labels(false, 'Saldo [euro]');
	$chart->stroke();
}
?>
