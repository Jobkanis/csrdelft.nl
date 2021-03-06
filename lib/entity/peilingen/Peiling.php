<?php

namespace CsrDelft\entity\peilingen;

use CsrDelft\common\datatable\DataTableEntry;
use CsrDelft\entity\profiel\Profiel;
use CsrDelft\model\security\LoginModel;
use CsrDelft\view\datatable\DataTableColumn;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 *
 * @ORM\Entity(repositoryClass="CsrDelft\repository\peilingen\PeilingenRepository")
 * @ORM\Table("peiling")
 */
class Peiling implements DataTableEntry {
	/**
	 * @var integer
	 * @ORM\Column(type="integer")
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @Serializer\Groups({"datatable", "vue"})
	 */
	public $id;
	/**
	 * @var string
	 * @ORM\Column(type="string")
	 * @Serializer\Groups({"datatable", "vue"})
	 */
	public $titel;
	/**
	 * @var string
	 * @ORM\Column(type="text")
	 * @Serializer\Groups({"datatable", "vue"})
	 */
	public $beschrijving;
	/**
	 * @var string
	 * @ORM\Column(type="uid", nullable=true)
	 * @Serializer\Groups("vue")
	 */
	public $eigenaar;
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean")
	 * @Serializer\Groups({"datatable", "vue"})
	 */
	public $mag_bewerken;
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean")
	 * @Serializer\Groups({"datatable", "vue"})
	 */
	public $resultaat_zichtbaar;
	/**
	 * @var integer
	 * @ORM\Column(type="integer")
	 * @Serializer\Groups({"datatable", "vue"})
	 */
	public $aantal_voorstellen;
	/**
	 * @var integer
	 * @ORM\Column(type="integer")
	 * @Serializer\Groups({"datatable", "vue"})
	 */
	public $aantal_stemmen;
	/**
	 * @var string|null
	 * @ORM\Column(type="string", nullable=true)
	 * @Serializer\Groups({"datatable", "vue"})
	 */
	public $rechten_stemmen;
	/**
	 * @var string|null
	 * @ORM\Column(type="string", nullable=true)
	 * @Serializer\Groups({"datatable", "vue"})
	 */
	public $rechten_mod;
	/**
	 * @var \DateTime|null
	 * @ORM\Column(type="datetime", nullable=true)
	 * @Serializer\Groups({"datatable", "vue"})
	 */
	public $sluitingsdatum;

	/**
	 * @var PeilingOptie[]
	 * @ORM\OneToMany(targetEntity="PeilingOptie", mappedBy="peiling")
	 * @Serializer\Groups({"datatable", "vue"})
	 */
	public $opties;

	/**
	 * @var PeilingStem[]
	 * @ORM\OneToMany(targetEntity="PeilingStem", mappedBy="peiling")
	 * @ORM\JoinColumn(name="id", referencedColumnName="peiling_id")
	 */
	public $stemmen;

	/**
	 * @var Profiel
	 * @ORM\ManyToOne(targetEntity="CsrDelft\entity\profiel\Profiel")
	 * @ORM\JoinColumn(name="eigenaar", referencedColumnName="uid", nullable=true)
	 */
	public $eigenaarProfiel;

	/**
	 * @return int
	 * @Serializer\Groups({"datatable", "vue"})
	 */
	public function getAantalGestemd() {
		if (!$this->opties) {
			return 0;
		}

		$stemmen = 0;
		foreach ($this->opties as $optie) {
			$stemmen += $optie->stemmen;
		}
		return $stemmen;
	}

	/**
	 * @Serializer\Groups("vue")
	 */
	public function getHeeftGestemd() {
		if (!$this->stemmen) {
			return false;
		}

		foreach ($this->stemmen as $stem) {
			if ($stem->uid == LoginModel::getUid()) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return bool
	 * @Serializer\Groups({"datatable", "vue"})
	 */
	public function getMagBewerken() {
		//Elk BASFCie-lid heeft voorlopig peilingbeheerrechten.
		return LoginModel::mag(P_ADMIN . ',bestuur,commissie:BASFCie');
	}

	/**
	 * @return bool
	 * @Serializer\Groups({"datatable", "vue"})
	 */
	public function getIsMod() {
		return LoginModel::mag(P_PEILING_MOD) || LoginModel::getUid() == $this->eigenaar;
	}

	/**
	 * @return bool
	 * @Serializer\Groups({"datatable", "vue"})
	 */
	public function getMagStemmen() {
		return LoginModel::mag(P_PEILING_VOTE) && ($this->eigenaar == LoginModel::getUid() || empty(trim($this->rechten_stemmen)) || LoginModel::mag($this->rechten_stemmen))
			&& $this->isPeilingOpen();
	}

	/**
	 * @return DataTableColumn|string
	 * @Serializer\Groups("datatable")
	 * @Serializer\SerializedName("eigenaar")
	 */
	public function getDataTableEigenaar() {
		return $this->eigenaarProfiel ? $this->eigenaarProfiel->getDataTableColumn() : '';
	}

	/**
	 * @return string
	 * @Serializer\Groups("datatable")
	 * @Serializer\SerializedName("detailSource")
	 */
	public function getDetailSource() {
		return '/peilingen/opties/' . $this->id;
	}

	/**
	 * @return bool
	 */
	private function isPeilingOpen() {
		return $this->sluitingsdatum == NULL || time() < $this->sluitingsdatum->getTimestamp();
	}

	/**
	 * @return bool
	 */
	public function magBekijken() {
		return LoginModel::mag(P_LOGGED_IN);
	}

}


