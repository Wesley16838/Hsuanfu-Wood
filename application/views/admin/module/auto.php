<div class="offauto">
  <?php foreach ($autoArray as $key => $value):
    $table = $value['xtb'];
    $outputName = $value['outputName'];
    $isurl = $value['isurl'];
    $xtb = $this->common->splittb($table);
    $settingArray = $this->admin_crud->result_array($this->admin_crud->query_where($this->tb_setting,array('xtb'=>$xtb,'xfield'=>$outputName)));
    $xoff = (count($settingArray)>0)?$settingArray[0]['xoff']:'no';
    $active = ($xoff=='no')?'active':''; $checked = ($xoff=='no')?'checked':'';
  ?>
  <div class="btn-group" data-toggle="buttons" onclick="javascript:offauto('<?=$value['inputName']?>','<?=$outputName?>','<?=$value['outputType']?>','<?=$isurl?>','<?=$table?>','<?=$outputName?>')">
    <label class="btn btn-default btn-xs m-t <?=$active?>">
      <input type="checkbox" <?=$checked?>> <i></i><?=$value['autoname']?>
    </label>
  </div>
  <?php endforeach; ?>
</div>
