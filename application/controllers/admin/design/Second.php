<?php defined('BASEPATH') OR exit('No direct script access allowed');

// controllers 名稱 // 須修改
class Second extends Admin_Controller
{
  // 基本設定
  public $table = ''; // 資料庫table name // 須修改

  /***********
    列表頁
  ***********/
  // 表格-呈現欄位 // 須修改
  public $tableTitle = array('刊登','主標','小圖','其他功能');
  public $tableColumn = array('xpublish','xtitle','xfile1','');
  public $tableHeadSize = array('8%','80%','10%','10%');
  public $tableSortValue = array(true,true,false,false);
  // 圖片-縮圖設定 // 須修改
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
    $this->load->helper('file'); // 讀取檔案

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

    // 自動建立圖片長寬限制資料 (有需要再使用) // 須修改
    // $this->common->checkImageLimit($this->table, 'xfile1');
    // 取得圖片資訊 // 須修改
    // $this->imageinfo['xfile1'] = $this->common->getImageinfo($this->table, 'xfile1');
    // 語系選單讀取
    $this->langArray = array();
    foreach ($this->data['langMenu'] as $key => $value) {
      $this->langArray[$key]['name'] = $value['xtitle'];
      $this->langArray[$key]['lang'] = $value['xcode'];
    }
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

    } else {
      $this->data['action'] = 'create';
    }

    $layout['content'] = $this->load->view($this->viewPath, $this->data, true);
    $this->load->view($this->adminview, $layout);
  }

  // 新增/更新
  public function save($id = NULL)
  {
    foreach ($this->langArray as $key => $value) {
      $lang = $value['lang'];
      $list = $this->input->post('x'.$lang,true);
      if (!write_file('locales/'.$lang.'.json', $list)) {
        $data['error'] = 'Unable to write the file';
        return $this->output->set_content_type('application/json')->set_output(json_encode($data));
      }
    }
    $data['success'] = "成功寫入";

    $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }

}

// application/controllers/
