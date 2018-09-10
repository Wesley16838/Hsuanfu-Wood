<?php defined('BASEPATH') OR exit('No direct script access allowed');

// controllers 名稱 // 須修改
class Flow extends Admin_Controller
{
  // 基本設定
  public $table = 'contact_tb'; // 資料庫table name // 須修改

  /***********
    列表頁
  ***********/
  // 表格-呈現欄位 // 須修改
  public $tableTitle = array('刊登','首頁','主標');
  public $tableColumn = array('xpublish','xindex','xtitle');
  public $tableHeadSize = array('8%','8%','70%');
  public $tableSortValue = array(true,true,true);
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
  }

  public function index($year = NULL)
  {
    $data = array();

    $this->data['action'] = 'index';
    // 目前
    $this->data['nowyear'] = $year;

    // 讀取列表
    if($year) $year = "$year";
    else $year = date('Y');

    $this->db->select("month(xcreate) as month");
    $this->db->from($this->table);
    $this->db->like('xcreate', $year);
    $this->db->order_by('xcreate');
    $list = $this->admin_crud->get();
    $result = array();
    foreach ($list as $key => $value) {
      $month = $value['month'];
      $result[$month]['month'] = $month;
      if(isset($result[$month]['count'])) $result[$month]['count']++;
      else {
        $result[$month]['count'] = 1;
      }
    }
    $this->data['data'] = $result;

    // 讀取選單
    $this->db->select("year(xcreate) as year");
    $this->db->from($this->table);
    $this->db->order_by('year','desc');
    $this->db->group_by('year');
    $this->data['menu'] = $this->admin_crud->get();
    
    $layout['content'] = $this->load->view($this->viewPath, $this->data, true);
    $this->load->view($this->adminview, $layout);
  }
}

// application/controllers/
