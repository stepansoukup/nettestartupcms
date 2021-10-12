<?php

declare(strict_types=1);

namespace App\AdminModule\Presenters;


use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Nette\Utils\Image;
use Nette\Utils\Strings;

final class HomePresenter extends BaseAdminPresenter
{


    const UPLOAD_PATH = 'img/';


    public function actionDefault(): void
    {
        $this->template->defaults = $this->database->table('settings')->fetchAll();

    }

    protected function createComponentEdit()
    {
        $form = new Form;

        $form->addText('1', 'Název webu')
             ->setHtmlAttribute('placeholder', 'Nette Startup CMS');

        $form->addText('2', 'Copyright')
             ->setHtmlAttribute('placeholder', 'Znění copyright textu v patičce')
             ->setDefaultValue('©');

        $form->addText('3', 'Popis webu')
             ->setHtmlAttribute('placeholder', 'Jednou větou popište web');

        $form->addUpload('4', 'Logo webu')
             ->addRule($form::IMAGE, 'Logo webu musí být obrázek typu JPEG, PNG, GIF nebo WebP.');

        $form->addUpload('5', 'Favicon')
            ->addRule($form::IMAGE, 'Favikona webu musí být obrázek typu JPEG, PNG, GIF nebo WebP.');

        $form->addText('6', 'Primární barva (číslo barvy bez #)')
            ->setHtmlAttribute('placeholder', '#ea4e4e');

        $form->addText('7', 'Sekundární barva (číslo barvy bez #)')
            ->setHtmlAttribute('placeholder', '#4f5d73');

        $form->addText('8', 'Barva pozadí menu (číslo barvy bez #)')
            ->setHtmlAttribute('placeholder', 'white');

        $form->addText('9', 'Barva pozadí obsahu (číslo barvy bez #)')
            ->setHtmlAttribute('placeholder', 'white');

        $form->addText('10', 'Barva nadpisů (číslo barvy bez #)')
            ->setHtmlAttribute('placeholder', '#444');

        $form->addText('11', 'Barva odstavcového textu (číslo barvy bez #)')
            ->setHtmlAttribute('placeholder', '#444');

        $form->addText('12', 'Výška úvodního slideru (px)')
            ->setHtmlAttribute('placeholder', '900');

        $form->addText('13', 'Výška slideru na podstránkách (px)')
            ->setHtmlAttribute('placeholder', '300');

        $form->addText('14', 'Facebook')
            ->setHtmlAttribute('placeholder', 'https://www.facebook.com/vasestranka');

        $form->addText('15', 'Messenger')
            ->setHtmlAttribute('placeholder', 'https://m.me/vasestranka');

        $form->addText('16', 'Instagram')
            ->setHtmlAttribute('placeholder', 'https://www.instagram.com/vasestranka');

        $form->addText('17', 'YouTube')
            ->setHtmlAttribute('placeholder', 'https://www.youtube.com/c/vasestranka');

        $form->addText('18', 'Twitter')
            ->setHtmlAttribute('placeholder', 'https://www.twitter.com/vasestranka');

        $form->addText('19', 'Linkedin')
            ->setHtmlAttribute('placeholder', 'https://www.linkedin.com/vasestranka');

        $form->addText('20', 'FB group')
            ->setHtmlAttribute('placeholder', 'https://www.facebook.com/group/vasestranka');

        $form->addText('21', 'E-mail')
            ->setHtmlAttribute('placeholder', 'vas@email.cz');

        $form->addTextArea('22', 'Skripty (analytics, pixel)')
            ->setHtmlAttribute('rows','15');

        $form->addText('23', 'Rychlost slideru (s)')
            ->setHtmlAttribute('placeholder', '3');

        $form->addText('24', 'Rychlost slideru na podstránkách (s)')
            ->setHtmlAttribute('placeholder', '3');

        $pages = $this->database->table('pages')->where('deleted', '0');

        $pageList[0] = 'Ne! Zobrazit výpis článků.';
        foreach ($pages as $page) {
            $pageList[$page->id] = $page->title;
        }

        $form->addSelect('25', 'Na úvodu zobrazit konkrétní stránku místo výpisu článků', $pageList)
             ->setDefaultValue(0);

        $form->addCheckbox('26', 'Zobrazovat slider na úvodní stránce');

        $form->onSuccess[] = [$this, 'onSuccess'];

        if($this->settings) {
            $form->setDefaults($this->database->table('settings')->fetchPairs('id', 'value'));
        }

        return $form;
    }

    public function onSuccess(Form $form, ArrayHash $values): void
    {
        // logo webu
        if ($values[4]->hasFile()) {

            $fileExt = $values[4]->getImageFileExtension();
            $imgName = ('logo.' . $fileExt);

            $image = $values[4]; // nahraný obrázek uložíme do image
            $image->move(self::UPLOAD_PATH . $imgName); // soubor přesuneme do požadované složky

            $values[4] = $imgName;

        } else {
            unset($values[4]);
        }

        // favikona
        if ($values[5]->hasFile()) {

            $fileExt = $values[5]->getImageFileExtension();
            $imgName = ('icon.' . $fileExt);


            $image = $values[5]; // nahraný obrázek uložíme do image
            $image->move(self::UPLOAD_PATH . $imgName); // soubor přesuneme do požadované složky

            $apple = Image::fromFile(self::UPLOAD_PATH . $imgName);
            $apple->resize(180, 180, Image::EXACT);
            $apple->save(self::UPLOAD_PATH . 'apple-touch-' . $imgName, 80); // JPEG, kvalita 80%

            $main = Image::fromFile(self::UPLOAD_PATH . $imgName);
            $main->resize(32, 32, Image::EXACT);
            $main->save(self::UPLOAD_PATH . 'fav' . $imgName, 80); // JPEG, kvalita 80%

            //Nette\Utils\FileSystem::delete(self::UPLOAD_PATH . $imgName);

            $values[5] = $imgName;

        } else {
            unset($values[5]);
        }

        foreach ($values as $key => $value)
        {
            $this->database->table('settings')->get($key)->update([
                'value' => $value
            ]);
        }


        $this->flashMessage('Úvodní stránka byla upravena.', 'success');
        $this->redirect('Home:');
    }


    }
