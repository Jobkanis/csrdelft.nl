<?php

namespace CsrDelft\entity\commissievoorkeuren;

use CsrDelft\entity\profiel\Profiel;
use CsrDelft\model\security\AccessModel;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class VoorkeurVoorkeur
 * @package CsrDelft\model\entity\commissievoorkeuren
 * @ORM\Entity(repositoryClass="CsrDelft\repository\commissievoorkeuren\CommissieVoorkeurRepository")
 * @ORM\Table("voorkeurVoorkeur")
 */
class VoorkeurVoorkeur {
	/**
	 * @var string
	 * @ORM\Column(type="uid")
	 * @ORM\Id()
	 */
	public $uid;

	/**
	 * @var int
	 * @ORM\Column(type="integer")
	 * @ORM\Id()
	 */
	public $cid;

	/**
	 * @var int
	 * @ORM\Column(type="integer")
	 */
	public $voorkeur;

	/**
	 * @var DateTime
	 * @ORM\Column(type="datetime")
	 */
	public $timestamp;

	/**
	 * @ORM\PreUpdate
	 */
	public function setTimestamp()
	{
		$this->timestamp = new \DateTime();
	}

	/**
	 * @var Profiel
	 * @ORM\ManyToOne(targetEntity="CsrDelft\entity\profiel\Profiel")
	 * @ORM\JoinColumn(name="uid", referencedColumnName="uid")
	 */
	public $profiel;

	/**
	 * @var VoorkeurCommissie
	 * @ORM\ManyToOne(targetEntity="VoorkeurCommissie")
	 * @ORM\JoinColumn(name="cid")
	 */
	public $commissie;

	/**
	 * cid is onderdeel van primary key en moet dus gezet zijn bij saven.
	 *
	 * @param VoorkeurCommissie $commissie
	 */
	public function setCommissie(VoorkeurCommissie $commissie) {
		$this->commissie = $commissie;
		$this->cid = $commissie->id;
	}

	/**
	 * uid is onderdeel van primary key en moet dus gezet zijn bij saven.
	 *
	 * @param Profiel $profiel
	 */
	public function setProfiel(Profiel $profiel) {
		$this->profiel = $profiel;
		$this->uid = $profiel->uid;
	}

	public function heeftGedaan() {
		return AccessModel::mag($this->profiel->getAccount(), 'commissie:' . $this->commissie->naam . ',commissie:' . $this->commissie->naam . ':ot');
	}
}
