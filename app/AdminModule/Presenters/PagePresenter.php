<?php

declare(strict_types=1);


namespace App\AdminModule\Presenters;


use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;


class PagePresenter extends BaseAdminPresenter
{
    private $page;

    public function actionDefault ($slug): void
    {
        $this->template->pageList = $this->database->table('pages')
                                                   ->order('id DESC')
                                                   ->where('deleted = 0')
                                                   ->fetchAll();
    }

    public function actionEdit(?int $id): void
    {
        if($id) {
            $this->page = $this->database->table('pages')->get($id);

            if (!$this->page) {
                $this->error('Podstránka nebyla nalezena.');
            }
        }
    }

    protected function createComponentEdit()
    {
        $form = new Form;

        $form->addText('id', 'ID')
            ->setHtmlAttribute('readonly', true);

        $form->addText('title', 'Titulek')
            ->setRequired('Toto pole je vyžadováno.');

        $form->addText('slug', 'URL');

        $form->addTextArea('content', 'Obsah')
             ->setRequired('Toto pole je vyžadováno.');

        $form->onSuccess[] = [$this, 'onSuccess'];

        if($this->page) {
            $form->setDefaults($this->page->toArray());
        }

        return $form;
    }

    public function onSuccess(Form $form, ArrayHash $values): void
    {
        $postId = $values->id;

        $values->slug = Strings::webalize(($values->slug) ?: $values->title);

        if ($postId) {
            $userEntity = $this->database->table('pages')->get($postId);
            $userEntity->update($values);
        } else {
            $userEntity = $this->database->table('pages')->insert($values);
        }

        $this->flashMessage('Podstránka byla upravena.', 'success');
        $this->redirect('Page:');
    }

    public function handleDelete(int $id): void
    {
        if (!($this->database->table('pages')->get($id))) {
            throw new \RuntimeException('Podstránka nenalezena.');
        }

        $userEntity = $this->database->table('pages')->get($id);
        $userEntity->update(['deleted' => 1]);

        if($userEntity) {
            $this->flashMessage('Smazání proběhlo v pořádku.');
            $this->redirect('this');
        }
    }
}