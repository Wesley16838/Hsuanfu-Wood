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
              <?php foreach ($this->langArray as $key => $value):
                $name = $value['name'];
                $active = ($key==0)?'active':'';
              ?>
              <li class="<?=$active?>"><a data-toggle="tab" href="#tab-<?=$key+1?>"> <?=$name?></a></li>
              <?php endforeach; ?>
          </ul>
          <div class="tab-content">
            <?php foreach ($this->langArray as $key => $value):
              $lang = $value['lang'];
              $active = ($key==0)?'active':'';
            ?>
            <div id="tab-<?=$key+1?>" class="tab-pane <?=$active?>"> <!--須修改-->
              <div class="panel-body">
                <fieldset class="form-horizontal">
                  <div class="form-group">
                    <div class="col-sm-12">
                      <textarea class="form-control" name="x<?=$lang?>" rows="30"><?=read_file('locales/'.$lang.'.json');?></textarea>
                    </div>
                  </div>
                </fieldset>
              </div>
            </div>
            <?php endforeach; ?>
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

  var LArray = [], LNArray = [];
  <?php foreach ($this->langArray as $key => $value):
    $name = $value['name'];
    $lang = $value['lang'];
  ?>
  LNArray.push("<?=$name?>");
  LArray.push("<?=$lang?>");
  <?php endforeach; ?>

  // 表單驗證
  $("#createform").validate({
      rules: {
        xtitle:{required: true},xpostdate:{date: true},duedate:{date:true,dateAfter:"input[name='xpostdate']"},xurltitle:{strings: true},
      },
      messages: {
        xtitle:{required: "欄位必填"},xpostdate:{date:"欄位須為日期格式yyyy-mm-dd"},xduedate:{date:"欄位須為日期格式yyyy-mm-dd",dateAfter:"下刊日期須大於發佈日期"},
      },
      submitHandler: function(form) {
       for (var i = 0; i < LArray.length; i++) {
        bool = checkjson(LArray[i],LNArray[i]);
        if(bool) {alert(bool);return;}
       }
       var url = '<?=(isset($list['pid']))?site_url($this->indexPath."/save/".$list['pid']):site_url($this->indexPath."/save/".NULL);?>';
       $.post(url,$("#createform").serialize(),function(a){if(a.error){alert(a.error);return false}location.reload();});
     }
  });

});
}
function checkjson(em,label) {
  try { var theJson = jQuery.parseJSON($("textarea[name='x"+em+"']").val()); }
  catch (e) { return "錯誤的 Json 格式 : "+label; }
}
</script>
