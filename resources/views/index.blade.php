<div class="row pds" id="pds-app">
    <div class="col-md-9" style="width:1010px !important;min-width:1010px;">

        <div class="card-header with-border d-flex">
            <h3 class="card-title">Page Deisgner</h3>
            <div id="tips" class="ms-auto"></div>
        </div>

        <div class="card-body no-padding" style="position:relative;">
            <div class="pt-2 pb-4">
                <a class="btn btn-light me-2" onclick="window.addHeight(100,'top');return false;">add 100px</a>
                <a class="btn btn-light me-2" onclick="window.addHeight(200,'top');return false;">add 200px</a>
                <a class="btn btn-light me-2" onclick="window.addHeight(500,'top');return false;">add 500px</a>
                <a class="btn btn-light me-2" onclick="window.addHeight(1000,'top');return false;">add 1000px</a>
            </div>
            <div id="page" class="dropzone" style="border:1px solid #CCC;min-height:400px;">
            </div>
            <div class="pt-4">
                <a class="btn btn-light me-2" onclick="window.addHeight(100);return false;">add 100px</a>
                <a class="btn btn-light me-2" onclick="window.addHeight(200);return false;">add 200px</a>
                <a class="btn btn-light me-2" onclick="window.addHeight(500);return false;">add 500px</a>
                <a class="btn btn-light me-2" onclick="window.addHeight(1000);return false;">add 1000px</a>
            </div>
        </div>

        <textarea name="{{$name}}" id="{{$name}}" style="display:none;">{!!$doc!!}</textarea>

    </div>

    <div class="col-md-3">
        <div class="card card-solid">
            <div class="card-header with-border">
                <h3 class="card-title">Elements (Drag)</h3>
            </div>
            <div class="card-body no-padding" id="page-drop">
                <div class="position-sticky sticky-top" style="padding-top:4.1rem;">
                @foreach($item_types as $item_type)
                <div class="item drop {{$item_type['type']}}" draggable="true" data-type="{{$item_type['type']}}"><span class="icon {{$item_type['icon']}}"></span>{{$item_type['title']}} </div>
                @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="height:80%;">
            <div class="modal-content" style="height:100%;">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="edit_model-iframe" style="width:100%;height:100%;"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    @foreach ($scripts as $script)
        {!!$script!!}
    @endforeach

    var editModal = new bootstrap.Modal(document.getElementById('edit_modal'), {});
    var item_types = {!! json_encode($item_types) !!};
    var item_data =  {!!json_encode($items) !!};

    var pageDesignerObj = new pageDesigner({
        field            : "{{$config['field']}}",
        snap             : {{$config['snap']}},
        page_designer_id : "{{$page_designer_id}}",
        tips             : "#tips",
        page             : "#page",
        item_types       : item_types,
        item_data        : item_data,
        editModal        : editModal,
    });

    function updateItem(data){
        pageDesignerObj.updateItem(data);
    }

    function addHeight(height,side = 'bottom'){
        pageDesignerObj.addHeight(height,side);
    }

    window.updateItem = updateItem;
    window.addHeight = addHeight;



</script>