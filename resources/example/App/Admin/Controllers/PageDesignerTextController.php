<?php

namespace App\Admin\Controllers;

use App\Models\PageDesignerText;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\PageDesigner\Traits\PageDesignItem;
use OpenAdmin\Admin\Show;

class PageDesignerTextController extends AdminController
{
    use PageDesignItem;

    public function __construct()
    {
        $this->initPageDesignItem();
    }

    public static function pageDesign()
    {
        return [
            'parent_field'=> 'page_id',
            'type'        => 'text',
            'title'       => 'text',
            'icon'        => 'icon-align-left',
            'model'       => "\App\Models\PageDesignerText",
        ];
    }

    public static function pageDesignScripts()
    {
        return <<<'JS'
            window.textSetContent = function(data,current_content){
                current_content.innerHTML = data.body;
            };
        JS;
    }

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'PageDesignerText';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PageDesignerText());

        $grid->column('id', __('Id'));
        $grid->column('page_id', __('Page id'));
        $grid->column('title', __('Title'));
        $grid->column('body', __('Body'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(PageDesignerText::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('page_id', __('Page id'));
        $show->field('title', __('Title'));
        $show->field('body', __('Body'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new PageDesignerText());

        $form->text('title', __('Title'));
        $form->ckeditor('body', __('Body'));

        return $form;
    }
}
