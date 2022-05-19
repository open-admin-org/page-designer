<?php

namespace OpenAdmin\Admin\PageDesigner;

use OpenAdmin\Admin\Extension;
use OpenAdmin\Admin\PageDesigner\PageDesigner;
use OpenAdmin\Admin\Admin;
use OpenAdmin\Admin\PageDesigner\Traits\PageDesignItem;
use Illuminate\Support\Str;

/**
 * Class PageDesigner.
 */
class PageDesignerExtention extends Extension
{
    use BootExtension;

    public $items = [];
    public $item_data = [];
    /**
     * PageDesigner constructor.
     *
     * @param null $file
     */
    public function __construct($page_id = null)
    {
        $this->pageDesigner = new PageDesigner($page_id);
    }
}
