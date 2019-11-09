<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Model\HomepageModel;


final class HomepagePresenter extends Nette\Application\UI\Presenter
{
        private $hpm;

        public function __construct(HomepageModel $hpm)
	{
                $this->hpm = $hpm;
	}


	public function renderDefault(int $page = 1): void
	{
                $this->template->page = $page;
		$this->template->posts = $this->hpm->getPosts($page);
        }
}
