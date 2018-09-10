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

          <div class="user-ctrl-nav padding"> <!--須修改-->
            <?=generatebutton('link_self_click', 'btn btn-info', 'javascript:batchItem("status")', $permission['UpdateAction'], '已處理');?>
            <?=generatebutton('link_self_click', 'btn btn-default', 'javascript:batchItem("_status")', $permission['UpdateAction'], '待處理');?>
            <?=generatebutton('link_self_click', 'btn btn-info', 'javascript:batchItem("mark")', $permission['UpdateAction'], 'Mark');?>
            <?=generatebutton('link_self_click', 'btn btn-default', 'javascript:batchItem("_mark")', $permission['UpdateAction'], 'UnMark');?>
            <?=generatebutton('link_self_click', 'btn btn-danger', 'javascript:batchItem("delete")', $permission['DeleteAction'], '刪除');?>
            <a class="btn btn-primary" href="<?=$this->indexPath?>/export" target="_blank" id="export">匯出</a>
          </div>

          <div class="table-responsive padding">
            <!--列表-->
            <table class="table table-striped table-bordered table-hover dataTables-example" id="editable"></table>
          </div>

          <?php if($this->showSearching == true):?>
          <div class="search-area padding">
            <div class="row">
                <div class="col-lg-12">
                  <!--個別搜尋-->
                  <!-- <div class="col-lg-2 m-b">
                    <select class="column_filter form-control">
                    <?php for ($i=0; $i < count($this->searchTitle); $i++):?>
                      <option value="<?=$this->searchColumn[$i]?>"><?=$this->searchTitle[$i]?></option>
                    <?php endfor?>
                    </select>
                  </div>
                  <div class="col-lg-4 m-b">
                    <input class="column_filter form-control" type="text" id="col_filter">
                  </div> -->
                </div>
                <div class="col-lg-12">
                  <div class="col-lg-2 m-b">
                    <select class="column_statusfilter form-control" onchange="">
                      <option value="">所有狀態</option>
                      <option value="yes">已處理</option>
                      <option value="no">待處理</option>
                    </select>
                  </div>
                  <div class="col-lg-2 m-b">
                    <select class="column_markfilter form-control" onchange="">
                      <option value="">未指定</option>
                      <option value="yes">重要</option>
                      <option value="no">不重要</option>
                    </select>
                  </div>
                  <?php if ($this->showdateSearching == true): ?>
                  <div class="form-group col-lg-6 m-b">
                      <div class="input-daterange input-group">
                          <input class="input-sm form-control " type="date" id="min" name="min">
                          <span class="input-group-addon">到</span>
                          <input class="input-sm form-control " type="date" id="max" name="max">
                      </div>
                  </div>
                  <?php endif; ?>
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
                                    <label class="col-sm-2 control-label">已處理:</label>
                                    <div class="col-sm-10">
                                      <div class="btn-group" data-toggle="buttons">
                                        <label class="btn btn-default"><input type="radio" value="yes" name="xstatus">是</label>
                                        <label class="btn btn-default"><input type="radio" value="no" name="xstatus">否</label>
                                      </div>
                                    </div>
                                  </div>

                                  <div class="form-group">
                                    <label class="col-sm-2 control-label">Mark:</label>
                                    <div class="col-sm-10">
                                      <div class="btn-group" data-toggle="buttons">
                                        <label class="btn btn-default"><input type="radio" value="yes" name="xmark">是</label>
                                        <label class="btn btn-default"><input type="radio" value="no" name="xmark">否</label>
                                      </div>
                                    </div>
                                  </div>

                                  <div class="form-group"><label class="col-sm-2 control-label">附註:</label>
                                    <div class="col-sm-10"><textarea class="form-control" rows='<?=$this->textrow?>' name="xanote"><?=(isset($list['xanote']))?$list['xanote']:''?></textarea></div>
                                  </div>

                                  <div class="form-group"><label class="col-sm-2 control-label">問題類型:</label>
                                    <div class="col-sm-10"><label class="control-label"><?=(isset($list['xsubjectname']))?$list['xsubjectname']:''?></label></div>
                                  </div>

                                  <div class="form-group"><label class="col-sm-2 control-label">收件人E-mail:</label>
                                    <div class="col-sm-10">
                                      <ul class="tag-list" style="padding: 0">
                                        <?php $xsubjectmail = explode(',',$list['xsubjectmail'])?>
                                        <?php foreach ($xsubjectmail as $value): ?>
                                          <li><a><i class=""></i> <?=$value?></a></li>
                                        <?php endforeach; ?>
                                      </ul>
                                    </div>
                                  </div>

                                  <div class="form-group"><label class="col-sm-2 control-label">聯絡姓名:</label>
                                    <div class="col-sm-10"><label class="control-label"><?=(isset($list['xname']))?$list['xname']:''?></label></div>
                                  </div>

                                  <div class="form-group"><label class="col-sm-2 control-label">公司名稱:</label>
                                    <div class="col-sm-10"><label class="control-label"><?=(isset($list['xcompany']))?$list['xcompany']:''?></label></div>
                                  </div>

                                  <div class="form-group"><label class="col-sm-2 control-label">聯絡電話:</label>
                                    <div class="col-sm-10"><label class="control-label"><?=(isset($list['xtel']))?$list['xtel']:''?></label></div>
                                  </div>

                                  <div class="form-group"><label class="col-sm-2 control-label">聯絡信箱:</label>
                                    <div class="col-sm-10"><label class="control-label"><?=(isset($list['xmail']))?$list['xmail']:''?></label></div>
                                  </div>

                                  <div class="form-group"><label class="col-sm-2 control-label">留言訊息:</label>
                                    <div class="col-sm-10"><div><?=nl2br($list['xmessage'])?></div></div>
                                  </div>

                                  <!-- <div class="form-group"><label class="col-sm-2 control-label">修改日期:</label>
                                    <div class="col-sm-10"><label class="control-label"><?=($list['xmodify'])? $list['xmodify']:'無'?></label></div>
                                  </div> -->

                                  <div class="form-group"><label class="col-sm-2 control-label">建立日期:</label>
                                    <div class="col-sm-10"><label class="control-label"><?=(isset($list['xcreate']))?$list['xcreate']:''?></label></div>
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
          orderDataType: (xcolumn=='xstatus' || xcolumn=='xmark')?'dom-text':'', // 需修改
          type: 'string',
          render: function ( data, type, row ) { // 顯示資料值前，先處理資料值
            switch (xcolumn) {  // 需修改 // 每個欄位都需設定
              case 'xname':
                return '<a href="<?=$this->formPath?>/'+data.pid+'">'+data.xname+'</a>';
                break;
              case 'xstatus':
                var checked = ''; var disabled = 'disabled';
                if(data.xstatus == 'yes') { checked = 'checked'; disabled = ''; }
                return '<input type="radio" '+checked+' value="'+data.xstatus+'" '+disabled+'>';
                break;
              case 'xmark':
                var checked = ''; var disabled = 'disabled';
                if(data.xmark == 'yes') { checked = 'checked'; disabled = ''; }
                return '<input type="radio" '+checked+' value="'+data.xmark+'" '+disabled+'>';
                break;
              case 'xcompany':
                return data.xcompany;
                break;
              case 'xtel':
                return data.xtel;
                break;
              case 'xmodifyuser':
                return data.xmodifyuser;
                break;
              case 'xcreate':
                return data.xcreate;
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
      // 日期選單式欄位搜尋
      dateInit(table,7); // 須修改 // 對應列表欄位第N欄(N=0~)
      // 狀態選單式欄位搜尋
      $('select.column_statusfilter').on( 'change', function () {
         $('.dataTables-example').DataTable().column('xstatus:name')
         .search(this.value)
         .draw();
         exporthref();
      });
      $('select.column_markfilter').on( 'change', function () {
         $('.dataTables-example').DataTable().column('xmark:name')
         .search(this.value)
         .draw();
         exporthref();
      });

    });
  }
  // 組合匯出url
  function exporthref() {
    var status = $('select.column_statusfilter').val();
    var mark = $('select.column_markfilter').val();
    var start = $('input[name="min"]').val();
    var end = $('input[name="max"]').val();
    var url = '<?=$this->indexPath.'/export'?>'+'?start='+start+'&end='+end+'&status='+status+'&mark='+mark;
    $('#export').attr('href',url);
  }
  // 日期過濾初始化
  function dateInit(table,xcolumn) {
    var xcolumn = xcolumn || 0;
    generateDatepicker('min');
    generateDatepicker('max');
    $('#min').change( function() { table.draw();
      exporthref();
    });
    $('#max').change( function() { table.draw();
      exporthref();
    });

    $.fn.dataTable.ext.search.push(
      function( settings, searchData, index, rowData, counter ) {
          var min = $('#min').val()
          var max = $('#max').val();
          var date = searchData[xcolumn] || ''; // 讀取列表第幾欄
          if(min) min+=' 00:00:00';
          if(max) max+=' 23:59:59';
          if ( ( !min && !max ) ||
               ( !min && date <= max ) ||
               ( min <= date && !max ) ||
               ( min <= date && date <= max )
          ) {
              return true;
          }
          return false;
      }
    );
  }
</script>

<script>
  function forminit() {
    $(function() {
      // 初始值設定 // 須修改
      InitForm('checked', "xstatus", "<?=(isset($list["xstatus"]))?$list["xstatus"]:'yes'?>");
      InitForm('checked', "xmark", "<?=(isset($list["xmark"]))?$list["xmark"]:'yes'?>");

      // 表單驗證
      $("#createform").validate({
        rules: {
          xname:{required:true},
        },
        messages: {
          xname:{required:"欄位必填"},
        },
        submitHandler: function(form) {
          var url = '<?=(isset($list['pid']))?site_url($this->indexPath."/save/".$list['pid']):site_url($this->indexPath."/save/".NULL);?>';
          $.post(url,$("#createform").serialize(),function(a){if(a.error){alert(a.error);return false}redirect(document.referrer)});
        }
      });
    });
  }
</script>
