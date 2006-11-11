<?php

#
# C.S.R. Delft
#
# -------------------------------------------------------------------
# class.maaltijdvoorkeurcontent.php
# -------------------------------------------------------------------
#
# Bekijken en wijzigen van voorkeuren voor maaltijdinschrijving 
# en abonnementen
#
# -------------------------------------------------------------------
# Historie:
# 20-01-2006 Hans van Kranenburg
# . gemaakt
#

require_once ('class.maaltrack.php');

class MaaltijdVoorkeurContent extends SimpleHTML {

	### private ###

	# de objecten die data leveren
	var $_lid;
	var $_maaltrack;

	### public ###

	function MaaltijdVoorkeurContent (&$lid, &$maaltrack) {
		$this->_lid =& $lid;
		$this->_maaltrack =& $maaltrack;
	}
	function getTitel(){ return 'Maaltijdketzer - Voorkeuren'; }
	function viewWaarBenik(){ echo '<a href="/maaltijden/">Maaltijden</a> &raquo; Voorkeuren'; }
	function view(){
		//de html template in elkaar draaien en weergeven
		$profiel=new Smarty_csr();
		$profiel->caching=false;
		
		//Dingen ophalen voor....
		//...de abonnementen
		$aMaal['abo']['abos']=$this->_maaltrack->getAbo();
		$aMaal['abo']['nietAbos']=$geenabo = $this->_maaltrack->getNotAboSoort();
		
		//...de eetwens
		$aMaal['eetwens']=$this->_lid->getEetwens();
		//arrays toewijzen en weergeven
		$profiel->assign('maal', $aMaal);
		$profiel->display('maaltijdvoorkeuren.tpl');
	}
}

?>
