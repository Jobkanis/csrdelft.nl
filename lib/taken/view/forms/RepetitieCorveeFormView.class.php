<?php
namespace Taken\CRV;

require_once 'formulier.class.php';

/**
 * RepetitieCorveeFormView.class.php	| 	P.W.G. Brussee (brussee@live.nl)
 *
 * Formulier voor nieuw periodiek corvee.
 * 
 */
class RepetitieCorveeFormView extends \SimpleHtml {

	private $_form;
	
	public function __construct(CorveeRepetitie $repetitie, $beginDatum=null, $eindDatum=null, $mid=null) {
		
		$formFields[] = new \HTMLComment('<p>Aanmaken op '. strftime('%A', $repetitie->getDagVanDeWeekTimestamp()) .'en voor '. $repetitie->getPeriodeInDagenText() .' in de periode:</p>');
		$formFields['begin'] = new \DatumField('begindatum', $beginDatum, 'Vanaf', date('Y')+1, date('Y'));
		$formFields['eind'] = new \DatumField('einddatum', $eindDatum, 'Tot en met', date('Y')+1, date('Y'));
		$formFields[] = new \HiddenField('maaltijd_id', $mid);
		
		$this->_form = new \Formulier('taken-repetitie-aanmaken-form', '/actueel/taken/corveebeheer/aanmaken/'. $repetitie->getCorveeRepetitieId(), $formFields);
	}
	
	public function getTitel() {
		return 'Periodiek corvee aanmaken';
	}
	
	public function view() {
		$smarty = new \Smarty_csr();
		$smarty->assign('melding', $this->getMelding());
		$smarty->assign('kop', $this->getTitel());
		$this->_form->cssClass .= ' popup';
		$smarty->assign('form', $this->_form);
		$smarty->display('taken/popup_form.tpl');
	}
	
	public function validate() {
		$fields = $this->_form->getFields();
		if (strtotime($fields['eind']->getValue()) < strtotime($fields['begin']->getValue())) {
			$fields['eind']->error = 'Moet na begindatum liggen';
			return false;
		}
		return $this->_form->valid(null);
	}
	
	public function getValues() {
		return $this->_form->getValues(); // escapes HTML
	}
}

?>