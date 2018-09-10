<!--麵包屑-->
<div class="row wrapper page-heading">
    <div class="col-lg-10">
        <h2><?=$permission['xname']?></h2>
        <ol class="breadcrumb">
            <?=$this->navPath?>
            <?php if($action == 'update'):?>
            (<?=$nav?> )</li>
            <?php endif;?>
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
            <?php elseif($action == 'update'):?>編輯
            <?php endif;?>
          </h5>
        </div>

        <div class="ibox-content">

<?php if($action == 'index'):?>

        <div class="tabs-container">
              <ul class="nav nav-tabs" id="menu1">
                <?php $count = -1;
                  foreach ($menu as $value): $count++;
                  $xcode = $value['xcode'];

                  if($tabactive==$xcode) $active = 'active';
                  else if(!$tabactive && $count==0) $active = 'active';
                  else $active = '';
                ?>
                  <li class="<?=$active?>"><a data-toggle="tab" href="#tab2-<?=$xcode?>"><?=$value['xtitle']?></a></li>
                <?php endforeach; ?>
              </ul>
              <div class="tab-content">
                  <?php $count = -1; foreach ($menu as $value): $count++;
                    $xcode = $value['xcode'];
                    if($tabactive==$xcode) $active = 'active';
                    else if(!$tabactive && $count==0) $active = 'active';
                    else $active = '';
                  ?>
                  <div id="tab2-<?=$xcode?>" class="tab-pane <?=$active?>">
                      <div class="panel-body">

                        <div class="user-ctrl-nav">
                         <div class="row">
                             <div class="col-md-4">
                               <div class="nestable-menu">
                                   <?=generatebutton('normal', 'btn btn-white', 'javascript:sortable_create(0,"'.$xcode.'")', $permission['CreateAction'], '新增');?>
                                   <?=generatebutton('normal', 'btn btn-white', 'javascript:sortable_sort("'.$xcode.'")', $permission['UpdateAction'], '更改排序');?>
                               </div>
                             </div>
                         </div>
                        </div>

                         <div class="row">
                           <div class="col-lg-12">
                             <div class="ibox-content">
                               <div class="dd">
                                 <ol class="dd-list sortable" id="sortable_<?=$xcode?>"></ol>
                               </div>
                             </div>
                           </div>
                         </div>

                      </div>
                  </div>
                  <?php endforeach; ?>

              </div>
        </div>

<?php elseif($action == 'update'):?>

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
                                  <?php if ($this->session->taba!='module' && $this->session->taba!='sys'): ?>
                                  <div class="form-group">
                                    <label class="col-sm-2 control-label">是否多層級:</label>
                                    <div class="col-sm-10">
                                      <div class="btn-group" data-toggle="buttons">
                                        <label class="btn btn-default"><input type="radio" value="yes" name="xmulti">是</label>
                                        <label class="btn btn-default"><input type="radio" value="no" name="xmulti">否</label>
                                      </div>
                                    </div>
                                  </div>
                                  <?php else: ?>
                                  <input type="hidden" name="xmulti" value="<?=(isset($list['xmulti']))?$list['xmulti']:'no'?>">
                                  <?php endif; ?>

                                  <?php if($list['preid'] == 0): ?>
                                  <?php if ($this->session->taba!='module' && $this->session->taba!='sys'): ?><!-- http://fontawesome.io/icons/ -->
                                  <!-- <div class="form-group"><label class="col-sm-2 control-label">圖標:</label>
                                    <div class="col-sm-10">
                                      <select class="form-control m-b fa" name="xicon">
                                          <option value="">請選擇</option>
                                          <option value="fa fa-home">&#xf015;</option>
                                          <option value="fa fa-align-justify">&#xf039;</option>
                                          <option value="fa fa-sitemap">&#xf0e8;</option>
                                          <option value="fa fa-th-large">&#xf009;</option>
                                          <option value="fa fa-user">&#xf007;</option>
                                          <option value="fa fa-edit">&#xf044;</option>
                                          <option value="fa fa-suitcase">&#xf0f2;</option>
                                          <option value="fa fa-diamond">&#xf219;</option>
                                      </select>
                                    </div>
                                  </div> -->
                                  <?php endif; ?>
                                  <?php endif;?>

                                  <div class="form-group"><label class="col-sm-2 control-label">名稱:</label>
                                    <div class="col-sm-10"><input type="text" class="form-control" name="xname" value="<?=$list['xname']?>" placeholder="名稱" required></div>
                                  </div>

                                  <!-- <div class="form-group"><label class="col-sm-2 control-label">語系:</label>
                                    <div class="col-sm-10">
                                      <select class="form-control" name="xlang">
                                        <option value="0">語系/代號</option>
                                        <?php foreach ($menu as $value): ?>
                                        <option value="<?=$value['xcode']?>"><?=$value['xtitle']?></option>
                                        <?php endforeach; ?>
                                      </select>
                                    </div>
                                  </div> -->
                                  <input type="hidden" name="xlang" value="<?=$list['xlang']?>">

                                  <div class="form-group"><label class="col-sm-2 control-label">頁面:</label>
                                    <div class="col-sm-10"><input type="text" class="form-control" name="xpage" value="<?=$list['xpage']?>" placeholder="頁面"></div>
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
      // 樹狀層級 // 限制層級數
      var maxDepth = '<?=$this->maxDepth?>';
      var langArray = '<?=isset($langArray)?$langArray:''?>';
      array = langArray.split(',');
      for (var i = 0; i < array.length; i++) {
        if(lang = array[i]) {
          generateNestedSortableNotAllowed(maxDepth,lang);
        }
      }
    });

    $("ul.nav-tabs#menu1 > li")
    .on("click", function() { var data = '';
      var href = $(this).find('a').attr('href');
      var array = href.split('#tab2-');
      if(array.length>1) data = array[1];
      var url = "<?php echo site_url("$this->indexPath/recordtab"); ?>"+'/'+data;
      $.post(url,function(data){console.log(data);});
    });
  }

  // 載入 nestedSortable 資料
  function sortable_read(maxDepth,lang) {
    var url = "<?php echo site_url("$this->indexPath/readNode"); ?>"+'/'+lang;
    $.post(url, function(data){
      $('#sortable_'+lang).html(data.list);
    });
  }

  // 新增節點
  function sortable_create(id,type) {
    $.post("<?php echo site_url("$this->indexPath/NodeStyle/json"); ?>", function(data){
      // 輸入框
      var input = "<input type='text' name='addNode' onchange='javascript:changeInput("+id+")'>"
      // 判斷是否有原本就有<ol>

      if(id == 0) $('ol.sortable#sortable_'+type).append("<li class='"+data['li']+"'><div class='"+data['handle']+"'>"+input+"</div></li>");
      else if($('#list_'+id).find('ol').length == 0) $('#list_'+id).append("<ol class='"+data['ol']+"'><li class='"+data['li']+"'><div class='"+data['handle']+"'>"+input+"</div></li></ol>");
      else $('#list_'+id).find('ol').eq(0).append("<li class='"+data['li']+"'><div class='"+data['handle']+"'>"+input+"</div></li>");

      // focus 新增欄位
      $("input[name='addNode']").focus();

      // 未輸入時，重新整理
      $("input[name='addNode']").blur(function(){
        // 重新載入
        sortable_read(0,type);
      });
    });
  }

  // 有更改輸入框
  function changeInput(id) {
    var inputval = $("input[name='addNode']").val();
    if(inputval) {
      // 存入資料庫
      $.post("<?php echo site_url("$this->indexPath/createNode"); ?>", { inputval: inputval, id: id }, function(data){
        // 重新載入
        redirect('<?=base_url().$this->indexPath?>');
      });
    }
  }

  // 更新節點
  function sortable_update(id) {
    redirect('<?=base_url().$this->formPath?>/'+id);
  }

  // 刪除節點
  function sortable_delete(id) {
    if(confirm('確定執行?')){
      $.post("<?php echo site_url("$this->indexPath/deleteNode"); ?>", { id: id }, function(data){
        if(data.error) {
          alert(data.error);
          return false;
        }
        // 重新載入
        redirect('<?=base_url().$this->indexPath?>');
      });
    }
  }

  // 更改節點排序
  function sortable_sort(type) {
    sortdata = $('ol.sortable#sortable_'+type).nestedSortable('toHierarchy', {startDepthCount: 0});
    $.post("<?php echo site_url("$this->indexPath/sortNode"); ?>", { data: sortdata }, function(data){
      // 重新載入
      redirect('<?=base_url().$this->indexPath?>');
    });
  }

</script>

<script>
  function forminit() {
    $(function() {
      // 初始值設定 // 須修改
      InitForm('checked', "xmulti", "<?=(isset($list["xmulti"]))?$list["xmulti"]:'no'?>");
      InitForm('selectval', "xicon", "<?=(isset($list["xicon"]))?$list["xicon"]:''?>");
      InitForm('selectval', "xlang", "<?=(isset($list["xlang"]))?$list["xlang"]:''?>");

      // 表單驗證
      $("#createform").validate({
        rules: {
          xname:{required:true},xlevel:{required: true},
        },
        messages: {
          xname:{required:"欄位必填"},xlevel:{required:"欄位必填"},
        },
        submitHandler: function(form) {
          var url = '<?=(isset($list['pid']))?site_url($this->indexPath."/updateNode/".$list['pid']):site_url($this->indexPath."/save/".NULL);?>';
          $.post(url,$("#createform").serialize(),function(a){if(a.error){alert(a.error);return false}redirect(document.referrer)});
        }
      });
    });
  }
</script>
