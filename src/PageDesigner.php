<?php

namespace OpenAdmin\Admin\PageDesigner;

use OpenAdmin\Admin\PageDesigner\Traits\PageDesignItem;
use Illuminate\Support\Str;
use App\Models\PageDesignerImages;
use App\Models\PageDesignerMovie;
use App\Models\PageDesignerText;
use App\Models\PageDesignerInlineGallery;

/**
 * Class PageDesigner.
 */
class PageDesigner
{
    public $items = [];
    public $item_data = [];
    /**
     * PageDesigner constructor.
     *
     * @param null $file
     */
    public function __construct($page_id = null)
    {
        $this->page_id = $page_id;
        $this->collectPageDesignItems();
        $this->getItemdata();
    }

    public function getViewData()
    {
        return [
            "items"=>$this->items,
            "items_json"=>json_encode($this->items),
            "item_data"=>$this->item_data,
            "item_data_json"=>json_encode($this->item_data),
        ];
    }

    public function collectPageDesignItems()
    {
        $classPaths = glob(app_path() . '/Admin/Controllers/*.php');
        $pre = "\\App\\Admin\\Controllers\\";

        foreach ($classPaths as $classPath) {
            $segments = explode('/', $classPath);
            $className = str_replace(".php", "", $segments[count($segments)-1]);
            $class = $pre.$className;

            $path_class = str_replace("Controller", "", $className);
            $path = Str::plural(Str::kebab(class_basename($path_class)));
            $usingTrait = in_array(
                PageDesignItem::class,
                array_keys((new \ReflectionClass($class))->getTraits())
            );
            if ($usingTrait) {
                $data = $class::pageDesign();
                $data['path'] = $path;
                $data['class'] = $class;

                $this->items[$data['type']] = $data;
            }
        }
    }

    public function getItemdata()
    {
        $types = [
            "images"=>PageDesignerImages::class,
            "movie"=>PageDesignerMovie::class,
            "inline_gallery"=>PageDesignerInlineGallery::class,
            "text"=>PageDesignerText::class,
        ];

        $this->item_data = [];
        foreach ($types as $type => $model) {
            $items = $model::where("page_id", $this->page_id)->get()->toArray();
            $this->item_data[$type] = [];
            foreach ($items as $row) {
                $this->item_data[$type][$row['id']] = $row;
            }
        }
    }
}
