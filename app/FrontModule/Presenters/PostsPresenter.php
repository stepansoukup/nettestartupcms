<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use Nette;


class PostsPresenter extends BaseFrontPresenter
{

    public const HOMEPAGE_POSTS_LIMIT = 4;
    public const ARCHIVE_POSTS_LIMIT = 4;

    private $page;

    public function startup()
    {
        parent::startup();
        $this->template->category = $this->database->table('category');
        $this->template->users = $this->database->table('users');
	}

    public function renderDefault($category = null): void
	{
	    if($category) {
            // nejdříve prohledáme category, jestli podle slugu najdeme kategorii
            $cat = $this->database->table('category')->where('slug = ?', $category)->fetch();

            if (!$cat) {
                $this->error('Kategorie nebyla nalezena.');
            }

            // když máme kategorii, použijeme její ID k vyhledání článků
            $posts = $this->database->table('posts')->where('category = ? AND visible = 2', $cat->id)->order('date DESC')->limit(self::HOMEPAGE_POSTS_LIMIT)->fetchAll();

            if (!$posts) {
                $this->error('Kategorie je prázdná.');
            }

            // označení, že je to category stránka
            $this->template->category = $cat;

        } else {
            $posts = $this->database->table('posts')->where('visible = ?', 2)->order('date DESC')->limit(self::HOMEPAGE_POSTS_LIMIT)->fetchAll();

            if (!$posts) {
                $this->error('Nebyl nalezen žádný článek.');
            }

            $this->template->category = false;
        }

		$this->template->posts = $posts;


	}

    public function renderArchive(int $page, $category = null)
    {
        if($category) {
            // nejdříve prohledáme category, jestli podle slugu najdeme kategorii
            $cat = $this->database->table('category')->where('slug = ?', $category)->fetch();

            if (!$cat) {
                $this->error('Kategorie nebyla nalezena.');
            }


            // Zjistíme si celkový počet publikovaných článků v kategorii
            $articlesCount = $this->database->table('posts')->where('category = ? AND visible = 2', $cat->id)->count();

        } else {
            // Zjistíme si celkový počet publikovaných článků
            $articlesCount = $this->database->table('posts')->where('visible = 2')->count();
        }





        // Vyrobíme si instanci Paginatoru a nastavíme jej
        $paginator = new Nette\Utils\Paginator;
        $paginator->setItemCount($articlesCount); // celkový počet článků
        $paginator->setItemsPerPage(self::ARCHIVE_POSTS_LIMIT); // počet položek na stránce
        $paginator->setPage($page); // číslo aktuální stránky

        if($category) {
            // Z databáze si vytáhneme omezenou množinu článků podle výpočtu Paginatoru
            $articles = $this->database->table('posts')
                ->where('category = ? AND visible = 2', $cat->id)
                ->order('date DESC')
                ->limit($paginator->getLength(), $paginator->getOffset());

            $this->template->category = $cat;


        } else {
            // Z databáze si vytáhneme omezenou množinu článků podle výpočtu Paginatoru
            $articles = $this->database->table('posts')
                ->where('visible = 2')
                ->order('date DESC')
                ->limit($paginator->getLength(), $paginator->getOffset());

            $this->template->category = false;
        }


        // kterou předáme do šablony
        $this->template->articles = $articles;
        // a také samotný Paginator pro zobrazení možností stránkování
        $this->template->paginator = $paginator;
	}

	public function renderPost($slug): void
    {
        // tady by to mělo vypsat jeden konkrétní dle slugu
        $post = $this->database->table('posts')->where('slug = ?', $slug)->fetch();

        if (!$post) {
            $this->error('Článek nenalezen.');
        }

        $this->template->post = $post;
    }
}