class pageDesigner {

    constructor(options = {}) {

        if (typeof(options) == 'undefined'){
            options = {}
        }

        var defaults = {
            "app": "#pds-app",
            "page": "#page",
            "tips": "#tips",
            "item_class":".item",
            "snap": 40,
            "minSize": 80,
        }
        this.options = Object.assign({}, defaults, options);
        this.options.snapGrid = interact.snappers.grid({x: this.options.snap, y: this.options.snap});

        this.initKeystate();
        this.initPage();
        this.initResize();
        this.initDrag();
        this.initDrop();
        this.loadItemData();
        return this;

    }

    initKeystate = function()
    {
        var ref = this;
        this.shiftDown = false;
        document.addEventListener("keydown", function(evt){
            var evt2 = evt || window.event;
            var keyCode = evt2.keyCode || evt2.which;
            if(keyCode==16){
                ref.shiftDown = true;
            }
        });
        document.addEventListener("keyup", function(evt){
            ref.shiftDown = false;
        });
    }

    initPage = function ()
    {
        var ref = this;
        this.app = document.querySelector(this.options.app);
        this.page = document.querySelector(this.options.page);
        this.tips = document.querySelector(this.options.tips);
        this.resizeContainer = new resizable(this.options.page,{side:'vertical','onResized':function (sizes){
            ref.saveData();
        }});
    }

/*-----------------------------------------------*/
/* helpers */
/*-----------------------------------------------*/

    makeid = function(length = 10)
    {
        var result           = '';
        var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for ( var i = 0; i < length; i++ ) {
          result += characters.charAt(Math.floor(Math.random() * charactersLength));
       }
       return result;
    }

    onlyNumbers = function(str) {
        return /^[0-9]+$/.test(str);
    }

    tip = function(msg)
    {
        this.tips.innerHTML = msg;
    }

/*-----------------------------------------------*/
/* interact events */
/*-----------------------------------------------*/

    initResize = function()
    {
        var ref = this;

        interact('.resize')
        .resizable({
            // resize from all edges and corners
            edges: { left: true, right: true, bottom: true, top: true },
            listeners: {
                move (event) {
                    var target = event.target
                    var x = (parseFloat(target.getAttribute('data-x')) || 0)
                    var y = (parseFloat(target.getAttribute('data-y')) || 0)

                    // update the element's style
                    target.style.width = event.rect.width + 'px'
                    target.style.height = event.rect.height + 'px'

                    // translate when resizing from top or left edges
                    x += event.deltaRect.left
                    y += event.deltaRect.top

                    target.style.transform = 'translate(' + x + 'px,' + y + 'px)'
                    target.setAttribute('data-x', x)
                    target.setAttribute('data-y', y)

                    if (ref.shiftDown){
                        if (event.edges.top){
                            ref.moveLowerItems(event,"top");
                        }else if (event.edges.bottom){
                            ref.moveLowerItems(event,"bottom");
                        }
                    }

                },
            },
            modifiers: [
                // keep the edges inside the parent
                interact.modifiers.restrictEdges({
                    outer: 'parent'
                }),

                // minimum size
                interact.modifiers.restrictSize({
                    min: { width: this.options.minSize, height: this.options.minSize }
                }),

                interact.modifiers.snapSize({
                    targets: [
                      { width: this.options.snap, height: this.options.snap },
                      this.options.snapGrid
                    ],
                  }),
            ],

            inertia: true
        })
        .on("resizestart",function(event){

            if (ref.shiftDown){
                if (event.edges.top){
                    ref.collecMoveItems(event,"top");
                }else if (event.edges.bottom){
                    ref.collecMoveItems(event,"bottom");
                }
            }
        })
        .on("mouseover",function(event){
            ref.tip("Hold down shift to move items up or below as well");
        })
        .on("mouseout",function(event){
            ref.tip("");
        })
    }

    initDrag = function()
    {
        var ref = this;
        var x = 0;
        var y = 0;

        interact(".drag")
        .draggable({
            modifiers: [
                interact.modifiers.snap({
                    targets: [
                        ref.options.snapGrid
                    ],
                    offset: 'partent'
                }),
                interact.modifiers.restrict({
                    restriction: ref.page,
                    elementRect: { top: 0, left: 0, bottom: 1, right: 1 }
                })
            ],
            inertia: true
        })
        .on('dragmove', function (event) {
            var target = event.target
            var x = (parseFloat(target.getAttribute('data-x')) || 0)
            var y = (parseFloat(target.getAttribute('data-y')) || 0)
            x += event.dx;
            y += event.dy;
            var snapped = ref.options.snapGrid(x,y);
            target.setAttribute('data-x', snapped.x);
            target.setAttribute('data-y', snapped.y);
            event.target.style.transform = 'translate(' + snapped.x + 'px, ' + snapped.y + 'px)'
        })
    }

    initDrop = function()
    {
        var ref = this;
        var dragged;

        document.querySelectorAll(".drop").forEach(el =>{

            /* events fired on the draggable target */
            el.addEventListener("drag", function(event) {

            }, false);

            el.addEventListener("dragstart", function(event) {
                // store a ref. on the dragged elem
                dragged = event.target;
                // make it half transparent
                event.target.style.opacity = .5;
            }, false);

            el.addEventListener("dragend", function(event) {
                // reset the transparency
                event.target.style.opacity = "";
            }, false);

        })

        /* events fired on the drop targets */
        document.addEventListener("dragover", function(event) {
            // prevent default to allow drop
            event.preventDefault();
        }, false);


        document.addEventListener("dragenter", function(event) {
            // highlight potential drop target when the draggable element enters it
            if (event.target.classList.contains("dropzone")) {
                event.target.classList.add("allow");
            }

        }, false);

        document.addEventListener("dragleave", function(event) {
            // reset background of potential drop target when the draggable element leaves it
            if (event.target.classList.contains("dropzone")) {
                event.target.classList.remove("allow");
            }

        }, false);

        document.addEventListener("drop", function(event) {
            // prevent default action (open as link for some elements)
            event.preventDefault();
            // move dragged elem to the selected drop target
            if (event.target.classList.contains("dropzone")) {
                event.target.classList.remove("allow");
                //dragged.parentNode.removeChild( dragged );
                //var cloned = dragged.cloneNode();
                //cloned.draggable = false;
                //event.target.appendChild( cloned);
                ref.createNewElement(event,dragged);
            }
        }, false);

    }


/*-----------------------------------------------*/
/* alter block functions */
/*-----------------------------------------------*/

    createNewElement = function (event,org)
    {
        var data = {};
        data.id = this.makeid();
        data.height = 100;
        data.width = 100;
        data.type = org.dataset.type;
        data.x = event.layerX;
        data.y = event.layerY;

        this.createItem(data);
        this.saveData();
    }

    createItem = function(data)
    {
        var ref = this;
        var snapped = this.options.snapGrid(data.x,data.y);
        var div = document.createElement("div");
        div.setAttribute("id",data.id);
        div.className = "item resize drag "+data.type;
        div.style.height = data.height+"px";
        div.style.width = data.width+"px";
        div.setAttribute('data-x', snapped.x);
        div.setAttribute('data-y', snapped.y);
        div.setAttribute('data-type', data.type);
        div.style.transform = 'translate(' + snapped.x + 'px, ' + snapped.y + 'px)';

        var html = '<div class="item-nav">'
        html += '<span class="info icon '+this.options.item_types[data.type].icon+'" title="'+data.type+'"></span>';
        html += '<a class="edit icon-pencil-alt"></a>';
        html += '<a class="remove icon-trash-alt"></a></div>';
        html += '<div class="content"></div>';
        div.innerHTML = html;

        div.querySelector(".edit").addEventListener("click",function(event){
            ref.editItem(event.target.parentNode.parentNode);
        })

        div.querySelector(".remove").addEventListener("click",function(event){
            ref.removeItem(event.target.parentNode.parentNode);
        })

        this.page.appendChild(div);
        return div;
    }

    removeItem = function(item)
    {
        this.page.removeChild(item);
        this.saveData();
    }

    editItem = function (item)
    {
        this.current_item = item;
        this.current_type = item.dataset.type;
        this.current_content = item.querySelector(".content");

        var suf;
        var pre = "unknow-item";
        var id = item.getAttribute("id");
        if (this.onlyNumbers(id)){
            suf = id+"/edit?";
        }else{
            suf = "create?temp_id="+id;
        }

        pre = this.options.item_types[item.dataset.type].path;

        var url = "/admin/"+pre+"/"+suf+"&page_id="+this.options.page_id;

        document.getElementById("edit_model-iframe").setAttribute("src",url);
        this.options.editModal.show();
    }

    updateItem = function (data)
    {
        this.options.editModal.hide();
        this.current_item.id = data.id;
        window[this.current_type+"SetContent"](data,this.current_content);
    }

    moveLowerItems = function(event,dir)
    {
        this.moveItems.forEach(move_item => {
            var y = (parseFloat(move_item.getAttribute('data-y')) || 0)
            var x = (parseFloat(move_item.getAttribute('data-x')) || 0)
            y += event.dy;
            move_item.setAttribute('data-x', x);
            move_item.setAttribute('data-y', y);
            move_item.style.transform = 'translate(' + x + 'px,' + y + 'px)'
        });
    }

    collecMoveItems = function(event,dir)
    {
        var bounds = this.getBounds(event.target);
        var y = event.target.getAttribute('data-y');
        var y_start = (parseFloat(y) + bounds.height);
        this.moveItems = [];
        this.page.querySelectorAll(".item").forEach(item => {
            var y = item.getAttribute('data-y');
            if (dir == "bottom"){
                if (y > y_start && item != event.target){
                    this.moveItems.push(item);
                }
            }else{
                if (y < y_start  && item != event.target){
                    this.moveItems.push(item);
                }
            }
        });
    }

/*-----------------------------------------------*/
/* data saving functions */
/*-----------------------------------------------*/

    getBounds = function (element)
    {
        const {top, left, width, height} = element.getBoundingClientRect();
        return {
            x: left,
            y: top,
            width: width,
            height: height,
        };
    }

    getDistanceBetweenElements = function(a, b)
    {
       const aPosition = this.getBounds(a);
       const bPosition = this.getBounds(b);
       return {"x":(aPosition.x - bPosition.x), "y":(aPosition.y - bPosition.y)};
    }

    loadItemData = function()
    {
        var value = document.getElementById("data").value ? document.getElementById("data").value : '{"items":[],"settings":{"height":500}}';
        var doc = JSON.parse(value);
        this.page.style.height = doc.settings.height + "px";
        var i = 0;
        for(i in doc.items){
            var item = doc.items[i];
            var item_div = this.createItem(item);
            if (this.options.item_data[item.type][item.id]){
                var data = this.options.item_data[item.type][item.id];
                this.current_content = item_div.querySelector(".content");

                window[item.type+"SetContent"](data,this.current_content);
            }
        };
    }

    saveItemData = function()
    {
        var ref = this;
        var page_bounds = this.getBounds(this.page);
        var page_height = page_bounds.height;
        var page_width = page_bounds.width;

        this.items = [];
        this.page.querySelectorAll(".item").forEach(item => {

            var bounds = ref.getBounds(item);
            var x = Math.round(item.dataset.x);
            var y = Math.round(item.dataset.y);
            var height = Math.round(bounds.height);
            var width = Math.round(bounds.width);

            var px = Math.round((x / page_width) * 100);
            var py = Math.round((y / page_height) * 100);
            var pheight = Math.round((height / page_height) * 100);
            var pwidth = Math.round((width / page_width) * 100);

            var id = item.getAttribute("id");
            var type = item.dataset.type;

            if (!id || id == 'undefined'){
                id = this.makeid();
                item.setAttribute("id",id);
            }

            this.items.push({id:id,type:type,x:x,y:y,width:width,height:height,px:px,py:py,pwidth:pwidth,pheight:pheight});

        });
        return this.items;
    }

    saveDocsData = function()
    {
        var settings = this.getBounds(this.page);
        return settings;
    }

    saveData = function()
    {
        var doc = {};
        doc.items = this.saveItemData();
        doc.settings = this.saveDocsData();
        document.getElementById("data").value = JSON.stringify(doc);
    }
}