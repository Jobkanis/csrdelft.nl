<?php

namespace CsrDelft\entity\security;

use Doctrine\ORM\Mapping as ORM;

/**
 * OneTimeToken.class.php
 *
 * @author P.W.G. Brussee <brussee@live.nl>
 *
 * One time token for two-step authentication.
 * @ORM\Entity(repositoryClass="CsrDelft\repository\security\OneTimeTokensRepository")
 * @ORM\Table("onetime_tokens")
 */
class OneTimeToken {

	/**
	 * Lidnummer
	 * Shared primary key
	 * Foreign key
	 * @var string
	 * @ORM\Column(type="uid")
	 * @ORM\Id()
	 */
	public $uid;
	/**
	 * Protected action url
	 * Shared primary key
	 * @var string
	 * @ORM\Column(type="stringkey")
	 * @ORM\Id()
	 */
	public $url;
	/**
	 * Token string
	 * @var string
	 * @ORM\Column(type="stringkey")
	 */
	public $token;
	/**
	 * Moment of expiration
	 * @var \DateTime
	 * @ORM\Column(type="datetime")
	 */
	public $expire;
	/**
	 * Is verfied?
	 * @var boolean
	 * @ORM\Column(type="boolean")
	 */
	public $verified;
}
