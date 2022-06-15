class resizable {

    constructor(selector,options) {

        var ref = this;
        if (typeof(options) == 'undefined'){
            options = {}
        }

        var defaults = {
            "border_size": 8,
            "side":"right",
        }

        this.options = Object.assign({}, defaults, options);

        var resize_event = new Event('resize');
        var resized_event = new Event('resized');

        document.querySelectorAll(selector).forEach(panel=>{

            panel.classList.add("resizeable");
            panel.classList.add(ref.options.side);

            panel.addEventListener("mousedown", function(e){

                ref.panel = panel;
                ref.x_pos = e.x;
                ref.y_pos = e.y;

                let height = (parseInt(getComputedStyle(ref.panel, '').height));
                let width = (parseInt(getComputedStyle(ref.panel, '').width));

                if (e.offsetY <= ref.options.border_size && (ref.options.side == "top" || ref.options.side == "vertical")){
                    ref.side = "top";
                    document.addEventListener("mousemove", resizeY, false);
                }

                if (Math.abs(e.offsetY - height) <= ref.options.border_size && (ref.options.side == "bottom" || ref.options.side == "vertical")){
                    ref.side = "bottom";
                    document.addEventListener("mousemove", resizeY, false);
                }

                if (e.offsetX <= ref.options.border_size && (ref.options.side == "left" || ref.options.side == "horizontal")){
                    ref.side = "left";
                    document.addEventListener("mousemove", resizeX, false);
                }
                if ((e.offsetX -width) <= ref.options.border_size && (e.offsetX > ref.options.border_size) && (ref.options.side == "right" || ref.options.side == "horizontal")) {
                    ref.side = "right";
                    document.addEventListener("mousemove", resizeX, false);
                }

            }, false);

            // add events
            panel.addEventListener("resize", function(e){
                if (ref.options.onResize){
                    ref.options.onResize(getBounds(ref.panel));
                }
            },false);

            // add events
            panel.addEventListener("resized", function(e){
                if (ref.options.onResized){
                    ref.options.onResized(getBounds(ref.panel));
                }
            },false);

            panel.addEventListener("mouseup", function(){

                document.removeEventListener("mousemove", resizeX, false);
                document.removeEventListener("mousemove", resizeY, false);
                if (typeof(ref.panel) != 'undefined'){
                    ref.panel.dispatchEvent(resized_event);
                }

            }, false);
        });

        function resizeY(e){

            let dy = ref.y_pos - e.y;

            ref.y_pos = e.y;
            let height = parseInt(getComputedStyle(ref.panel, '').height);
            let new_height;
            if (ref.side == "top"){
                new_height = (height + dy) + "px";
            }else{
                new_height = (height - dy) + "px";
            }
            ref.panel.style.height = new_height;
            ref.panel.dispatchEvent(resize_event);
        }

        function resizeX(e){
            const dx = ref.x_pos - e.x;
            ref.x_pos = e.x;
            let width = parseInt(getComputedStyle(ref.panel, '').width);

            let new_width;
            if (ref.side == "left"){
                new_width = (width + dx) + "px";
            }else{
                new_width = (width - dx) + "px";
            }

            ref.panel.style.width = new_width;
            ref.panel.dispatchEvent(resize_event);
        }

        function getBounds(element) {
            const {top, left, width, height} = element.getBoundingClientRect();
            return {
                x: left,
                y: top,
                width: width,
                height: height,
            };
        }
    }
}



