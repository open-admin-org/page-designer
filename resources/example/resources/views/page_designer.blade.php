<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>OpenAdmin - page designer example page</title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.0.7/dist/css/splide.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/plyr.min.css">

        <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.0.7/dist/js/splide.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@17.8.2/dist/lazyload.min.js"></script>

        <style>

            #page{
                position:relative;
            }

            .item{
                position:absolute;
                background: #f1f1f1;
            }

            .item.text{
                background: none;
                padding: 10px 0;
                font-size:1rem;
                line-height:2.2rem;
            }

            .item.text a{
                color:#000;
            }

            .item .content{
                position:absolute;
                height:100%;
                width:100%;
            }

            .item .content img,
            .item .content .splide{
                position:absolute;
                height:100%;
                width:100%;
                object-fit: cover;
                z-index:0;
            }

            .item .content img.first{
                z-index:2;
            }

            .item .splide_images .splide__arrows--ltr{
                display:none;
            }

            .icon-play{
                position:absolute;
                left:50%;
                top:50%;
                transform: translate(-50%,-50%);
                border-radius:5px;
                background: rgba(255,255,255,0.7);
                height:50px;
                width:80px;
                cursor:pointer;
                transition: all 0.3s;
            }

            .icon-play:hover{
                transition: all 0.3s;
                background: rgba(255,255,255,0.6);
                transform:  translate(-50%,-50%) scale(1.1);
            }

            .icon-play:before{
                position:absolute;
                left:50%;
                top:50%;
                transform: translate(-50%,-50%);
                content:'';
                width: 0;
                height: 0;
                border-top: 10px solid transparent;
                border-bottom: 10px solid transparent;
                border-left: 15px solid #000;
            }

            .item .content > iframe{
                display:none;
            }

            @media (max-width:768px) {

                #page{
                    padding-left:20px;
                    padding-right:20px;
                    width:auto !important;
                    box-sizing:border-box;
                }

                .item{
                    width:100% !important;
                    position:relative !important;
                    margin-bottom:20px;
                    left:0 !important;
                    top:auto !important;
                    padding-bottom:100%;
                    box-sizing:border-box;
                }

                .item.text{
                    height:auto !important;
                }

                .item.text .content{
                    position:relative;
                }
            }

        </style>

    </head>
    <body class="antialiased">

        <div id="page" style="width:100%;height:{{$doc->settings->ratio}}vw;">

            @foreach ($doc->items as $item)
                <div class="item item_{{$item->id}} {{$item->type}}" style="width:{{$item->pwidth}}vw;height:{{$item->pheight}}vw;left:{{$item->px}}vw;top:{{$item->py}}vw;">
                    <div class="content">

                        @php
                            $data = false;
                            if(!empty($items[$item->type][$item->id])){
                                $data = $items[$item->type][$item->id];
                            }
                        @endphp

                        @if ($data)

                            @if($item->type == "images" || $item->type == "inline_gallery")
                                @if (count($data['images'])>1)
                                <div class="splide splide_{{$item->type}}" data-width="{{$item->pwidth}}" data-height="{{$item->pheight}}" data-mobile_height="{{ round((($item->pheight / $item->pwidth) * 100))}}vw">
                                    <div class="splide__track">
                                        <ul class="splide__list">
                                            @foreach ($data['images'] as $img)
                                                <li class="splide__slide">
                                                    @if($item->type == "images")
                                                        <a class="glightbox" href="/storage/{{str_ireplace(".jpg","-large.jpg",$img)}}">
                                                    @endif
                                                    <img data-src="/storage/{{str_ireplace(".jpg","-medium.jpg",$img)}}" class="lazy @if($loop->first)first @endif">
                                                    @if($item->type == "images")
                                                        </a>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                @else
                                    @if($item->type == "images")
                                        <a class="glightbox" href="/storage/{{str_ireplace(".jpg","-large.jpg",$data['images'][0])}}">
                                    @endif
                                    <img data-src="/storage/{{str_ireplace(".jpg","-medium.jpg",$data['images'][0])}}" @if($item->type == "images")data-fslightbox="lightbox"@endif class="lazy first">
                                    @if($item->type == "images")
                                        </a>
                                    @endif
                                @endif
                            @endif

                            @if($item->type == "video")
                                <a class="glightbox" href="storage/{{$data['video']}}">
                                    @if (!empty($data['thumb_video']))
                                        <video class="lazy" width="100%" height="100%" autoplay muted loop id="video_{{$item->id}}" data-src="storage/{{$data['thumb']}}" style="background:#000">
                                            <source data-src="storage/{{$data['thumb_video']}}" type="video/mp4">
                                        </video>
                                    @else
                                        <img class="lazy" data-src="/storage/{{str_ireplace(".jpg","-medium.jpg",$data['thumb'])}}">
                                    @endif
                                    <div class="icon icon-play"></div>
                                </a>
                            @endif

                            @if($item->type == "embed")
                                <a class="glightbox" href="{{$data['embed_data']['url'] ?? ''}}">
                                    @if (!empty($data['thumb_video']))
                                        <video class="lazy" width="100%" height="100%" autoplay muted loop id="video_{{$item->id}}" data-src="storage/{{$data['thumb']}}" style="background:#000">
                                            <source data-src="storage/{{$data['thumb_video']}}" type="video/mp4">
                                        </video>
                                    @elseif (empty($data['thumb']) && !empty($data['embed_data']['image']))
                                        <img class="lazy" data-src="{{$data['embed_data']['image']}}">
                                    @else
                                        <img class="lazy" data-src="/storage/{{str_ireplace(".jpg","-medium.jpg",$data['thumb'])}}">
                                    @endif
                                    <div class="icon icon-play"></div>
                                </a>

                            @endif

                            @if($item->type == "text")
                                {!!$data['body']!!}
                            @endif

                        @endif

                    </div>

                </div>
            @endforeach
        </div>

        <script>
            var current_width;
            var mobile_breakpoint = 768;
            var lightbox;
            document.addEventListener( 'DOMContentLoaded', function() {

                init_page_sliders();

                var lazyLoadInstance = new LazyLoad({
                    // Your custom settings go here
                });

                lightbox = GLightbox({
                    touchNavigation: true,
                    loop: true,
                    zoomable:false,
                    openEffect : "zoom",
                    autoplayVideos: true,

                    width: '80vw',
                    videosWidth : '80vw',
                    height : '80vh',

                    plyr: {
                        css: 'https://cdn.jsdelivr.net/npm/plyr@3.7.2/dist/plyr.css', // Default not required to include
                        js: 'https://cdn.jsdelivr.net/npm/plyr@3.7.2/dist/plyr.min.js', // Default not required to include
                        config: {
                            muted: false,
                            hideControls: true,
                            youtube: {
                                noCookie: true,
                                rel: 0,
                                showinfo: 0,
                                iv_load_policy: 3,
                            },
                            vimeo: {
                                byline: false,
                                title: false,
                                speed: true,
                                transparent: false,
                            }
                        }
                    }
                });

                window.onresize = windowResized;
            });


            var splide_arr;

            function init_page_sliders(){

                var win_width = window.innerWidth;
                current_width = win_width;
                splide_arr = new Array();
                document.querySelectorAll(".splide_images").forEach(elm => {

                    if (win_width <= mobile_breakpoint){
                        var width = "100%";
                        var height = "calc("+elm.dataset.mobile_height+" - 36px)";
                    }else{
                        var width = elm.dataset.width+"vw";
                        var height = elm.dataset.height+"vw";
                    }

                    var splide = new Splide( elm, {
                        width : width,
                        height : height,
                        type : "fade",
                        rewind : true,
                        lazyLoad : 'nearby',
                        pagination: false,
                        autoplay : true,
                        classes: {
                            arrows: '',
                            arrow : '',
                            prev  : 'prev',
                            next  : 'next',
                        },
                    })
                    splide.mount();

                    splide_arr.push(splide);
                });

                document.querySelectorAll(".splide_inline_gallery").forEach(elm => {

                    if (win_width <= mobile_breakpoint){
                        var width = "100%";
                        var height = "calc("+elm.dataset.mobile_height+" - 36px)";
                    }else{
                        var width = elm.dataset.width+"vw";
                        var height = elm.dataset.height+"vw";
                    }

                    var splide = new Splide( elm, {
                        width : width,
                        height : height,
                        type : "fade",
                        rewind : true,
                        lazyLoad : 'nearby',
                        pagination: false,
                        autoplay : true,
                        classes: {
                            arrows: '',
                            arrow : '',
                            prev  : 'prev',
                            next  : 'next',
                        },
                    }).mount();

                    splide_arr.push(splide);

                });
            }

            function destroySplides(){
                for(i in splide_arr){
                    var splide = splide_arr[i];
                    splide.destroy();
                }

            }

            var delay_timer;
            function windowResized() {
                maxWidthVideos();
                clearTimeout(delay_timer);
                delay_timer = setTimeout(windowResized_delayed,200);
            }

            function windowResized_delayed() {

                var win_width = window.innerWidth;
                if (win_width < mobile_breakpoint && current_width >= mobile_breakpoint){
                    destroySplides();
                    init_page_sliders();

                }
                if (win_width > mobile_breakpoint && current_width <= mobile_breakpoint){
                    destroySplides();
                    init_page_sliders();
                }
            }


        </script>

        <style>
            @media (max-width:720px) {
                @foreach ($doc->items as $item)
                    .item_{{$item->id}}{
                        padding-bottom: {{ round((($item->pheight / $item->pwidth) * 100))}}%;
                    }
                @endforeach
            }
        </style>

    </body>
</html

