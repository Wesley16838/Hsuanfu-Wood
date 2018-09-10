<?php defined('BASEPATH') OR exit('No direct script access allowed');

// controllers 名稱 // 須修改
class Contact extends Admin_Controller
{
  // 基本設定
  public $table = 'contact_tb'; // 資料庫table name // 須修改

  /***********
    列表頁
  ***********/
  // 表格-呈現欄位 // 須修改
  public $tableTitle = array('處理','重要','姓名','公司','電話','修改者','填表日');
  public $tableColumn = array('xstatus','xmark','xname','xcompany','xtel','xmodifyuser','xcreate');
  public $tableHeadSize = array('5%','5%','10%','15%','10%','10%','10%');
  public $tableSortValue = array(true,true,true,true,true,true,true);
  // 表格-縮圖設定 // 須修改
  protected $imageSize = 'original'; // original、mid、small
  protected $imageArray = array('xfile1');
  // 表格-搜尋欄位 // 須修改
  public $searchTitle = array('姓名','公司','電話','修改者');
  public $searchColumn = array('xname','xcompany','xtel','xmodifyuser');
  // 表格-其他功能 // 須修改
  public $showPaging = true;
  public $showSearching = true;
  public $showdateSearching = true;
  public $showMulti = true;
  // 其他頁面
  public $otherPage = true;

  function __construct()
  {
    parent::__construct();

    $this->is_logged_in(); // 判斷是否登入

    // 頁面設定 // 固定
    $this->maintable = $this->session->userdata('preMainTable');
    $showother = (strpos(current_url(),$this->indexPath)>-1)?false:true;
    if($this->session->userdata('otherPageUrl') && $showother) {
      $this->indexPath = $this->session->userdata('otherPageUrl');
      $this->viewPath = $this->indexPath.'_view'; // 首頁 // 固定
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

    // 匯出檔案名稱
    $this->exportName = date('Y-m-d H:i:s');
  }

  public function index()
  {
    $data = array();

    $this->data['action'] = 'index';

    // 讀取選單
    $this->data['menu'] = $this->admin_crud->read("distinct year(xcreate) as year", $this->table, 'year', 'desc');

    $layout['content'] = $this->load->view($this->viewPath, $this->data, true);
    $this->load->view($this->adminview, $layout);
  }

  // 讀取列表
  public function read($id = NULL)
  {
    $actionIndex = false; // 是否在列表頁
    // 排序:置入某筆資料
    if($id) $data['data'] = $this->admin_crud->result_array($this->admin_crud->query_where($this->table, array('pid !='=>$id), true, 'xcreate', 'desc'));
    else {
      $data['data'] = $this->admin_crud->read('*', $this->table, 'xcreate', 'desc');
      $actionIndex = true;
    }
    // 處理列表中圖片
    if(count($data['data']) > 0 && $actionIndex && count($this->imageArray) > 0) { $count = -1;
      foreach ($data['data'] as $value) { $count++;
        for ($i=0;$i<count($this->imageArray);$i++) {
          if(isset($data['data'][$count][$this->imageArray[$i]])) {
            $data['data'][$count][$this->imageArray[$i]] = $this->common->getImagethumb($this->table, $this->imageArray[$i], $this->imageSize, $value[$this->imageArray[$i]]);
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
      // 判斷該帳號是否已經讀取該表單
      $newxreadeach = ''; $match = md5($this->data['selfaccount']).',';
      foreach ($array as $value) {
        if(strpos($value['xreadeach'], $match) <= -1 && $value['xstatus'] == 'no') $newxreadeach .= $value['xreadeach'].$match;
        else $newxreadeach .= $value['xreadeach'];
      }
      $this->admin_crud->update($this->table,$id,array('xread'=>'yes','xreadeach'=>$newxreadeach));

    } else {
      $this->data['action'] = 'create';
    }

    $layout['content'] = $this->load->view($this->viewPath, $this->data, true);
    $this->load->view($this->adminview, $layout);
  }

  // 新增/更新
  public function save($id = NULL)
  {
    // DB資料
    $data = array(
      'xstatus'=> ($this->input->post('xstatus', true)) ? $this->input->post('xstatus', true) : '',
      'xmark'=> ($this->input->post('xmark', true)) ? $this->input->post('xmark', true) : '',
      'xanote'=> ($this->input->post('xanote', true)) ? $this->input->post('xanote', true) : '',
      'xmodifyuser'=> $this->data['selfaccount'],
      ($id) ? 'xmodify' : 'xcreate'=> date('Y-m-d H:i:s'),
    );

    // 新增、更新動作
    if($id) {
      $actionMessage = '修改';
      $this->admin_crud->update($this->table, $id, $data);
    }
    else {
      // $actionMessage = '新增';
      // $id = $this->admin_crud->create($this->table, $data);
    }
    $this->track->trackingDoing($this->table,'xname',$this->data['permission']['xname'],$actionMessage,$id,$this->data['selfaccount'],$this->data['selflevel']);

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
            $this->track->trackingDoing($this->table,'xname',$this->data['permission']['xname'],$data['success'],$value,$this->data['selfaccount'],$this->data['selflevel']);
            $this->admin_crud->delete($this->table, array('pid'=>$value));
          }
          break;
        case 'status':
          $data['success'] = '已處理';
          foreach ($array as $value) {
            $this->admin_crud->update($this->table, $value, array(
              'xstatus'=> 'yes',
              'xmodifyuser'=> $this->data['selfaccount'],
              'xmodify'=> $now
            ));
            $this->track->trackingDoing($this->table,'xname',$this->data['permission']['xname'],$data['success'],$value,$this->data['selfaccount'],$this->data['selflevel']);
          }
          break;
        case '_status':
          $data['success'] = '待處理';
          foreach ($array as $value) {
            $this->admin_crud->update($this->table, $value, array(
              'xstatus'=> 'no',
              'xmodifyuser'=> $this->data['selfaccount'],
              'xmodify'=> $now
            ));
            $this->track->trackingDoing($this->table,'xname',$this->data['permission']['xname'],$data['success'],$value,$this->data['selfaccount'],$this->data['selflevel']);
          }
          break;
        case 'mark':
          $data['success'] = 'Mark';
          foreach ($array as $value) {
            $this->admin_crud->update($this->table, $value, array(
              'xmark'=> 'yes',
              'xmodifyuser'=> $this->data['selfaccount'],
              'xmodify'=> $now
            ));
            $this->track->trackingDoing($this->table,'xname',$this->data['permission']['xname'],$data['success'],$value,$this->data['selfaccount'],$this->data['selflevel']);
          }
          break;
        case '_mark':
          $data['success'] = 'UnMark';
          foreach ($array as $value) {
            $this->admin_crud->update($this->table, $value, array(
              'xmark'=> 'no',
              'xmodifyuser'=> $this->data['selfaccount'],
              'xmodify'=> $now
            ));
            $this->track->trackingDoing($this->table,'xname',$this->data['permission']['xname'],$data['success'],$value,$this->data['selfaccount'],$this->data['selflevel']);
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

    foreach ($obj[0] as $key => $value) {
      $this->admin_crud->update($this->table, $value->id, array('xsort' => $start+$key+1));
    }
  }

  // 紀錄目前頁面
  public function recordPage()
  {
    $this->session->set_userdata('pageNumber', $this->input->post('page', true));
  }

  // 匯出
  public function export()
  {
    // 不寫入欄位
    $filter = array('pid','xstatus','xmark','xread','xreadeach','xanote','xerror','xmodifyuser','xmodify');
    // 轉換表頭文字
    $toF = array(
      'xsubjectname'=>'主旨標題','xsubjectmail'=>'收件人',
      'xname'=>'姓名','xcompany'=>'公司','xtel'=>'電話','xaddress'=>'地址',
      'xmail'=>'信箱','xmessage'=>'留言',
      'xcreate'=>'填表日'
    );
    $sql = "SELECT * FROM $this->table ";
    $wheresql = '';
    // 篩選項目
    if(isset($_GET['start'])) {
      if($start = $_GET['start']) {
        if(!$wheresql) $wheresql = " WHERE ";
        else $wheresql = " AND ";
        $sql .= $wheresql." xcreate >= '".date('Y-m-d',strtotime($start))."' ";
      }
    }
    if(isset($_GET['end'])) {
      if($end = $_GET['end']) {
        if(!$wheresql) $wheresql = " WHERE ";
        else $wheresql = " AND ";
        $sql .= $wheresql." xcreate <= '".date('Y-m-d',strtotime($end))."' ";
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
