<?php defined('BASEPATH') OR exit('No direct script access allowed');

// controllers 名稱 // 須修改
class Template extends Admin_Controller
{
  // 基本設定
  public $table = 'template_tb'; // 資料庫table name // 須修改

  /***********
    列表頁
  ***********/
  // 表格-呈現欄位 // 須修改
  public $tableTitle = array('刊登','主標');
  public $tableColumn = array('xpublish','xtitle');
  public $tableHeadSize = array('8%','80%');
  public $tableSortValue = array(true,true);
  // 表格-縮圖設定 // 須修改
  protected $imageSize = 'original'; // original、mid、small
  protected $imageArray = array('xfile1');
  // 表格-搜尋欄位 // 須修改
  public $searchTitle = array('主標');
  public $searchColumn = array('xtitle');
  // 表格-其他功能 // 須修改
  public $showPaging = true;
  public $showSearching = true;
  public $showMulti = true;
  // 其他頁面
  public $otherPage = true;
  public $otherPageName = '';
  public $otherPageLink = '';
  public $otherPageCustom = '';

  function __construct()
  {
    parent::__construct();

    $this->is_logged_in(); // 判斷是否登入

    // 頁面設定 // 固定
    $this->maintable = $this->session->userdata('preMainTable');
    $showother = (strpos(current_url(),$this->indexPath)>-1)?false:true;
    if($this->session->userdata('otherPageUrl') && $showother) {
      $this->indexPath = $this->session->userdata('otherPageUrl');
      $this->viewPath = $this->common->resetPath($this->indexPath); // 首頁 // 固定
      $this->formPath = $this->indexPath.'/form'; // 新增頁 // 固定
    }
    if($this->session->userdata('preMenuID')!='') {
      $perid = $this->session->userdata('preid');
    } else $perid = '';
    // 麵包屑
    $this->subnavPath = $this->common->getsubnav($perid); // 固定

    // 頁碼設定
    if(!$this->session->userdata('pageNumber')) {
      $this->session->set_userdata('pageNumber', 1);
    }
    $this->currentPage = $this->session->userdata('pageNumber');

    $this->customTbname = false; // 不更動資料表的前綴詞
    // 自動建立圖片長寬限制資料 (有需要再使用) // 須修改
    // $this->common->checkImageLimit($this->table, 'xfile1', $this->customTbname);
    // 取得圖片資訊 // 須修改
    // $this->imageinfo['xfile1'] = $this->common->getImageinfo($this->table, 'xfile1', $this->customTbname);

    // 匯出檔案名稱
    $this->exportName = date('Y-m-d H:i:s');
  }

  public function index()
  {
    $data = array();

    $this->data['action'] = 'index';

    $layout['content'] = $this->load->view($this->viewPath, $this->data, true);
    $this->load->view($this->adminview, $layout);
  }

  // 讀取列表
  public function read($id = NULL)
  {
    $actionIndex = false; // 是否在列表頁
    // 排序:置入某筆資料
    if($id) $data['data'] = $this->admin_crud->result_array($this->admin_crud->query_where($this->table, array('pid !='=>$id), true, 'xsort', 'asc'));
    else {
      $data['data'] = $this->admin_crud->read('*', $this->table, 'xsort', 'asc');
      $actionIndex = true;
    }
    // 處理列表中圖片
    if(count($data['data']) > 0 && $actionIndex && count($this->imageArray) > 0) { $count = -1;
      foreach ($data['data'] as $value) { $count++;
        for ($i=0;$i<count($this->imageArray);$i++) {
          if(isset($data['data'][$count][$this->imageArray[$i]])) {
            $data['data'][$count][$this->imageArray[$i]] = $this->common->getImagethumb($this->table, $this->imageArray[$i], $this->imageSize, $value[$this->imageArray[$i]], $this->customTbname);
          }
        }
      }
    }
    return $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }

  // 新增/更新介面表單
  public function form($id = NULL)
  {
    // 更新介面
    if(isset($id)) {

      $this->data['action'] = 'update';

      // 讀取列表
      $array = $this->admin_crud->result_array($this->admin_crud->query_where($this->table, array('pid'=>$id)));
      $this->data['list'] = $array[0];

    } else {
      $this->data['action'] = 'create';
    }

    $layout['content'] = $this->load->view($this->viewPath, $this->data, true);
    $this->load->view($this->adminview, $layout);
  }

  function checkRepeat($tb='',$val='',$field='',$id='')
  {
    $bool = false;
    if($tb&&$val&&$field) {
      $where = array($field=>$val);
      if($id) $where['pid !='] = $id;
      $array = $this->admin_crud->result_array($this->admin_crud->query_where($tb, $where, false));
      if(count($array)==0) $bool = true;
    }
    return $bool;
  }

  // 新增/更新
  public function save($id = NULL)
  {
    $xtitle = $this->input->post('xtitle', true);
    $xvar = ($this->input->post('xvar', true)) ? $this->input->post('xvar', true) : '';
    if(
      $xtitle && $xvar
    ) {
        $chR = $this->checkRepeat($this->table,$xtitle,'xtitle',$id);
        if ($chR===true) {
          // 排序選項處理
          $xsort = $this->common->processSort($this->table,$this->input->post('xsort', true),$this->input->post('insertxsortpid', true));

          if(!isset($data['error'])) {
            // DB資料
            $data = array(
              'xpublish'=> ($this->input->post('xpublish', true)) ? $this->input->post('xpublish', true) : '',
              'xtitle'=> $xtitle,
              'xwrap-fs'=> ($this->input->post('xwrap-fs', true)) ? $this->input->post('xwrap-fs', true) : '',
              'xwrap'=> ($this->input->post('xwrap', true)) ? $this->input->post('xwrap', true) : '',
              'xrow'=> ($this->input->post('xrow', true)) ? $this->input->post('xrow', true) : '',
              'xdivimg'=> ($this->input->post('xdivimg', true)) ? $this->input->post('xdivimg', true) : '',
              'xdivtxt'=> ($this->input->post('xdivtxt', true)) ? $this->input->post('xdivtxt', true) : '',
              ($id) ? 'xmodify' : 'xcreate'=> date('Y-m-d H:i:s'),
              'xsort'=> $xsort,
            );

            // 新增、更新動作
            if($id) {
              $actionMessage = '修改';
              $this->admin_crud->update($this->table, $id, $data);
            }
            else {
              $data['xvar'] = $xvar;
              $actionMessage = '新增';
              $id = $this->admin_crud->create($this->table, $data);
            }
            $this->track->trackingDoing($this->table,'xtitle',$this->data['permission']['xname'],$actionMessage,$id,$this->data['selfaccount'],$this->data['selflevel']);
          }
        } else $data['error'] = '請勿重複主標';
    } else {
      $data['error'] = '欄位未輸入';
    }
    $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }

  // 批次
  public function batchItem($action)
  {
    // 勾選的列表項目(pid)
    $array = $this->input->post('data', true);

    if(count($array) > 0) {

      $now = date('Y-m-d H:i:s');

      switch ($action) {
        case 'delete':
          $data['success'] = '刪除';
          foreach ($array as $value) {
            $this->track->trackingDoing($this->table,'xtitle',$this->data['permission']['xname'],$data['success'],$value,$this->data['selfaccount'],$this->data['selflevel']);
            $this->admin_crud->delete($this->table, array('pid'=>$value));
          }
          break;
        case 'release':
          $data['success'] = '刊登';
          foreach ($array as $value) {
            $this->admin_crud->update($this->table, $value, array(
              'xpublish'=> 'yes',
              'xmodify'=> $now
            ));
            $this->track->trackingDoing($this->table,'xtitle',$this->data['permission']['xname'],$data['success'],$value,$this->data['selfaccount'],$this->data['selflevel']);
          }
          break;
        case '_release':
          $data['success'] = '取消刊登';
          foreach ($array as $value) {
            $this->admin_crud->update($this->table, $value, array(
              'xpublish'=> 'no',
              'xmodify'=> $now
            ));
            $this->track->trackingDoing($this->table,'xtitle',$this->data['permission']['xname'],$data['success'],$value,$this->data['selfaccount'],$this->data['selflevel']);
          }
          break;
        case 'home':
          $data['success'] = '刊登首頁';
          foreach ($array as $value) {
            $this->admin_crud->update($this->table, $value, array(
              'xindex'=> 'yes',
              'xmodify'=> $now
            ));
            $this->track->trackingDoing($this->table,'xtitle',$this->data['permission']['xname'],$data['success'],$value,$this->data['selfaccount'],$this->data['selflevel']);
          }
          break;
        case '_home':
          $data['success'] = '取消刊登首頁';
          foreach ($array as $value) {
            $this->admin_crud->update($this->table, $value, array(
              'xindex'=> 'no',
              'xmodify'=> $now
            ));
            $this->track->trackingDoing($this->table,'xtitle',$this->data['permission']['xname'],$data['success'],$value,$this->data['selfaccount'],$this->data['selflevel']);
          }
          break;
        default:
          $data = array();
          break;
      }
    } else {
      $data['error'] = '請選擇項目';
    }
    $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }

  // 拖曳排序
  public function sort()
  {
    $obj = json_decode($this->input->post('data', true));
    $start = $this->input->post('start', true);
    $this->admin_crud->sorting($this->table,$obj[0],$start);
  }

  // 紀錄目前頁面
  public function recordPage()
  {
    $this->session->set_userdata('pageNumber', $this->input->post('page', true));
  }

  // 更新檔案欄位值
  public function saveFile()
  {
    $field = $this->input->post('field', true);
    $id = $this->input->post('id', true);

    $this->admin_crud->update($this->table, $id, array($field => NULL));
  }
  // 匯入
  public function import()
  {
    $data = array();

    $this->data['action'] = 'import';

    $this->data['uploadmsg'] = '';

    $config['upload_path'] = './uploads/';
    $config['allowed_types'] = 'csv|xlsx|xls';
    $config['max_size'] = 100;
    $this->load->library('upload', $config);

    if(isset($_FILES["userfile"])) {
      $file = $config['upload_path'].$_FILES["userfile"]["name"];
      if(!file_exists($file)) {
        if ($this->upload->do_upload('userfile'))
          $this->data['uploadmsg'] = $this->importFile($config['upload_path'].$this->upload->data('file_name'));
        else
          $this->data['uploadmsg'] = $this->upload->display_errors();
      } else if($_FILES["userfile"]["name"] && file_exists($file)) unlink($file);
    }

    $layout['content'] = $this->load->view($this->viewPath, $this->data, true);
    $this->load->view($this->adminview, $layout);
  }

  // 匯入
  public function importFile($file)
  {
    $this->load->helper('file');
    $msg = '';
    if($file) {
      $type = get_mime_by_extension($file);

      switch ($type) {
        case 'text/x-comma-separated-values':
        case 'csv':
          $msg .= 'csv';
          // 設定 索引值 為指定讀取csv中索引位置
          // 設定一個值，例如 'yes'，將該值當為該欄位匯入值
          // 排序、建立日期不需在建立
          $array = array(
            'xpublish'=>'yes',
            'xindex'=>'yes', // 預設值
            'xtitle'=>0, // 讀取csv中索引位置
            'xtarget'=>'yes',
          );
          $compareIndex = 'xtitle'; // 從 array 欄位做為比對依據
          if($count = $this->common->ImportCsv($file,$this->table,$array,$compareIndex))
            $msg .= '成功匯入'.$count.'筆資料';
          else $msg = '未匯入任何資料';
        break;
        case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
        case 'application/vnd.ms-excel':
        case 'xlsx':
          $msg .= 'excel';
          // 設定 A-Z 為指定讀取excel對應欄位
          // 設定一個值，例如 'yes'，將該值當為該欄位匯入值
          // 排序、建立日期不需在建立
          $array = array(
            'xpublish'=>'yes',
            'xindex'=>'yes', // 預設值
            'xtitle'=>'A', // 讀取excel對應欄位
            'xtarget'=>'yes',
          );
          $compareIndex = 'xtitle'; // 從 array 欄位做為比對依據
          if($count = $this->common->ImportExcel($file,$this->table,$array,$compareIndex))
            $msg .= '成功匯入'.$count.'筆資料';
          else $msg = '未匯入任何資料';
        break;
        default:
          $msg = '請匯入excel/csv檔案';
        break;
      }
      unlink($file);
    }
    return $msg;
  }

  // 匯出
  public function export()
  {
    // 不寫入欄位
    $filter = array('pid','xtarget','xfile1','xfile2',
    'xalign','xcontent','xedit2',
    'xurltitle','xseotitle','xseokeyword','xseodescription',
    'xmodify','xsort');
    // 轉換表頭文字
    $toF = array('xpublish'=>'刊登',
      'xindex'=>'刊登首頁','xpostdate'=>'發佈日期','xduedate'=>'下刊日期',
      'xtitle'=>'主標','xsubtitle'=>'副標',
      'xlink'=>'連結',
      'xcreate'=>'建立日'
    );
    $sql = "SELECT * FROM $this->table ";
    $wheresql = '';
    // 篩選項目
    if(isset($_GET['start'])) {
      if($start = $_GET['start']) {
        if(!$wheresql) $wheresql = " WHERE ";
        else $wheresql = " AND ";
        $sql .= $wheresql." xdate >= '".date('Y-m-d',strtotime($start))."' ";
      }
    }
    if(isset($_GET['end'])) {
      if($end = $_GET['end']) {
        if(!$wheresql) $wheresql = " WHERE ";
        else $wheresql = " AND ";
        $sql .= $wheresql." xdate <= '".date('Y-m-d',strtotime($end))."' ";
      }
    }
    if(isset($_GET['status'])) {
      if($xstatus = $_GET['status']) {
        if(!$wheresql) $wheresql = " WHERE ";
        else $wheresql = " AND ";
        $sql .= $wheresql." xstatus = '".$xstatus."' ";
      }
    }
    if(isset($_GET['mark'])) {
      if($xmark = $_GET['mark']) {
        if(!$wheresql) $wheresql = " WHERE ";
        else $wheresql = " AND ";
        $sql .= $wheresql." xmark = '".$xmark."' ";
      }
    }
    $sql .= ' ORDER BY pid ASC';
    $query = $this->db->query($sql);
    if(!$query) return false;

    // 讀取excel函式
    $this->load->library('excel');

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
    $objPHPExcel->setActiveSheetIndex(0);
    // 寫入第一列資訊
    $fields = $query->list_fields();
    $col = 0; // 目前所在欄數
    foreach ($fields as $field)
    {
      // 過濾欄位
      if(!in_array($field,$filter)) {
        // 處理資料 // 更換表頭
        if(isset($toF[$field])) $rowttitle = $toF[$field];
        else $rowttitle = $data->$field;
        // 寫入excel
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $rowttitle);
        $col++;
      }
    }
    // 寫入第二列及之後資訊
    $row = 2; // 第二列
    foreach($query->result() as $data)
    {
      $col = 0; // 目前所在欄數
        foreach ($fields as $field) {
          // 過濾欄位
          if(!in_array($field,$filter)) {
            $add_data = '';
            // 處理資料
            $xval = $data->$field;
            $add_data = $xval;
            // // 範例2
            // if($field=='example') {
            //   $data->欄位名
            // // 其他
            // } else $add_data = $data->$field;

            // 寫入excel
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $add_data);
            $col++;
          }
        }
        $row++;
    }
    $objPHPExcel->setActiveSheetIndex(0);
    //
    /**生成xls文件*/ // php5.2用
    // $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    // header('Content-Type: application/vnd.ms-excel');
    // header('Content-Disposition: attachment;filename="'.$this->exportName.'.xls"');
    // header('Cache-Control: max-age=0');
    /**生成xls文件*/

    /**生成xlsx文件*/ // php5.6用
    $objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$this->exportName.'.xlsx"');
    header('Cache-Control: max-age=0');
    /**生成xlsx文件*/

    $objWriter->save('php://output');
  }
}

// application/controllers/
