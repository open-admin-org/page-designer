<div class="row pds" id="pds-app">


    <div class="col-md-9" style="width:1024px !important;min-width:1024px;">
        <form method="post" action="/admin/page-designer/save?page_id={{$page_id}}" id="page-designer-form">
        @csrf
        <div class="card card-primary">
            <div class="card-header with-border d-flex">
                <button type="button" class="btn btn-primary btn-sm log-refresh me-2" onclick="document.forms['page-designer-form'].submit();"><i class="icon-save"></i> {{ trans('admin.save') }}</button>
                <div id="tips" class="ms-auto"></div>
            </div>
            <div class="card-body no-padding" style="position:relative;">
                <div id="page" class="dropzone" style="border:1px solid #CCC;min-height:400px;">
                </div>
                <textarea name="data" id="data" style="display:none;min-height:300px;width:100%;">{!!$page_data!!}</textarea>
            </div>
        </div>
         </form>
    </div>


    <div class="col-md-3">
        <div class="card card-solid">
            <div class="card-header with-border">
                <h3 class="card-title">Elements</h3>
            </div>
            <div class="card-body no-padding">
                Drag me
                @foreach($items as $item)
                <div class="item drop {{$item['type']}}" draggable="true" data-type="{{$item['type']}}">{{$item['title']}} </div>
                @endforeach
            </div>
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
                ...
            </div>
        </div>
    </div>
</div>

<script data-exec-on-popstate>

    function updateItem(data){
        pageDesignerObj.updateItem(data);
    }
    window.updateItem = updateItem;

    var editModal = new bootstrap.Modal(document.getElementById('edit_modal'), {});
    var item_types = {!!$items_json!!};
    var item_data =  {!!$item_data_json!!};
    var pageDesignerObj = new pageDesigner({
        page_id : "{{$page_id}}",
        tips : "#tips",
        page : "#page",
        item_types : item_types,
        item_data : item_data,
        editModal : editModal,
    });

</script>