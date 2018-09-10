<!--麵包屑-->
<div class="row wrapper page-heading">
</div>
<div class="wrapper wrapper-content animated fadeInRight">

<?php if ($selflevel==0): ?>
<div class="row">
  <div class="col-lg-12">
      <div class="widget-text-box">
        <?php
          $this->load->library('parsedown');
          $Parsedown = new Parsedown();

          $handle = @fopen("sys.md", "r");
          if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
              echo $Parsedown->text($buffer);
            }
            if (!feof($handle)) {
              echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
          }
        ?>
      </div>
  </div>
</div>
<?php endif; ?>

<?php if ($this->valid == true): ?>
  <div class="row">
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>工作階段</h5>
            </div>
            <div class="ibox-content padding">
                <h1 class="no-margins"><?=$totalSessions?></h1>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>使用者</h5>
            </div>
            <div class="ibox-content padding">
                <h1 class="no-margins"><?=$totalUsers?></h1>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>瀏覽量</h5>
            </div>
            <div class="ibox-content padding">
                <div class="row">
                    <div class="col-md-6">
                        <h1 class="no-margins"><?=$totalPageViews?></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>

  <div class="ibox-content" id='slowly'>
    <div class="row">
      <div class="col-lg-9">
        <div><canvas id="lineChart" height="114"></canvas></div>
      </div>
      <div class="col-lg-3">
        <div id="canvas-holder">
            <canvas id="chart-area"/>
        </div>
      </div>
    </div>
  </div>
  <br>
<?php endif; ?>

  <div class="row">
    <?php
      function showmsg($compare)
      {
        $today = strtotime(date("Y-m-d H:i:s"));
        $days = ($today - $compare) / 86400; $hours = $days*24; $minutes = $hours*60;
        if($days > 1) $msg = floor($days).' 天前';
        else if($minutes > 60) $msg = floor($hours).' 小時前';
        else if($minutes < 60 && $minutes >= 1) $msg = floor($minutes).' 分鐘前';
        else $msg = '幾秒前';
        return $msg;
      }
      $readcount = count($readlist);
      $newcount = count($newlist);
      $oldcount = count($oldlist);
      $sum = $readcount+$oldcount;
      if ($sum>0):
    ?>
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-title">
          <h5>公告</h5>
          <?php if ($readcount>0): ?>
          <span class="label label-danger"><?=$readcount?></span>
          <?php endif; ?>
          <div class="ibox-tools">
          </div>
        </div>
        <?php if ($readcount>0 || $newcount): ?>
        <div class="ibox-content ibox-heading padding">
          <?php if ($readcount>0): ?>
          <h3><i class="fa fa-envelope-o"></i> 新訊息</h3>
          <small><i class="fa fa-tim"></i> 你有 <b class="text-danger"><?=$readcount?></b> 個新表單，前往<a href='<?=$this->gopage?>'>聯絡表單</a> </small>
          <?php elseif ($newcount>0): ?>
          <small><i class="fa fa-tim"></i> 你有 <b class="text-danger"><?=$newcount?></b> 個新表單，前往<a href='<?=$this->gopage?>'>聯絡表單</a> </small>
          <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if ($newcount>0): $count = -1; ?>
        <div class="ibox-content padding">
          <div class="feed-activity-list">
            <?php foreach ($newlist as $value): $count++;
              $xcreate = $value['xcreate'];
              $compare = strtotime($xcreate);
              $time = showmsg($compare);
              $isnew = ($compare - $logtime > -1 && $value['xread'] == 'no')?true:'';
              $isread = (strpos($value['xreadeach'], md5($selfaccount)) > -1)?true:'';
            ?>
            <?php if ($count < 5): ?>
            <div class="feed-element">
              <div>
                <small class="pull-right <?=($isnew)?'text-danger':'text-navy'?>"><?=$time?></small>
                <?php if ($isnew): ?>
                <span class="pull-right badge badge-danger m-r"> New </span>
                <?php else: ?>
                <span class="pull-right badge <?=($isread)?'badge-success':'badge-warning'?> m-r"> <?=($isread)?'已讀':'未讀'?> </span>
                <?php endif; ?>
                <strong><?=$value['xname']?></strong>
                <div><?=$value['xcompany']?></div>
              </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
        <?php if ($oldcount>0): ?>
        <div class="ibox-content ibox-heading padding">
          <small><i class="fa fa-tim"></i> 你有 <b class="text-navy"><?=$oldcount?></b> 個未處理表單，前往<a href='<?=$this->gopage?>'>聯絡表單</a> </small>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
  <br>

</div>
<script>
  function listinit() {
    var valid = "<?=$this->valid?>";
    if(valid==true) $(document).ready(function(){$("#slowly").block({message:"Loading",css:{border:"none",padding:"15px",backgroundColor:"#000","-webkit-border-radius":"10px","-moz-border-radius":"10px",opacity:.5,color:"#fff"}});$.post("<?=site_url($this->indexPath.'/read');?>",function(a){var b={type:"pie",data:{datasets:[{data:[a.NewVisitor,a.returnVisitor],backgroundColor:[window.chartColors.blue,window.chartColors.green,window.chartColors.red,window.chartColors.orange,window.chartColors.yellow],label:"Dataset 1"}],labels:["\u65b0\u8a2a\u5ba2 ("+a.rateNewVisitor+") ","\u56de\u8a2a\u5ba2 ("+a.ratereturnVisitor+") "]},options:{responsive:!0}},c=document.getElementById("chart-area").getContext("2d");window.myPie=new Chart(c,b);b={type:"line",data:{labels:a.char1date,datasets:[{label:"\u5de5\u4f5c\u968e\u6bb5",fill:!1,backgroundColor:window.chartColors.blue,borderColor:window.chartColors.blue,data:a.char1sessions},{label:"\u4f7f\u7528\u8005",backgroundColor:window.chartColors.red,borderColor:window.chartColors.red,data:a.char1users,fill:!1},{label:"\u700f\u89bd\u91cf",fill:!1,backgroundColor:window.chartColors.green,borderColor:window.chartColors.green,data:a.char1pageviews}]},options:{responsive:!0,title:{display:!1,text:"Chart.js Line Chart"},tooltips:{mode:"index",intersect:!1},hover:{mode:"nearest",intersect:!0},scales:{xAxes:[{display:!0,scaleLabel:{display:!1,labelString:"Month"}}],yAxes:[{display:!0,scaleLabel:{display:!1,labelString:"Value"}}]}}};c=document.getElementById("lineChart").getContext("2d");window.myLine=new Chart(c,b);$("#slowly").unblock()})});
  }
  function change(id) {
    Menu("<?=$this->gopage?>","24");
    redirect('<?=site_url($this->gopage.'/form')?>'+'/'+id);
  }
</script>
