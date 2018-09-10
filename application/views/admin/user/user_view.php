<!--麵包屑-->
<div class="row wrapper page-heading">
<div class="col-lg-10">
    <h2><?=$permission['xname']?></h2>
    <ol class="breadcrumb">
        <?=$this->navPath?>
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
        <?php elseif($action == 'updatepsw'):?>更改密碼
        <?php endif;?>
      </h5>
    </div>

    <div class="ibox-content">

<?php if($action == 'index'):?>

      <div class="user-ctrl-nav padding">
        <?=generatebutton('link_self_href', 'btn btn-primary', $this->formPath, $permission['CreateAction'], '新增');?>
        <?=generatebutton('link_self_click', 'btn btn-danger', 'javascript:batchItem("delete")', $permission['DeleteAction'], '刪除');?>
        <?=generatebutton('link_self_click', 'btn btn-info', 'javascript:batchItem("release")', $permission['UpdateAction'], '啟用');?>
        <?=generatebutton('link_self_click', 'btn btn-default', 'javascript:batchItem("_release")', $permission['UpdateAction'], '取消啟用');?>
      </div>

      <div class="table-responsive padding">
        <!--列表-->
        <table class="table table-striped table-bordered table-hover dataTables-example sorted_table" id="editable"></table>
      </div>

<?php elseif($action == 'create' || $action == 'update'):?>

      <form class="form-horizontal" role="form" id="createform" enctype="multipart/form-data">

        <div class="row">
            <div class="col-lg-12">
              <div class="tabs-container">
                  <ul class="nav nav-tabs">
                      <li class="active"><a data-toggle="tab" href="#tab-1"> 細目編輯</a></li>
                  </ul>
                  <div class="tab-content">
                      <div id="tab-1" class="tab-pane active">
                          <div class="panel-body">

                            <fieldset class="form-horizontal">
                              <div class="form-group">
                                <label class="col-sm-2 control-label">啟用:</label>
                                <div class="col-sm-10">
                                  <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-default"><input type="radio" value="yes" name="xpublish">是</label>
                                    <label class="btn btn-default"><input type="radio" value="no" name="xpublish">否</label>
                                  </div>
                                </div>
                              </div>

                              <div class="form-group"><label class="col-sm-2 control-label">登入帳號:</label>
                                <div class="col-sm-10">
                                  <?php if($list['xaccount'] == $selfaccount):?>
                                  <label class="control-label"><?=$list['xaccount']?></label>
                                  <input type="hidden" class="form-control" name="xaccount" value="<?=$list['xaccount']?>" placeholder="帳號" required>
                                  <?php else:?>
                                  <input type="text" class="form-control" name="xaccount" value="<?=$list['xaccount']?>" placeholder="帳號" required>
                                  <?php endif;?>
                                </div>
                              </div>

                              <?php if($action != 'update'):?>
                              <div class="form-group"><label class="col-sm-2 control-label">登入密碼:</label>
                                <div class="col-sm-10"><input type="password" class="form-control" name="xpassword" value="<?=$list['xpassword']?>" placeholder="密碼" required></div>
                              </div>

                              <div class="form-group"><label class="col-sm-2 control-label">確認密碼:</label>
                                <div class="col-sm-10"><input type="password" class="form-control" name="Checkxpassword" placeholder="密碼" required></div>
                              </div>
                              <?php endif;?>

                              <!-- <div class="form-group"><label class="col-sm-2 control-label">大頭照:</label>
                                <div class="col-sm-10">
                                  <?php $field = 'xpic'; ?>
                                  <div class="col-sm-3">
                                    <?php if ($this->config->item('ckfinder_version')==2): ?>
                                    <input type="button" onclick="BrowseServer('<?=$field?>,false,true');" value="選擇檔案">
                                    <?php else: ?>
                                    <input type="button" onclick="generateCkfinder('<?=$field?>',false,true)" value="選擇檔案">
                                    <?php endif; ?>
                                    <input type="hidden" class="form-control" name="<?=$field?>">
                                    <span class="output"><?=$this->filemsg?></span>
                                  </div>
                                  <div class="col-sm-6">
                                    <a title="<?=$this->imageinfo[$field]?>"><small class="label label-primary"><i class="fa fa-search"></i> 格式</small></a>
                                    <?php if(isset($list[$field]) && $list[$field]):?>
                                    <a onclick="previewFile('<?=(isset($list[$field]))?$this->common->addroot($list[$field]):''?>')" title="預覽"><small class="label label-primary"><i class="fa fa-picture-o"></i> 預覽</small></a>
                                    <a onclick="deleteFile('<?=(isset($list['xaccount']))?$list['xaccount']:''?>','<?=$field?>')" title="刪除"><small class="label label-primary"><i class="fa fa-times-circle"></i> 刪除</small></a>
                                    <?php endif;?>
                                  </div>
                                </div>
                              </div> -->

                              <div class="form-group"><label class="col-sm-2 control-label">姓名:</label>
                                <div class="col-sm-10"><input type="text" class="form-control" name="xnickname" value="<?=$list['xnickname']?>" placeholder="姓名"></div>
                              </div>

                              <!-- <div class="form-group"><label class="col-sm-2 control-label">信箱:</label>
                                <div class="col-sm-10"><input type="email" class="form-control" name="xmail" value="<?=$list['xmail']?>" placeholder="信箱"></div>
                              </div> -->

                              <div class="form-group"><label class="col-sm-2 control-label">職稱:</label>
                                <div class="col-sm-10"><input type="text" class="form-control" name="xjobtitle" value="<?=$list['xjobtitle']?>" placeholder="職稱"></div>
                              </div>

                              <div class="form-group"><label class="col-sm-2 control-label">說明:</label>
                                <div class="col-sm-10"><input type="text" class="form-control" name="xextend" value="<?=$list['xextend']?>" placeholder="說明"></div>
                              </div>

                              <div class="form-group"><label class="col-sm-2 control-label">群組設定:</label>
                                <div class="col-sm-10">
                                    <select class="form-control m-b" name="GroupID" id="Group" required>
                                        <?php foreach ($group as $value): ?>
                                          <option value="<?=$value['pid']?>"><?=$value['xname']?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
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

<?php elseif($action == 'updatepsw'):?>

  <div class="row">
    <div class="col-lg-12">
      <div class="tabs-container">
          <ul class="nav nav-tabs">
              <li class="active"><a data-toggle="tab" href="#tab-1"> 更改密碼</a></li>
          </ul>
          <div class="tab-content">
              <div id="tab-1" class="tab-pane active">
                  <div class="panel-body">
                    <form class="form-horizontal" role="form" id="pswform">

                      <div class="row">
                          <div class="col-lg-12">
                            <?php if($selfaccount == $list['xaccount']):?>
                              <div class="form-group"><label class="col-sm-2 control-label">舊密碼:</label>
                                <div class="col-sm-10"><input type="password" class="form-control" name="Oldxpassword" placeholder="密碼" required></div>
                              </div>
                            <?php endif;?>

                            <div class="form-group"><label class="col-sm-2 control-label">新密碼:</label>
                              <div class="col-sm-10"><input type="password" class="form-control" name="Newxpassword" placeholder="密碼" required></div>
                            </div>

                            <div class="form-group"><label class="col-sm-2 control-label">確認密碼:</label>
                              <div class="col-sm-10"><input type="password" class="form-control" name="NewCheckxpassword" placeholder="密碼" required></div>
                            </div>
                          </div>
                      </div>

                      <?=$this->load->view('admin/module/btn',array('mbtype'=>'send','seturl'=>$this->indexPath),true)?>
                    </form>
                  </div>
              </div>
          </div>
      </div>
    </div>
  </div>

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
    if(i==0&&showMulti==true){options.columnDefs.push({data:null,render:function(b,a,c){return'<input type="checkbox" value="'+b.xaccount+'" name="checkbox[]">'},title:'<input type="checkbox" name="clickAll" id="clickAll">',orderable:false,targets:0,width:"2%",})};
    options.columnDefs.push({
      data: null,
      orderDataType: (xcolumn=='xpublish')?'dom-text':'', // 需修改
      type: 'string',
      render: function ( data, type, row ) { // 顯示資料值前，先處理資料值
        switch (xcolumn) {  // 需修改 // 每個欄位都需設定
          case 'xaccount':
            return '<a href="<?=$this->formPath?>/'+data.xaccount+'">'+data.xaccount+'</a>';
            break;
          case 'xpublish':
            var checked = ''; var disabled = 'disabled';
            if(data.xpublish == 'yes') { checked = 'checked'; disabled = ''; }
            return '<input type="radio" '+checked+' value="'+data.xpublish+'" '+disabled+'>';
            break;
          case 'xnickname':
            return data.xnickname;
            break;
          case 'GroupName':
            return data.GroupName;
            break;
          case '':
            return data.button;
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
  setTableOther();
});
}
</script>

<script>
function forminit() {
$(function() {
  // 初始值設定 // 須修改
  InitForm('checked', "xpublish", "<?php if(isset($list["xpublish"])) echo $list["xpublish"]; ?>");
  InitForm('selectval', "GroupID", "<?php if(isset($list["GroupID"])) echo $list["GroupID"]; ?>");

  // 表單驗證
  $("#createform").validate({
    rules: {
      xaccount:{required: true,minlength:4,strings: true},
      xpassword:{required: true,minlength:4},
      Checkxpassword:{required:true,equalTo:"input[name='xpassword']"},
      GroupID:{required:true},
    },
    messages: {
      xaccount:{required:"欄位必填",minlength:jQuery.validator.format("至少需要 {0} 字元")},
      xpassword:{required:"欄位必填",minlength:jQuery.validator.format("至少需要 {0} 字元")},
      Checkxpassword:{required:"欄位必填",equalTo:'密碼不相符'},
      GroupID:{required:"欄位必填"},
    },
    submitHandler: function(form) {
      var url = '<?=(isset($list['xaccount']))?site_url($this->indexPath."/save/".$list['xaccount']):site_url($this->indexPath."/save/".NULL);?>';
      $.post(url,$("#createform").serialize(),function(a){if(a.error){alert(a.error);return false}redirect(document.referrer)});
    }
  });

  // 表單驗證
  $("#pswform").validate({
    rules:{
      Oldxpassword:{required:true,minlength:4},
      Newxpassword:{required:true,minlength:4},
      NewCheckxpassword:{required:true,equalTo:"input[name='Newxpassword']"},
    },
    messages:{
      Oldxpassword:{required:"欄位必填",minlength:jQuery.validator.format("至少需要 {0} 字元")},
      Newxpassword:{required:"欄位必填",minlength:jQuery.validator.format("至少需要 {0} 字元")},
      NewCheckxpassword:{required:"欄位必填",equalTo:'密碼不相符'},
    },
    submitHandler: function(form) {
      var url = '<?=(isset($list['xaccount']))?site_url($this->indexPath."/savePassword/".$list['xaccount']):site_url($this->indexPath."/savePassword/".NULL);?>';
      $.post(url,$("#pswform").serialize(),function(a){if(a.error){alert(a.error);return false}redirect(document.referrer)});
    }
  });
});
}
</script>
