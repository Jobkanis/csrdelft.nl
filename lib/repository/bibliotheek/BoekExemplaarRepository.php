<?php

namespace CsrDelft\repository\bibliotheek;

use CsrDelft\entity\bibliotheek\Boek;
use CsrDelft\entity\bibliotheek\BoekExemplaar;
use CsrDelft\entity\profiel\Profiel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BoekExemplaar|null find($id, $lockMode = null, $lockVersion = null)
 * @method BoekExemplaar|null findOneBy(array $criteria, array $orderBy = null)
 * @method BoekExemplaar[]    findAll()
 * @method BoekExemplaar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoekExemplaarRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, BoekExemplaar::class);
	}

	/**
	 * @param $id
	 * @return BoekExemplaar|null
	 */
	public function get($id) {
		return $this->find($id);
	}

	public function getExemplaren(Boek $boek) {
		return $this->find("boek_id = ?", [$boek->id]);
	}

	/**
	 * @param Profiel $profiel
	 * @return BoekExemplaar[]
	 */
	public function getGeleend(Profiel $profiel) {
		return $this->findBy(['uitgeleend_uid' => $profiel->uid]);
	}

	/**
	 * @param $uid
	 * @return BoekExemplaar[]
	 */
	public function getEigendom($uid) {
		return $this->findBy(['eigenaar_uid' => $uid]);
	}

	public function leen(BoekExemplaar $exemplaar, string $uid) {
		if (!$exemplaar->kanLenen($uid)) {
			return false;
		} else {
			$exemplaar->status = 'uitgeleend';
			$exemplaar->uitgeleend_uid = $uid;
			$this->getEntityManager()->persist($exemplaar);
			$this->getEntityManager()->flush();
			return true;
		}
	}

	public function addExemplaar(Boek $boek, string $uid) {
		$exemplaar = new BoekExemplaar();
		$exemplaar->boek = $boek;
		$exemplaar->eigenaar_uid = $uid;
		$exemplaar->toegevoegd = date_create();
		$exemplaar->uitleendatum = null;
		$exemplaar->opmerking = '';
		$exemplaar->leningen = 0;
		$this->getEntityManager()->persist($exemplaar);
		$this->getEntityManager()->flush();
	}

	public function terugGegeven(BoekExemplaar $exemplaar) {
		if ($exemplaar->isUitgeleend()) {
			$exemplaar->status = 'teruggegeven';
			$this->getEntityManager()->persist($exemplaar);
			$this->getEntityManager()->flush();
			return true;
		} else {
			return false;
		}
	}

	public function terugOntvangen(BoekExemplaar $exemplaar) {
		if ($exemplaar->isUitgeleend() || $exemplaar->isTeruggegeven()) {
			$exemplaar->status = 'beschikbaar';
			$this->getEntityManager()->persist($exemplaar);
			$this->getEntityManager()->flush();
			return true;
		} else {
			return false;
		}
	}

	public function setVermist(BoekExemplaar $exemplaar) {
		if ($exemplaar->isBeschikbaar()) {
			$exemplaar->status = 'vermist';
			$this->getEntityManager()->persist($exemplaar);
			$this->getEntityManager()->flush();
			return true;
		} else {
			return false;
		}
	}

	public function setGevonden(BoekExemplaar $exemplaar) {
		if ($exemplaar->isVermist()) {
			$exemplaar->status = 'beschikbaar';
			$this->getEntityManager()->persist($exemplaar);
			$this->getEntityManager()->flush();
			return true;
		} else {
			return false;
		}
	}
}
