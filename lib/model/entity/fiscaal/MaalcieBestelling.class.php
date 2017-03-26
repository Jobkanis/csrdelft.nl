<?php

use CsrDelft\Orm\Entity\PersistentEntity;
use CsrDelft\Orm\Entity\T;

require_once 'model/fiscaal/MaalcieBestellingInhoudModel.class.php';
require_once 'model/entity/fiscaal/MaalcieBestelling.class.php';

class MaalcieBestelling extends PersistentEntity {
	public $id;
	public $uid;
	public $totaal = 0;
	public $deleted;

	/**
	 * @var MaalcieBestellingInhoud[]
	 */
	public $inhoud = array();

	public function add(MaalcieBestellingInhoud $maaltijd) {
		$this->inhoud[] = $maaltijd;
		$maaltijd->bestellingid = $this->id;

		$this->totaal += MaalcieBestellingInhoudModel::instance()->getPrijs($maaltijd);
	}

	protected static $table_name = 'maalciebestelling';
	protected static $persistent_attributes = array(
		'id' => array(T::Integer, false, 'auto_increment'),
		'uid' => array(T::UID),
		'totaal' => array(T::Integer),
		'deleted' => array(T::Boolean)
	);
	protected static $primary_key = array('id');
}
