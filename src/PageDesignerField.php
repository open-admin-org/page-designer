<?php

namespace OpenAdmin\Admin\PageDesigner;

use OpenAdmin\Admin\Form\Field\Textarea;

class PageDesignerField extends Textarea
{
    public $config = [];

    protected $view = 'open-admin-page-designer::index';

    public function snap($pixels = 40)
    {
        return $this->config(['snap'=>intval($pixels)]);
    }

    public function config($config = [])
    {
        $this->config = array_merge($config, $this->config);

        return $this;
    }

    public function render()
    {
        $config = array_merge($this->config, ['field'=>$this->column]);

        $pageDesigner = new PageDesigner($config);
        $pageDesigner->init($this->form->model()->id);
        $pageDesigner->setData($this->value);

        view()->share($pageDesigner->getViewData());
        view()->share('is_field', true);

        return parent::render();
    }
}
