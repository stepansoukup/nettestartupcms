<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;


class PagePresenter extends BaseFrontPresenter
{

	public function renderDefault($slug): void
	{
		$page = $this->database->table('pages')->where('slug = ? AND deleted = 0', $slug)->fetch();

		if (!$page) {
			$this->error('Stránka nebyla nalezena');
		}

		$this->template->page = $page;
	}
}