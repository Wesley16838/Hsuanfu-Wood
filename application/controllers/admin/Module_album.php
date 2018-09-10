<?php defined('BASEPATH') OR exit('No direct script access allowed');

// controllers 名稱 // 須修改
class Module_album extends Admin_Controller
{
  // 基本設定
  public $table = 'module_album'; // 資料庫table name // 須修改

  /***********
    列表頁
  ***********/
  // 表格-呈現欄位 // 須修改
  public $tableTitle = array('刊登','縮圖','名稱');
  public $tableColumn = array('xpublish','xfile1','xtitle');
  public $tableHeadSize = array('8%','10%','70%');
  public $tableSortValue = array(true,false,true);
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

  function __construct()
  {
    parent::__construct();

    $this->is_logged_in(); // 判斷是否登入
    // $this->backurl = $this->indexPath; // 單層 // 多層根目錄==0 // 須修改
    // $this->backurl = $this->session->prePage; // 多層級底層 // 須修改
    // $this->backurl = $this->indexPath.'/index/'.$this->session->userdata('preid'); // 多層根目錄 !=0 // 須修改

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
    $this->subnavPath = $this->common->getsubnav($perid,''); // 其他頁面讀取方式，請加上上層的tablename // 須修改

    // 頁碼設定
    if(!$this->session->userdata('pageNumber')) {
      $this->session->set_userdata('pageNumber', 1);
    }
    $this->currentPage = $this->session->userdata('pageNumber');

    $this->customTbname = false; // 不更動資料表的前綴詞
    // 自動建立圖片長寬限制資料 (有需要再使用) // 須修改
    // $this->common->checkImageLimit($this->table, 'xfile1', $this->customTbname);
    // 取得圖片資訊 // 須修改
    $this->imageinfo['xfile1'] = $this->common->getImageinfo($this->table, 'xfile1', $this->customTbname);
    // * 讀取共用的view
    $this->viewPath = 'admin/module_album_view';
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

  // 新增/更新
  public function save($id = NULL)
  {
    if(
      $this->input->post('xtitle', true)
    ) {

        // 排序選項處理
        if($this->input->post('xsort', true)) {
          $xsort = $this->common->processSort($this->table,$this->input->post('xsort', true),$this->input->post('insertxsortpid', true));
        } else {
          $xsort = $this->common->processSort($this->table,'first','');
        }

        // 圖片處理
        $xfile1 = $this->common->processImg($this->table,$id,'xfile1',$this->input->post('xfile1', true));

        if(!isset($data['error'])) {

          // DB資料
          $data = array(
            'ofkpid'=> $this->session->userdata('ofkpid'), // 其他頁面讀取方式
            'xpublish'=> 'yes',
            'xtitle'=> $this->input->post('xtitle', true),
            'xsubtitle'=> ($this->input->post('xsubtitle', true)) ? $this->input->post('xsubtitle', true) : '',
            'xfile1'=> $xfile1,
            ($id) ? 'xmodify' : 'xcreate'=> date('Y-m-d H:i:s'),
            'xsort'=> $xsort,
          );

          // 新增、更新動作
          if($id) {
            $actionMessage = '修改';
            $this->admin_crud->update($this->table, $id, $data);
          }
          else {
            $actionMessage = '新增';
            $id = $this->admin_crud->create($this->table, $data);
          }
          $this->track->trackingDoing($this->table,'xtitle',$this->data['permission']['xname'],$actionMessage,$id,$this->data['selfaccount'],$this->data['selflevel']);
        }
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

    foreach ($obj[0] as $key => $value) {
      $this->admin_crud->update($this->table, $value->id, array('xsort' => $start+$key+1));
    }
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
    $config['allowed_types'] = 'csv|xlsx';
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
}

// application/controllers/
