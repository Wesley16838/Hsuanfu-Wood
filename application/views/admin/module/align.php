<div class="form-group">
  <label class="col-sm-2 control-label">版面配置:</label>
  <div class="col-sm-10">
    <div class="btn-group" data-toggle="buttons">
      <?php foreach ($alignlist as $key => $value):
        $xtitle = $value['xtitle'];
      ?>
      <label class="btn btn-default"><input type="radio" value="<?=$xtitle?>" name="xalign"><img src="assets/img/template/<?=$xtitle?>.png" title="<?=$xtitle?>"></label>
      <?php endforeach; ?>
      <label class="btn btn-default"><input type="radio" value="edit" name="xalign">編輯器</label>
    </div>
  </div>
</div>
<div class="noEditor">
  <?=$this->load->view('admin/module/form',array('name'=>'背景圖','em'=>'xbg_img','type'=>'editimg'),true)?>
  <div class="showEimg">
  <?=$this->load->view('admin/module/form',array('name'=>'小圖','em'=>'ximg1','type'=>'editimg'),true)?>
  </div>
  <?=$this->load->view('admin/module/form',array('name'=>'編輯器主標','em'=>'xedit_title','type'=>'title','autoArray'=>array()),true)?>
  <?=$this->load->view('admin/module/form',array('name'=>'編輯器副標','em'=>'xedit_desc','type'=>'desc','autoArray'=>array()),true)?>
</div>
<div class="Editor">
  <?=$this->load->view('admin/module/form',array('em'=>'xcontent','type'=>'edit'),true)?>
</div>
<div class="form-group xpreview">
    <a href="javascript:js2html(true)"><i class="fa fa-search"></i> 原尺寸</a>
    <iframe id="preview" width="100%" height="300px"></iframe>
</div>
<script>
function changeData(a,c,e,d){a=a||"";d=d||"";a&&d&&$.post("common/template/datachange",{olddata:c||"",newdata:e||"",inputtype:d},function(c){a.val(c.result);js2html()})}
function initedit(){$('input[name="xalign"]').change(function(){if($(this).is(":checked"))switch($(this).val()){case "text-A":case "text-B":case "text-C":$(".showEimg").hide();$(".Editor").hide();$(".xpreview").show();$(".noEditor").show();js2html();break;case "pic-top-A":case "pic-top-B":case "pic-top-C":case "pic-left":case "pic-right":$(".showEimg").show();$(".Editor").hide();$(".xpreview").show();$(".noEditor").show();js2html();break;case "edit":$(".showEimg").hide(),$(".Editor").show(),$(".xpreview").hide(),
$(".noEditor").hide()}}).change();var a=["xbg_img","ximg1","xedit_title","xedit_desc"],c=$('input[name="'+a[0]+'"]').val(),e=$('input[name="'+a[1]+'"]').val(),d=$('input[name="'+a[2]+'"]').val(),f=$('textarea[name="'+a[3]+'"]').val(),b="";b=$('input[name="'+a[0]+'"]');b.change(function(){var a=$(this).val(),b=$(this).attr("name");changeData($(this),c,a,b)});b="";b=$('input[name="'+a[1]+'"]');b.change(function(){var a=$(this).val(),b=$(this).attr("name");changeData($(this),e,a,b)});b="";b=$('input[name="'+
a[2]+'"]');b.change(function(){var a=$(this).val(),b=$(this).attr("name");changeData($(this),d,a,b)});b="";b=$('textarea[name="'+a[3]+'"]');b.change(function(){var a=$(this).val(),b=$(this).attr("name");changeData($(this),f,a,b)})}
function js2html(a){var c=$('input[name="xalign"]:checked').val();a=a||"";if(c){var e=$('input[name="xbg_img"]').val(),d=$('input[name="ximg1"]').val(),f=$('input[name="xedit_title"]').val(),b=$('textarea[name="xedit_desc"]').val();c="common/template/getcontent/<?=$this->template_tb?>/"+c+("?xbg_img="+e)+("&ximg1="+d);c+="&xedit_title="+f;b=b.replace(/\n/g,"<br>");c+="&xedit_desc="+b;a?window.open(c,"\u9810\u89bd"):$("#preview").attr("src",c)}}
function prevFile(a,c){$('input[name="'+a+'"]').val(c);$('input[name="'+a+'"]').trigger("change")}function delFile(a){$('input[name="'+a+'"]').val("");$('input[name="'+a+'"]').trigger("change");$('input[name="'+a+'"]').next("span").html("<?=$this->filemsg?>")};
// // 資料更動
// function changeData(em,olddata,newdata,inputtype) {
//   var em = em || '';
//   var olddata = olddata || '';
//   var newdata = newdata || '';
//   var inputtype = inputtype || '';
//   if (em&&inputtype) {
//     $.post('common/template/datachange',{olddata:olddata,newdata:newdata,inputtype:inputtype},function(a) {
//       var return_val = a.result;
//       em.val(return_val);
//       // 組合html
//       js2html();
//     })
//   }
// }
// // 初始化編輯器樣板
// function initedit() {
//   $('input[name="xalign"]').change(function () {
//     if ($(this).is(':checked')) {
//       switch($(this).val()){
//         case "text-A":
//         case "text-B":
//         case "text-C":
//           $(".showEimg").hide();
//           $(".Editor").hide();
//           $(".xpreview").show();
//           $(".noEditor").show();
//           js2html();
//         break;
//         case "pic-top-A":
//         case "pic-top-B":
//         case "pic-top-C":
//         case "pic-left":
//         case "pic-right":
//           $(".showEimg").show();
//           $(".Editor").hide();
//           $(".xpreview").show();
//           $(".noEditor").show();
//           js2html();
//         break;
//         case "edit":
//           $(".showEimg").hide();
//           $(".Editor").show();
//           $(".xpreview").hide();
//           $(".noEditor").hide();
//           break;
//       }
//     }
//   }).change();
//   //
//   var inputArr = ['xbg_img','ximg1','xedit_title','xedit_desc'];
//   var xbg_img = $('input[name="'+inputArr[0]+'"]').val();
//   var ximg1 = $('input[name="'+inputArr[1]+'"]').val();
//   var xedit_title = $('input[name="'+inputArr[2]+'"]').val();
//   var xedit_desc = $('textarea[name="'+inputArr[3]+'"]').val();
//   // 欄位更改偵測
//   var em = '';
//   var em = $('input[name="'+inputArr[0]+'"]');
//   em.change(function() {
//     var olddata = xbg_img;
//     var newdata = $(this).val();
//     var inputtype = $(this).attr('name');
//     changeData($(this),olddata,newdata,inputtype);
//   });
//   // 欄位更改偵測
//   var em = '';
//   var em = $('input[name="'+inputArr[1]+'"]');
//   var newdata = '/example_new/uploads/images/new.jpg';
//   em.change(function() {
//     var olddata = ximg1;
//     var newdata = $(this).val();
//     var inputtype = $(this).attr('name');
//     changeData($(this),olddata,newdata,inputtype);
//   });
//   // 欄位更改偵測
//   var em = '';
//   var em = $('input[name="'+inputArr[2]+'"]');
//   var newdata = '/example_new/uploads/images/new.jpg';
//   em.change(function() {
//     var olddata = xedit_title;
//     var newdata = $(this).val();
//     var inputtype = $(this).attr('name');
//     changeData($(this),olddata,newdata,inputtype);
//   });
//   // 欄位更改偵測
//   var em = '';
//   var em = $('textarea[name="'+inputArr[3]+'"]');
//   var newdata = '/example_new/uploads/images/new.jpg';
//   em.change(function() {
//     var olddata = xedit_desc;
//     var newdata = $(this).val();
//     var inputtype = $(this).attr('name');
//     changeData($(this),olddata,newdata,inputtype);
//   });
// }
// // 組合html
// function js2html(winopen) {
//   var tb = "<?=$this->template_tb?>";
//   var styleT = $('input[name="xalign"]:checked').val();
//   var winopen = winopen || '';
//   if (tb && styleT) {
//     var xbg_img = $('input[name="xbg_img"]').val();
//     var ximg1 = $('input[name="ximg1"]').val();
//     var xedit_title = $('input[name="xedit_title"]').val();
//     var xedit_desc = $('textarea[name="xedit_desc"]').val();
//     var url = 'common/template/getcontent/'+tb+'/'+styleT;
//     url += '?xbg_img='+xbg_img;
//     url += '&ximg1='+ximg1;
//     url += '&xedit_title='+xedit_title;
//     xedit_desc = xedit_desc.replace(/\n/g,"<br>");
//     url += '&xedit_desc='+xedit_desc;
//     // console.log(url);
//     if(winopen) window.open(url, '預覽'/*, config='height=800,width=1200'*/);
//     else $('#preview').attr('src',url);
//   }
// }
// // 恢復圖片
// function prevFile(em,val) {
//   $('input[name="'+em+'"]').val(val);
//   $('input[name="'+em+'"]').trigger('change');
// }
// // 刪除圖片
// function delFile(em) {
//   $('input[name="'+em+'"]').val('');
//   $('input[name="'+em+'"]').trigger('change');
//   $('input[name="'+em+'"]').next('span').html('<?=$this->filemsg?>');
// }
</script>
