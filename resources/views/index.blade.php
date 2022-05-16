<div class="row pds" id="pds-app">

    <div class="col-md-9" style="width:1024px !important;min-width:1024px;">
        <div class="card card-primary">
            <div class="card-header with-border d-flex">
                <button type="button" class="btn btn-primary btn-sm log-refresh me-2"><i class="icon-save"></i> {{ trans('admin.save') }}</button>

                <button class="btn btn-primary btn-sm  me-2" onclick="pageDesignerObj.loadItemData();">load data</button>

                <button class="btn btn-primary btn-sm  me-2" onclick="pageDesignerObj.saveData();">save data</button>
                <div id="tips" class="ms-auto">
                </div>
            </div>
            <div class="card-body no-padding" style="position:relative;">

                <div id="page"  style="border:1px solid #CCC;min-height:400px;">
                    <div class="item resize drag drop image">images <a onclick="openItem(this,'images');" class="icon-pencil-alt"></a></div>
                    <div class="item resize drag drop movie">movie <a onclick="openItem(this,'movie');" class="icon-pencil-alt"></a></div>
                    <div class="item resize drag drop gallery">inline gallery <a onclick="openItem(this,'inline-gallery');" class="icon-pencil-alt"></a></div>
                    <div class="item resize drag drop text">text <a onclick="openItem(this,'text');" class="icon-pencil-alt"></a></div>
                </div>

                <textarea id="data" style="min-height:300px;width:100%;">{"items":[{"id":"2P4BdzUTlF","x":434,"y":306,"width":123,"height":105},{"id":"h2KtFQwRyf","x":84,"y":106,"width":100,"height":132},{"id":"EozlMTgqsJ","x":534,"y":56,"width":162,"height":121},{"id":"bKwy6koZpX","x":234,"y":56,"width":218,"height":151}],"settings":{"height":488}}</textarea>

            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-solid">
            <div class="card-header with-border">
                <h3 class="card-title">Elements</h3>
            </div>
            <div class="card-body no-padding">

                    Drag me
                    <div class="item drag drop image">image </div>
                    <div class="item drag drop movie">movie</div>
                    <div class="item drag drop gallery">gallery</div>
                    <div class="item drag drop text">text</div>

            </div>
            <!-- /.card-body -->
        </div>

        <div class="card card-solid mt-4">
            <div class="card-header with-border">
                <h3 class="card-title">Info</h3>
            </div>
            <div class="card-body no-padding">
                <ul class="nav nav-pills nav-stacked">
                    <li class="margin: 10px;">
                        <a>Size:</a>
                    </li>
                    <li class="margin: 10px;">
                        <a>Updated at: </a>
                    </li>
                </ul>
            </div>
            <!-- /.card-body -->
        </div>

        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- Modal -->
<div class="modal fade" id="edit_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="height:80%;">
    <div class="modal-content" style="height:100%;">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <iframe id="edit_model-iframe" style="width:100%;height:100%;"></iframe>
        ...
      </div>
    </div>
  </div>
</div>

<script data-exec-on-popstate>

    function onlyNumbers(str) {
        return /^[0-9]+$/.test(str);
    }

    var editModal = new bootstrap.Modal(document.getElementById('edit_modal'), {});
    function openItem(link,type){

        var suf;
        console.log(link.parentNode.getAttribute("id"));

        var pre = "unknow-item";
        if (type == "images"){
            pre = "page-designer-images/";
        }
        if (type == "movie"){
            pre = "page-designer-movies/";
        }
        if (type == "inline-gallery"){
            pre = "page-designer-inline-galleries/";
        }
        if (type == "text"){
            pre = "page-designer-texts/";
        }

        var id = link.parentNode.getAttribute("id");
        if (onlyNumbers(id)){
            suf = id+"/edit";
        }else{
            suf = "create?temp_id="+id;
        }

        var url = pre+suf;

        document.getElementById("edit_model-iframe").setAttribute("src",url);

        editModal.show();




    }


    document.querySelector('.log-refresh').addEventListener('click', function() {
        admin.ajax.reload();
    });

    var pageDesignerObj = new pageDesigner({
        tips : "#tips",
        page : "#page"
    });




</script>