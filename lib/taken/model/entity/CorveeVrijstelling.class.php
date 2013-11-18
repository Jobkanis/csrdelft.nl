<?php
namespace Taken\CRV;
/**
 * CorveeVrijstelling.class.php	| 	P.W.G. Brussee (brussee@live.nl)
 * 
 * 
 * Een crv_vrijstelling instantie bevat het volgende per lid:
 *  - begindatum van de periode waarvoor de vrijstelling geldt
 *  - einddatum van de periode waarvoor de vrijstelling geldt
 *  - percentage van de corveepunten die in een jaar gehaald dienen te worden
 * 
 * Wordt gebruikt bij de indeling van corveetaken om bijv. leden die
 * in het buitenland zitten niet in te delen gedurende die periode.
 * 
 */
class CorveeVrijstelling {

	# primary key
	private $lid_id; # foreign key lid.uid
	
	private $begin_datum; # date
	private $eind_datum; # date
	private $percentage; # int 3
	
	public function __construct($uid=null, $begin='', $eind='', $percentage=100) {
		$this->lid_id = $uid;
		if ($begin === '') {
			$begin = date('Y-m-d');
		}
		$this->setBeginDatum($begin);
		if ($eind === '') {
			$eind = date('Y-m-d');
		}
		$this->setEindDatum($eind);
		$this->setPercentage($percentage);
	}
	
	public function getLidId() {
		return $this->lid_id;
	}
	public function getLid() {
		return \LidCache::getLid($this->getLidId());
	}
	
	public function getBeginDatum() {
		return $this->begin_datum;
	}
	public function getEindDatum() {
		return $this->eind_datum;
	}
	public function getPercentage() {
		return (int) $this->percentage;
	}
	
	public function setBeginDatum($datum) {
		if (!is_string($datum)) {
			throw new \Exception('Geen string: begin datum');
		}
		$this->begin_datum = $datum;
	}
	public function setEindDatum($datum) {
		if (!is_string($datum)) {
			throw new \Exception('Geen string: eind datum');
		}
		$this->eind_datum = $datum;
	}
	public function setPercentage($int) {
		if (!is_int($int) || $int < 0 || $int > 100) {
			throw new \Exception('Geen integer: percentage');
		}
		$this->percentage = $int;
	}
}

?>