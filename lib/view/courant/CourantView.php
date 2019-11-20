<?php

namespace CsrDelft\view\courant;

use CsrDelft\common\Ini;
use CsrDelft\model\entity\courant\Courant;
use CsrDelft\model\entity\courant\CourantCategorie;
use Symfony\Component\HttpFoundation\Response;

/**
 * CourantView.class.php
 *
 * @author C.S.R. Delft <pubcie@csrdelft.nl>
 * @property Courant $model
 *
 */
class CourantView extends Response {

	private $model;
	private $berichten;
	private $instellingen;

	/**
	 * CourantView constructor.
	 * @param Courant $courant
	 * @param $berichten
	 */
	public function __construct(Courant $courant, $berichten) {
		parent::__construct();
		$this->model = $courant;
		setlocale(LC_ALL, 'nl_NL@euro');
		$this->instellingen = Ini::lees(Ini::CSRMAIL);
		$this->berichten = $berichten;
	}

	public function getTitel() {
		return 'C.S.R.-courant van ' . $this->getVerzendMoment();
	}

	public function getVerzendMoment() {
		return strftime('%d %B %Y', strtotime($this->model->verzendMoment));
	}

	public function getHtml($headers = false) {
		return view('courant.mail', [
			'headers' => $headers,
			'instellingen' => $this->instellingen,
			'courant' => $this->model,
			'berichten' => $this->berichten,
			'catNames' => CourantCategorie::getSelectOptions(),
		])->getHtml();
	}

	public function getContent() {
		return $this->getHtml(false);
	}

	public function view() {
		echo $this->getHtml();
	}
}
