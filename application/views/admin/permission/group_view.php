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
            <?php endif;?>
          </h5>
        </div>

        <div class="ibox-content">

<?php if($action == 'index'):?>

          <div class="user-ctrl-nav padding">
            <?=generatebutton('link_self_href', 'btn btn-primary', $this->formPath, $permission['CreateAction'], '新增');?>
            <?=generatebutton('link_self_click', 'btn btn-danger', 'javascript:batchItem("delete")', $permission['DeleteAction'], '刪除');?>
          </div>

          <div class="table-responsive padding">
            <table class="table table-striped table-bordered table-hover dataTables-example sorted_table" id="editable">
              <thead>
              <tr>
                  <th><input type="checkbox" id="clickAll"></th>
                  <th>帳號</th>
                  <th>說明</th>
                  <th>權限大小</th>
              </tr>
              </thead>
              <tbody>
                <?php foreach($data as $value): ?>
                <tr class="gradeX" data-id="<?=$value['pid']?>">
                    <td><input type="checkbox" value="<?=$value['pid']?>" name="checkbox[]"></td>
                    <td><a href="<?=$this->formPath."/".$value['pid']?>"><?=$value['xname']?></a></td>
                    <td><?=$value['xextend']?></td>
                    <td><?=$value['xlevel']?><?php if($value['xlevel'] == 1):?> : 最高權限者 <?php endif;?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

<?php elseif($action == 'create' || $action == 'update'):?>

          <form class="form-horizontal" role="form" id="createform">

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
                                  <div class="form-group"><label class="col-sm-2 control-label">帳號:</label>
                                    <div class="col-sm-10"><input type="text" class="form-control" name="xname" value="<?=$list['xname']?>" placeholder="帳號" required></div>
                                  </div>

                                  <div class="form-group"><label class="col-sm-2 control-label">權限大小:</label>
                                    <div class="col-sm-10"><input type="number" class="form-control" name="xlevel" value="<?=$list['xlevel']?>" required></div>
                                  </div>

                                  <div class="form-group"><label class="col-sm-2 control-label">說明:</label>
                                    <div class="col-sm-10"><input type="text" class="form-control" name="xextend" value="<?=$list['xextend']?>" placeholder="說明"></div>
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
      var table = $('.dataTables-example').DataTable({
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [],
        paging: false, // 隱藏分頁
        searching: false, // 隱藏search bar
        info: false, // 隱藏介紹
        language: {
          emptyTable: "尚未有任何資料"
        },
        order: [], // 不預設排序
        columnDefs : [
          { "orderable": false, "targets": 0 } // 第一個欄位取消排序功能
        ],
      });
      setTableOther();
    });
  }
</script>

<script>
  function forminit() {
    $(function() {
      // 表單驗證
      $("#createform").validate({
        rules: {
          xname:{required:true},xlevel:{required: true},
        },
        messages: {
          xname:{required:"欄位必填"},xlevel:{required:"欄位必填"},
        },
        submitHandler: function(form) {
          var url = '<?=(isset($list['pid']))?site_url($this->indexPath."/save/".$list['pid']):site_url($this->indexPath."/save/".NULL);?>';
          $.post(url,$("#createform").serialize(),function(a){if(a.error){alert(a.error);return false}redirect(document.referrer)});
        }
      });
    });
  }
</script>
