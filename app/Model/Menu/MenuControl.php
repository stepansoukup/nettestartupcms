<?php


namespace App\Model\Menu;


class MenuControl extends \Nette\Application\UI\Control

{
    public $data;

    public $type;

    public function setData($data)
    {
        $this->data = $this->object_to_array($data);
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function render()
    {
        $this->template->menuItems = $this->buildTree();

        $this->template->setFile(__DIR__ . '/templates/menu-'. $this->type. '.latte');
        $this->template->render();
    }

    public function buildTree(): array
    {
        $tree = [];

        foreach ($this->data as $item)
        {
            $item['current'] = $this->linkStatus($item['link']);

            $item['sub'] = null;

            $tree[$item['parent_id']][] = $item;
        }

        return $this->createTree($tree, $tree[0]);

    }

    public function createTree(&$list, $parent): array
    {
        $tree = [];

        foreach ($parent as $k => $l)
        {

            if(isset($list[$l['id']])){
                $l['sub'] = $this->createTree($list, $list[$l['id']]);
            }

            $tree[] = $l;
        }

        return $tree;
    }

    function object_to_array($data)
    {
        if (is_array($data) || is_object($data))
        {
            $result = array();
            foreach ($data as $key => $value)
            {
                $result[$key] = $this->object_to_array($value);
            }
            return $result;
        }
        return $data;
    }

    public function linkStatus($link = null): bool
    {
        $presenter = $this->getPresenter();

        if($link) {
            $url = $presenter->getHttpRequest()->getUrl();
            $actualUrl = '/'.ltrim($url->path, '/');

            if($actualUrl === $link) {
                return true;
            }
        }

        return false;
    }
}