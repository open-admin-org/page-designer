<?php

namespace App\Admin\Controllers;

use App\Models\PageDesigner;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;

class PageDesignerController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Page';

    public function __construct()
    {
        $this->hook('alterGrid', function ($scope, $grid) {
            return $grid;
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PageDesigner());

        $grid->sortable();

        $grid->column('id', __('Id'));
        $grid->column('pid', __('Pid'));
        $grid->column('menu_pos', __('Menu Pos'))->select(['left'=>'Left', 'right'=>'Right']);
        $grid->column('title', __('Title'));
        $grid->column('type', __('Type'));
        $grid->column('slug', __('Slug'));
        $grid->column('status', __('Status'))->switch();
        $grid->column('created_at', __('Created at'))->dateFormat('Y-m-d H:m:s');
        $grid->column('updated_at', __('Updated at'))->dateFormat('Y-m-d H:m:s');

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
        $show = new Show(PageDesigner::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('rank', __('Rank'));
        $show->field('type', __('Type'));
        $show->field('slug', __('Slug'));
        $show->field('title', __('Title'));
        $show->field('title_content', __('Title content'));
        $show->field('body', __('Body'));
        $show->field('image', __('Image'));
        $show->field('meta_title', __('Meta title'));
        $show->field('meta_description', __('Meta description'));
        $show->field('meta_keywords', __('Meta keywords'));
        $show->field('status', __('Status'));
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
        $form = new Form(new PageDesigner());

        $form->tab('Page Design', function ($form) {
            $form->pagedesigner('data', __('pageDesign'));
        });

        $form->tab('Page', function ($form) {
            $form->switch('status', __('Status'));
            $form->number('rank', __('Rank'));
            $form->select('type', __('Type'))->options(['root'=>'Homepage', 'page_designer'=>'Page Designer', 'contact'=>'Contact', 'external'=>'External Link'])->default('page_designer');
            $form->text('title', __('Title'));
            $form->text('slug', __('Slug'));
        });

        $form->tab('Seo', function ($form) {
            $form->text('meta_title', __('Meta title'));
            $form->textarea('meta_description', __('Meta description'));
            $form->textarea('meta_keywords', __('Meta keywords'));
        });

        return $form;
    }
}
