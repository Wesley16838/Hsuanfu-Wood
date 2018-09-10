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
              case 'xadmin':
                return data.xadmin;
                break;
              case 'xpage':
                return data.xpage;
                break;
              case 'xaction':
                return data.xaction;
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
      var table = $('.dataTables-example').DataTable(options);

      // 紀錄目前page
      table.on("page.dt",function(){var a=table.page.info();$.post("<?=site_url($this->indexPath.'/recordPage');?>",{page:(a.page+1)},function(b){})});
      // setTableSort();
      setTableOther();
    });
  }
</script>

<script>
  function forminit() {
  }
</script>
