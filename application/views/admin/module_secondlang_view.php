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
    </div>

    <div class="ibox-content padding">

<?php if($action == 'index'):?>

      <form class="form-horizontal" role="form" id="createform">

        <div class="row">
            <div class="col-lg-12">
              <div class="col-sm-6">
                <div class="form-group"><label class="col-sm-2 control-label">原語系</label>
                </div>
                <hr>

                <div class="form-group"><label class="col-sm-4 control-label">語系名稱:</label>
                  <div class="col-sm-8"><input type="text" class="form-control" name="oldlangname" value="<?=(isset($list['oldlangname']))?$list['oldlangname']:''?>" placeholder="tw" required></div>
                </div>

                <div class="form-group"><label class="col-sm-4 control-label">資料表名稱開頭:</label>
                  <div class="col-sm-8"><input type="text" class="form-control" name="oldprefixname" value="<?=(isset($list['oldprefixname']))?$list['oldprefixname']:''?>" placeholder="tw_" required></div>
                </div>
              </div>

              <div class="col-sm-6">
                <div class="form-group"><label class="col-sm-2 control-label">新語系</label>
                </div>
                <hr>

                <div class="form-group"><label class="col-sm-4 control-label">語系名稱:</label>
                  <div class="col-sm-8"><input type="text" class="form-control" name="newlangname" value="<?=(isset($list['newlangname']))?$list['newlangname']:''?>" placeholder="en" required></div>
                </div>

                <div class="form-group"><label class="col-sm-4 control-label">資料表名稱開頭:</label>
                  <div class="col-sm-8"><input type="text" class="form-control" name="newprefixname" value="<?=(isset($list['newprefixname']))?$list['newprefixname']:''?>" placeholder="en_" required></div>
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
<?php endif;?>

    </div>
  </div>
</div>
</div>
</div>

<script>
function listinit() {
$(function() {
  // 表單驗證
  $("#createform").validate({
      rules: {
        oldprefixname: {required: true},newprefixname: {required: true},
        oldlangname: {required: true},newlangname: {required: true},
      },
      messages: {
        oldprefixname: {required: "欄位必填"},newprefixname: {required: "欄位必填"},
        oldlangname: {required: "欄位必填"},newlangname: {required: "欄位必填"},
       },
      submitHandler: function(form) {
       var url = '<?php (isset($list['pid'])) ? $id = $list['pid'] : $id = NULL; echo site_url($this->indexPath."/save/".$id); ?>';
       $.post(url, $('#createform').serialize(), function(data){ $('form')[0].reset();
         if(data.error) {alert(data.error); return false;}
         if(data.success) {alert(data.success); redirect('<?=base_url().$this->indexPath?>');}
      });
     }
  });
});
}
</script>
