
<nav class="navbar-default navbar-static-side" role="navigation">
<?php
      $xlang = $this->searchlang;
?>
<div class="profile-element <?=$xlang?>">
      <span>
          <img alt="image" class="img-circle" src="<?=$userData['xpic']?>"/>
        </span>
      <?php if($this->showLangName):?>
        <span class="lang"><?=$this->showLangName?></span>
        <?php endif;?>
</div>
<div class="sidebar-collapse">
  <ul class="nav metismenu" id="side-menu">
    <!-- <li class="nav-header">
      <div class="dropdown profile-element"> -->
        
        <!-- <a data-toggle="dropdown" class="dropdown-toggle" href="#">
          <span class="clear">
            <span class="block m-t-xs"> <strong class="text-muted font-bold"><?=$userData['xnickname']?></strong> <b class="caret"></b></span>
            <span class="text-muted text-xs block"><?=$userData['xjobtitle']?></span>
          </span>
        </a> -->
        
        <!-- <ul class="dropdown-menu animated fadeInRight m-t-xs">
          <li><a href="admin/user/user">管理員</a></li>
          <li class="divider"></li>
          <li><a href="javascript:logout()">登出</a></li>
        </ul> -->
      <!-- </div> 

    </li> -->
    <?php
      $xlang = $this->searchlang;
      $lang = ($xlang)?'?lang='.$xlang:'';
    ?>
    <li>
      <a href="admin/dashboard<?=$lang?>"> 
        <span class="icon-analytics"></span>
        <span class="nav-label">主控台</span>
      </a>
    </li>
    <?=$LeftMenu?>
  </ul>
</div>
</nav>
