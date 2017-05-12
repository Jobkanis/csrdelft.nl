<?php
require_once 'model/entity/fiscaat/CiviSaldo.class.php';

/**
 * BeheerCiviSaldoView.class.php
 *
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @date 07/04/2017
 */
class BeheerCiviSaldoView extends DataTable {
	public function __construct() {
		parent::__construct('CiviSaldo', '/fiscaat/saldo', 'Saldobeheer');

		$this->addColumn('naam', 'saldo');
		$this->addColumn('lichting', 'saldo');
		$this->addColumn('saldo', null, null, 'prijs_render', 'saldo', 'num-fmt');
		$this->setOrder(array('saldo' => 'asc'));

		$this->searchColumn('naam');

		$this->addKnop(new DataTableKnop('== 0', $this->dataTableId, '/fiscaat/saldo/registreren', 'post', 'Registreren', 'Lid registreren', 'toevoegen'));
		$this->addKnop(new DataTableKnop('>= 1', $this->dataTableId, '/fiscaat/saldo/verwijderen', 'post', 'Verwijderen', 'Saldo van lid verwijderen', 'verwijderen', 'confirm'));
		$this->addKnop(new DataTableKnop('== 1', $this->dataTableId, '/fiscaat/saldo/inleggen', 'post', 'Inleggen', 'Saldo van lid ophogen', 'coins_add'));
	}

	public function getBreadcrumbs() {
		return '<a href="/" title="Startpagina"><span class="fa fa-home module-icon"></span></a> » <a href="/fiscaat"><span class="fa fa-eur module-icon"></span></a> » <span class="active">Saldo</span>';
	}

	public function getJavascript() {
		return /** @lang JavaScript */
			parent::getJavascript() . <<<JS
function prijs_render(data) {
	return "€" + (data/100).toFixed(2);
}
JS;
	}
}

/**
 * Class LidRegistratieForm
 *
 * Maak het mogelijk om een lid te registreren, wordt uiteindelijk samengetrokken met het aanmaken van een lid.
 */
class LidRegistratieForm extends ModalForm {
	public function __construct(CiviSaldo $model) {
		parent::__construct($model, '/fiscaat/saldo/registreren/lid', false, true);

		$fields[] = new LidField('uid', $model->uid, 'Lid');
		$fields[] = new IntField('saldo', $model->saldo, 'Initieel saldo');
		$fields['btn'] = new FormDefaultKnoppen();

		$this->addFields($fields);
	}
}

class InleggenForm extends ModalForm {
	public function __construct(Civisaldo $model) {
		parent::__construct($model, '/fiscaat/saldo/inleggen', "Inleggen: "  . ProfielModel::getNaam($model->uid, 'volledig'), true);

		$fields['saldo'] = new BedragField('saldo', $model->saldo, 'Huidig saldo');
		$fields['saldo']->readonly = true;
		$fields[] = new BedragField('inleg', 0, 'Inleg', '€', 0.01);
		$fields['btn'] = new FormDefaultKnoppen();

		$this->addFields($fields);
	}
}

class BeheerSaldoResponse extends DataTableResponse {
	/**
	 * @param CiviSaldo $entity
	 * @return string
	 */
	public function getJson($entity) {
		return parent::getJson(array(
			'UUID' => $entity->getUUID(),
			'id' => $entity->id,
			'uid' => $entity->uid,
			'naam' => ProfielModel::existsUid($entity->uid) ? ProfielModel::getNaam($entity->uid, 'volledig') : $entity->naam,
			'lichting' => substr($entity->uid, 0, 2),
			'saldo' => $entity->saldo,
			'laatst_veranderd' => $entity->laatst_veranderd,
			'deleted' => $entity->deleted
		));
	}
}