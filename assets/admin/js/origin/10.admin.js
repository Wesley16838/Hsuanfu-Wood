function logout() {
  $.ajax({
    url: "common/administer/logout",
    success: function(data){
      redirect(data);
   }
 });
}

function Menu(link, id) {
  $.ajax({
    url: "common/administer/menu/"+id,
    success: function(data){
   }
 });
}

// 轉址
function redirect(url, id) {
    var id = id || 0;
    if (!id && id != 0) {
        $.ajax({
            url: "common/administer/menu/" + id,
            success: function(data) {
                top.location.href = url;
            }
        });
    } else top.location.href = url;
}

// 初始化表單預設值
function InitForm(type, element, val) {
  val = val || '';
  switch (type) {
    case 'checked':
      $('input:radio[name="'+element+'"]').parent().removeClass('active');
      $('input:radio[name="'+element+'"][value="'+val+'"]').attr('checked',true);
      $('input:radio[name="'+element+'"][value="'+val+'"]').parent().addClass('active');
      break;
    case 'inputval':
      $('input[name="'+element+'"]').val(val);
      break;
    case 'selectval':
      $('select[name="'+element+'"]').val(val);
      break;
    default:
  }
}

// 初始化NestedSortable的套件
function generateNestedSortable(maxDepth) {

  $(document).ready(function(){
    // 初始化 nestedSortable
    var lastclass = 'dep'+(maxDepth-1);
    var prelastclass = 'dep'+(maxDepth-2);

    $('ol.sortable').nestedSortable({
        handle: 'div',
        items: 'li',
        toleranceElement: '> div',
        maxLevels: maxDepth,
        protectRoot: true, // 第一層不可動，只可以平行移動
        isAllowed: function(item, parent) {

          // 不可移動到第一層、中間層往上移，若有帶最後一層不可移動，除了平行移動之外
          if(item.find("li").hasClass(lastclass) && !parent || item.find("li").hasClass(lastclass) && !parent.hasClass(item.parent().closest("li").attr('class'))) return false;

          // 不可移動到第一層、不可移到中間層(=上層不為 prelastclass)
          if(item.hasClass(lastclass) && !parent || item.hasClass(lastclass) && !parent.hasClass(prelastclass)) return false;

          return true;
        }
    });

    // nestedSortable 按鈕控制
    $('.nestable-menu').on('click', function (e) {
        var target = $(e.target),
            action = target.data('action');
        if (action === 'sort') {
            sortable_sort();
        }
    });
  });

  // 載入 nestedSortable 資料
  sortable_read(maxDepth);
}

// 初始化NestedSortable的套件 (NotAllowed)
function generateNestedSortableNotAllowed(maxDepth,xlang) {
  $(document).ready(function(){
    // 初始化 nestedSortable
    var lastclass = 'dep'+(maxDepth-1);
    var prelastclass = 'dep'+(maxDepth-2);

    $('ol.sortable').nestedSortable({
        handle: 'div',
        items: 'li',
        toleranceElement: '> div',
        maxLevels: maxDepth,
    });

    // nestedSortable 按鈕控制
    $('.nestable-menu').on('click', function (e) {
        var target = $(e.target),
            action = target.data('action');
        if (action === 'sort') {
            sortable_sort();
        }
    });
  });

  // 載入 nestedSortable 資料
  sortable_read(maxDepth,xlang);
}

// 初始化CKEDITOR套件
function generateCkeditor(ckName, template) {
  template = template || 'empty';
  var array = location.pathname.split('/');
  var baseurl = "//"+location.hostname+'/';
  if(array[1] != 'admin') baseurl += array[1]+'/';

  var editor = CKEDITOR.replace(ckName,{
    templates: template,
    on: {
      instanceReady: function(argument) {
        $.ajax({
          type: "POST",
          url: 'common/administer/getTemplates/'+template,
          async: false,
          dataType: "json",
          success: function(data) {
            CKEDITOR.addTemplates(template,{
              imagesPath:CKEDITOR.getUrl('//'+location.hostname+'/'),
              templates: data
            });
          }
        });
        $.ajax({
          type: "POST",
          url: 'common/administer/getHtml/'+template,
          async: false,
          dataType: "json",
          success: function(data) {
            CKEDITOR.config.headerHtml = data.header;
            CKEDITOR.config.footerHtml = data.footer;
          }
        });
      }
    },
     filebrowserBrowseUrl : baseurl+'assets/ckfinder/ckfinder.html',
     filebrowserImageBrowseUrl : baseurl+'assets/ckfinder/ckfinder.html?type=Files',
     filebrowserUploadUrl : baseurl+'assets/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
     filebrowserImageUploadUrl : baseurl+'assets/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
  });

  var url = window.location.pathname;
  $.ajax({
    type: "POST",
    url: 'common/administer/getCss',
    data: {'url': url},
    async: false,
    dataType: "json",
    success: function(data) {
      var array = [baseurl+'assets/css/style.min.css'];
      CKEDITOR.config.contentsCss = (data.css)?baseurl+data.css:array;
    }
  });

  ckfinderversion(editor);

  editor.on( 'change', function( evt ) {
    $("textarea[name='"+evt.editor.name+"']").val(evt.editor.getData()); // 同步PHP
  });
  // 不使用還是會有Bug，輸入後讀取不到最新的
  editor.on('key',function(evt){
    $("textarea[name='"+evt.editor.name+"']").val(evt.editor.getData()); // 同步PHP
  });
  editor.on('blur', function(evt) {
    $("textarea[name='"+evt.editor.name+"']").val(evt.editor.getData()); // 同步PHP
  });
}

// 初始化Datepicker套件
function generateDatepicker(Input) {
  $('input[name="'+Input+'"]').datepicker({
    todayBtn: "linked",
    format: "yyyy-mm-dd",
    keyboardNavigation: false,
    forceParse: false,
    calendarWeeks: true,
    autoclose: true
  });
}

// 產生縮圖
function generateThumb(finder, file, HWarray) {
  for (var i = 0; i < HWarray.length; i++) {
    finder.request( 'image:resize', { file: file, name: '__custom', size: HWarray[i], save: true }).then( function( file ) {});
    finder.request( 'image:getResized', { file: file } ).then( function( file ) {
    });
  }
}

function setTableSort() {
  var group2 = $('table.sorted_table').sortable({
    delay: 300,
    group2: 'sorted_table',
    onDrop: function ($item, container, _super) {
      var data = group2.sortable("serialize").get();
      var jsonString = JSON.stringify(data, null, ' ');
      sort(jsonString);
      _super($item, container);
    },
    containerSelector: 'table', // 選擇器
    itemPath: '> tbody', // 項目上層
    itemSelector: 'tr', // 項目
    placeholder: '<tr class="placeholder"/>',
  });
}

function setTableOther() {
  // 個別欄位搜尋
  $('input.column_filter').on( 'keyup click', function () {
      filterColumn($('select.column_filter').val());
  });
  // 全選/取消全選
  $("#clickAll").click(function() {
     if($("#clickAll").prop("checked")) {
       $("input[name='checkbox[]']").each(function() {
           $(this).prop("checked", true);
       });
     } else {
       $("input[name='checkbox[]']").each(function() {
           $(this).prop("checked", false);
       });
     }
  });
}

// 個別欄位搜尋
function filterColumn (Column) {
  $('.dataTables-example').DataTable().column( Column+':name' ).search(
      $('#col_filter').val()
  ).draw();
}

// 預覽檔案
function previewFile(path) {
  window.open(path);
}


/** 同步欄位 **/
// 去除前後空格
function _trim(str) {
  return str.replace(/(^\s+)|(\s+$)/g, "");
}
// 去除所有空格
function removeAllSpace(str) {
  return str.replace(/\s+/g, "-");
}
// 去除特殊符號
function clearString(s){
  var pattern = new RegExp("[`~!@#$^&*()=|{}':;',\\[\\].<>/?~！@#￥……&*（）&;|{}【】‘；：”“'。，、？]")
  var rs = ""; var item='';
  for (var i = 0; i < s.length; i++) {
      item = s.substr(i, 1);
      if(/^[\u0391-\uFFE5|,\d,A-Za-z,\-,%]+$/.test(item)) {
        if(!pattern.test(item)) {
          if (i!=0 && item=='-') {
            if(rs.substr(-1,1)!=item) rs = rs+item;
          } else rs = rs+item;
        }
      }
  }
  return rs;
}
// 接值
function autofield(tb,IelNstr,OelNstr,OelTstr,urlstr) {
    var urlstr = urlstr || '';
    var IelNarray = IelNstr.split(','); var OelNarray = OelNstr.split(',');
    var OelTarray = OelTstr.split(','); var urlarray = urlstr.split(',');
    var inputtype = '',inputname = '',outputtype = '',outputname = '',isurl = '';
    for (var key in OelNarray) {
      inputname = IelNarray[key]; outputtype = OelTarray[key];
      outputname = OelNarray[key]; isurl = urlarray[key];
      processauto(tb,inputname,outputname,outputtype,isurl);
    }
}
// 是否需要同步
function processauto(tb,inputname,outputname,outputtype,isurl) {
  $.post('common/administer/offauto'+'/'+tb+'/'+outputname+'/"true"',function(data) {
    if(data=='no') autoinput(inputname,outputname,outputtype,isurl);
  })
}
// 同步
function autoinput(inputname,outputname,outputtype,isurl) {
  var inputname = inputname || ''; var outputtype = outputtype || ''; var outputname = outputname || '';
  var isurl = isurl || false;
  if(inputname && outputtype && outputname) {
    var input = $(outputtype+'[name='+inputname+']').val();
    var output = '';
    if(isurl==='1') {
        output = _trim(input);
        output = removeAllSpace(output);
        output = clearString(output);
    } else output = input;
    $(outputtype+'[name='+outputname+']').val(output);
  }
  return;
}
// 開啟/關閉同步
function offauto(tb,inputname,outputname,outputtype,urlstr) {
  var urlstr = urlstr || false;
  $.post('common/administer/offauto'+'/'+tb+'/'+outputname,function(data) {
    if(outputtype == 'textarea') var mt = 'm-t';
    else var mt = '';
    var input = $(outputtype+'[name='+inputname+']');
    var output = $(outputtype+'[name='+outputname+']');
    // 同步 // 暗 // 顯示自定義
    if(data=='no') {var btnname = '自定義'; var color = 'btn-default'; output.attr('disabled',true)
      autoinput(inputname,outputname,outputtype,urlstr); // 將值塞到目的地
    }
    else {var btnname = '開啟同步'; var color = 'btn-default'; output.attr('disabled',false)}
    var elspen = output.next(); // 更改按鈕文字
    var spen = '<button type="button" class="'+mt+' btn '+color+'"'+
    'onclick="javascript:offauto(\''+tb+'\',\''+inputname+'\',\''+outputname+'\',\''+outputtype+'\',\''+urlstr+'\')">'+
    btnname+'</button>';
    elspen.html(spen);
  })
}
/** 同步欄位 **/
