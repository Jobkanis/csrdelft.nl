<?php

namespace CsrDelft\controller\api;

use CsrDelft\common\ContainerFacade;
use CsrDelft\entity\forum\ForumDraad;
use CsrDelft\entity\forum\ForumPost;
use CsrDelft\model\security\LoginModel;
use CsrDelft\repository\forum\ForumDradenGelezenRepository;
use CsrDelft\repository\forum\ForumDradenRepository;
use CsrDelft\repository\forum\ForumPostsRepository;
use CsrDelft\repository\ProfielRepository;
use CsrDelft\view\bbcode\CsrBB;
use Exception;
use Jacwright\RestServer\RestException;

class ApiForumController {
	private $forumDradenRepository;
	private $forumPostsModel;
	private $forumDradenGelezenModel;

	public function __construct() {
		$container = ContainerFacade::getContainer();

		$this->forumDradenGelezenModel = $container->get(ForumDradenGelezenRepository::class);
		$this->forumPostsModel = $container->get(ForumPostsRepository::class);
		$this->forumDradenRepository = $container->get(ForumDradenRepository::class);
	}

	/**
	 * @return boolean
	 */
	public function authorize() {
		return ApiAuthController::isAuthorized() && LoginModel::mag(P_OUDLEDEN_READ);
	}

	/**
	 * @url GET /recent
	 * @param int offset
	 * @param int limit
	 * @return ForumDraad[]
	 */
	public function getRecent() {
		$offset = filter_input(INPUT_GET, 'offset', FILTER_VALIDATE_INT) ?: 0;
		$limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT) ?: 10;

		$draden = $this->forumDradenRepository->getRecenteForumDraden($limit, null, false, $offset, true);

		foreach ($draden as $draad) {
			$draad->ongelezen = $draad->getAantalOngelezenPosts();
			$draad->laatste_post = $this->forumPostsModel->get($draad->laatste_post_id);
			$draad->laatste_wijziging_naam = ProfielRepository::getNaam($draad->laatste_wijziging_uid, 'civitas');
		}

		return array('data' => array_values($draden));
	}

	/**
	 * @url GET /onderwerp/$id
	 * @param int offset
	 * @param int limit
	 * @return ForumPost[]
	 */
	public function getOnderwerp($id) {
		$offset = filter_input(INPUT_GET, 'offset', FILTER_VALIDATE_INT) ?: 0;
		$limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT) ?: 10;

		try {
			$draad = $this->forumDradenRepository->get((int)$id);
		} catch (Exception $e) {
			throw new RestException(404);
		}

		if (!$draad->magLezen()) {
			throw new RestException(403);
		}

		$this->forumDradenGelezenModel->setWanneerGelezenDoorLid($draad, time());

		$posts = $this->forumPostsModel->prefetch('draad_id = ? AND wacht_goedkeuring = FALSE AND verwijderd = FALSE', array($id), null, 'datum_tijd DESC', $limit, $offset);

		// Most recent first
		$posts = array_reverse($posts);

		foreach ($posts as $post) {
			$post->uid_naam = ProfielRepository::getNaam($post->uid, 'civitas');
			$post->tekst = CsrBB::parseLight($post->tekst);
		}

		return array('data' => $posts);
	}

}
