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

        <form class="form-horizontal" role="form" id="createform">

        <div class="row">
            <div class="col-lg-12">

              <div class="tabs-container">
                <ul class="nav nav-tabs"> <!--須修改-->
                    <li class="active"><a data-toggle="tab" href="#tab-1"> 細目編輯</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-2"> Og 預設圖</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-3"> GA追蹤碼</a></li>
                </ul>

                <div class="tab-content">
                  <div id="tab-1" class="tab-pane active"> <!--須修改-->
                    <div class="panel-body">
                      <fieldset class="form-horizontal">
                        <!-- <?=$this->load->view('admin/module/form',array('name'=>'主標','em'=>'xtitle','type'=>'title'),true)?> -->

                        <?php $dfNArray = array('favicon','apple-touch-icon'); $dfFArray = array('xfile1','xfile2'); $dfMArray = array('assets/img/favicon.png','assets/img/apple-icon-144x144.png');
                            foreach ($dfNArray as $key => $value):
                                $name = $value;
                                $em = $dfFArray[$key];
                        ?>
                        <div class="form-group"><label class="col-sm-2 control-label"><?=$name?>:</label>
                            <div class="col-sm-10">
                                <div class="col-sm-3">
                                    <?php if ($this->config->item('ckfinder_version')==2): ?>
                                    <input type="button" onclick="BrowseServer('<?=$em?>');" value="選擇檔案">
                                    <?php else: ?>
                                    <input type="button" onclick="generateCkfinder('<?=$em?>')" value="選擇檔案">
                                    <?php endif; ?>
                                    <input type="hidden" class="form-control" name="<?=$em?>">
                                    <span class="output"><?=$this->filemsg?></span>
                                </div>
                                <div class="col-sm-6">
                                    <a title="<?=$this->imageinfo[$em]?>"><small class="label label-primary"><i class="fa fa-search"></i> 格式</small></a>
                                    <?php if(isset($list[$em]) && $list[$em]):?>
                                    <a onclick="previewFile('<?=(isset($list[$em]))?$list[$em]:''?>')" title="預覽"><small class="label label-primary"><i class="fa fa-picture-o"></i> 預覽</small></a>
                                    <?php endif;?>
                                </div>
                                <div class="remark">
                                    <div class="col-sm-12">
                                        <label class="control-label text-danger">*檔案名稱 : <?=$dfMArray[$key]?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <!-- <?=$this->load->view('admin/module/order',array('multi'=>false),true)?> -->
                      </fieldset>
                    </div>
                  </div>

                  <div id="tab-2" class="tab-pane"> <!--須修改-->
                    <div class="panel-body">
                      <fieldset class="form-horizontal">

                        <?php foreach ($gaogArr as $key => $value):
                          $name = $value['xtitle'];
                          $em = 'xfile3_'.$value['xcode'];
                        ?>
                        <div class="form-group"><label class="col-sm-2 control-label"><?=$name?>:</label>
                            <div class="col-sm-10">
                                <div class="col-sm-3">
                                    <?php if ($this->config->item('ckfinder_version')==2): ?>
                                    <input type="button" onclick="BrowseServer('<?=$em?>');" value="選擇檔案">
                                    <?php else: ?>
                                    <input type="button" onclick="generateCkfinder('<?=$em?>')" value="選擇檔案">
                                    <?php endif; ?>
                                    <input type="hidden" class="form-control" name="<?=$em?>">
                                    <span class="output"><?=$this->filemsg?></span>
                                </div>
                                <div class="col-sm-6">
                                    <a title="<?=$this->imageinfo[$em]?>"><small class="label label-primary"><i class="fa fa-search"></i> 格式</small></a>
                                    <?php if(isset($list[$em]) && $list[$em]):?>
                                    <a onclick="previewFile('<?=(isset($list[$em]))?$list[$em]:''?>')" title="預覽"><small class="label label-primary"><i class="fa fa-picture-o"></i> 預覽</small></a>
                                    <?php endif;?>
                                </div>
                                <div class="remark">
                                    <div class="col-sm-12">
                                        <label class="control-label text-danger">*檔案名稱 : <?=$value['xfile3']?></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php endforeach; ?>

                      </fieldset>
                    </div>
                  </div>

                  <div id="tab-2" class="tab-pane"> <!--須修改-->
                    <div class="panel-body">
                      <fieldset class="form-horizontal">
                        <?php foreach ($gaogArr as $key => $value):
                          $name = $value['xtitle'];
                          $em = 'xfile3_'.$value['xcode'];
                        ?>
                        <div class="form-group"><label class="col-sm-2 control-label"><?=$name?>:</label>
                            <div class="col-sm-10">
                                <div class="col-sm-3">
                                    <?php if ($this->config->item('ckfinder_version')==2): ?>
                                    <input type="button" onclick="BrowseServer('<?=$em?>');" value="選擇檔案">
                                    <?php else: ?>
                                    <input type="button" onclick="generateCkfinder('<?=$em?>')" value="選擇檔案">
                                    <?php endif; ?>
                                    <input type="hidden" class="form-control" name="<?=$em?>">
                                    <span class="output"><?=$this->filemsg?></span>
                                </div>
                                <div class="col-sm-6">
                                    <a title="<?=$this->imageinfo[$em]?>"><small class="label label-primary"><i class="fa fa-search"></i> 格式</small></a>
                                    <?php if(isset($list[$em]) && $list[$em]):?>
                                    <a onclick="previewFile('<?=(isset($list[$em]))?$list[$em]:''?>')" title="預覽"><small class="label label-primary"><i class="fa fa-picture-o"></i> 預覽</small></a>
                                    <?php endif;?>
                                </div>
                                <div class="remark">
                                    <div class="col-sm-12">
                                        <label class="control-label text-danger">*檔案名稱 : <?=$value['xfile3']?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                      </fieldset>
                    </div>
                  </div>

                  <div id="tab-3" class="tab-pane"> <!--須修改-->
                    <div class="panel-body">
                      <fieldset class="form-horizontal">
                        <?php foreach ($gaogArr as $key => $value):
                          $name = $value['xtitle'];
                          $em = 'xga_'.$value['xcode'];
                          $xgacode = $value['xgacode'];
                        ?>
                        <div class="form-group"><label class="col-sm-2 control-label"><?=$name?>:</label>
                            <div class="col-sm-10">
                              <input type="text" class="form-control" name="<?=$em?>" value="<?=$xgacode?>" placeholder="範例:UA-12345678-1">
                            </div>
                        </div>
                        <?php endforeach; ?>
                      </fieldset>
                    </div>
                  </div>

                </div>
              </div>
            </div>
        </div>

        <div class="btn-fixed-area">
          <div>
            <button class="btn btn-lg btn-primary" type="submit">確認送出</button>
          </div>
        </div>
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
       $.post(url,$("#createform").serialize(),function(a){if(a.error){alert(a.error);return false}location.reload();});

      }
    });
  });
}
</script>
<script>
function forminit() {
}
</script>
<script>function importform(){$("#uploadform").submit(function(a){$('input[name="userfile"]').val()||(a.preventDefault(),alert("\u8acb\u9078\u64c7\u6a94\u6848"))})};</script>
