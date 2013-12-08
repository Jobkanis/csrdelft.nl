<?php
namespace Taken\MLT;

require_once 'taken/model/AbonnementenModel.class.php';
require_once 'taken/model/MaaltijdRepetitiesModel.class.php';
require_once 'taken/view/MijnAbonnementenView.class.php';

/**
 * MijnAbonnementenController.class.php	| 	P.W.G. Brussee (brussee@live.nl)
 * 
 */
class MijnAbonnementenController extends \ACLController {

	public function __construct($query) {
		parent::__construct($query);
		if (!parent::isPOSTed()) {
			$this->acl = array(
				'mijn' => 'P_MAAL_IK'
			);
		}
		else {
			$this->acl = array(
				'inschakelen' => 'P_MAAL_IK',
				'uitschakelen' => 'P_MAAL_IK'
			);
		}
		$this->action = 'mijn';
		if ($this->hasParam(1)) {
			$this->action = $this->getParam(1);
		}
		$mrid = null;
		if ($this->hasParam(2)) {
			$mrid = intval($this->getParam(2));
		}
		$this->performAction($mrid);
	}
	
	public function action_mijn() {
		$abonnementen = AbonnementenModel::getAbonnementenVoorLid(\LoginLid::instance()->getUid(), true, true);
		$this->content = new MijnAbonnementenView($abonnementen);
		$this->content = new \csrdelft($this->getContent());
		$this->content->addStylesheet('taken.css');
		$this->content->addScript('taken.js');
	}
	
	public function action_inschakelen($mrid) {
		$abo_aantal = AbonnementenModel::inschakelenAbonnement($mrid, \LoginLid::instance()->getUid());
		$this->content = new MijnAbonnementenView($abo_aantal[0]);
		if ($abo_aantal[1] > 0) {
			$this->content->setMelding('Automatisch aangemeld voor '. $abo_aantal[1] .' maaltijd'. ($abo_aantal[1] === 1 ? '' : 'en'), 2);
		}
	}
	
	public function action_uitschakelen($mrid) {
		$abo_aantal = AbonnementenModel::uitschakelenAbonnement($mrid, \LoginLid::instance()->getUid());
		$this->content = new MijnAbonnementenView($mrid);
		if ($abo_aantal[1] > 0) {
			$this->content->setMelding('Automatisch afgemeld voor '. $abo_aantal[1] .' maaltijd'. ($abo_aantal[1] === 1 ? '' : 'en'), 2);
		}
	}
}

?>