<?php defined('BASEPATH') OR exit('No direct script access allowed');

// controllers 名稱 // 須修改
class Dashboard extends Admin_Controller
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

    // for ga
    $this->load->config('gapi');
    if(file_exists($this->config->item('p12_key'))) $this->valid = true;
    else $this->valid = false;

    if($this->valid == true) {
      $params = array( 'client_email' => $this->config->item('account_email'), 'key_file' => $this->config->item('p12_key') );
      $this->load->library('gapi', $params);
      $this->gapi->requestReportData($this->config->item('ga_profile_id'), array('day','month','year'), array('sessions','users','pageviews','newusers'), 'day', '', '31daysAgo', 'yesterday', 1, 500);
    }

    // for contact
    $this->gopage = 'admin/contact/contact'; // 須修改
    $this->settable = 'contact_tb'; // 須修改
  }

  public function index()
  {
    $data = array();

    $this->data['action'] = 'index';

    // ga
    $data['totalSessions']	= 0;
		$data['totalUsers']	= 0;
		$data['totalPageViews']	= 0;

    if($this->valid == true) {
  		$this->data['totalSessions'] = $this->gapi->getSessions(); // 工作階段
  		$this->data['totalUsers']		= $this->gapi->getUsers();
  		$this->data['totalPageViews']	= $this->gapi->getPageviews();
    }

    // contact
    $array = $this->admin_crud->result_array($this->admin_crud->query_where('tracking_tb',array('xadmin'=>$this->data['selfaccount'],'xaction'=>'Login'),true,'xcreate','desc'));
    if(count($array)>1) $logtime = $array[1]['xcreate'];
    else $logtime = date('Y-m-d 00:00:00');
    $this->data['logtime'] = strtotime($logtime);

    // 登入期間出現表單，顯示
    $this->data['readlist'] = $this->admin_crud->result_array($this->admin_crud->query_where($this->settable, array('xcreate >='=>$logtime, 'xstatus'=> 'no', 'xread'=> 'no'), true, 'xcreate', 'desc'));
    // 未讀取過表單或登入期間表單，顯示
    $this->db->from($this->settable)
      ->where('xstatus', 'no')
      ->where('xread', 'no')
      ->or_where('xstatus', 'no')
      ->where('xcreate >=', $logtime)
      ->order_by('xcreate', 'desc');
    $this->data['newlist'] = $this->admin_crud->get();
    // 未處理表單
    $this->data['oldlist'] = $this->admin_crud->result_array($this->admin_crud->query_where($this->settable, array('xstatus'=> 'no'), true, 'xcreate', 'desc'));

    $layout['content'] = $this->load->view($this->viewPath, $this->data, true);
    $this->load->view($this->adminview, $layout);
  }
  // 讀取GA
  public function read()
  {
		$totalSessions	= $this->gapi->getSessions(); // 工作階段
    $data['NewVisitor'] = $NewVisitor	= $this->gapi->getNewUsers();
    $data['returnVisitor']	= $totalSessions-$NewVisitor;
    $rateNewVisitor	= ($NewVisitor>0)?round($NewVisitor/$totalSessions,3):0;
    $data['ratereturnVisitor']	= ($NewVisitor>0)?((1-$rateNewVisitor)*100).'%':0;
    $data['rateNewVisitor'] = ($NewVisitor>0)?($rateNewVisitor*100).'%':0;

		$count = -1;
		$data['users'] = array();
		$data['sessions'] = array();
		$data['pageviews'] = array();

    $data['char1date'] = array();
    $data['char1users'] = array();
		$data['char1sessions'] = array();
		$data['char1pageviews'] = array();

		foreach ($this->gapi->getResults() as $value) { $count++;
			$data['users'][$count] = array();
			$data['sessions'][$count] = array();
			$data['pageviews'][$count] = array();

      $Dimensions = $value->getDimensions();
			$data['char1date'][$count] = $date = strtotime($Dimensions['year'].'-'.$Dimensions['month'].'-'.$Dimensions['day']);
      $date = str_pad($date,13,0);

      $getMetrics = $value->getMetrics();
			$amount = $getMetrics['users'];
			array_push($data['users'][$count],$date);
      array_push($data['users'][$count],$amount);

			$amount = $getMetrics['sessions'];
			array_push($data['sessions'][$count],$date);
			array_push($data['sessions'][$count],$amount);

			$amount = $getMetrics['pageviews'];
			array_push($data['pageviews'][$count],$date);
			array_push($data['pageviews'][$count],$amount);
		}
    sort($data['users']);
    sort($data['sessions']);
    sort($data['pageviews']);
    sort($data['char1date']);

    for ($i=0; $i < count($data['users']); $i++) {
      $data['char1users'][$i] = $data['users'][$i][1];
      $data['char1sessions'][$i] = $data['sessions'][$i][1];
      $data['char1pageviews'][$i] = $data['pageviews'][$i][1];
      $data['char1date'][$i] = date('m/d',$data['char1date'][$i]);
    }

		return $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}
}

// application/controllers/
