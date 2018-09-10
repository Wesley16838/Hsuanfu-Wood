
  <div class="row border-bottom">
  <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
    <div class="navbar-header">
        <a class="navbar-minimalize minimalize-styl-2"><span class="left-menu-toggle"></span></a>
    </div>

    <ul class="navbar-top-links navbar-right">
        <?php if (count($langMenu)>0): ?>
        <li class="dropdown">
            <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">                
                <span class="icon-lang" ></span>
                <span>切換語系</span>       
            </a>
            <ul class="dropdown-menu dropdown-alerts">
                <?php $count = -1;
                foreach ($langMenu as $value): $count++;?>
                <li>
                    <a href="javascript:changelang('<?=$value['xcode']?>')">
                        <div>
                            <?=$value['xtitle']?>
                        </div>
                    </a>
                </li>
                <?php if ($count+1 != count($langMenu)): ?>
                <li class="divider"></li>
                <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </li>
        <?php endif; ?>

        <?php if ($sysMenu): ?>
        <li class="dropdown">
            <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                <span class="icon-setting"></span>
                <span>設定</span>   
            </a>
            <ul class="dropdown-menu dropdown-alerts">
                <?php $count = -1; $now = -1;
                foreach ($sysMenu as $value):
                  foreach ($value as $sub):
                  $xname = $sub['xname'];
                  $xpage = ($sub['xpage'] && $sub['xpage']!='#')?'admin/'.$sub['xpage']:'';
                  $preid = ($sub['preid'])?$sub['preid']:'';
                  $prename = ($sub['prename'])?$sub['prename']:'';
                  if($now != $preid) { $show = true; $now = $preid; $count++;}
                  else $show = false;
                ?>
                  <?php if ($show && $count>1): ?>
                  <li class="divider"></li>
                  <?php endif; ?>
                  <?php if ($xpage): ?>
                  <li>
                      <a href="<?=$xpage?>">
                          <div>
                              <?=$xname?>
                              <?php if ($show): ?>
                              <span class="pull-right text-muted small"><?=$prename?></span>
                              <?php endif; ?>
                          </div>
                      </a>
                  </li>
                  <?php endif; ?>
                  <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </li>
        <?php endif; ?>
        <?php if ($moduleMenu): ?>
        <li class="dropdown">
            <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                <span class="icon-module"></span>
                <span>模組管理</span>   
            </a>
            <ul class="dropdown-menu dropdown-alerts">
                <?php $count = -1; $now = -1;
                foreach ($moduleMenu as $value):
                  foreach ($value as $sub):
                  $xname = $sub['xname'];
                  $xpage = ($sub['xpage'] && $sub['xpage']!='#')?'admin/'.$sub['xpage']:'';
                  $preid = ($sub['preid'])?$sub['preid']:'';
                  $prename = ($sub['prename'])?$sub['prename']:'';
                  if($now != $preid) { $show = true; $now = $preid; $count++;}
                  else $show = false;
                ?>
                  <?php if ($show && $count!=0): ?>
                  <li class="divider"></li>
                  <?php endif; ?>
                  <?php if ($xpage): ?>
                  <li>
                      <a href="<?=$xpage?>">
                          <div>
                              <?=$xname?>
                              <?php if ($show): ?>
                              <span class="pull-right text-muted small"><?=$prename?></span>
                              <?php endif; ?>
                          </div>
                      </a>
                  </li>
                  <?php endif; ?>
                  <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </li>
        <?php endif; ?>
        <li>
            <a href="javascript:logout()">
                <span class="icon-logout" ></span>
                <span>登出</span>                
            </a>
        </li>

        <!-- <li>
          <a class="right-sidebar-toggle">
            <i class="fa fa-tasks"></i>
          </a>
        </li> -->
    </ul>
  </nav>
</div>
