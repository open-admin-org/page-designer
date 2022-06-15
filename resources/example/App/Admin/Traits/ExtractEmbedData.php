<?php

namespace App\Admin\Traits;

use OpenAdmin\Admin\Form;

//addBodyClass
trait ExtractEmbedData
{
    public function extractEmbedDataOnSave($form)
    {
        $form->saving(function (Form $form) {
            $model = $form->model();
            if (!empty(request()->embed)) {
                $model->embed_data = $this->get_video_data(request()->embed);
            }
        });

        return $form;
    }

    public function get_video_data($embed_code)
    {
        $url = $this->custom_get_embededurl($embed_code);
        $code = '';
        $image = '';

        if (strpos($url, 'youtube') != false) {
            $code = $this->get_youtube_id_from_url($url);
            $image = 'https://i.ytimg.com/vi/'.$code.'/maxresdefault.jpg';
        }

        if (strpos($url, 'vimeo') != false) {
            $code = $this->get_vimeo_id_from_url($url);
            $raw_data = file_get_contents('https://vimeo.com/api/oembed.json?url=https%3A//vimeo.com/'.$code.'&width=480&height=360');
            $data = json_decode($raw_data, true);
            $image = $data['thumbnail_url'];
        }

        return ['code'=>$code, 'image'=>$image, 'image'=>$image, 'url'=>$url];
    }

    public function get_youtube_id_from_url($url)
    {
        preg_match('/(http(s|):|)\/\/(www\.|)yout(.*?)\/(embed\/|watch.*?v=|)([a-z_A-Z0-9\-]{11})/i', $url, $results);

        return $results[6];
    }

    public function get_vimeo_id_from_url($url = '')
    {
        $regs = [];
        $id = '';
        if (preg_match('%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $url, $regs)) {
            $id = $regs[3];
        }

        return $id;
    }

    /**
     * Get the video URL in $content and return it.
     *
     * @param string $content
     *
     * @return bool|string
     */
    public function custom_get_embededurl($content)
    {
        $url = false;

        // Youtube
        if (false !== strpos($content, 'youtube') || false !== strpos($content, 'youtu.be')) {
            $regex = '/.*(((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu\.be|youtube-nocookie\.com))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?).*/i';
            if (preg_match_all($regex, $content, $matches)) {
                $url = (isset($matches[1]) && isset($matches[1][0])) ? $matches[1][0] : false;

                // The regex will not return https:// or https://www so we have to add it manually.
                if (false !== strpos($content, 'youtube')) {
                    // For youtube.com links add https://www
                    $url = 'https://www.'.$url;
                } else {
                    // For youtu.be links add https://
                    $url = 'https://'.$url;
                }
                $url = str_replace('"', '', $url);
            }
        }

        // Vimeo
        if (false !== strpos($content, 'vimeo')) {
            if (preg_match("/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/", $content, $output_array)) {
                if (!empty($output_array[5])) {
                    $url = 'https://vimeo.com/video/'.$output_array[5];
                }
            }
        }

        // Dailymotion
        if (false !== strpos($content, 'dailymotion')) {
            $regex = '/(http|https)?:\/\/(www\.dailymotion\.com\/video\/(\w+))/i';
            if (preg_match_all($regex, $content, $matches)) {
                $url = (isset($matches[0]) && isset($matches[0][0])) ? $matches[0][0] : false;
            }
        }

        return $url;
    }
}
