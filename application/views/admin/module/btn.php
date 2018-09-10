<?php
/*
$mbtype : btn type [ back|send ]
$seturl : btn type = 'send', set url
*/
/* example

<?=$this->load->view('admin/module/btn',array('mbtype'=>'back'),true)?>
表單確認送出 :
<?=$this->load->view('admin/module/btn',array('mbtype'=>'send','seturl'=>$this->indexPath),true)?>
<?=$this->load->view('admin/module/btn',array('mbtype'=>'send','seturl'=>$this->indexPath.'/index/'.$this->session->userdata('preid')),true)?>

*/
?>
<?php if ($mbtype=='back'): ?>
    <?php if (isset($this->backurl)): ?>
        <?php if ($this->backurl): ?>
        <a class="btn btn-default" href="<?=$this->backurl?>" target="_self"><i class="fa fa-mail-reply fa-xs"></i></a>
        <?php endif; ?>
    <?php endif; ?>
<?php elseif ($mbtype=='send'): ?>
  <div class="btn-fixed-area">
    <div>
      <a class="btn btn-lg btn-default" type="button" href="<?=$seturl?>">返回列表</a>
      <button class="btn btn-lg btn-primary" type="submit">確認送出</button>
    </div>
  </div>
<?php endif; ?>
