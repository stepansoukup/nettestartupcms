<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Database\Connection;
use Nette;
use Nette\Utils\ArrayHash;
use Nette\Utils\Image;
use Nette\Utils\Random;
use Nette\Utils\Strings;

final class SliderPresenter extends BaseAdminPresenter
{
    private $slide = null;

    const UPLOAD_PATH = 'img/slider/';

    public function renderDefault(): void
    {
        $this->template->slides = $this->database->table('slides')->order('order ASC');
    }

    public function actionEdit(?int $id): void
    {
        if($id) {
            $this->slide = $this->database->table('slides')->get($id);
            $this->template->default = $this->slide;

            if (!$this->slide) {
                $this->error('Slide nebyl nalezen.');
            }
        }
    }

    protected function createComponentEdit()
    {
        $form = new Form;

        $form->addText('id', 'id')
        ->setHtmlAttribute('readonly', true);

        $form->addText('url', 'Odkaz URL');

        $form->addUpload('img', 'Slide (1920×900)')
            ->addRule($form::IMAGE, 'Slide musí být JPEG, PNG, GIF nebo WebP.');

        $form->onSuccess[] = [$this, 'onSuccess'];

        if($this->slide) {
            $form->setDefaults($this->slide->toArray());
        }

        return $form;
    }

    public function onSuccess(Form $form, ArrayHash $values): void
    {
        $postId = $values->id;
        $fileName = Random::generate(10);

        // Kontrola, jestli byl přidán obrázek
            if ($values->img->hasFile()) {

	            $fileExt = $values->img->getImageFileExtension();
	            $imgName = Strings::webalize($fileName . '.' . $fileExt, '.');

                $image = $values->img; // nahraný obrázek uložíme do image
                $image->move(self::UPLOAD_PATH . $values->img->name); // soubor přesuneme do požadované složky

                $sub = Image::fromFile(self::UPLOAD_PATH . $values->img->name);
                $sub->resize(150, 70, Image::EXACT);
                $sub->save(self::UPLOAD_PATH . 'thumb_' . $imgName, 80); // JPEG, kvalita 80%

                $mainHeight = $this->settings = $this->database->table('settings')->get(12)->value;
                $main = Image::fromFile(self::UPLOAD_PATH . $values->img->name);
                $main->resize(1920, $mainHeight, Image::EXACT);
                $main->save(self::UPLOAD_PATH . '' . $imgName, 80); // JPEG, kvalita 80%

                $subHeight = $this->settings = $this->database->table('settings')->get(13)->value;
                $sub = Image::fromFile(self::UPLOAD_PATH . $values->img->name);
                $sub->resize(1920, $subHeight, Image::EXACT);
                $sub->save(self::UPLOAD_PATH . 'sub_' . $imgName, 80); // JPEG, kvalita 80%

	            Nette\Utils\FileSystem::delete(self::UPLOAD_PATH . $values->img->name);

                $values->img = $imgName;

            } else {
                unset($values->img);
            }

        if ($postId) {
            $slide = $this->database->table('slides')->get($postId);
            $slide->update($values);
        } else {
            $slide = $this->database->table('slides')->insert($values);
        }

        $this->flashMessage('Slide byl upraven.', 'success');
        $this->redirect('Slider:');
    }

    public function handleSortable() {

        $i = 1;

        foreach ($_POST['order'] as $sliderId) {
            $sliderEntity = $this->database->table('slides')->get($sliderId);
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
        if (!($slide = $this->slide = $this->database->table('slides')->get($id))) {
            throw new \RuntimeException('Slide nenalezen.');
        }

        if($this->database->table('slides')->where('id', $id)->delete($id)) {
            $this->flashMessage('Slide byl smazán.');
            $this->redirect('Slider:');
        }
    }

}