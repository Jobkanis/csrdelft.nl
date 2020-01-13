<?php

namespace CsrDelft\model;

use CsrDelft\model\fiscaat\CiviBestellingModel;
use CsrDelft\model\fiscaat\CiviSaldoModel;
use CsrDelft\Orm\DependencyManager;

class BarModel extends DependencyManager {
	private $csrfToken;
	private $beheer;

	private $civiSaldoModel;
	private $civiBestellingModel;

	public function __construct(CiviSaldoModel $civiSaldoModel, CiviBestellingModel $civiBestellingModel) {
		$this->civiSaldoModel = $civiSaldoModel;
		$this->civiBestellingModel = $civiBestellingModel;
	}

	public function getCsrfToken() {
		if ($this->csrfToken === null) {
			if ($this->isBeheer()) {
				$this->csrfToken = md5('Barsysteem CSRF-token C.S.R. Delft' . $_COOKIE['barsysteembeheer']);
			} else {
				$this->csrfToken = md5('Barsysteem CSRF-token C.S.R. Delft' . $_COOKIE['barsysteem']);
			}
		}
		return $this->csrfToken;
	}

	function isLoggedIn() {
		return isset($_COOKIE['barsysteem']) && password_verify($_COOKIE['barsysteem'], '$2y$10$ekSjztz/3ZFEe47nglQGAuTrXTeYxl8BCN3l8mdct9hr/Qhj1pNEK');
	}

	function isBeheer() {
		if (!$this->beheer) //TODO Migrate to login + user account based auth
			$this->beheer = isset($_COOKIE['barsysteembeheer']) && password_verify($_COOKIE['barsysteembeheer'], '$2y$10$Foeh2Ke62JUWNbJuJEgLO.oNvC/t7pYD8LBrD4.EEpKrnccVGS/C6');

		return $this->beheer;
	}
}
