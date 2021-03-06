<?php

namespace CsrDelft\view\bbcode\tag;

use CsrDelft\bb\BbTag;
use CsrDelft\model\entity\groepen\AbstractGroep;
use CsrDelft\model\entity\groepen\Lichting;
use CsrDelft\model\groepen\LichtingenModel;
use CsrDelft\model\groepen\VerticalenModel;
use CsrDelft\model\security\LoginModel;
use CsrDelft\view\bbcode\BbHelper;
use CsrDelft\view\ledenmemory\LedenMemoryScoreTable;

/**
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since 27/03/2019
 */
class BbLedenmemoryscores extends BbTag {

	/**
	 * @var AbstractGroep|Lichting|false|null
	 */
	private $groep;
	private $titel;
	/**
	 * @var VerticalenModel
	 */
	private $verticalenModel;
	/**
	 * @var LichtingenModel
	 */
	private $lichtingenModel;

	public function __construct(VerticalenModel $verticalenModel, LichtingenModel $lichtingenModel) {
		$this->verticalenModel = $verticalenModel;
		$this->lichtingenModel = $lichtingenModel;
	}

	public static function getTagName() {
		return 'ledenmemoryscores';
	}

	public function isAllowed() {
		LoginModel::mag(P_LOGGED_IN);
	}

	public function renderLight() {
		return BbHelper::lightLinkBlock('ledenmemoryscores', '/forum/onderwerp/8017', 'Ledenmemory Scores', $this->titel);
	}

	/**
	 * @param $arguments
	 */
	function parse($arguments = []) {
		$groep = null;
		$titel = null;
		if (isset($arguments['verticale'])) {
			$v = filter_var($arguments['verticale'], FILTER_SANITIZE_STRING);
			if (strlen($v) > 1) {
				$verticale = $this->verticalenModel->find('naam LIKE ?', array('%' . $v . '%'), null, null, 1)->fetch();
			} else {
				$verticale = $this->verticalenModel->get($v);
			}
			if ($verticale) {
				$titel = ' Verticale ' . $verticale->naam;
				$groep = $verticale;
			}
		} elseif (isset($arguments['lichting'])) {
			$l = (int)filter_var($arguments['lichting'], FILTER_SANITIZE_NUMBER_INT);
			if ($l < 1950) {
				$l = LichtingenModel::getJongsteLidjaar();
			}
			$lichting = $this->lichtingenModel->get($l);
			if ($lichting) {
				$titel = ' Lichting ' . $lichting->lidjaar;
				$groep = $lichting;
			}
		}
		$this->groep = $groep;
		$this->titel = $titel;
	}

	public function render() {
		$table = new LedenMemoryScoreTable($this->groep, $this->titel);
		return $table->getHtml();
	}
}
