<?php
/*
$name : label name
$em : em name
$type : input type
*/
/* example
<?=$this->load->view('admin/module/form',array('name'=>'刊登','em'=>'xpublish','type'=>'radio'),true)?>
<?=$this->load->view('admin/module/form',array('name'=>'文字位置','em'=>'xpostion','xArr'=>array(array('title'=>'置左','val'=>'left'),array('title'=>'置中','val'=>'center'),array('title'=>'置右','val'=>'right')),'type'=>'multiradio'),true)?>
<?=$this->load->view('admin/module/form',array('name'=>'發佈日期','em'=>'xpostdate','type'=>'date'),true)?>
<?=$this->load->view('admin/module/form',array('name'=>'主標','em'=>'xtitle','type'=>'title','autoArray'=>array()),true)?>
<?=$this->load->view('admin/module/form',array('name'=>'主標','em'=>'xtitle','type'=>'title','autoArray'=>array(
  array('autoname'=>'Rewrite','xtb'=>$this->table,'inputName'=>'xtitle','outputName'=>"xurltitle",'outputType'=>'input','isurl'=>true),
  array('autoname'=>'Seo title','xtb'=>$this->table,'inputName'=>'xtitle','outputName'=>"xseotitle",'outputType'=>'input','isurl'=>false)
)),true)?>
<?=$this->load->view('admin/module/form',array('name'=>'副標','em'=>'xsubtitle','type'=>'desc','autoArray'=>array()),true)?>
<?=$this->load->view('admin/module/form',array('name'=>'副標','em'=>'xsubtitle','type'=>'desc','autoArray'=>array(
  array('autoname'=>'Seo desc.','xtb'=>$this->table,'inputName'=>'xsubtitle','outputName'=>"xseodescription",'outputType'=>'textarea','isurl'=>false),
)),true)?>
<?=$this->load->view('admin/module/form',array('name'=>'連結網址','em'=>'xlink','type'=>'link','typebtn'=>true,'btnyes'=>'置換本頁','btnno'=>'另開視窗','btnem'=>'xtarget'),true)?>
<?=$this->load->view('admin/module/form',array('name'=>'連結網址(沒有按鈕)','em'=>'xlink','type'=>'link','typebtn'=>false),true)?>
<?=$this->load->view('admin/module/form',array('name'=>'檔案','em'=>'xfile1','type'=>'file'),true)?>
<?=$this->load->view('admin/module/form',array('name'=>'小圖','em'=>'xfile1','type'=>'img'),true)?>
<?=$this->load->view('admin/module/form',array('em'=>'xcontent','type'=>'edit'),true)?>
<?=$this->load->view('admin/module/form',array('name'=>'URL Rewrite','em'=>'xurltitle','type'=>'url'),true)?>
<?=$this->load->view('admin/module/form',array('type'=>'seo'),true)?>
<?=$this->load->view('admin/module/form',array('type'=>'seo','showitem'=>'t','inputfield'=>''),true)?>
<?=$this->load->view('admin/module/form',array('type'=>'seo','showitem'=>'k','inputfield'=>''),true)?>
<?=$this->load->view('admin/module/form',array('type'=>'seo','showitem'=>'d','inputfield'=>''),true)?>
<?=$this->load->view('admin/module/form',array('type'=>'import'),true)?>
// auto
<?=$this->load->view('admin/module/form',array('name'=>'URL Rewrite','em'=>'xurltitle','type'=>'url','inputfield'=>'xtitle','xtb'=>$this->table),true)?>
<?=$this->load->view('admin/module/form',array('type'=>'seo','showitem'=>'t','inputfield'=>'xtitle','xtb'=>$this->table),true)?>
<?=$this->load->view('admin/module/form',array('type'=>'seo','showitem'=>'d','inputfield'=>'xsubtitle','xtb'=>$this->table),true)?>
// 編輯器樣板
<?=$this->load->view('admin/module/form',array('name'=>'背景圖','em'=>'xbg_img','type'=>'editimg'),true)?>
<?=$this->load->view('admin/module/form',array('name'=>'小圖','em'=>'ximg1','type'=>'editimg'),true)?>
*/
?>
<?php if ($type=='title'): ?>
  <div class="form-group"><label class="col-sm-2 control-label"><?=$name?>:</label>
    <div class="col-sm-10">
      <?php if (isset($autoArray) && count($autoArray)>0):?>
        <?php $autoinit = $this->common->precessAutoinit($autoArray); ?>
        <input type="text" class="form-control" name="<?=$em?>" value="<?=(isset($list[$em]))?$list[$em]:''?>" onkeyup="javascript:autofield('<?=$autoinit['xtb']?>','<?=$autoinit['IelNstr']?>','<?=$autoinit['OelNstr']?>','<?=$autoinit['OelTstr']?>','<?=$autoinit['urlStr']?>','<?=$autoinit['xoffstr']?>')">
      <?php else: ?>
        <input type="text" class="form-control" name="<?=$em?>" value="<?=(isset($list[$em]))?$list[$em]:''?>" >
      <?php endif; ?>
    </div>
  </div>
<?php elseif ($type=='date'): ?>
  <div class="form-group"><label class="col-sm-2 control-label"><?=$name?>:</label>
    <div class="col-sm-10">
      <div class="input-group date">
        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        <input class="form-control" type="text" name="<?=$em?>">
      </div>
    </div>
  </div>
<?php elseif ($type=='desc'): ?>
  <div class="form-group"><label class="col-sm-2 control-label"><?=$name?>:</label>
    <div class="col-sm-10">
      <?php if (isset($autoArray) && count($autoArray)>0): ?>
        <?php $autoinit = $this->common->precessAutoinit($autoArray); ?>
        <textarea class="form-control" rows="<?=$this->textareaRow?>" name="<?=$em?>" onkeyup="javascript:autofield('<?=$autoinit['xtb']?>','<?=$autoinit['IelNstr']?>','<?=$autoinit['OelNstr']?>','<?=$autoinit['OelTstr']?>','<?=$autoinit['urlStr']?>','<?=$autoinit['xoffstr']?>')"><?=(isset($list[$em]))?$list[$em]:''?></textarea>
      <?php else: ?>
        <textarea class="form-control" rows="<?=$this->textareaRow?>" name="<?=$em?>"><?=(isset($list[$em]))?$list[$em]:''?></textarea>
      <?php endif; ?>
    </div>
  </div>
<?php elseif ($type=='link'): ?>
  <div class="form-group"><label class="col-sm-2 control-label"><?=$name?>:</label>
    <div class="col-sm-10">
      <div class="col-sm-6">
        <input type="text" class="form-control" name="<?=$em?>" value="<?=(isset($list[$em]))?$list[$em]:''?>">
      </div>
      <?php if ($typebtn): ?>
      <div class="col-sm-6">
        <div class="btn-group" data-toggle="buttons">
          <label class="btn btn-default"><input type="radio" value="yes" name="<?=$btnem?>"><?=$btnyes?></label>
          <label class="btn btn-default"><input type="radio" value="no" name="<?=$btnem?>"><?=$btnno?></label>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
<?php elseif ($type=='file' || $type=='img'): ?>
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
        <a onclick="previewFile('<?=(isset($list[$em]))?$this->common->addroot($list[$em]):''?>')" title="預覽"><small class="label label-primary"><i class="fa fa-picture-o"></i> 預覽</small></a>
        <a onclick="deleteFile('<?=(isset($list['pid']))?$list['pid']:''?>','<?=$em?>')" title="刪除"><small class="label label-primary"><i class="fa fa-times-circle"></i> 刪除</small></a>
        <?php endif;?>
      </div>
      <div class="remark">
        <div class="col-sm-12">
          <label class="control-label text-danger">*注意 : 上傳時「資料夾命名」及「檔名」請勿有「空白.特殊符號」</label>
        </div>
      </div>
    </div>
  </div>
<?php elseif ($type=='editimg'): ?>
  <div class="form-group"><label class="col-sm-2 control-label"><?=$name?>:</label>
    <div class="col-sm-10">
      <div class="col-sm-3">
        <?php if ($this->config->item('ckfinder_version')==2): ?>
        <input type="button" onclick="BrowseServer('<?=$em?>');" value="選擇檔案">
        <?php else: ?>
        <input type="button" onclick="generateCkfinder('<?=$em?>');" value="選擇檔案">
        <?php endif; ?>
        <input type="hidden" class="form-control" name="<?=$em?>" value="<?=(isset($list[$em]) && $list[$em])?$this->common->addroot($list[$em]):''?>">
        <span class="output"><?=$this->filemsg?></span>
      </div>
      <div class="col-sm-6">
        <a title="<?=$this->imageinfo[$em]?>"><small class="label label-primary"><i class="fa fa-search"></i> 格式</small></a>
        <?php if(isset($list[$em]) && $list[$em]):?>
        <a onclick="prevFile('<?=$em?>','<?=(isset($list[$em]))?$this->common->addroot($list[$em]):''?>')" title="原圖"><small class="label label-primary"><i class="fa fa-picture-o"></i> 重設</small></a>
        <a onclick="delFile('<?=$em?>')" title="不使用"><small class="label label-primary"><i class="fa fa-times-circle"></i> 不使用</small></a>
        <?php endif;?>
      </div>
      <div class="remark">
        <div class="col-sm-12">
          <label class="control-label text-danger">*注意 : 上傳時「資料夾命名」及「檔名」請勿有「空白.特殊符號」</label>
        </div>
      </div>
    </div>
  </div>
<?php elseif ($type=='radio'): ?>
  <div class="form-group">
    <label class="col-sm-2 control-label"><?=$name?>:</label>
    <div class="col-sm-10">
      <div class="btn-group" data-toggle="buttons">
        <label class="btn btn-default"><input type="radio" value="yes" name="<?=$em?>">是</label>
        <label class="btn btn-default"><input type="radio" value="no" name="<?=$em?>">否</label>
      </div>
    </div>
  </div>
<?php elseif ($type=='multiradio'): ?>
  <div class="form-group">
    <label class="col-sm-2 control-label"><?=$name?>:</label>
    <div class="col-sm-10">
      <div class="btn-group" data-toggle="buttons">
        <?php foreach ($xArr as $value): ?>
          <label class="btn btn-default"><input type="radio" value="<?=$value['val']?>" name="<?=$em?>"><?=$value['title']?></label>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php elseif ($type=='edit'): ?>
  <div class="form-group">
    <textarea name="<?=$em?>"><?=(isset($list[$em]))?$list[$em]:''?></textarea>
  </div>
<?php elseif ($type=='url'): ?>
  <div class="form-group"><label class="col-sm-2 control-label"><?=$name?>:</label>
    <?php if (isset($inputfield)): ?>
    <div class="col-sm-10">
      <div class="input-group">
        <?php
          $outputfield = 'xurltitle'; $ouputtype = 'input'; $ifurl='true';
          $xtb = $this->common->splittb($xtb);
          $settingArray = $this->admin_crud->result_array($this->admin_crud->query_where($this->tb_setting,array('xtb'=>$xtb,'xfield'=>$outputfield)));
          $xoff = (count($settingArray)>0)?$settingArray[0]['xoff']:'no';
          if ($xoff=='no') { $disable = 'disabled';$btntext = '自定義';}
          else { $disable = '';$btntext = '開啟同步';}
        ?>
        <input type="text" class="form-control" name="<?=$em?>" value="<?=(isset($list[$em]))?$list[$em]:''?>" <?=$disable?>>
        <span class="input-group-btn"><button type="button" class="btn btn-default <?=($outputfield=='xseodescription')?'m-t':''?>"
          onclick="javascript:offauto('<?=$xtb?>','<?=$inputfield?>','<?=$outputfield?>','<?=$ouputtype?>','<?=$ifurl?>')"
        ><?=$btntext?></button></span>
      </div>
    </div>
    <?php else: ?>
    <div class="col-sm-10"><input type="text" class="form-control" name="<?=$em?>" value="<?=(isset($list[$em]))?$list[$em]:''?>"></div>
    <?php endif; ?>
  </div>
<?php elseif ($type=='seo'): ?>
  <?php if (isset($showitem)): ?>
    <?php if($showitem=='k'):?>
    <div class="form-group"><label class="col-sm-2 control-label">SEO Keywords:</label>
      <div class="col-sm-10"><input type="text" class="form-control" name="xseokeyword" value="<?=(isset($list['xseokeyword']))?$list['xseokeyword']:''?>"></div>
    </div>
    <?php elseif ($showitem=='t' || $showitem=='d'):
      if($showitem=='t') {$labelname = 'SEO Title'; $outputfield = 'xseotitle'; $ouputtype = 'input'; $ifurl='false'; }
      if($showitem=='d') {$labelname = 'SEO Description'; $outputfield = 'xseodescription'; $ouputtype = 'textarea'; $ifurl='false'; }
    ?>
    <div class="form-group">
      <label class="col-sm-2 control-label"><?=$labelname?>:</label>
    <?php if ($inputfield): ?>
      <div class="col-sm-10">
        <?php if($outputfield=='xseotitle'): ?>
        <div class="input-group">
        <?php endif; ?>
          <?php
            $xtb = $this->common->splittb($xtb);
            $settingArray = $this->admin_crud->result_array($this->admin_crud->query_where($this->tb_setting,array('xtb'=>$xtb,'xfield'=>$outputfield)));
            $xoff = (count($settingArray)>0)?$settingArray[0]['xoff']:'no';
            if ($xoff=='no') { $disable = 'disabled';$btntext = '自定義';}
            else { $disable = '';$btntext = '開啟同步';}
          ?>
          <?php if($outputfield=='xseotitle'): ?>
          <input type="text" class="form-control" name="xseotitle" value="<?=(isset($list['xseotitle']))?$list['xseotitle']:''?>" <?=$disable?>>
          <?php elseif($outputfield=='xseodescription'): ?>
          <textarea class="form-control" rows='<?=$this->textareaRow?>' name="xseodescription" <?=$disable?>><?=(isset($list['xseodescription']))?$list['xseodescription']:''?></textarea>
          <?php endif; ?>
          <span class="input-group-btn"><button type="button" class="btn btn-default <?=($outputfield=='xseodescription')?'m-t':''?>"
            onclick="javascript:offauto('<?=$xtb?>','<?=$inputfield?>','<?=$outputfield?>','<?=$ouputtype?>','<?=$ifurl?>')"
          ><?=$btntext?></button></span>
        <?php if($outputfield=='xseotitle'): ?>
        </div>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <?php if ($showitem=='t'):?>
      <div class="col-sm-10"><input type="text" class="form-control" name="xseotitle" value="<?=(isset($list['xseotitle']))?$list['xseotitle']:''?>"></div>
      <?php elseif ($showitem=='d'):?>
      <div class="col-sm-10"><input type="text" class="form-control" name="xseodescription" value="<?=(isset($list['xseodescription']))?$list['xseodescription']:''?>"></div>
      <?php endif; ?>
    <?php endif; ?>
    </div>
    <?php endif; ?>
  <?php else: ?>
  <div class="form-group"><label class="col-sm-2 control-label">SEO Title:</label>
    <div class="col-sm-10"><input type="text" class="form-control" name="xseotitle" value="<?=(isset($list['xseotitle']))?$list['xseotitle']:''?>"></div>
  </div>
  <div class="form-group"><label class="col-sm-2 control-label">SEO Keywords:</label>
    <div class="col-sm-10"><input type="text" class="form-control" name="xseokeyword" value="<?=(isset($list['xseokeyword']))?$list['xseokeyword']:''?>"></div>
  </div>
  <div class="form-group"><label class="col-sm-2 control-label">SEO Description:</label>
    <div class="col-sm-10"><textarea class="form-control" rows='<?=$this->textareaRow?>' name="xseodescription"><?=(isset($list['xseodescription']))?$list['xseodescription']:''?></textarea></div>
  </div>
  <?php endif; ?>
<?php elseif ($type=='import'): ?>
  <form class="form-horizontal" role="form" id="uploadform"  method="post" enctype="multipart/form-data" accept-charset="utf-8" action="<?=$this->indexPath."/import"?>">
    <div class="row">
      <div class="col-lg-12">

        <div class="tabs-container">
          <ul class="nav nav-tabs"> <!--須修改-->
              <li class="active"><a data-toggle="tab" href="#tab-1"> 匯入</a></li>
          </ul>

          <div class="tab-content">
            <div id="tab-1" class="tab-pane active"> <!--須修改-->
              <div class="panel-body">
                <fieldset class="form-horizontal">
                  <div class="form-group"><label class="col-sm-2 control-label">檔案:</label>
                    <div class="col-sm-6">
                      <div class="input-group">
                        <input type="file" class="form-control" name="userfile" size="20">
                        <span class="input-group-addon">* csv . xls 格式</span>
                      </div>
                    </div>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
    <div class="btn-fixed-area">
      <div>
        <a class="btn btn-lg btn-default" type="button" href="<?=$this->indexPath?>">返回列表</a>
        <button class="btn btn-lg btn-primary" type="submit" onclick="javascript:importform()">確認送出</button>
      </div>
    </div>
  </form>
<?php endif; ?>
