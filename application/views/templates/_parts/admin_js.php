<!--啟動(結束)ajax時出現(隱藏)遮罩-->
<script>
window.addEventListener("beforeunload",function(a){
  $.blockUI({
    css:{
      border:"none",
      padding:"15px",
      backgroundColor:"#000",
      "-webkit-border-radius":"10px",
      "-moz-border-radius":"10px",
      opacity:0.5,
      color:"#fff"}
  });
});
$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
</script>
<!--主旨多個信箱-->
<script>
  $(document).on("click",".add_new",function(c){
    var a=[];
    $.each($("input[name='xmail[]']"),function(){
      a.push($(this).val())
    });
    c.preventDefault();
    var b=$(this).parent().parent().clone();
    b.appendTo(".destination")
  });
  $(document).on("click",".remove_one",function(b){
    b.preventDefault();
    var a=$(".copysection").size()-1;if(a){
      if(confirm("您確定要刪除所選取的項目嗎")){
        $(this).closest(".copysection").remove()
      }
    }
  });
</script>
<script>
  synchronousMenu();
  // 左測選單多層同步
  function synchronousMenu() {
    var preid = "<?=$this->session->preid?>";
    var preMenuID = "<?=$this->session->preMenuID?>";
    var href = ''; var url = '';
    url = location.href;
    var ifindex = url.indexOf('<?=$this->defaultURL?>');
    if(preid!=0 && preMenuID && ifindex<=0) {
      href = "<?=(isset($this->mainurlPath))?$this->mainurlPath.'/index/'.$this->session->preid:$this->indexPath.'/index/'.$this->session->preid?>";
      $(".nav.metismenu a[href$='"+href+"']").parents('li').addClass('active');
      $(".nav.metismenu a[href$='"+href+"']").parents('ul.collapse').addClass('in');
      // 子層級目前路徑加上onclick
      var element = $('ul.nav.metismenu a').filter(function() {
          return this.href.indexOf(href) > -1;
      });
      console.log('1');
      console.log(element);
      if(element.length>0) {
        var em2 = element.filter(function() {
            return this.href == "<?=base_url()?>"+href;
        });
        console.log('1-2');
        console.log(em2);
        em2.attr('onclick','javascript:void(0)');
        em2.parent('li').addClass('nowopen');
      } else {
        console.log('1-1');
        element.attr('onclick','javascript:void(0)');
        element.parent('li').addClass('nowopen');
      }
    } else { // 單層、preid=0
      href = "<?=$this->session->nowurl?>";
      var em = $('.nav.metismenu a').filter(function() { // 首頁有 ?lang
          return this.href.indexOf(href) > -1;
      });
      if(em.length>1) {
        console.log('2-1');
        if(ifindex>-1) {
          em.eq(0).parents('li').addClass('active');
          em.eq(0).parent('li').addClass('nowopen');
        }
        else $(".nav.metismenu a[href$='"+href+"']").parents('li').addClass('active');
        console.log(em);
      } else {
        console.log('2-2');
        em.parents('li').addClass('active');
        em.parent('li').addClass('nowopen');
        console.log(em);
      }

      // 多層級第一層目前路徑加上onclick
      if(preMenuID) {
        url = window.location;
        var element = $('ul.nav.metismenu a').filter(function() {
          return this.href == url || this.href.indexOf(href) > -1;
        });
        console.log('3');
        console.log(element);
        // 系列第一層加上onclick
        if(element.length>0) {
          element.eq(0).attr('onclick','javascript:void(0)');
          element.eq(0).parent('li').addClass('nowopen');
        }
        else element.attr('onclick','javascript:void(0)');
      }
    }
  }
  // 轉址+Session
  function recordurl(id, url, name, custom) {
    $.post("<?php echo site_url('common/administer/currenturl'); ?>", { id: id, url: url, name: name, custom: custom }, function(data){
        redirect(data);
    });
  }
  // ckfinder版本判斷
  function ckfinderversion(editor){
    var version = "<?=$this->config->item('ckfinder_version')?>";
    if(version==2) CKFinder.setupCKEditor(editor,'../','Files');
    else CKFinder.setupCKEditor(editor,{resourceType: 'Files',});
  }
  // 初始化CkFinder套件 V2
  function BrowseServer(config)
  {
    var finder = new CKFinder();
    finder.basePath = '../';
    finder.selectActionData = config;
    finder.selectActionFunction = SetFileField;
    finder.resourceType = 'Images';
    finder.popup( 800, 480 );
  }
  function SetFileField(fileUrl, data, allFiles)
  {
    var configid = ''; var type = ''; var custom = '';

    var selectActionData = (data["selectActionData"])?data["selectActionData"].split(','):[];
    if(selectActionData.length > 0) configid = selectActionData[0];
    if(selectActionData.length > 1) type = selectActionData[1];
    if(selectActionData.length > 2) custom = selectActionData[2];

    var msg = '';

    var element = $('input[name="'+configid+'"]');
    if(type == 'multi') msg = '已選擇檔案';
    else {
      if(allFiles.length > 1) {
        alert('不可選擇多個檔案');
        return;
      }
      var split = '';
      if(fileUrl) split = fileUrl.split('/');
      msg = (split.length>0)?split[split.length-1]:fileUrl;
    }

    var fileArray = [];

    for (var i = 0; i < allFiles.length; i++) {
      var path = allFiles[i].url;
      var split = path.split('.');
      var imagetype = split[1];
      var valid = validtype(configid, imagetype, custom);
      if(valid == true) fileArray.push(path);
    }

    if(fileArray.length > 0) element.next().html(msg); // output
    element.val(fileArray); // record
    element.trigger("change"); // 編輯器樣板圖欄位變動觸發change
  }
  // 初始化CkFinder套件 V3
  function generateCkfinder(configid,type,custom) {
    var custom = custom || '';
    CKFinder.popup({
      resourceType: 'Images',
      chooseFiles: true,
      width: 800,
      height: 600,
      onInit: function( finder ) {
        finder.on('files:choose', function(evt) {
            var element = $('input[name="'+configid+'"]');
            var files = evt.data.files;
            // show messages
            var msg = '';
            if(type == 'multi') msg = '已選擇檔案';
            else {
              if(files.length > 1) {
                alert('不可選擇多個檔案');
                return;
              }
              msg = files.first().get('name');
            }
            // read file information
            var fileArray = [];
            // read thumb HxW
            // var HWarray = getHWarray(configid,custom);

            files.forEach( function( file, i ) {
              var path = file.getUrl();
              var split = path.split('.');
              var imagetype = split[1];
              var valid = validtype(configid, imagetype, custom);
              if(valid == true) fileArray.push(file.getUrl());
               // generate Thumb Images
              //  if(HWarray.length > 0) generateThumb(finder, file, HWarray);
            });
            // result
            if(fileArray.length > 0) element.next().html(msg); // output
            element.val(fileArray); // record
            element.trigger("change"); // 編輯器樣板圖欄位變動觸發change
        });
        finder.on('file:choose:resizedImage', function(evt) {
            var filepath = evt.data.resizedUrl;
            var element = $('input[name="'+configid+'"]');
            element.next().html(evt.data.file.get('name')); // output
            element.val(filepath); // record
        });
        // 點選按鈕後，清空input
        if(finder.fire( 'files')) {
            var element = $('input[name="'+configid+'"]');
            element.next().html("<?=$this->filemsg?>"); // output
            element.val(''); // record
        }
      }
    });
  }
  // 初始化 相簿 CkFinder套件 V2
  function BrowseServerAlbum(config)
  {
    var finder = new CKFinder();
    finder.basePath = '../';
    finder.selectActionData = config;
    finder.selectActionFunction = SetAlbumFileField;
    finder.resourceType = 'Images';
    finder.popup( 800, 480 );
  }
  function SetAlbumFileField(fileUrl, data, allFiles)
  {
    var configid = ''; var type = ''; var custom = '';

    var selectActionData = (data["selectActionData"])?data["selectActionData"].split(','):[];
    if(selectActionData.length > 0) configid = selectActionData[0];
    if(selectActionData.length > 1) type = selectActionData[1];
    if(selectActionData.length > 2) custom = selectActionData[2];

    var msg = '';

    var element = $('input[name="'+configid+'"]');
    if(type == 'multi') msg = '已選擇檔案';
    else {
      if(allFiles.length > 1) {
        alert('不可選擇多個檔案');return false;
      }
      var split = '';
      if(fileUrl) split = fileUrl.split('/');
      msg = (split.length>0)?split[split.length-1]:fileUrl;
    }

    var fileArray = [];

    for (var i = 0; i < allFiles.length; i++) {
      var path = allFiles[i].url;
      var split = path.split('.');
      var imagetype = split[1];
      var valid = validtype(configid, imagetype, custom);
      if(valid == true) {
        var xfile1 = path;
        var Namearray = path.split('/');
        var xtitle = Namearray[Namearray.length-1];
        var url = '<?=site_url($this->indexPath."/save/".NULL);?>';
        $.post(url,{'xtitle':xtitle,'xfile1':xfile1},function(a){
          if(a.error){alert(a.error);return false}
        });
      }
    }
    location.reload();

    // if(fileArray.length > 0) element.next().html(msg); // output
    // element.val(fileArray); // record
  }
  // 初始化 相簿 CkFinder套件 V3
  function generateAlbumCkfinder(configid,type,custom) {
    var custom = custom || '';
    CKFinder.popup({
      resourceType: 'Images',
      chooseFiles: true,
      width: 800,
      height: 600,
      onInit: function( finder ) {
        finder.on('files:choose', function(evt) {
            var element = $('input[name="'+configid+'"]');
            var files = evt.data.files;
            // show messages
            var msg = '';
            if(type == 'multi') msg = '已選擇檔案';
            else {
              if(files.length > 1) {
                alert('不可選擇多個檔案'); return false;
              }
              msg = files.first().get('name');
            }
            // read file information
            var fileArray = []; var fileNameArray = [];
            // read thumb HxW
            // var HWarray = getHWarray(configid,custom);

            files.forEach( function( file, i ) {
              var path = file.getUrl();
              var split = path.split('.');
              var imagetype = split[1];
              var valid = validtype(configid, imagetype, custom);
              if(valid == true) {
                var xfile1 = file.getUrl();
                var xtitle = file.get('name');
                var url = '<?=site_url($this->indexPath."/save/".NULL);?>';
                $.post(url,{'xtitle':xtitle,'xfile1':xfile1},function(a){
                  if(a.error){alert(a.error);return false}
                });
              }
               // generate Thumb Images
              //  if(HWarray.length > 0) generateThumb(finder, file, HWarray);
            });
            location.reload();
            // // result
            // if(fileArray.length > 0) element.next().html(msg); // output
            // element.val(fileArray); // record
        });
        finder.on('file:choose:resizedImage', function(evt) {
            var filepath = evt.data.resizedUrl;
            var element = $('input[name="'+configid+'"]');
            element.next().html(evt.data.file.get('name')); // output
            element.val(filepath); // record
        });
        // 點選按鈕後，清空input
        if(finder.fire( 'files')) {
            var element = $('input[name="'+configid+'"]');
            element.next().html("<?=$this->filemsg?>"); // output
            element.val(''); // record
        }
      }
    });
  }
  // album
  // 初始化 相簿 CkFinder套件 V2
  function BrowseServerAlbum(config)
  {
    var finder = new CKFinder();
    finder.basePath = '../';
    finder.selectActionData = config;
    finder.selectActionFunction = SetAlbumFileField;
    finder.resourceType = 'Images';
    finder.popup( 800, 480 );
  }
  function SetAlbumFileField(fileUrl, data, allFiles)
  {
    var configid = ''; var type = ''; var custom = '';

    var selectActionData = (data["selectActionData"])?data["selectActionData"].split(','):[];
    if(selectActionData.length > 0) configid = selectActionData[0];
    if(selectActionData.length > 1) type = selectActionData[1];
    if(selectActionData.length > 2) custom = selectActionData[2];

    var msg = '';

    var element = $('input[name="'+configid+'"]');
    if(type == 'multi') msg = '已選擇檔案';
    else {
      if(allFiles.length > 1) {
        alert('不可選擇多個檔案');return false;
      }
      var split = '';
      if(fileUrl) split = fileUrl.split('/');
      msg = (split.length>0)?split[split.length-1]:fileUrl;
    }

    var fileArray = [];

    for (var i = 0; i < allFiles.length; i++) {
      var path = allFiles[i].url;
      var split = path.split('.');
      var imagetype = split[1];
      var valid = validtype(configid, imagetype, custom);
      if(valid == true) {
        var xfile1 = path;
        var Namearray = path.split('/');
        var xtitle = Namearray[Namearray.length-1];
        var url = '<?=site_url($this->indexPath."/save/".NULL);?>';
        $.post(url,{'xtitle':xtitle,'xfile1':xfile1},function(a){
          if(a.error){alert(a.error);return false}
        });
      }
    }
    location.reload();

    // if(fileArray.length > 0) element.next().html(msg); // output
    // element.val(fileArray); // record
  }
  // 初始化 相簿 CkFinder套件 V3
  function generateAlbumCkfinder(configid,type,custom) {
    var custom = custom || '';
    CKFinder.popup({
      resourceType: 'Images',
      chooseFiles: true,
      width: 800,
      height: 600,
      onInit: function( finder ) {
        finder.on('files:choose', function(evt) {
            var element = $('input[name="'+configid+'"]');
            var files = evt.data.files;
            // show messages
            var msg = '';
            if(type == 'multi') msg = '已選擇檔案';
            else {
              if(files.length > 1) {
                alert('不可選擇多個檔案'); return false;
              }
              msg = files.first().get('name');
            }
            // read file information
            var fileArray = []; var fileNameArray = [];
            // read thumb HxW
            // var HWarray = getHWarray(configid,custom);

            files.forEach( function( file, i ) {
              var path = file.getUrl();
              var split = path.split('.');
              var imagetype = split[1];
              var valid = validtype(configid, imagetype, custom);
              if(valid == true) {
                var xfile1 = file.getUrl();
                var xtitle = file.get('name');
                var url = '<?=site_url($this->indexPath."/save/".NULL);?>';
                $.post(url,{'xtitle':xtitle,'xfile1':xfile1},function(a){
                  if(a.error){alert(a.error);return false}
                });
              }
               // generate Thumb Images
              //  if(HWarray.length > 0) generateThumb(finder, file, HWarray);
            });
            location.reload();
            // // result
            // if(fileArray.length > 0) element.next().html(msg); // output
            // element.val(fileArray); // record
        });
        finder.on('file:choose:resizedImage', function(evt) {
            var filepath = evt.data.resizedUrl;
            var element = $('input[name="'+configid+'"]');
            element.next().html(evt.data.file.get('name')); // output
            element.val(filepath); // record
        });
        // 點選按鈕後，清空input
        if(finder.fire( 'files')) {
            var element = $('input[name="'+configid+'"]');
            element.next().html("<?=$this->filemsg?>"); // output
            element.val(''); // record
        }
      }
    });
  }
  // album

  // 取得圖片限制長寬
  function getHWarray(configid,custom) {
     var HWarray; var custom = custom || '';
     $.ajax({
        url: "common/administer/getImageLimit/<?=$this->table?>/"+configid+'/'+custom,
        async: false,
        success: function(data){
          HWarray = data;
       }
     });
    return HWarray;
  }
  // 檢查副檔名是否可以合法
  function validtype(configid, type, custom) {
    var result = ''; var custom = custom || '';
    $.ajax({
       url: "common/administer/checkImagetype/<?=$this->table?>/"+configid+'/'+type+'/'+custom,
       async: false,
       success: function(data){
         result = data;
      }
    });
   return result;
  }
  // 表格初始化
  function initTableOption(url) {
    var page = parseInt('<?php if(isset($this->currentPage)) echo $this->currentPage; ?>');
    var pagesize = parseInt('<?php if(isset($this->pageSize)) echo $this->pageSize; ?>');
    var menuArray = [ 10, 25, 50, 75, 100 ];  var menuArray2 = [];
    var hasitem = menuArray.indexOf(pagesize);
    if(hasitem==-1) { var count = 0; var add = 0;
      for (var i = 0; i < menuArray.length; i++) {
        if(menuArray[i]>pagesize && add==0) { add++;
          menuArray2[count] = pagesize;
        }
        menuArray2[count+add] = menuArray[i];
        count++;
      }
    } else menuArray2 = menuArray;
    // 表格參數
    options = {
      language: {
        emptyTable: "尚未有任何資料",
        infoEmpty: "總共 0 筆",
        lengthMenu: "每頁顯示 _MENU_ 筆",
        search: "搜尋全部欄位:",
        paginate: {
          first: "第一頁",
          previous: "上一頁",
          next: "下一頁",
          last: "最後一頁",
        }
      },
      info: false, // 顯示/隱藏介紹
      paging: "<?=$this->showPaging?>", // 顯示/隱藏分頁
      searching: "<?=$this->showSearching?>", // 顯示/隱藏search bar
      lengthChange: true, // 顯示/隱藏每頁顯示 _MENU_ 筆
      lengthMenu: menuArray2,
      pageLength: pagesize,
      processing: true,
      ajax: {
        url: url,
      },
      order: [], // 不預設排序
      columnDefs:[],
      createdRow: function( row, data, dataIndex ) {
        $(row).addClass('gradeX');
        $(row).attr('data-id', data.pid)
      },
      pagingType: "simple_numbers", // 頁碼顯示格式
      displayStart : (page-1) * pagesize, // 第一個顯示項目索引值
      autoWidth: false,
    };
    return options;
  }
  // 列表 input 可用 value 排序
  $.fn.dataTable.ext.order['dom-text'] = function  ( settings, col )
  {
      return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
          return $('input', td).val();
      } );
  }
  // 批次動作
  function batchItem(string){
   // 取得項目
   var chkBoxArray = [];
   $.each($("input[name='checkbox[]']:checked"), function(){
       chkBoxArray.push($(this).val());
   });

   if(chkBoxArray.length == 0) {
     alert('請選擇項目');
     return false;
   }
   // 執行動作 // 跳出確定
   if(confirm('確定執行?')){
     $.post("<?php echo site_url("$this->indexPath/batchItem"); ?>"+"/"+string, { data : chkBoxArray}, function(data){
       if(data.error) alert(data.error);
       if(data.success) location.reload();
     });
   }
  }
  // 拖曳排序
  function sort(jsonString) {
    var start = $('.dataTables-example').DataTable().page.info().start;
    $.post("<?php echo site_url("$this->indexPath/sort"); ?>", { data : jsonString, start : start }, function(data){
    });
  }
  // 置入某筆排序
  function readSort(id,column,xMulti) {
    id = id || 0;
    column = column || 'xtitle';
    xMulti = xMulti || false;
    // 讀取表格資料
    if ($.fn.dataTable.isDataTable('.sortDataTables')) {
      table = $('.sortDataTables').DataTable();
    } else {
      if(xMulti==true) var url = "<?php echo site_url($this->indexPath.'/read');?>"+"/"+id+"/<?=$this->session->userdata('preid')?>";
      else var url = "<?php echo site_url($this->indexPath.'/read');?>"+"/"+id;

      var table = $('.sortDataTables').DataTable({
        language: {
        emptyTable: "尚未有任何資料",
        infoEmpty: "總共 0 筆",
        lengthMenu: "每頁顯示 _MENU_ 筆",
        search: "搜尋全部欄位:",
        paginate: {
          first: "第一頁",
          previous: "上一頁",
          next: "下一頁",
          last: "最後一頁",
          }
        },
        info: false, // 顯示/隱藏介紹
        processing: true,
        ajax: {
          url: url,
        },
        columns: [
          { "data": column },
        ],
        select: {
            style: 'single'
        },
        order: [],
      });
    }

    // 紀錄已選擇/取消選擇項目
    table.on('select', function (e, dt, type, indexes) {
         var rowData = table.rows( indexes ).data().toArray();
         $("input[name='insertxsortpid']").val(rowData[0]['pid']);
     })
     .on('deselect', function ( e, dt, type, indexes) {
       $("input[name='insertxsortpid']").val('');
     });
  }
  // 刪除檔案
  function deleteFile(id, field) {
    if(confirm("確定刪除")) {
      $.post("<?php echo site_url("$this->indexPath/saveFile");?>",{ id: id, field: field }, function(data){
        alert('刪除成功');
        redirect(document.referrer);
      });
    }
  }
  // 判斷目前要讀取哪個function
  var action = "<?=$action?>";
  if(action == 'index') listinit();
  if(action != 'index') {
    forminit();
    // 置入某筆排序
    $('input:radio[name="xsort"]').on( 'change', function () {
        if(this.value == 'insert') $('.table-insert').show();
        else $('.table-insert').hide();
    });
  }
  // unlink
  function handler(){return;}
  // 手機移除排序class
  if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
    $('table').removeClass('sorted_table');
  }
  // 上稿後SEO自動同步
  function AutoUpdSeo(tb,input,output,isurl) {
    if(tb && input && output && isurl) {
      $.post('common/administer/AutoUpdSeo'+'/'+tb,{input:input,output:output,isurl:isurl},function(data) {alert('同步成功'); location.reload();});
    }
  }
</script>
<!--首頁圖表-->
<script>
  window.chartColors = {
    red: 'rgb(255, 99, 132)',
    orange: 'rgb(255, 159, 64)',
    yellow: 'rgb(255, 205, 86)',
    green: 'rgb(75, 192, 192)',
    blue: 'rgb(54, 162, 235)',
    purple: 'rgb(153, 102, 255)',
    grey: 'rgb(231,233,237)'
  };
</script>
<!--切換語系-->
<script>
  function changelang(lang) {
    var url = '<?=$this->defaultURL?>'+'?lang='+lang;
    var bool = '<?=(isset($_GET["lang"]))?true:false?>';
    var oldlang = '<?=$this->searchlang?>';
    var inform = "<?=strpos(current_url(),'/form')?>"; // 有form導回首頁
    if(!bool && oldlang) {
      var str = '/'+oldlang+'/';
      var array = window.location.href.split(str);
      if(array.length>0 && inform<=0) url = array[0]+'/'+lang+'/'+array[1];
    }
    redirect(url);
  }
</script>
