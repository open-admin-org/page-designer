
class pageDesigner {

    constructor(options = {}) {

        var ref = this;
        var page;


        if (typeof(options) == 'undefined'){
            options = {}
        }

        var defaults = {
            "app": "#pds-app",
            "page": "#page",
            "tips": "#tips",
            "item_class":".item",
        }

        this.options = Object.assign({}, defaults, options);


        this.initKeystate();
        this.initPage();
        this.initResize();
        this.initDrag();
        this.loadItemData();
        return this;

    }

    initKeystate = function(){
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

    initPage = function (){
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

    makeid = function(length = 10) {
        var result           = '';
        var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for ( var i = 0; i < length; i++ ) {
          result += characters.charAt(Math.floor(Math.random() * charactersLength));
       }
       return result;
    }

    tip = function(msg){
        this.tips.innerHTML = msg;
    }

/*-----------------------------------------------*/
/* interact events */
/*-----------------------------------------------*/

    initResize = function(){

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
                    //target.textContent = Math.round(event.rect.width) + '\u00D7' + Math.round(event.rect.height)


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
                    min: { width: 100, height: 50 }
                })
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

    initDrag = function(){

        var ref = this;
        var x = 0;
        var y = 0;
        var gridTarget = interact.snappers.grid({x: 50, y: 50});

        interact(".drag")
        .draggable({
            modifiers: [
                interact.modifiers.snap({
                    targets: [
                        gridTarget
                    ],
                    range: Infinity,
                    relativePoints: [ { x: 0, y: 0 } ]
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
            target.setAttribute('data-x', x);
            target.setAttribute('data-y', y);
            event.target.style.transform = 'translate(' + x + 'px, ' + y + 'px)'
        })
    }

    initResizeable = function(){
        interact('.resizable')
        .resizable({
            edges: { top: true, left: true, bottom: true, right: true },
            listeners: {
                move: function (event) {
                    let { x, y } = event.target.dataset

                    x = (parseFloat(x) || 0) + event.deltaRect.left
                    y = (parseFloat(y) || 0) + event.deltaRect.top

                    Object.assign(event.target.style, {
                        width: `${event.rect.width}px`,
                        height: `${event.rect.height}px`,
                        transform: `translate(${x}px, ${y}px)`
                    })

                    Object.assign(event.target.dataset, { x, y })
                }
            }
        })
    }

/*-----------------------------------------------*/
/* alter block functions */
/*-----------------------------------------------*/

    moveLowerItems = function(event,dir){

        this.moveItems.forEach(move_item => {
            var y = (parseFloat(move_item.getAttribute('data-y')) || 0)
            var x = (parseFloat(move_item.getAttribute('data-x')) || 0)

            y += event.dy;

            move_item.setAttribute('data-x', x);
            move_item.setAttribute('data-y', y);
            move_item.style.transform = 'translate(' + x + 'px,' + y + 'px)'
        });
    }

    collecMoveItems = function(event,dir){

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

    getBounds = function (element) {
        const {top, left, width, height} = element.getBoundingClientRect();
        return {
            x: left,
            y: top,
            width: width,
            height: height,
        };
    }

    getDistanceBetweenElements = function(a, b) {
       const aPosition = this.getBounds(a);
       const bPosition = this.getBounds(b);

       return {"x":(aPosition.x - bPosition.x), "y":(aPosition.y - bPosition.y)};
    }

    loadItemData = function(){

        var ref = this;
        var doc = JSON.parse(document.getElementById("data").value);
        this.page.style.height = doc.settings.height + "px";
        var items_data = doc.items
        var i = 0;
        this.page.querySelectorAll(".item").forEach(item => {

            var item_data = items_data[i];
            item.id = item_data.id;

            Object.assign(item.style, {
                width: `${item_data.width}px`,
                height: `${item_data.height}px`,
                transform: `translate(${item_data.x}px, ${item_data.y}px)`
            })

            Object.assign(item.dataset, { x:item_data.x, y:item_data.y });

            i ++;

        });
    }

    saveItemData = function(){

        var ref = this;
        this.items = [];

        this.page.querySelectorAll(".item").forEach(item => {

            var bounds = ref.getBounds(item);
            var x = item.getAttribute('data-x');
            var y = item.getAttribute('data-y');
            var id = item.getAttribute("id");

            if (!id || id == 'undefined'){
                id = this.makeid();
                item.setAttribute("id",id);
            }

            this.items.push({id:id,x:Math.round(x),y:Math.round(y),width:Math.round(bounds.width),height:Math.round(bounds.height)});

        });
        return this.items;
    }

    saveDocsData = function(){
        var settings = {
            height: this.getBounds(this.page).height
        }
        return settings;
    }

    saveData = function(){

        var doc = {};
        doc.items = this.saveItemData();
        doc.settings = this.saveDocsData();

        document.getElementById("data").value = JSON.stringify(doc);
    }


}