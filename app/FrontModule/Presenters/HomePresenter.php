<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\Menu\MenuControl;
use Nette;


final class HomePresenter extends BaseFrontPresenter
{

    private $settings;

    public function actionDefault(int $page = 1): void
    {
        $this->template->settings = $this->settings = $this->database->table('settings')->fetchAll();
//        $this->template->slides = $this->database->table('slides')->order('order ASC');

        if($this->settings[25]->value === '0') {
            $this->forward('Posts:');
        } else {
            $page = $this->database->table('pages')->get($this->settings[25]->value);
            $this->forward('Page:', $page->slug);
        }
    }






}
