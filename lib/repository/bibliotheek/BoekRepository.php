<?php

namespace CsrDelft\repository\bibliotheek;

use CsrDelft\common\CsrGebruikerException;
use CsrDelft\entity\bibliotheek\Boek;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Boek|null find($id, $lockMode = null, $lockVersion = null)
 * @method Boek|null findOneBy(array $criteria, array $orderBy = null)
 * @method Boek[]    findAll()
 * @method Boek[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoekRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Boek::class);
	}

	public function existsTitel($value) {
		return count($this->findBy(['titel' => $value])) > 0;
	}

	/**
	 * @param string $zoekveld
	 * @param string $zoekterm
	 * @return Boek[]
	 * @throws CsrGebruikerException
	 */
	public function autocompleteProperty(string $zoekveld, string $zoekterm) {
		$allowedFields = ['titel', 'auteur', 'taal'];
		if (!in_array($zoekveld, $allowedFields)) {
			throw new CsrGebruikerException("Autocomplete niet toegestaan voor dit veld");
		}
		$qb = $this->createQueryBuilder('b');
		$qb->where($qb->expr()->like('b.' . $zoekveld, '%' . $zoekterm . '%'));
		return $qb->getQuery()->getArrayResult();
	}

	/**
	 * @param string $zoekterm
	 * @return Boek[]
	 * @throws CsrGebruikerException
	 */
	public function autocompleteBoek(string $zoekterm) {
		$qb = $this->createQueryBuilder('boek');
		$qb->where($qb->expr()->like('boek.titel', ':zoekterm'));
		$qb->setParameters(['zoekterm' => sql_contains($zoekterm)]);
		return $qb->getQuery()->getResult();
	}
}
