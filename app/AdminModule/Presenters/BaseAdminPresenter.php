<?php


namespace App\AdminModule\Presenters;

use Nette;
use Nette\Application\UI\Presenter;


class BaseAdminPresenter extends Nette\Application\UI\Presenter
{
    /** @var Nette\Database\Explorer @inject */
    protected $database;
    protected $settings;

    public function __construct(Nette\Database\Explorer $database)
    {
        parent::__construct();
        $this->database = $database;
    }

    public function handleCacheDelete(): void
    {
        $cachePath = __DIR__ . "/../../../temp";

        $paths = [realpath($cachePath . '/cache')];

        foreach (Nette\Utils\Finder::find('*')->in($paths) as $k => $file) {
            Nette\Utils\FileSystem::delete($k);
        }


        $this->flashMessage('Cache byla smazána.');
        $this->redirect('this');
    }

    public function handleLogout(): void
    {
        $this->getUser()->logout(TRUE);
        $this->flashMessage('Odhlášení proběhlo v pořádku.');
        $this->redirect(":Admin:Home:");
    }

    protected function startup()
    {
        parent::startup();

        // nastavení webu
        $this->template->settings = $this->settings = $this->database->table('settings')->fetchAll();

        // kontrola přihlášení uživatele
        if (!$this->getUser()->isLoggedIn() and $this->getPresenter()->getName() !== 'Admin:Sign') {
            $this->redirect('Sign:in');
        }

        // kontrola, zda je uživatel admin
        if (!$this->getUser()->isInRole('admin') and $this->getUser()->isLoggedIn()) {

            $this->getUser()->logout(TRUE);
            $this->flashMessage('Proběhlo odhlášení, protože nemáte potřebná práva.');
            $this->redirect('Sign:in');
        }

        if ($this->getUser()->isLoggedIn()) {
            $this->template->u = $this->getUser()->getIdentity()->getData();
        }
    }
}