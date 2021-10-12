<?php

declare(strict_types=1);


namespace App\AdminModule\Presenters;


use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;


class CategoryPresenter extends BaseAdminPresenter
{
    private $category;

    public function actionDefault ($slug): void
    {
        $this->template->category = $this->database->table('category')
                                                   ->order('order ASC')
                                                   ->where('deleted = 0')
                                                   ->fetchAll();
    }

    public function actionEdit(?int $id): void
    {
        if($id) {
            $this->category = $this->database->table('category')->get($id);

            if (!$this->category) {
                $this->error('Kategorie nebyla nalezena.');
            }
        }
    }

    protected function createComponentEdit()
    {
        $form = new Form;

        $form->addText('id', 'ID')
            ->setHtmlAttribute('readonly', true);

        $form->addText('name', 'Jméno kategorie')
            ->setRequired('Toto pole je vyžadováno.');

        $form->onSuccess[] = [$this, 'onSuccess'];

        if($this->category) {
            $form->setDefaults($this->category->toArray());
        }

        return $form;
    }

    public function onSuccess(Form $form, ArrayHash $values): void
    {
        $postId = $values->id;

        $values->slug = Strings::webalize($values->name);

        if ($postId) {
            $userEntity = $this->database->table('category')->get($postId);
            $userEntity->update($values);
        } else {
            $userEntity = $this->database->table('category')->insert($values);
        }

        $this->flashMessage('Podstránka byla upravena.', 'success');
        $this->redirect('Category:');
    }

    public function handleSortable() {

        $i = 1;

        foreach ($_POST['order'] as $sliderId) {
            $sliderEntity = $this->database->table('category')->get($sliderId);
            $sliderEntity->update(['order' => $i]);

            $i++;
        }

        if ($this->isAjax()) {
            $this->flashMessage('Upraveno');
            $this->redrawControl('flashMessages');
        }

    }

    public function handleDelete(int $id): void
    {
        if (!($category = $this->category = $this->database->table('category')->get($id))) {
            throw new \RuntimeException('Kategorie nenalezena.');
        }

        if($this->database->table('category')->where('id', $id)->delete($id)) {
            $this->flashMessage('Kategorie byla smazána.');
            $this->redirect('Category:');
        }
    }
}