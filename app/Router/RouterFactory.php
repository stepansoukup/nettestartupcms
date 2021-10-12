<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
    use Nette\StaticClass;

    public static function createRouter(): RouteList
    {
        $router = new RouteList();

        $router[] = $admin = new RouteList('Admin');
        $admin[] = new Route('admin/<presenter>/<action>[/<id>]', 'Home:default');

        $router[] = $front = new RouteList('Front');
        $front[] = new Route('page/<slug>', 'Page:default');

        $front[] = new Route('post/<slug>', 'Posts:post');
        $front[] = new Route('posts/archive[/<page>][/<category>]', 'Posts:archive');
        $front[] = new Route('posts[/<category>]', 'Posts:default');

        $front[] = new Route('<presenter>/<action>', 'Home:default');

        return $router;

    }
}
