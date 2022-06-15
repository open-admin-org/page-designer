<?php

namespace OpenAdmin\Admin\PageDesigner;

use OpenAdmin\Admin\Form\Field\Textarea;

class PageDesignerField extends Textarea
{
    protected $view = 'open-admin-page-designer::index';

    public function render()
    {
        $parent_id = $this->form->model()->id;

        $pageDesigner = new PageDesigner();
        $pageDesigner->init($parent_id);
        $pageDesigner->setData($this->value);

        view()->share($pageDesigner->getViewData());
        view()->share('is_field', true);

        return parent::render();
    }
}
