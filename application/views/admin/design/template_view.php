<!--麵包屑-->
<div class="row wrapper page-heading">
<div class="col-lg-10">
    <h2><?=$permission['xname']?></h2>
    <ol class="breadcrumb">
      <?=$this->navPath?>
      <?=$this->subnavPath?>
    </ol>
</div>
</div>

<!--內容-->
<div class="wrapper wrapper-content animated fadeInRight">
<div class="row">
<div class="col-lg-12">
  <div class="ibox float-e-margins">
    <div class="ibox-title">
      <h5>
        <?php if($action == 'index'):?>列表
        <?php elseif($action == 'create'):?>新增
        <?php elseif($action == 'update'):?>編輯
        <?php elseif($action == 'import'):?>匯入
        <?php endif;?>
      </h5>
    </div>

    <div class="ibox-content">

<?php if($action == 'index'):?>

      <div class="user-ctrl-nav padding"> <!--須修改-->
        <?=generatebutton('link_self_href', 'btn btn-primary', $this->formPath, $permission['CreateAction'], '新增');?>
        <?=generatebutton('link_self_click', 'btn btn-danger', 'javascript:batchItem("delete")', $permission['DeleteAction'], '刪除');?>
        <?=generatebutton('link_self_click', 'btn btn-info', 'javascript:batchItem("release")', $permission['UpdateAction'], '刊登');?>
        <?=generatebutton('link_self_click', 'btn btn-default', 'javascript:batchItem("_release")', $permission['UpdateAction'], '取消刊登');?>
      </div>

      <div class="table-responsive padding">
        <!--列表-->
        <table class="table table-striped table-bordered table-hover dataTables-example sorted_table" id="editable"></table>
      </div>

      <?php if($this->showSearching == true):?>
      <div class="search-area padding">
        <div class="row">
            <div class="col-lg-12">
              <div class="col-lg-2">
                <!--個別搜尋-->
                <select class="column_filter form-control">
                <?php for ($i=0; $i < count($this->searchTitle); $i++):?>
                  <option value="<?=$this->searchColumn[$i]?>"><?=$this->searchTitle[$i]?></option>
                <?php endfor?>
                </select>
              </div>
              <div class="col-lg-6">
                <input class="column_filter form-control" type="text" id="col_filter">
              </div>
            </div>
        </div>
      </div>
      <?php endif;?>

<?php elseif($action == 'create' || $action == 'update'):?>

      <form class="form-horizontal" role="form" id="createform">

        <div class="row">

            <div class="col-lg-12">

              <div class="tabs-container">
                <ul class="nav nav-tabs"> <!--須修改-->
                    <li class="active"><a data-toggle="tab" href="#tab-1"> 細目編輯</a></li>
                </ul>

                <div class="tab-content">
                  <div id="tab-1" class="tab-pane active"> <!--須修改-->
                    <div class="panel-body">
                      <fieldset class="form-horizontal">

                        <div class="col-sm-8 b-r">
                          <?=$this->load->view('admin/module/form',array('name'=>'刊登','em'=>'xpublish','type'=>'radio'),true)?>

                          <div class="form-group"><label class="col-sm-2 control-label">主標:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="xtitle" value="<?=(isset($list['xtitle']))?$list['xtitle']:''?>" >
                                <span class="help-block m-b-none">圖檔位於 assets/img/template/主標.png</span>
                            </div>
                          </div>
                          <?php if ($action=='create'): ?>
                            <div class="form-group"><label class="col-sm-2 control-label">切割參數值:</label>
                              <div class="col-sm-10">
                                  <input type="text" class="form-control" placeholder="xbg_img,ximg1,xedit_title,xedit_desc" name="xvar" value="<?=(isset($list['xvar']))?$list['xvar']:''?>" >
                              </div>
                            </div>
                          <?php else: ?>
                            <input type="hidden" class="form-control" name="xvar" value="<?=(isset($list['xvar']))?$list['xvar']:''?>" >
                          <?php endif; ?>
                          <?=$this->load->view('admin/module/form',array('name'=>'wrap-fs 層級','em'=>'xwrap-fs','type'=>'title'),true)?>
                          <?=$this->load->view('admin/module/form',array('name'=>'wrap 層級','em'=>'xwrap','type'=>'title'),true)?>
                          <?=$this->load->view('admin/module/form',array('name'=>'row 層級','em'=>'xrow','type'=>'title'),true)?>
                          <?=$this->load->view('admin/module/form',array('name'=>'圖層級','em'=>'xdivimg','type'=>'title'),true)?>
                          <?=$this->load->view('admin/module/form',array('name'=>'文層級','em'=>'xdivtxt','type'=>'title'),true)?>
                          <?=$this->load->view('admin/module/order',array('multi'=>false),true)?>
                        </div>
                        <div class="col-sm-4">
                            <img src="assets/admin/img/template.png" width="100%" alt="">
                        </div>
                      </fieldset>
                    </div>
                  </div>

                </div>
              </div>
            </div>
        </div>

        <?=$this->load->view('admin/module/btn',array('mbtype'=>'send','seturl'=>$this->indexPath),true)?>
      </form>
<?php elseif($action == 'import'): ?>
      <?=$this->load->view('admin/module/form',array('type'=>'import'),true)?>
<?php endif;?>

    </div>
  </div>
</div>
</div>
</div>

<script>
function listinit() {
$(document).ready(function(){
  // 初始化，表格套件
  var options = initTableOption("<?=site_url($this->indexPath.'/read')?>");
  // 取得設定檔參數
  var tableTitle = <?php echo '["' . implode('", "', $this->tableTitle) . '"]' ?>;
  var tableColumn = <?php echo '["' . implode('", "', $this->tableColumn) . '"]' ?>;
  var tableHeadSize = <?php echo '["' . implode('", "', $this->tableHeadSize) . '"]' ?>;
  var tableSortValue = <?php echo '["' . implode('", "', $this->tableSortValue) . '"]' ?>;
  var showMulti = "<?=$this->showMulti?>";
  // 顯示表格資料
  for (var i=0;i<tableTitle.length;i++) {
    processlistdata(tableColumn[i], i);
  }
  function processlistdata(xcolumn, i) {
    if(i==0&&showMulti==true){options.columnDefs.push({data:null,render:function(b,a,c){return'<input type="checkbox" value="'+b.pid+'" name="checkbox[]">'},title:'<input type="checkbox" name="clickAll" id="clickAll">',orderable:false,targets:0,width:"2%",})};
    options.columnDefs.push({
      data: null,
      orderDataType: (xcolumn=='xpublish' || xcolumn=='xindex')?'dom-text':'', // 需修改
      type: 'string',
      render: function ( data, type, row ) { // 顯示資料值前，先處理資料值
        switch (xcolumn) {  // 需修改 // 每個欄位都需設定
          case 'xtitle':
            return '<a href="<?=$this->formPath?>/'+data.pid+'">'+data.xtitle+'</a>';
            break;
          case 'xpublish':
            var checked = ''; var disabled = 'disabled';
            if(data.xpublish == 'yes') { checked = 'checked'; disabled = ''; }
            return '<input type="radio" '+checked+' value="'+data.xpublish+'" '+disabled+'>';
            break;
          default:
            return data;
            break;
        }
      },
      name:tableColumn[i],title:tableTitle[i],orderable:Boolean(tableSortValue[i]),targets:(showMulti==true)?i+1:i,width:tableHeadSize[i],
    });
  }
  var table = $('.dataTables-example').DataTable(options);

  // 紀錄目前page
  table.on("page.dt",function(){var a=table.page.info();$.post("<?=site_url($this->indexPath.'/recordPage');?>",{page:(a.page+1)},function(b){})});
  setTableSort();
  setTableOther();
});
}
// 組合匯出url
function exporthref() {
  var start = '';
  var end = '';
  var url = '<?=$this->indexPath.'/export'?>'+'?start='+start+'&end='+end;
  $('#export').attr('href',url);
}
</script>

<script>
function forminit() {
$(function() {
  // 初始值設定 // 須修改
  InitForm('checked', "xpublish", "<?=(isset($list["xpublish"]))?$list["xpublish"]:'yes';?>");
  InitForm('checked', "xsort", "<?=(isset($list["xsort"]))?$list["xsort"]:'first';?>");

  // 表單驗證
  $("#createform").validate({
    rules: {
      xtitle:{required: true},xpostdate:{date: true},duedate:{date:true,dateAfter:"input[name='xpostdate']"},xurltitle:{strings: true},
    },
    messages: {
      xtitle:{required: "欄位必填"},xpostdate:{date:"欄位須為日期格式yyyy-mm-dd"},xduedate:{date:"欄位須為日期格式yyyy-mm-dd",dateAfter:"下刊日期須大於發佈日期"},
    },
    submitHandler: function(form) {
     var b=CKEDITOR.instances;for(c in b){var d=CKEDITOR.instances[c].getData();$("textarea[name='"+c+"']").val(d)};
     var url = '<?=(isset($list['pid']))?site_url($this->indexPath."/save/".$list['pid']):site_url($this->indexPath."/save/".NULL);?>';
     $.post(url,$("#createform").serialize(),function(a){if(a.error){alert(a.error);return false}redirect(document.referrer)}).error(function(a) {console.log(a.responseText);})
    }
  });
});

// 判斷是否返回import
var action = '<?=$action?>';
if(action=='import') { var msg = '<?=(isset($uploadmsg))?$uploadmsg:""?>';
  if(msg) {alert(msg);redirect("<?=site_url($this->indexPath)?>");}
}
}
</script>
<script>function importform(){$("#uploadform").submit(function(a){$('input[name="userfile"]').val()||(a.preventDefault(),alert("\u8acb\u9078\u64c7\u6a94\u6848"))})};</script>
