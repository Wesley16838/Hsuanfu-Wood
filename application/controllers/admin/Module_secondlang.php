<?php defined('BASEPATH') OR exit('No direct script access allowed');

// controllers 名稱 // 須修改
class Module_secondlang extends Admin_Controller
{
  // 基本設定
  public $table = ''; // 資料庫table name // 須修改

  /***********
    列表頁
  ***********/
  // 表格-呈現欄位 // 須修改
  public $tableTitle = array('刊登','首頁','主標','小圖');
  public $tableColumn = array('xpublish','xindex','xtitle','xfile1');
  public $tableHeadSize = array('8%','8%','70%','10%');
  public $tableSortValue = array(true,true,true,false);
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

  public function index()
  {
    $data = array();

    $this->data['action'] = 'index';

    $layout['content'] = $this->load->view($this->viewPath, $this->data, true);
    $this->load->view($this->adminview, $layout);
  }

  public function save()
  {
    if(
      $this->input->post('oldprefixname', true) &&
      $this->input->post('newprefixname', true) &&
      $this->input->post('oldlangname', true) &&
      $this->input->post('newlangname', true)
    ) {
      $oldprefixname = $this->input->post('oldprefixname', true); // tw_
      $newprefixname = $this->input->post('newprefixname', true); // en_
      $oldlangname = $this->input->post('oldlangname', true); // tw
      $newlangname = $this->input->post('newlangname', true); // en

      // 判斷是否有該版本存在
      $this->db->from($this->tb_menu)
              ->where('xlang', $oldlangname);
      $array = $this->admin_crud->get();
      if(count($array)>0) {
        $bool = $this->common->generatesecondlang($oldprefixname, $newprefixname, $oldlangname, $newlangname);
        if($bool===true) {
          $data['success'] = '已成功建立次語系';
        } else $data['error'] = '建立次語系失敗'.$bool;
      } else $data['error'] = '該語系版本未存在';
    } else $data['error'] = '請輸入所有欄位';
    $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }
}

// application/controllers/
