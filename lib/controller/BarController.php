<?php

namespace CsrDelft\controller;

use CsrDelft\model\BarModel;
use Symfony\Component\HttpFoundation\Request;

class BarController extends AbstractController {
	protected $model;

	public function __construct(BarModel $barModel) {
		$this->model = $barModel;
	}

	public function main(Request $request) {
		// Laad Vue app.
		return view('bar', [
			"CsrfToken" => $this->model->getCsrfToken()
		]);
	}
}
