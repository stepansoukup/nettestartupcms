<?php


namespace App\FrontModule\Presenters;

use App\Model\Menu\MenuControl;
use Nette;


class BaseFrontPresenter extends Nette\Application\UI\Presenter
{

    /** @var Nette\Database\Explorer @inject */
    protected $database;

    private $settings;

    public function __construct(Nette\Database\Explorer $database)
    {
        parent::__construct();
        $this->database = $database;
    }

    public function startup()
    {
        parent::startup();
        $this->template->settings = $this->settings = $this->database->table('settings')->fetchAll();
        $this->template->slides = $this->database->table('slides')->order('order ASC');
    }


    protected function createComponentMenu(): MenuControl
    {
        $menu = new MenuControl;
        $menu->setType('front');
        $menu->setData($this->database->table('menu')->fetchPairs('id'));

        return $menu;
    }

}