class pageDesigner {

    constructor(options = {}) {

        if (typeof (options) == 'undefined') {
            options = {}
        }

        var defaults = {
            "field": "data",
            "app": "#pds-app",
            "page": "#page",
            "pageDrop": "#page-drop",
            "tips": "#tips",
            "item_class": "item",
            "snap": 40,
            "minSize": 80,
        }

        this.options = Object.assign({}, defaults, options);
        this.options.snapGrid = interact.snappers.grid({ x: this.options.snap, y: this.options.snap });
        this.options.item_selector = '.' + this.options.item_class;

        this.beforeInit();
        this.initKeystate();
        this.initPage();
        this.initResize();
        this.initDrag();
        this.initMultiDrag();
        this.initDrop();
        this.loadItemData();
        return this;

    }

    initKeystate = function () {
        var ref = this;
        this.shiftDown = false;
        document.addEventListener("keydown", function (evt) {
            var evt2 = evt || window.event;
            var keyCode = evt2.keyCode || evt2.which;
            if (keyCode == 16) {
                ref.shiftDown = true;
            }
        });
        document.addEventListener("keyup", function (evt) {
            ref.shiftDown = false;
        });
    }

    initPage = function () {
        var ref = this;
        this.app = document.querySelector(this.options.app);
        this.page = document.querySelector(this.options.page);
        this.pageDrop = document.querySelector(this.options.pageDrop);
        this.tips = document.querySelector(this.options.tips);
        this.resizeContainer = new resizable(this.options.page, {
            side: 'vertical', 'onResized': function (sizes) {
                ref.pageDrop.style.height = sizes.height + "px";
                ref.saveData();
            }
        });
    }

    /*-----------------------------------------------*/
    /* helpers */
    /*-----------------------------------------------*/

    makeid = function (length = 10) {
        var result = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    onlyNumbers = function (str) {
        return /^[0-9]+$/.test(str);
    }

    tip = function (msg) {
        this.tips.innerHTML = msg;
    }

    /*-----------------------------------------------*/
    /* interact events */
    /*-----------------------------------------------*/

    beforeInit = function () {

        interact('.drag').unset();
        interact('.resize').unset();

        if (typeof (document.pageDesigener_ref) != 'undefined') {
            document.removeEventListener("dragover", document.pageDesigener_ref.onPageDragover, false);
            document.removeEventListener("dragenter", document.pageDesigener_ref.onPageDragenter, false);
            document.removeEventListener("dragleave", document.pageDesigener_ref.onPageDragleave, false);
            document.removeEventListener("drop", document.pageDesigener_ref.onPageDrop, false);
        }
    }

    initResize = function () {
        var ref = this;

        interact('.resize')
            .resizable({
                // resize from all edges and corners
                edges: { left: true, right: true, bottom: true, top: true },
                listeners: {
                    move(event) {
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

                        if (ref.shiftDown) {
                            if (event.edges.top) {
                                ref.moveItemsY(event.dy);
                            } else if (event.edges.bottom) {
                                ref.moveItemsY(event.dy);
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
            .on("resizestart", function (event) {

                if (ref.shiftDown) {
                    if (event.edges.top) {
                        ref.collectMoveItems(event, "top");
                    } else if (event.edges.bottom) {
                        ref.collectMoveItems(event, "bottom");
                    }
                }
            })
            .on("mouseover", function (event) {
                ref.tip("Holding down shift lets you: 1) Select mulitple items for moving. 2) Move items up/down while scaling");
            })
            .on("mouseout", function (event) {
                ref.tip("");
            })
    }

    initDrag = function () {
        var ref = this;
        var x = 0;
        var y = 0;

        ref.dragBounds = ref.setDragBounds(null);

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
                        restriction: function (x, y, element) {
                            return ref.getDragBounds(x, y, element)
                        },
                        elementRect: { top: 0, left: 0, bottom: 1, right: 1 }
                    })
                ],
                inertia: true
            })
            .on("dragstart", function (event) {
                ref.dragBounds = ref.setDragBounds(event);
            })
            .on('dragmove', function (event) {

                var targets = [event.target];
                var items = document.querySelectorAll(ref.options.item_selector + ".selected");

                if (items.length > 0) {
                    targets = items;
                }

                for (var i = 0; i < targets.length; i++) {

                    var target = targets[i];

                    var x = (parseFloat(target.getAttribute('data-x')) || 0)
                    var y = (parseFloat(target.getAttribute('data-y')) || 0)
                    x += event.dx;
                    y += event.dy;
                    var snapped = ref.options.snapGrid(x, y);
                    target.setAttribute('data-x', snapped.x);
                    target.setAttribute('data-y', snapped.y);
                    target.style.transform = 'translate(' + snapped.x + 'px, ' + snapped.y + 'px)';
                }
            })
    }

    setDragBounds = function (event) {

        var page_bounds = this.page.getBoundingClientRect();
        var items = document.querySelectorAll(this.options.item_selector + ".selected");
        var scrollPos = this.getScrollPos();

        if (!items.length) {

            var x = page_bounds.x + scrollPos.x;
            var y = page_bounds.y + scrollPos.y;
            var width = page_bounds.width;
            var height = page_bounds.height;
            var bounds = new DOMRect(x, y, width, height);
            return bounds;
        } else {

            if (event instanceof Event) {

                var target_bounds = event.target.getBoundingClientRect();
                var min_x = 0;
                var min_y = 0;
                var max_x = 0;
                var max_y = 0;

                items.forEach(item => {
                    if (item != event.target) {
                        var item_bounds = item.getBoundingClientRect();
                        if (item_bounds.y < target_bounds.y) {
                            min_y = ((item_bounds.y - target_bounds.y) < min_y) ? (item_bounds.y - target_bounds.y) : min_y;
                        }
                        if (item_bounds.x < target_bounds.x) {
                            min_x = ((item_bounds.x - target_bounds.x) < min_x) ? (item_bounds.x - target_bounds.x) : min_x;
                        }

                        if (item_bounds.right > target_bounds.right) {
                            max_x = ((item_bounds.right - target_bounds.right) > max_x) ? (item_bounds.right - target_bounds.right) : max_x;
                        }
                        if (item_bounds.bottom > target_bounds.bottom) {
                            max_y = ((item_bounds.bottom - target_bounds.bottom) > max_y) ? (item_bounds.bottom - target_bounds.bottom) : max_y;
                        }
                    }
                });

                var x = (page_bounds.x - min_x) + scrollPos.x;
                var y = (page_bounds.y - min_y) + scrollPos.y;
                var width = ((page_bounds.width + min_x) - max_x) + scrollPos.x;
                var height = ((page_bounds.height + min_y) - max_y) + scrollPos.y;
                var bounds = new DOMRect(x, y, width, height);
                return bounds;
            }
        }
    }

    getScrollPos = function () {
        var supportPageOffset = window.pageXOffset !== undefined;
        var isCSS1Compat = ((document.compatMode || "") === "CSS1Compat");
        var x = supportPageOffset ? window.pageXOffset : isCSS1Compat ? document.documentElement.scrollLeft : document.body.scrollLeft;
        var y = supportPageOffset ? window.pageYOffset : isCSS1Compat ? document.documentElement.scrollTop : document.body.scrollTop;
        return { x: x, y: y };
    }

    getDragBounds = function (x, y, element) {
        return this.dragBounds;
    }

    initDrop = function () {
        var ref = this;
        var dragged;
        document.pageDesigener_ref = this;

        document.querySelectorAll(".drop").forEach(el => {

            /* events fired on the draggable target */
            el.addEventListener("drag", function (event) {

            }, false);

            el.addEventListener("dragstart", function (event) {
                // store a ref. on the dragged elem
                ref.dragged = event.target;
                // make it half transparent
                event.target.style.opacity = .5;
            }, false);

            el.addEventListener("dragend", function (event) {
                // reset the transparency
                event.target.style.opacity = "";
            }, false);

        })

        /* events fired on the drop targets */
        document.addEventListener("dragover", this.onPageDragover, false);
        document.addEventListener("dragenter", this.onPageDragenter, false);
        document.addEventListener("dragleave", this.onPageDragleave, false);
        document.addEventListener("drop", this.onPageDrop, false);
    }

    onPageDragover = function (event) {
        event.preventDefault();
    }

    onPageDragenter = function (event) {
        if (event.target.classList.contains("dropzone")) {
            event.target.classList.add("allow");
        }
    }

    onPageDragleave = function (event) {
        if (event.target.classList.contains("dropzone")) {
            event.target.classList.remove("allow");
        }
    }

    onPageDrop = function (event) {

        var ref = this.pageDesigener_ref;
        event.preventDefault();
        if (event.target.classList.contains("dropzone")) {
            event.target.classList.remove("allow");
            ref.createNewElement(event, ref.dragged);
        }
    }

    /*-----------------------------------------------*/
    /* alter block functions */
    /*-----------------------------------------------*/

    createNewElement = function (event, org) {
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

    createItem = function (data) {
        var ref = this;
        var snapped = this.options.snapGrid(data.x, data.y);
        var div = document.createElement("div");

        div.setAttribute("id", data.id);
        div.className = ref.options.item_class + " resize drag " + data.type;
        div.style.height = data.height + "px";
        div.style.width = data.width + "px";
        div.setAttribute('data-x', snapped.x);
        div.setAttribute('data-y', snapped.y);
        div.setAttribute('data-type', data.type);
        div.style.transform = 'translate(' + snapped.x + 'px, ' + snapped.y + 'px)';

        var html = '<div class="item-nav">'
        html += '<span class="info icon ' + this.options.item_types[data.type].icon + '" title="' + data.type + '"></span>';
        html += '<a class="edit icon-pencil-alt"></a>';
        html += '<a class="remove icon-trash-alt"></a></div>';
        html += '<div class="content"></div>';
        div.innerHTML = html;

        div.querySelector(".edit").addEventListener("click", function (event) {
            ref.editItem(event.target.parentNode.parentNode);
        })

        div.querySelector(".remove").addEventListener("click", function (event) {
            ref.removeItem(event.target.parentNode.parentNode);
        })

        this.page.appendChild(div);
        return div;
    }

    removeItem = function (item) {
        this.page.removeChild(item);
        this.saveData();
    }

    editItem = function (item) {
        this.current_item = item;
        this.current_type = item.dataset.type;
        this.current_content = item.querySelector(".content");

        var suf;
        var id = item.getAttribute("id");
        var type_settings = this.options.item_types[item.dataset.type];

        if (this.onlyNumbers(id)) {
            suf = id + "/edit?";
        } else {
            suf = "create?temp_id=" + id;
        }

        var url = "/admin/" + type_settings.path + "/" + suf + "&" + type_settings.parent_field + "=" + this.options.page_designer_id;

        document.getElementById("edit_model-iframe").setAttribute("src", url);

        this.options.editModal._element.querySelector('.modal-title').innerHTML = "Edit " + item.dataset.type;
        this.options.editModal.show();
    }

    updateItem = function (data) {
        this.options.editModal.hide();
        this.current_item.setAttribute("id", data.id);
        this.saveData();
        window[this.current_type + "SetContent"](data, this.current_content);
    }

    /*-----------------------------------------------*/
    /* shit Scale function */
    /*-----------------------------------------------*/

    moveItemsY = function (move_y) {
        this.moveItems.forEach(move_item => {
            var y = (parseFloat(move_item.getAttribute('data-y')) || 0)
            var x = (parseFloat(move_item.getAttribute('data-x')) || 0)
            y += move_y;
            move_item.setAttribute('data-x', x);
            move_item.setAttribute('data-y', y);
            move_item.style.transform = 'translate(' + x + 'px,' + y + 'px)'
        });
    }

    collectMoveItems = function (event, dir) {
        this.moveItems = [];
        if (event == null) {
            this.page.querySelectorAll(this.options.item_selector).forEach(item => {
                this.moveItems.push(item);
            });
        } else {
            var bounds = event.target.getBoundingClientRect();
            var y = event.target.getAttribute('data-y');
            var y_start = (parseFloat(y) + bounds.height);

            this.page.querySelectorAll(this.options.item_selector).forEach(item => {
                var y = item.getAttribute('data-y');
                if (dir == "bottom") {
                    if (y > y_start && item != event.target) {
                        this.moveItems.push(item);
                    }
                } else {
                    if (y < y_start && item != event.target) {
                        this.moveItems.push(item);
                    }
                }
            });
        }
    }

    /*-----------------------------------------------*/
    /* multidrag / select */
    /*-----------------------------------------------*/

    initMultiDrag = function () {

        let element;
        var ref = this;
        var ref_page = this.page;

        const mouse = {
            x: 0,
            y: 0,
            startX: 0,
            startY: 0
        };

        function mouseDown(e) {
            if (e.target.id === "page") {
                const rects = [...ref_page.querySelectorAll(".selection")];

                if (rects) {
                    for (const rect of rects) {
                        ref_page.removeChild(rect);
                    }
                }

                mouse.startX = mouse.x;
                mouse.startY = mouse.y;
                element = document.createElement("div");
                element.className = "selection";
                element.style.border = "1px dashed black";
                element.style.position = "absolute";
                element.style.left = mouse.x + "px";
                element.style.top = mouse.y + "px";
                ref_page.appendChild(element);
            }
        }

        function setMousePosition(e) {
            const ev = e || window.event;

            let pageBounds = ref_page.getBoundingClientRect();

            if (ev.pageX) {
                mouse.x = (ev.pageX - window.pageXOffset) - pageBounds.x;
                mouse.y = (ev.pageY - window.pageYOffset) - pageBounds.y;
            } else if (ev.clientX) {
                mouse.x = (ev.clientX - document.body.scrollLeft) - pageBounds.x;
                mouse.y = (ev.clientY - document.body.scrollTop) - pageBounds.y;
            }
        }

        function mouseMove(e) {
            setMousePosition(e);
            if (element) {

                element.style.width = Math.abs(mouse.x - mouse.startX) + "px";
                element.style.height = Math.abs(mouse.y - mouse.startY) + "px";
                element.style.left = mouse.x - mouse.startX < 0 ? mouse.x + "px" : mouse.startX + "px";
                element.style.top = mouse.y - mouse.startY < 0 ? mouse.y + "px" : mouse.startY + "px";
            }
        }

        function mouseUp(e) {
            element = null;

            const rect = ref_page.querySelector(".selection");
            const boxes = [...ref_page.querySelectorAll(ref.options.item_selector)];

            if (ref.shiftDown) {
                e.target.closest(ref.options.item_selector).classList.toggle("selected");
            }

            if (rect) {
                const inBounds = [];

                for (const box of boxes) {
                    if (isInBounds(rect, box)) {
                        inBounds.push(box);
                    } else {
                        box.classList.remove("selected");
                    }
                }

                if (inBounds.length >= 2) {
                    for (const box of inBounds) {
                        box.classList.add("selected");
                    }
                }

                if (rect) {
                    ref_page.removeChild(ref_page.querySelector(".selection"));
                }
            }

        }

        function isInBounds(obj1, obj2) {
            const a = obj1.getBoundingClientRect();
            const b = obj2.getBoundingClientRect();

            return (
                a.x < b.x + b.width &&
                a.x + a.width > b.x &&
                a.y < b.y + b.height &&
                a.y + a.height > b.y
            );
        }

        this.page.addEventListener("mousedown", mouseDown);
        this.page.addEventListener("mouseup", mouseUp);
        this.page.addEventListener("mousemove", mouseMove);
    }

    /*-----------------------------------------------*/
    /* alter canvas functions */
    /*-----------------------------------------------*/

    addHeight = function (add_height, side = "bottom") {
        var page_bounds = this.page.getBoundingClientRect();
        var page_height = page_bounds.height;
        this.page.style.height = (page_height + add_height) + "px";

        if (side == "top") {
            this.collectMoveItems(null, "top");
            this.moveItemsY(add_height);
        }
        this.saveData();
        return false;
    }

    /*-----------------------------------------------*/
    /* data saving functions */
    /*-----------------------------------------------*/

    getDistanceBetweenElements = function (a, b) {
        const aPosition = a.getBoundingClientRect();
        const bPosition = b.getBoundingClientRect();
        return { "x": (aPosition.left - bPosition.left), "y": (aPosition.top - bPosition.top) };
    }

    loadItemData = function () {

        var value = document.getElementById(this.options.field).value ? document.getElementById(this.options.field).value : '{"items":[],"settings":{"height":500}}';
        var doc = JSON.parse(value);

        this.page.style.height = doc.settings.height + "px";
        this.pageDrop.style.height = doc.settings.height + "px";
        var i = 0;
        for (i in doc.items) {
            var item = doc.items[i];
            var item_div = this.createItem(item);

            if (typeof (this.options.item_data[item.type]) != 'undefined') {
                if (typeof (this.options.item_data[item.type][item.id]) != 'undefined') {
                    var data = this.options.item_data[item.type][item.id];
                    this.current_content = item_div.querySelector(".content");

                    window[item.type + "SetContent"](data, this.current_content);
                }
            }
        };
    }

    saveItemData = function () {
        var page_bounds = this.page.getBoundingClientRect();
        var page_width = page_bounds.width;

        this.items = [];
        this.page.querySelectorAll(this.options.item_selector).forEach(item => {

            var bounds = item.getBoundingClientRect();
            var x = Math.round(item.dataset.x);
            var y = Math.round(item.dataset.y);
            var height = Math.round(bounds.height);
            var width = Math.round(bounds.width);

            // dont round the percentages for some nice percission
            var px = ((x / page_width) * 100);
            var py = ((y / page_width) * 100);
            var pheight = ((height / page_width) * 100);
            var pwidth = ((width / page_width) * 100);

            var id = item.getAttribute("id");
            var type = item.dataset.type;

            if (!id || id == 'undefined') {
                id = this.makeid();
                item.setAttribute("id", id);
            }

            this.items.push({ id: id, type: type, x: x, y: y, width: width, height: height, px: px, py: py, pwidth: pwidth, pheight: pheight });

        });
        return this.items;
    }

    saveDocsData = function () {
        var settings = this.page.getBoundingClientRect();
        return settings;
    }

    saveData = function () {
        var doc = {};
        doc.items = this.saveItemData();
        doc.settings = this.saveDocsData();
        document.getElementById(this.options.field).value = JSON.stringify(doc);
    }
}