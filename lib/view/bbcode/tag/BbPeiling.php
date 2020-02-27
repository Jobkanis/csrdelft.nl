<?php

namespace CsrDelft\view\bbcode\tag;

use CsrDelft\bb\BbException;
use CsrDelft\bb\BbTag;
use CsrDelft\common\ContainerFacade;
use CsrDelft\entity\peilingen\Peiling;
use CsrDelft\model\peilingen\PeilingenLogic;
use CsrDelft\model\security\LoginModel;
use CsrDelft\repository\peilingen\PeilingenRepository;
use CsrDelft\view\bbcode\BbHelper;

/**
 * Peiling
 *
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since 27/03/2019
 * @example [peiling=2]
 * @example [peiling]2[/peiling]
 */
class BbPeiling extends BbTag {

	/**
	 * @var Peiling
	 */
	private $peiling;

	public static function getTagName() {
		return 'peiling';
	}
	public function isAllowed()
	{
		return $this->peiling->magBekijken();
	}

	public function renderLight() {
		$url = '#/peiling/' . urlencode($this->content);
		return BbHelper::lightLinkBlock('peiling', $url, $this->peiling->titel, $this->peiling->beschrijving);
	}

	public function render() {
		$optionsAsJson = ContainerFacade::getContainer()->get(PeilingenLogic::class)->getOptionsAsJson($this->peiling->id, LoginModel::getUid());
		$serializer = ContainerFacade::getContainer()->get('serializer');
		$peilingSerialized = $serializer->serialize($this->peiling, 'json', ['groups' => 'vue']);
		return view('peilingen.peiling', [
			'peiling' => $peilingSerialized,
		])->getHtml();
	}

	/**
	 * @param string|null $peiling_id
	 * @return Peiling
	 * @throws BbException
	 */
	private function getPeiling($peiling_id): Peiling {
		$peilingenRepository = ContainerFacade::getContainer()->get(PeilingenRepository::class);
		$peiling = $peilingenRepository->getPeilingById($peiling_id);
		if ($peiling === false) {
			throw new BbException('[peiling] Er bestaat geen peiling met (id:' . (int)$peiling_id . ')');
		}

		return $peiling;
	}

	/**
	 * @param array $arguments
	 * @return mixed
	 * @throws BbException
	 */
	public function parse($arguments = [])
	{
		$this->readMainArgument($arguments);
		$this->peiling = $this->getPeiling($this->content);
	}
}
