<?php

declare(strict_types=1);


namespace App\AdminModule\Presenters;


use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;


class MenuPresenter extends BaseAdminPresenter
{
    private $menu;

    public function actionDefault ($slug): void
    {
        $this->template->menu = $this->database->table('menu')
                                                   ->order('order ASC')
                                                   ->fetchAll();
    }

    public function actionEdit(?int $id): void
    {
        if($id) {
            $this->menu = $this->database->table('menu')->get($id);

            if (!$this->menu) {
                $this->error('Kategorie nebyla nalezena.');
            }
        }
    }

    protected function createComponentEdit()
    {
        $form = new Form;

        $form->addText('id', 'ID')
            ->setHtmlAttribute('readonly', true);

        $form->addText('title', 'Položka menu')
            ->setRequired('Toto pole je vyžadováno.');

        $form->addText('link', 'Odkaz')
            ->setRequired('Toto pole je vyžadováno.');

        $parents = $this->database->table('menu');

        $parentList[0] = 'Nemá nadřazenou položku.';
        foreach ($parents as $parent) {
            $parentList[$parent->id] = $parent->title;
        }

        $form->addSelect('parent_id', 'Nadřazená položka menu', $parentList)
            ->setDefaultValue(0);

        $form->onSuccess[] = [$this, 'onSuccess'];

        if($this->menu) {
            $form->setDefaults($this->menu->toArray());
        }

        return $form;
    }

    public function onSuccess(Form $form, ArrayHash $values): void
    {
        $postId = $values->id;

        if ($postId) {
            $userEntity = $this->database->table('menu')->get($postId);
            $userEntity->update($values);
        } else {
            $userEntity = $this->database->table('menu')->insert($values);
        }

        $this->flashMessage('Podstránka byla upravena.', 'success');
        $this->redirect('Menu:');
    }

    public function handleSortable() {

        $i = 1;

        foreach ($_POST['order'] as $sliderId) {
            $sliderEntity = $this->database->table('menu')->get($sliderId);
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
        if (!($menu = $this->menu = $this->database->table('menu')->get($id))) {
            throw new \RuntimeException('Položka menu nenalezena.');
        }
        $menu = $this->database->table('menu');

        foreach($menu as $row) {
            if($row->parent_id === $id) {
                $row->update(['parent_id' => 0]);
            }
        }

        if($this->database->table('menu')->where('id', $id)->delete($id)) {
            $this->flashMessage('Položka menu byla smazána.');
            $this->redirect('Menu:');
        }
    }
}