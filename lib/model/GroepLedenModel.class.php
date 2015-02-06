<?php

/**
 * GroepLedenModel.class.php
 * 
 * @author P.W.G. Brussee <brussee@live.nl>
 * 
 */
class GroepLedenModel extends CachedPersistenceModel {

	const orm = 'GroepLid';

	protected static $instance;
	/**
	 * Default ORDER BY
	 * @var string
	 */
	protected $default_order = 'lid_sinds ASC';
	/**
	 * Store leden array as a whole in memcache
	 * @var boolean
	 */
	protected $memcache_prefetch = true;

	public static function get(Groep $groep, $uid) {
		return static::instance()->retrieveByPrimaryKey(array($groep->id, $uid));
	}

	protected function __construct() {
		parent::__construct('groepen/');
	}

	public function nieuw(Groep $groep, $uid) {
		$class = static::orm;
		$lid = new $class();
		$lid->groep_id = $groep->id;
		$lid->uid = $uid;
		$lid->door_uid = LoginModel::getUid();
		$lid->lid_sinds = getDateTime();
		$lid->opmerking = null;
		return $lid;
	}

	/**
	 * Return leden van groep.
	 * 
	 * @param Groep $groep
	 * @return GroepLid[]
	 */
	public function getLedenVoorGroep(Groep $groep) {
		return $this->prefetch('groep_id = ?', array($groep->id));
	}

	/**
	 * Bereken statistieken van de groepleden.
	 * 
	 * @param Groep $groep
	 * @return array
	 */
	public function getStatistieken(Groep $groep) {
		$uids = Database::sqlSelect(array('DISTINCT uid'), $groep->getTableName());
		$count = count($uids);
		if ($count < 1) {
			return array();
		}
		$in = implode(', ', array_fill(0, $count, '?'));
		$stats['Verticale'] = Database::instance()->sqlSelect(array('naam', 'count(*)'), 'profielen LEFT JOIN verticalen ON profielen.verticale = verticalen.letter', 'uid IN (' . $in . ')', $uids, 'verticale', null)->fetchAll();
		$stats['Geslacht'] = Database::instance()->sqlSelect(array('geslacht', 'count(*)'), 'profielen', 'uid IN (' . $in . ')', $uids, 'geslacht', null)->fetchAll();
		$stats['Lichting'] = Database::instance()->sqlSelect(array('lidjaar', 'count(*)'), 'profielen', 'uid IN (' . $in . ')', $uids, 'lidjaar', null)->fetchAll();
		$stats['Tijd'] = array();
		foreach ($groep->getLeden() as $groeplid) {
			$time = strtotime($groeplid->lid_sinds) * 1000;
			if (isset($stats['Tijd'][$time])) {
				$stats['Tijd'][$time] += 1;
			} else {
				$stats['Tijd'][$time] = 1;
			}
		}
		$stats['Totaal'] = $count;
		if (property_exists($groep, 'aanmeld_limiet')) {
			if ($groep->aanmeld_limiet === null) {
				$stats['Totaal'] .= ' (geen limiet)';
			} else {
				$stats['Totaal'] .= ' van ' . $groep->aanmeld_limiet;
			}
		}
		return $stats;
	}

}

class OnderverLedenModel extends GroepLedenModel {

	const orm = 'OnderverLid';

	protected static $instance;

}

class BewonersModel extends GroepLedenModel {

	const orm = 'Bewoner';

	protected static $instance;

}

class LichtingLedenModel extends GroepLedenModel {

	const orm = 'LichtingsLid';

	protected static $instance;

	/**
	 * Return leden van lichting.
	 * 
	 * @param int $lidjaar
	 * @param mixed $status
	 * @return LichtingLid[]
	 */
	public function getLeden($lidjaar, $status = null) {
		if (is_array($status)) {
			$count = count($status);
			array_unshift($status, $lidjaar);
			return $this->prefetch('lidjaar = ? AND lidstatus IN (' . implode(', ', array_fill(0, $count, '?')) . ')', $status);
		} elseif ($status !== null) {
			return $this->prefetch('lidjaar = ? AND lidstatus = ?', array($lidjaar, $status));
		} else {
			return $this->prefetch('lidjaar = ?', array($lidjaar));
		}
	}

}

class VerticaleLedenModel extends GroepLedenModel {

	const orm = 'VerticaleLid';

	protected static $instance;

	/**
	 * Return leders van groep.
	 * 
	 * @param Verticale $verticale
	 * @return VerticaleLid[]
	 */
	public function getLeiders(Verticale $verticale) {
		return $this->prefetch('groep_id = ? AND leider = TRUE', array($verticale->id));
	}

}

class KringLedenModel extends GroepLedenModel {

	const orm = 'KringLid';

	protected static $instance;

}

class CommissieLedenModel extends GroepLedenModel {

	const orm = 'CommissieLid';

	protected static $instance;

}

class BestuursLedenModel extends GroepLedenModel {

	const orm = 'BestuursLid';

	protected static $instance;

}

class KetzerDeelnemersModel extends GroepLedenModel {

	const orm = 'KetzerDeelnemer';

	protected static $instance;

}

class WerkgroepDeelnemersModel extends KetzerDeelnemersModel {

	const orm = 'WerkgroepDeelnemer';

	protected static $instance;

}

class ActiviteitDeelnemersModel extends KetzerDeelnemersModel {

	const orm = 'ActiviteitDeelnemer';

	protected static $instance;

}
