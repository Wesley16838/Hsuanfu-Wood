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

    <?php if(count($menu)>0):?>
    <div class="padding m-b">
      <!--年份-->
      <select class="form-control" name="group">
        <?php foreach($menu as $value):
          if($nowyear == $value['year']) $select = 'selected';
          else $select = '';
        ?>
        <option value="<?=$value['year']?>"<?=$select?>><?=$value['year']?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php endif;?>
    <div class="ibox-content">
      <div class="table-responsive padding">
        <!--列表-->
        <table class="table table-striped table-bordered table-hover" id="editable">
          <thead>
            <tr>
              <th>月份</th>
              <th>統計</th>
            </tr>
          </thead>
          <tboby>
            <?php for($i=1;$i<=12;$i++): ?>
              <?php if(isset($data[$i])): $value = $data[$i]; ?>
              <tr>
                <td><?=str_pad($value['month'],2,"0",STR_PAD_LEFT)?></td>
                <td><?=$value['count']?></td>
              </tr>
              <?php else: ?>
              <tr>
                <td><?=str_pad($i,2,"0",STR_PAD_LEFT)?></td>
                <td>0</td>
              </tr>
              <?php endif; ?>
            <?php endfor; ?>
          </tboby>
        </table>
      </div>

    </div>
  </div>
</div>
</div>
</div>

<script>
function listinit() {
$('select').on( 'change', function () {
  redirect("<?php echo site_url("$this->indexPath/index"); ?>"+"/"+this.value);
});
}
</script>
