<?php
namespace Taken\CRV;
/**
 * CorveeKwalificatie.class.php	| 	P.W.G. Brussee (brussee@live.nl)
 * 
 * 
 * Een crv_kwalificatie instantie geeft aan dat een lid gekwalificeerd is voor een functie en sinds wanneer.
 * Dit is benodigd voor sommige CorveeFuncties zoals kwalikok.
 * 
 * 
 * Zie ook CorveeFunctie.class.php
 * 
 */
class CorveeKwalificatie {

	# shared primary key
	private $lid_id; # foreign key lid.uid
	private $functie_id; # foreign key crv_functie.id
	
	private $wanneer_toegewezen; #datetime
	
	public function __construct($uid='', $fid=0, $wanneer='') {
		$this->lid_id = $uid;
		$this->functie_id = (int) $fid;
		$this->setWanneerToegewezen($wanneer);
	}
	
	public function getLidId() {
		return $this->lid_id;
	}
	/**
	 * Laad het Lid object behorende bij deze kwalificatie.
	 * @return Lid if exists, false otherwise
	 */
	public function getLid() {
		return \LidCache::getLid($this->getLidId());
	}
	public function getFunctieId() {
		return (int) $this->functie_id;
	}
	
	public function getWanneerToegewezen() {
		return $this->wanneer_toegewezen;
	}
	
	public function setWanneerToegewezen($datumtijd) {
		if (!is_string($datumtijd)) {
			throw new \Exception('Geen string: wanneer toegewezen');
		}
		$this->wanneer_toegewezen = $datumtijd;
	}
}

?>