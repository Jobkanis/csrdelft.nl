<?php

namespace CsrDelft\view\fiscaat\saldo;

use CsrDelft\model\entity\fiscaat\CiviSaldo;
use CsrDelft\view\formulier\getalvelden\IntField;
use CsrDelft\view\formulier\invoervelden\LidField;
use CsrDelft\view\formulier\invoervelden\TextField;
use CsrDelft\view\formulier\knoppen\FormDefaultKnoppen;
use CsrDelft\view\formulier\ModalForm;

/**
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @date 07/05/2017
 *
 * Maak het mogelijk om een lid te registreren, wordt uiteindelijk samengetrokken met het aanmaken van een lid.
 */
class LidRegistratieForm extends ModalForm {
	public function __construct(CiviSaldo $model) {
		parent::__construct($model, '/fiscaat/saldo/registreren/lid', false, true);

		$fields = [];
		$fields['naam'] = new TextField('naam', $model->naam, 'Bijnaam');
		$fields['uid'] = new LidField('uid', $model->uid, 'Lid');
		$fields[] = new IntField('saldo', $model->saldo, 'Initieel saldo');

		$this->addFields($fields);

		$this->formKnoppen = new FormDefaultKnoppen();
	}

	public function validate() {
		if (!parent::validate()) {
			return false;
		}

		$fields = $this->getFields();

		if (is_null($fields['naam']->getValue()) && is_null($fields['uid']->getValue())) {
			$this->error = 'Vul in ieder geval een uid of een naam in.';
			$this->css_classes[] = 'metFouten';

			return false;
		}

		return true;
	}
}
