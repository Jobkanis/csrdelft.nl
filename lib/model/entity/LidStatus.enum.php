<?php

namespace CsrDelft\model\entity;

use CsrDelft\Orm\Entity\PersistentEnum;

/**
 * LidStatus.enum.php
 *
 * @author C.S.R. Delft <pubcie@csrdelft.nl>
 * @author P.W.G. Brussee <brussee@live.nl>
 */
abstract class LidStatus extends PersistentEnum {

	/**
	 * Status voor h.t. leden.
	 */
	const Noviet = 'S_NOVIET';
	const Lid = 'S_LID';
	const Gastlid = 'S_GASTLID';

	/**
	 * Status voor o.t. leden.
	 */
	const Oudlid = 'S_OUDLID';
	const Erelid = 'S_ERELID';

	/**
	 * Status voor niet-leden.
	 */
	const Overleden = 'S_OVERLEDEN';
	const Exlid = 'S_EXLID';
	const Nobody = 'S_NOBODY';
	const Commissie = 'S_CIE';
	const Kringel = 'S_KRINGEL';

	/**
	 * @var string[]
	 */
	protected static $lidlike = [
		self::Noviet => self::Noviet,
		self::Lid => self::Lid,
		self::Gastlid => self::Gastlid,
	];

	/**
	 * @var string[]
	 */
	protected static $oudlidlike = [
		self::Oudlid => self::Oudlid,
		self::Erelid => self::Erelid,
	];

	/**
	 * @var string[]
	 */
	protected static $supportedChoices = [
		self::Noviet => self::Noviet,
		self::Lid => self::Lid,
		self::Gastlid => self::Gastlid,
		self::Oudlid => self::Oudlid,
		self::Erelid => self::Erelid,
		self::Overleden => self::Overleden,
		self::Exlid => self::Exlid,
		self::Nobody => self::Nobody,
		self::Commissie => self::Commissie,
		self::Kringel => self::Kringel,
	];

	/**
	 * @var string[]
	 */
	protected static $mapChoiceToDescription = [
		self::Noviet => 'Noviet',
		self::Lid => 'Lid',
		self::Gastlid => 'Gastlid',
		self::Oudlid => 'Oudlid',
		self::Erelid => 'Erelid',
		self::Overleden => 'Overleden',
		self::Exlid => 'Ex-lid',
		self::Nobody => 'Nobody',
		self::Commissie => 'Commissie (LDAP)',
		self::Kringel => 'Kringel',
	];

	/**
	 * @var string[]
	 */
	protected static $mapChoiceToChar = [
		self::Noviet => '',
		self::Lid => '',
		self::Gastlid => '',
		self::Commissie => '∈',
		self::Exlid => '∉',
		self::Nobody => '∉',
		self::Kringel => '~',
		self::Oudlid => '•',
		self::Erelid => '☀',
		self::Overleden => '✝',
	];

	/**
	 * @return string[]
	 */
	public static function getLidLike() {
		return array_values(static::$lidlike);
	}

	/**
	 * @return string[]
	 */
	public static function getOudlidLike() {
		return array_values(static::$oudlidlike);
	}

	/**
	 * @param string $option
	 *
	 * @return bool
	 */
	public static function isLidLike($option) {
		return isset(static::$lidlike[$option]);
	}

	/**
	 * @param string $option
	 *
	 * @return bool
	 */
	public static function isOudlidLike($option) {
		return isset(static::$oudlidlike[$option]);
	}
}
