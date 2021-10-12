<?php

declare(strict_types=1);


namespace App\AdminModule\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Nette\Utils\Image;
use Nette\Utils\Strings;
use Nette\Utils\DateTime;


class PostsPresenter extends BaseAdminPresenter
{
    private $post;
    const UPLOAD_PATH = 'images/';

    public function actionDefault ($slug): void
    {
        $this->template->posts = $this->database->table('posts')
                                                   ->order('date DESC')
                                                   ->where('visible > 0')
                                                   ->fetchAll();
        $this->template->category = $this->database->table('category');
        $this->template->users = $this->database->table('users');
    }

    public function actionEdit(?int $id): void
    {
        if($id) {
            $this->post = $this->database->table('posts')->get($id);

            if (!$this->post) {
                $this->error('Podstránka nebyla nalezena.');
            }
        }
    }

    protected function createComponentEdit()
    {
        $form = new Form;

        $form->addText('id', 'ID')
            ->setHtmlAttribute('readonly', true);

        $form->addText('title', 'Nadpis článku')
            ->setRequired('Toto pole je vyžadováno.');

        $form->addTextArea('content', 'Obsah')
            ->setRequired('Toto pole je vyžadováno.');

        $form->addUpload('image', 'Titulní obrázek článku (1110×500px)')
            ->addRule($form::IMAGE, 'Obrázek musí být JPEG, PNG, GIF nebo WebP.');

        $categories = $this->database->table('category')->fetchAll();

        foreach ($categories as $category) {
            $categories[$category->id] = $category->name;
        }

        $form->addSelect('category', 'Kategorie', $categories)
            ->setRequired('Vyplňte prosím kategorii.');

        $form->onSuccess[] = [$this, 'onSuccess'];

        if($this->post) {
            $form->setDefaults($this->post->toArray());
        }

        return $form;
    }

    public function onSuccess(Form $form, ArrayHash $values): void
    {
        $postId = $values->id;
        $values->slug = Strings::webalize($values->title);
        $values->user = $this->getUser()->id;
        $values->date = new DateTime();
        $fileName = $values->slug;

        if ($values->image->hasFile()) {

            $fileExt = $values->image->getImageFileExtension();
            $imageName = Strings::webalize($fileName . '.' . $fileExt, '.');

            $image = $values->image; // nahraný obrázek uložíme do image
            $image->move(self::UPLOAD_PATH . $values->image->name); // soubor přesuneme do požadované složky

            $sub = Image::fromFile(self::UPLOAD_PATH . $values->image->name);
            $sub->resize(150, 70, Image::EXACT);
            $sub->save(self::UPLOAD_PATH . 'thumb_' . $imageName, 80); // JPEG, kvalita 80%

            $mainHeight = $this->settings = $this->database->table('settings')->get(12)->value;
            $main = Image::fromFile(self::UPLOAD_PATH . $values->image->name);
            $main->resize(1110, 500, Image::EXACT);
            $main->save(self::UPLOAD_PATH . '' . $imageName, 80); // JPEG, kvalita 80%

            Nette\Utils\FileSystem::delete(self::UPLOAD_PATH . $values->image->name);

            $values->image = $imageName;

        } else {
            unset($values->image);
        }

        if ($postId) {
            $userEntity = $this->database->table('posts')->get($postId);
            $userEntity->update($values);
        } else {
            $userEntity = $this->database->table('posts')->insert($values);
        }

        $this->flashMessage('Podstránka byla upravena.', 'success');
        $this->redirect('Posts:');
    }

    public function handlePublic(int $id): void
    {
        if (!($this->database->table('posts')->get($id))) {
            throw new \RuntimeException('Podstránka nenalezena.');
        }

        $userEntity = $this->database->table('posts')->get($id);
        $userEntity->update(['visible' => 2]);

        if($userEntity) {
            $this->flashMessage('Zveřejnění proběhlo v pořádku.');
            $this->redirect('this');
        }
    }

    public function handleUnpublic(int $id): void
    {
        if (!($this->database->table('posts')->get($id))) {
            throw new \RuntimeException('Podstránka nenalezena.');
        }

        $userEntity = $this->database->table('posts')->get($id);
        $userEntity->update(['visible' => 1]);

        if($userEntity) {
            $this->flashMessage('Zveřejnění proběhlo v pořádku.');
            $this->redirect('this');
        }
    }

    public function handleDelete(int $id): void
    {
        if (!($this->database->table('posts')->get($id))) {
            throw new \RuntimeException('Podstránka nenalezena.');
        }

        $userEntity = $this->database->table('posts')->get($id);
        $userEntity->update(['visible' => 0]);

        if($userEntity) {
            $this->flashMessage('Smazání proběhlo v pořádku.');
            $this->redirect('this');
        }
    }
}