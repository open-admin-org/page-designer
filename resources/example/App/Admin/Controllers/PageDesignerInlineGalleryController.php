<?php

namespace App\Admin\Controllers;

use App\Models\PageDesignerInlineGallery;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\PageDesigner\Traits\PageDesignItem;
use OpenAdmin\Admin\Show;

class PageDesignerInlineGalleryController extends AdminController
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
            'type'        => 'inline_gallery',
            'title'       => 'inline gallery',
            'icon'        => 'icon-image',
            'model'       => "\App\Models\PageDesignerInlineGallery",
        ];
    }

    public static function pageDesignScripts()
    {
        return <<<'JS'
            window.inline_gallerySetContent = function(data,current_content){
                current_content.innerHTML = '<img src="/storage/'+data.images[0]+'">';
            };
        JS;
    }

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'PageDesignerInlineGallery';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PageDesignerInlineGallery());

        $grid->column('id', __('Id'));
        $grid->column('page_id', __('Page id'));
        $grid->column('title', __('Title'));
        $grid->column('images', __('Images'));
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
        $show = new Show(PageDesignerInlineGallery::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('page_id', __('Page id'));
        $show->field('title', __('Title'));
        $show->field('images', __('Images'));
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
        $form = new Form(new PageDesignerInlineGallery());

        $form->text('title', __('Title'));
        $form->multipleImage('images', __('Images'))->thumbnailFunction('medium', function ($image) {
            $image->resize(1024, 1024, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            return $image;
        });

        return $form;
    }
}
