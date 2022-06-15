<?php

namespace OpenAdmin\Admin\PageDesigner;

use Illuminate\Support\Str;
use OpenAdmin\Admin\PageDesigner\Traits\PageDesignItem;

/**
 * Class PageDesigner.
 */
class PageDesigner
{
    public $item_types = [];
    public $items = [];
    public $doc;
    public $config = [];
    public $script = [];

    /**
     * PageDesigner constructor.
     *
     * @param null $file
     */
    public function __construct($set_config = [])
    {
        $default = [
            'snap'=> 40,
        ];
        $this->config = (array) PageDesignerExtention::config();
        $this->config = array_merge($default, $this->config, $set_config);
    }

    public function init($page_designer_id = null)
    {
        $this->page_designer_id = $page_designer_id;
        $this->collectPageDesignItems();
        $this->getItems();
    }

    public function setData($data = false)
    {
        $this->doc = $data;
    }

    public function getViewData($frontend = false)
    {
        if ($frontend) {
            $this->fixFrontendDoc();
        }

        return [
            'config'          => $this->config,
            'item_types'      => $this->item_types,
            'items'           => $this->items,
            'doc'             => $this->doc,
            'page_designer_id'=> $this->page_designer_id,
            'scripts'         => $this->scripts,
        ];
    }

    public function fixFrontendDoc()
    {
        if (empty($this->doc->settings)) {
            $this->doc = new \stdClass();
            $this->doc->settings = (object) [];
            $this->doc->settings->ratio = 1;
            $this->doc->items = [];
        } else {
            $this->doc->settings->ratio = round(($this->doc->settings->height / $this->doc->settings->width) * 100, 1);

            function build_sorter($key)
            {
                return function ($a, $b) use ($key) {
                    return strnatcmp($a->$key, $b->$key);
                };
            }

            usort($this->doc->items, build_sorter('y'));
        }
    }

    public function collectPageDesignItems()
    {
        $classPaths = glob(app_path().'/Admin/Controllers/*.php');
        $pre = '\\App\\Admin\\Controllers\\';

        foreach ($classPaths as $classPath) {
            $segments = explode('/', $classPath);
            $className = str_replace('.php', '', $segments[count($segments) - 1]);
            $class = $pre.$className;

            $model = str_replace('Controller', '', $className);
            $path = Str::plural(Str::kebab(class_basename($model)));

            if ($this->classHasPageDignItemTrait($class)) {
                $data = $class::pageDesign();
                $data['path'] = $path;
                $data['class'] = $class;
                if (method_exists($class, 'pageDesignScripts')) {
                    $this->scripts[] = $class::pageDesignScripts();
                }

                $this->item_types[$data['type']] = $data;
            }
        }
    }

    public function classHasPageDignItemTrait($class_name)
    {
        return in_array(
            PageDesignItem::class,
            array_keys((new \ReflectionClass($class_name))->getTraits())
        );
    }

    public function getItems()
    {
        $this->items = [];
        foreach ($this->item_types as $item_type) {
            $modelojb = new $item_type['model']();
            $items = $modelojb::where($item_type['parent_field'], $this->page_designer_id)->get()->toArray();
            $this->item_data[$item_type['type']] = [];
            foreach ($items as $row) {
                $this->items[$item_type['type']][$row['id']] = $row;
            }
        }
    }
}
