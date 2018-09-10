<body>
    <!--start nav.html-->
    <?php $this->load->view($this->lang.'/include/nav.php');?>
    <!--end nav.html-->
    <!--start header.html-->
    <?php $this->load->view($this->lang.'/include/header.php');?>
    <!--end header.html-->
    <div class="language"><a href="#" class="text">CH/EN</a></div>
      <?php foreach ($banner_list as $key => $value):
              $xtitle = $value['xtitle'];
              $xsubtitle = ($value['xsubtitle'])?nl2br($value['xsubtitle']):'';
              $xfile1 = $this->front->getfilepath($this->prefix.'_index_banner','xfile1',$value,'',true);
      ?>
        <div class="page" style="background-image: url('<?=$xfile1?>');">
            <div class="linearGradient"><span class="title"><p><?=$xtitle?></p><p><?=$xsubtitle?></p></span></div>
        </div>
      <?php endforeach; ?>
    <div class="article">
        <div class="searchbox wrap">
            <div class="search-detail">
                <h2>尋找可靠的合作夥伴?</h2>
                <p>聯繫我們</p><input type="text" id="email" name="email" placeholder="電子信箱"> <a href="javascript:void(0)">聯絡我們</a></div>
        </div>
        <?php foreach ($place_list as $key => $value):
            $xcontent = $value['xcontent'];
        ?>
          <?=$xcontent?>
        <?php endforeach; ?> 
    </div>
    <div class="board">
      <?php   foreach ($shortcut_list as $key => $value):
                  $xtitle = $value['xtitle'];
                  $xsubtitle = ($value['xsubtitle'])?nl2br($value['xsubtitle']):'';
                  $xbtntitle = $value['xbtntitle'];
                  $xfile1 = $this->front->getfilepath($this->prefix.'_index_shortcut','xfile1',$value,'',true);
                  $xlink = ($value['xlink'])?$value['xlink']:'';
                  $xtarget = ($value['xtarget'] == 'yes'||$value['xlink']=='')?'_self':'_blank';

      ?>
        <div class="board-detail" style="background-image: url(<?=$xfile1?>);background-color: #F4EFEB;">
            <h2><?=$xtitle?></h2>
            <p><?=$xsubtitle?></p>
            <!--<button>尋找可靠的合作夥伴&nbsp;&nbsp;<img src="../../../../assets/img/arrowBlack.svg" width="10px" height="13px" alt="arrow"></button>-->
            <a href="<?=$xlink?>" target="<?=$xtarget?>" title="<?=$xbtntitle?>"><?=$xbtntitle?><img src="assets/img/index/arrowBlack.svg" width="10px" height="13px" alt="arrow"></a>
        </div>
        <?php endforeach; ?>
    </div>
    <!--start footer.html-->
    <?php $this->load->view($this->lang.'/include/footer.php');?>
    <!--end footer.html-->
    <!--start loading.html-->
    <?php $this->load->view($this->lang.'/include/loading.php');?>
    <!--end loading.html-->
    <script src="assets/js/main.min.js"></script>
</body>

</html>
