<?php

namespace App\Admin\Controllers;

use App\Models\PageDesignerVideo;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\PageDesigner\Traits\PageDesignItem;
use OpenAdmin\Admin\Show;

class PageDesignerVideoController extends AdminController
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
            'type'        => 'video',
            'title'       => 'video',
            'icon'        => 'icon-file-video',
            'model'       => "\App\Models\PageDesignerVideo",
        ];
    }

    public static function pageDesignScripts()
    {
        return <<<'JS'
            window.videoSetContent = function(data,current_content){
                current_content.innerHTML = '<img src="/storage/'+data.thumb+'"><div class="icon icon-play"></div>';
            };
        JS;
    }

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'PageDesignerVideo';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PageDesignerVideo());

        $grid->column('id', __('Id'));
        $grid->column('page_id', __('Page id'));
        $grid->column('thumb', __('Thumb'));
        $grid->column('thumb_video', __('Thumb video'));
        $grid->column('video', __('Video'));
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
        $show = new Show(PageDesignerVideo::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('page_id', __('Page id'));
        $show->field('thumb', __('Thumb'));
        $show->field('thumb_video', __('Thumb Video'));
        $show->field('video', __('Video'));
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
        $form = new Form(new PageDesignerVideo());

        $form->image('thumb', __('Thumb'))->thumbnailFunction('medium', function ($image) {
            $image->resize(1024, 1024, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            return $image;
        });
        $form->file('thumb_video', __('Thumb video'));
        $form->file('video', __('Video'));
        $form->text('title', __('Title'));

        return $form;
    }
}
