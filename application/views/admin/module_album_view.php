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
            <?php if ($this->config->item('ckfinder_version')==2): ?>
              <?=generatebutton('link_self_click', 'btn btn-primary', 'javascript:BrowseServerAlbum("xfile1,multi")', $permission['UpdateAction'], '新增');?>
            <?php else: ?>
              <?=generatebutton('link_self_click', 'btn btn-primary', 'javascript:generateAlbumCkfinder("xfile1","multi")', $permission['UpdateAction'], '新增');?>
            <?php endif; ?>
            <?=generatebutton('link_self_click', 'btn btn-danger', 'javascript:batchItem("delete")', $permission['DeleteAction'], '刪除');?>
            <?=generatebutton('link_self_click', 'btn btn-info', 'javascript:batchItem("release")', $permission['UpdateAction'], '刊登');?>
            <?=generatebutton('link_self_click', 'btn btn-default', 'javascript:batchItem("_release")', $permission['UpdateAction'], '取消刊登');?>
            <a class="btn btn-default" title="<?=$this->imageinfo['xfile1']?>"><i class="fa fa-search"></i> 格式</a>
            <?=$this->load->view('admin/module/btn',array('mbtype'=>'back'),true)?>
          </div>
          <div class="remark padding">
            <p class="text-danger">*注意 : 上傳時「資料夾命名」及「檔名」請勿有「空白.特殊符號」</p>
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

                            <?=$this->load->view('admin/module/form',array('name'=>'刊登','em'=>'xpublish','type'=>'radio'),true)?>
                            <?=$this->load->view('admin/module/form',array('name'=>'主標','em'=>'xtitle','type'=>'title'),true)?>
                            <?=$this->load->view('admin/module/form',array('name'=>'描述','em'=>'xsubtitle','type'=>'desc'),true)?>
                            <?=$this->load->view('admin/module/form',array('name'=>'小圖','em'=>'xfile1','type'=>'img'),true)?>
                            <?=$this->load->view('admin/module/order',array('multi'=>false),true)?>
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
              case 'xfile1':
                return data.xfile1 ? '<img class="feed-photo" src="'+data.xfile1+'" width="100px">' : '';
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
         var url = '<?=(isset($list['pid']))?site_url($this->indexPath."/save/".$list['pid']):site_url($this->indexPath."/save/".NULL);?>';
         $.post(url,$("#createform").serialize(),function(a){if(a.error){alert(a.error);return false}redirect(document.referrer)});
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
