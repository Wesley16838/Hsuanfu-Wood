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
            <?php endif;?>
          </h5>
        </div>

        <div class="ibox-content">

<?php if($action == 'index'):?>

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
                                  <div class="form-group">
                                    <label class="col-sm-2 control-label">是否使用SMTP API:</label>
                                    <div class="col-sm-10">
                                      <div class="btn-group" data-toggle="buttons">
                                        <label class="btn btn-default"><input type="radio" value="yes" name="xsmtpapi">是</label>
                                        <label class="btn btn-default"><input type="radio" value="no" name="xsmtpapi">否</label>
                                      </div>
                                    </div>
                                  </div>

                                  <div class="form-group"><label class="col-sm-2 control-label">SMTP 伺服器:</label>
                                    <div class="col-sm-10"><input type="text" class="form-control" name="xserver" value="<?=(isset($list['xserver']))?$list['xserver']:''?>"></div>
                                  </div>

                                  <div class="form-group"><label class="col-sm-2 control-label">SMTP 埠:</label>
                                    <div class="col-sm-10"><input type="text" class="form-control" name="xport" value="<?=(isset($list['xport']))?$list['xport']:''?>"></div>
                                  </div>

                                  <div class="form-group">
                                    <label class="col-sm-2 control-label">是否驗證:</label>
                                    <div class="col-sm-10">
                                      <div class="btn-group" data-toggle="buttons">
                                        <label class="btn btn-default"><input type="radio" value="yes" name="xauth">是</label>
                                        <label class="btn btn-default"><input type="radio" value="no" name="xauth">否</label>
                                      </div>
                                    </div>
                                  </div>

                                  <div class="form-group"><label class="col-sm-2 control-label">SMTP 用戶名:</label>
                                    <div class="col-sm-10"><input type="text" class="form-control" name="xusername" value="<?=(isset($list['xusername']))?$list['xusername']:''?>"></div>
                                  </div>

                                  <div class="form-group"><label class="col-sm-2 control-label">SMTP 密碼:</label>
                                    <div class="col-sm-10"><input type="password" class="form-control" name="xpassword" value="<?=(isset($list['xpassword']))?$list['xpassword']:''?>"></div>
                                  </div>

                                  <div class="form-group"><label class="col-sm-2 control-label">SMTP 寄發帳號:</label>
                                    <div class="col-sm-10"><input type="text" class="form-control" name="xfrom" value="<?=(isset($list['xfrom']))?$list['xfrom']:''?>"></div>
                                  </div>

                                  <div class="form-group"><label class="col-sm-2 control-label">SMTP 寄信名稱:</label>
                                    <div class="col-sm-10"><input type="text" class="form-control" name="xfromname" value="<?=(isset($list['xfromname']))?$list['xfromname']:''?>"></div>
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
          orderDataType: (xcolumn=='xsmtpapi')?'dom-text':'', // 需修改
          type: 'string',
          render: function ( data, type, row ) { // 顯示資料值前，先處理資料值
            switch (xcolumn) {  // 需修改 // 每個欄位都需設定
              case 'xserver':
                return '<a href="<?=$this->formPath?>/'+data.pid+'">'+data.xserver+'</a>';
                break;
              case 'xsmtpapi':
                var checked = ''; var disabled = 'disabled';
                if(data.xsmtpapi == 'yes') { checked = 'checked'; disabled = ''; }
                return '<input type="radio" '+checked+' value="'+data.xsmtpapi+'" '+disabled+'>';
                break;
              default:
                return data;
                break;
            }
          },
          name:tableColumn[i],title:tableTitle[i],orderable:Boolean(tableSortValue[i]),targets:(showMulti==true)?i+1:i,width:tableHeadSize[i],
        });
      }

      // 初始化，表格套件
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
      InitForm('checked', "xsmtpapi", "<?=(isset($list["xsmtpapi"]))?$list['xsmtpapi']:'yes' ?>");
      InitForm('checked', "xauth", "<?=(isset($list["xauth"]))?$list['xauth']:'yes' ?>");

      // 表單驗證
      $("#createform").validate({
        rules: {
          xtitle:{required: true},
        },
        messages: {
          xtitle:{required: "欄位必填"},
        },
        submitHandler: function(form) {
         var url = '<?=(isset($list['pid']))?site_url($this->indexPath."/save/".$list['pid']):site_url($this->indexPath."/save/".NULL);?>';
         $.post(url,$("#createform").serialize(),function(a){if(a.error){alert(a.error);return false}redirect(document.referrer)});
        }
      });
    });
  }
</script>
