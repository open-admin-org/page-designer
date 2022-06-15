<?php

namespace App\Admin\Controllers;

use App\Models\PageDesignerImages;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\PageDesigner\Traits\PageDesignItem;
use OpenAdmin\Admin\Show;

class PageDesignerImagesController extends AdminController
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
            'type'        => 'images',
            'title'       => 'images',
            'icon'        => 'icon-file-image',
            'model'       => "\App\Models\PageDesignerImages",
        ];
    }

    public static function pageDesignScripts()
    {
        return <<<'JS'
            window.imagesSetContent = function(data,current_content){
                current_content.innerHTML = '<img src="/storage/'+data.images[0]+'">';
            };
        JS;
    }

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'PageDesignerImages';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PageDesignerImages());

        $grid->column('id', __('Id'));
        $grid->column('page_id', __('Page id'));
        $grid->column('images', __('Images'));
        $grid->column('title', __('Title'));
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
        $show = new Show(PageDesignerImages::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('page_id', __('Page id'));
        $show->field('images', __('Images'));
        $show->field('title', __('Title'));
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
        $form = new Form(new PageDesignerImages());

        $form->multipleImage('images', __('Image'))->thumbnailFunction('medium', function ($image) {
            $image->resize(1024, 1024, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            return $image;
        })->thumbnailFunction('large', function ($image) {
            $image->resize(1920, 1080, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            return $image;
        });
        $form->text('title', __('Title'));

        return $form;
    }
}
