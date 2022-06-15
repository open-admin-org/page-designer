<?php

namespace App\Admin\Controllers;

use App\Admin\Traits\ExtractEmbedData;
use App\Models\PageDesignerEmbed;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\PageDesigner\Traits\PageDesignItem;
use OpenAdmin\Admin\Show;

class PageDesignerEmbedController extends AdminController
{
    use PageDesignItem;
    use ExtractEmbedData;

    public function __construct()
    {
        $this->initPageDesignItem();
    }

    public static function pageDesign()
    {
        return [
            'parent_field'=> 'page_id',
            'type'        => 'embed',
            'title'       => 'embed',
            'icon'        => 'icon-code',
            'model'       => "\App\Models\PageDesignerEmbed",
        ];
    }

    public static function pageDesignScripts()
    {
        return <<<'JS'
            window.embedSetContent = function(data,current_content){
                var thumb = "";
                if (data.thumb != ""){
                    thumb = "/storage/"+data.thumb;
                }
                if (data.embed_data != ""){
                    if (data.embed_data.image != ""){
                        thumb = data.embed_data.image;
                    }
                }

                current_content.innerHTML = '<img src="'+thumb+'" /><div class="icon icon-play"></div>';
            };
        JS;
    }

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'PageDesignerEmbed';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new PageDesignerEmbed());

        $grid->column('id', __('Id'));
        $grid->column('page_id', __('Page id'));
        $grid->column('thumb', __('Thumb'));
        $grid->column('embed', __('Embed'));
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
        $show = new Show(PageDesignerEmbed::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('page_id', __('Page id'));
        $show->field('thumb', __('Thumb'));
        $show->field('embed', __('Embed'));
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
        $form = new Form(new PageDesignerEmbed());

        $form->image('thumb', __('Thumb'))->thumbnailFunction('medium', function ($image) {
            $image->resize(1024, 1024, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            return $image;
        });
        $form->file('thumb_video', __('Thumb Video'));
        $form->textarea('embed', __('Embed'));

        $form = $this->extractEmbedDataOnSave($form);

        return $form;
    }
}
