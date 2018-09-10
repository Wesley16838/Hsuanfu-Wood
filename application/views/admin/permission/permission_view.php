<!--麵包屑-->
<div class="row wrapper page-heading">
<div class="col-lg-10">
    <h2><?=$permission['xname']?></h2>
    <ol class="breadcrumb">
        <?=$this->navPath?>
    </ol>
</div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
<div class="row">
<div class="col-lg-12">
  <div class="ibox float-e-margins">
    <!-- <div class="ibox-title"></div> -->
    <div class="padding m-b">
     <select class="form-control" name="group">
        <?php foreach ($menu as $value): ?>
          <option value="<?=$value['pid']?>"><?=$value['xname']?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="ibox-content">
      <div class="row">
        <div class="col-lg-12">
            <div class="tabs-container">
                  <ul class="nav nav-tabs" id="menu1">
                    <?php $count = -1;
                      foreach ($tabmenu as $value): $count++;
                      $xcode = $value['xcode'];

                      if($tabactive==$xcode) $active = 'active';
                      else if(!$tabactive && $count==0) $active = 'active';
                      else $active = '';
                    ?>
                    <?php if ($data[$xcode]): ?>
                    <li class="<?=$active?>"><a data-toggle="tab" href="#tab2-<?=$xcode?>"><?=$value['xtitle']?></a></li>
                    <?php endif; ?>
                    <?php endforeach; ?>
                  </ul>
                  <div class="tab-content">
                      <?php $count = -1; foreach ($tabmenu as $value): $count++;
                        $xcode = $value['xcode'];
                        if($tabactive==$xcode) $active = 'active';
                        else if(!$tabactive && $count==0) $active = 'active';
                        else $active = '';
                      ?>
                      <div id="tab2-<?=$xcode?>" class="tab-pane <?=$active?>">
                          <div class="panel-body">
                            <div class="table-responsive padding">
                              <table class="table tree" id="table<?=$xcode?>">
                                <?=$data[$xcode]?>
                              </table>
                            </div>
                          </div>
                      </div>
                      <?php endforeach; ?>
                  </div>
            </div>
        </div>
      </div>

    </div>

  </div>
</div>
</div>
</div>

<script>
function listinit() {
$(document).ready(function() {
  $('.tree').treegrid({
    'initialState': 'expanded',
  });

  $("ul.nav-tabs#menu1 > li")
  .on("click", function() { var data = '';
    var href = $(this).find('a').attr('href');
    var array = href.split('#tab2-');
    if(array.length>1) data = array[1];
    var url = "<?php echo site_url("$this->indexPath/recordtab"); ?>"+'/'+data;
    $.post(url,function(data){console.log(data);});
  });
});

// 群組 select 更改控制
$("select[name='group']")
.change(function () {
  var val = $(this).val();
  $.post("<?php echo site_url("$this->indexPath/readNode"); ?>", { val: val }, function(data){
    ReadCheckbox(data);
  });
})
.change();
}

// 讀取 checkbox 預設值
function ReadCheckbox(array) {
var xlang = "<?=$this->session->taba?>";
// 清空 checkbox
$(".table.tree input[type='checkbox']").prop("checked", false);
// 勾選 checkbox 值
for (var key in array) {
  var menuid = array[key]['MenuID'];

  // checkebox value 0、1
  var rank = parseInt(array[key]['RankAction']);
  var list = parseInt(array[key]['ReadAction']);
  var create = parseInt(array[key]['CreateAction']);
  var update = parseInt(array[key]['UpdateAction']);
  var del = parseInt(array[key]['DeleteAction']);

  $('input[name="rank'+menuid+'"]').prop("checked", rank);
  $('input[name="list'+menuid+'"]').prop("checked", list);
  $('input[name="create'+menuid+'"]').prop("checked", create);
  $('input[name="update'+menuid+'"]').prop("checked", update);
  $('input[name="delete'+menuid+'"]').prop("checked", del);

  var hasdel = $('input[name="delete'+menuid+'"]').prop("checked")==false;
  // 全都勾選 = 全選
  if(!hasdel && rank && list) {
    $('input[name="all'+menuid+'"]').prop("checked", true);
  } else if(rank && list && create && update && del)
    $('input[name="all'+menuid+'"]').prop("checked", true);
  else
    $('input[name="all'+menuid+'"]').prop("checked", false);
}
}

// 更新 checkbox
function SaveCheckbox(xact, menuid, type) {

var groupid = $('select[name="group"]').val();
var xbool = 0;

// 全選
if(xact == 'all') {
  // 勾取
  if($('input[name="all'+menuid+'"]').prop("checked")) xbool = 1;

  $('input[name="rank'+menuid+'"]').prop("checked", xbool);
  $('input[name="list'+menuid+'"]').prop("checked", xbool);

  // SUB
  if (!type) {
    $('input[name="create'+menuid+'"]').prop("checked", xbool);
    $('input[name="update'+menuid+'"]').prop("checked", xbool);
    $('input[name="delete'+menuid+'"]').prop("checked", xbool);
  }
// 其他 xact
} else {
  if($('input[name="'+xact+menuid+'"]').prop("checked")) xbool = 1; // 勾取
  else $('input[name="all'+menuid+'"]').prop("checked", xbool); // 移除全選勾取
}

$.post("<?php echo site_url("$this->indexPath/updateNode"); ?>", { groupid: groupid , menuid: menuid, xact: xact, xbool: xbool}, function(data){
  if(data.error) alert(data.error);
});
}
</script>
