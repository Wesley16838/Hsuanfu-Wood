<?php
/*
$multi : true | false
$field : field name
*/
/* example
<?=$this->load->view('admin/module/order',array('multi'=>false),true)?>
<?=$this->load->view('admin/module/order',array('multi'=>true,'field'=>'xname'),true)?>
<?=$this->load->view('admin/module/order',array('multi'=>true,'field'=>'xtitle'),true)?>
*/
?>
<div class="form-group">
  <label class="col-sm-2 control-label">排序:</label>
  <div class="col-sm-10">
    <div class="btn-group m-b" data-toggle="buttons">
      <?php if($action =='update'):?><label class="btn btn-default"><input type="radio" value="<?=(isset($list['xsort']))?$list['xsort']:''?>" id="remain" name="xsort">保留不變</label> <?php endif;?>
      <label class="btn btn-default"><input type="radio" value="first" id="first" name="xsort">置於第一筆</label>
      <label class="btn btn-default"><input type="radio" value="last" id="last" name="xsort">置於最後一筆</label>
      <?php if ($multi==false): ?>
      <label class="btn btn-default"><input type="radio" value="insert" id="insert" name="xsort" onchange="readSort('<?=(isset($list['pid']))?$list['pid']:''?>')">置入所選資料之後</label>
      <?php else: ?>
      <label class="btn btn-default"><input type="radio" value="insert" id="insert" name="xsort" onchange="readSort('<?=(isset($list['pid']))?$list['pid']:''?>','<?=$field?>',true)">置入所選資料之後</label>
      <?php endif; ?>
      <input type="hidden" name='insertxsortpid'>
    </div>

    <div class="table-responsive table-insert" style="display:none">
      <table class="table table-striped table-bordered table-hover sortDataTables">
        <thead>
        <tr>
            <th>主標</th>
        </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
