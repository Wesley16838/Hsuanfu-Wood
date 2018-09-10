<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Admin_Controller
{
  // 基本設定
  public $table = 'admin_user'; // 資料庫table name
  public $jointable = 'admin_group'; // 關連資料庫table name

  /***********
    列表頁
  ***********/
  // 表格-呈現欄位 // 須修改
  public $tableTitle = array('啟用','帳號','姓名','群組','');
  public $tableColumn = array('xpublish','xaccount','xnickname','GroupName','');
  public $tableHeadSize = array('8%','20%','50%','20%','8%');
  public $tableSortValue = array(true,true,true,true,false);
  // 表格-縮圖設定 // 須修改
  protected $imageSize = 'original'; // original、mid、small
  protected $imageArray = array('xpic');
  // 表格-搜尋欄位 // 須修改
  public $searchTitle = array('姓名');
  public $searchColumn = array('xnickname');
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

    // 自動建立圖片長寬限制資料 (有需要再使用) // 須修改
    // $this->common->checkImageLimit($this->table, 'xpic', true);
    // 取得圖片資訊 // 須修改
    $this->imageinfo['xpic'] = $this->common->getImageinfo($this->table, 'xpic', true);
  }

  public function index($tag = NULL, $filed = NULL)
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
      // 判斷該使用者權限  // 不可看到權限比自己高 + 自己本身 + xlevel為 NULL
      $query = $this->db->select("$this->table.xaccount, $this->table.xpublish, $this->table.xnickname, $this->jointable.xname as GroupName, $this->table.xextend")
                        ->from($this->table)
                        ->join($this->jointable, "$this->jointable.pid = $this->table.GroupID", 'left')
                        ->where("$this->jointable.xlevel >=", $this->data['selflevel'])
                        ->where("$this->jointable.xlevel !=", 0)
                        ->or_where("$this->jointable.xlevel", NULL) // (避免GroupID被刪除)
                        ->or_where_in("$this->table.xaccount", $this->data['selfaccount']); // 可看到自己
      $data['data'] = $this->admin_crud->get($query);
      $actionIndex = true;
    }
    // 處理列表中圖片
    if(count($data['data']) > 0 && $actionIndex && count($this->imageArray) > 0) { $count = -1;
      foreach ($data['data'] as $value) { $count++;
        $data['data'][$count]['button'] = generatebutton('link_self_href', 'btn btn-xs btn-primary', "admin/user/user/formPassword/".$value['xaccount'], $this->data['permission']['UpdateAction'], '更改密碼');
        for ($i=0;$i<count($this->imageArray);$i++) {
          if(isset($data['data'][$count][$this->imageArray[$i]])) {
            $data['data'][$count][$this->imageArray[$i]] = $this->common->getImagethumb($this->table, $this->imageArray[$i], $this->imageSize, $value[$this->imageArray[$i]], true);
          }
        }
      }
    }
    echo json_encode($data);
  }

  // 新增/更新介面表單
  public function form($account = NULL)
  {
    // 讀取選單 // 可看到與自己同級或以下群組
    $this->data['group'] = $this->admin_crud->result_array($this->admin_crud->query_where($this->jointable, array('xlevel >='=> $this->data['selflevel'], 'xlevel !='=> 0), true, 'xlevel'));

    // 更新介面
    if(isset($account)) {

      $this->data['action'] = 'update';

      // 判斷該使用者權限  // 可修改自己帳號 + 不可修改權限比自己高
      $bool = $this->admin_crud->check_user_level($this->table, $account, $this->data['selfaccount'], $this->data['selflevel']);
      if($bool === true) {
        // 讀取列表
        $query = $this->db->select('GroupID, xaccount, xpassword, xpic, xnickname, xjobtitle, xmail, xextend, xpublish')
                ->from($this->table)
                ->where('xaccount', $account);
        $array = $this->admin_crud->get($query);
        $this->data['list'] = $array[0];
      } else {
        redirect($this->indexPath);
      }

    } else {

      $this->data['action'] = 'create';

      // 初始化，所有欄位
      $this->data['list'] = array(
        'GroupID' => (isset($this->data['group'][0]['pid'])) ? $this->data['group'][0]['pid'] : '',
        'xaccount' => '',
        'xpassword' => '',
        'xpic' => '',
        'xnickname' => '',
        'xjobtitle' => '',
        'xextend' => '',
        'xpublish' => 'yes',
      );
    }

    $layout['content'] = $this->load->view($this->viewPath, $this->data, true);
    $this->load->view($this->adminview, $layout);
  }

  // 更新密碼介面表單
  public function formPassword($account)
  {

    $this->data['action'] = 'updatepsw';

    // 判斷該使用者權限  // 可修改自己帳號 + 不可修改權限比自己高
    $bool = $this->admin_crud->check_user_level($this->table, $account, $this->data['selfaccount'], $this->data['selflevel']);
    if($bool===true) {

      // 讀取列表
      $query = $this->db->select('xaccount')
              ->from($this->table)
              ->where('xaccount', $account);
      $array = $this->admin_crud->get($query);
      $this->data['list'] = $array[0];
    } else {
      redirect($this->indexPath);
    }

    $layout['content'] = $this->load->view($this->viewPath, $this->data, true);
    $this->load->view($this->adminview, $layout);
  }

  // 新增/更新
  public function save($oldAccount = NULL)
  {
    if(
      !$this->input->post('GroupID', true) ||
      !$this->input->post('xaccount', true) ||
      $this->input->post('xpassword', true) && $oldAccount ||
      !$this->input->post('xpublish', true)
    ) {
      $data['error'] = '欄位未輸入';
      return $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    // 格式處理
    $xaccount = $this->input->post('xaccount', true);
    if($xaccount) {
      if(!$this->common->checkurlvalid($xaccount)) {
        $data['error'] = '帳號不可為特殊符號或僅有純數字';
        return $this->output->set_content_type('application/json')->set_output(json_encode($data));
      }
    }

    // 是否重登
    $logout = '';

    // 存入資料庫
    $newAccount = $this->input->post('xaccount', true);
    $newPassword = $this->admin_crud->cryptpsw($this->input->post('xpassword', true));

    // 更新
    if($oldAccount) {

      // 取得更新用戶舊資料
      $oldData = $this->admin_crud->checkUser($oldAccount,true);
      $id = $oldData['id'];
      $newPassword = $oldData['psw'];

      // 有更動帳號
      if($this->data['selfaccount'] == $oldAccount && $oldAccount != $newAccount) $logout = true;
    }

    // 查詢是否有該帳號存在
    $this->db->from($this->table);
    if($oldAccount) $this->db->where_not_in('pid', $id); // 排除用戶本人
    $this->db->where('xaccount', $newAccount);
    $query =$this->db->get();

    if($query->num_rows() > 0) {
      $data['error'] = '該帳號已存在';
      return $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    // 群組處理
    // 判斷該使用者權限 // 不可更改比自己權限高的(不含本身)
    $bool = $this->admin_crud->check_level_equal($this->jointable, $this->input->post('GroupID', true), $this->data['selflevel']);
    if($bool !== true) {
      $data['error'] = '權限不夠';
      return $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    // 圖片處理
    $xpic = $this->common->removeroot($this->input->post('xpic', true));
    if($oldAccount) {
      $row = $this->admin_crud->row($this->admin_crud->query_where($this->table, array('pid'=>$id)));
      if($xpic == '') $xpic = $row->xpic; // 沒輸入，讀取原路徑
    }

    // DB資料
    $data = array(
      'GroupID'=> $this->input->post('GroupID', true),
      'xaccount'=> $newAccount,
      'xpassword'=> $newPassword,
      'xpic'=> $xpic,
      'xnickname'=> ($this->input->post('xnickname', true)) ? $this->input->post('xnickname', true) : NULL,
      'xjobtitle'=> ($this->input->post('xjobtitle', true)) ? $this->input->post('xjobtitle', true) : NULL,
      'xmail'=> ($this->input->post('xmail', true)) ? $this->input->post('xmail', true) : NULL,
      'xextend'=> ($this->input->post('xextend', true)) ? $this->input->post('xextend', true) : NULL,
      'xpublish'=> $this->input->post('xpublish', true),
      ($oldAccount) ? 'xmodify' : 'xcreate'=> date('Y-m-d H:i:s'),
    );

    // 新增、更新動作
    if($oldAccount) {
      $actionMessage = '修改';
      $this->admin_crud->update($this->table, $id, $data);
    }
    else {
      $actionMessage = '新增';
      $id = $this->admin_crud->create($this->table, $data);
    }
    // 更新 accesskey
    $accesskey = $this->admin_crud->cryptkey($id,$newAccount);
    $this->admin_crud->update($this->table, $id, array('xaccesskey'=> $accesskey));

    $this->track->trackingDoing($this->table,'xaccount',$this->data['permission']['xname'],$actionMessage,$id,$this->data['selfaccount'],$this->data['selflevel']);
    // 更動自身帳密
    if($logout) $this->session->unset_userdata('access_key');
  }

  // 更新密碼
  public function savePassword($oldAccount)
  {
    if(
      !$this->input->post('Newxpassword', true) ||
      !$this->input->post('NewCheckxpassword', true)
    ) {
      $data['error'] = '欄位未輸入';
      return $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    // 是否重登
    $logout = '';

    // 查詢是否有該帳號存在
    $oldData = $this->admin_crud->checkUser($oldAccount,true);

    if(count($oldData) == 0) {
      $data['error'] = '不是系統用戶';
      return $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    // 取得更新用戶舊資料
    $id = $oldData['id'];
    $newPassword = $oldData['psw'];

    // 更改本身資料
    if($this->data['selfaccount'] == $oldAccount) {

      if(!$this->input->post('Oldxpassword', true)) {
        $data['error'] = '欄位未輸入';
        return $this->output->set_content_type('application/json')->set_output(json_encode($data));
      }

      if($this->admin_crud->cryptpsw($this->input->post('Oldxpassword', true)) != $this->data['selfpassword']) {
        $data['error'] = '舊密碼錯誤';
        return $this->output->set_content_type('application/json')->set_output(json_encode($data));
      }
      $logout = true;
    }

    // 密碼處理
    if($this->input->post('Newxpassword', true)) $newPassword = $this->admin_crud->cryptpsw($this->input->post('Newxpassword', true));
    // 更新資料庫
    $this->admin_crud->update($this->table, $id, array('xpassword'=> $newPassword));
    $this->track->trackingDoing($this->table,'xaccount',$this->data['permission']['xname'],'重設密碼',$id,$this->data['selfaccount'],$this->data['selflevel']);
    // 更動自身帳密
    if($logout) $this->session->unset_userdata('access_key');
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
            // 判斷該使用者權限  // 不可刪除比自己權限高的群組
            $bool = $this->admin_crud->check_user_level($this->table, $value, $this->data['selfaccount'], $this->data['selflevel']);
            if($bool===true && $this->data['selfaccount'] != $value) {
              $pid = $this->admin_crud->row($this->admin_crud->query_where($this->table, array('xaccount'=> $value)))->pid;
              $this->track->trackingDoing($this->table,'xaccount',$this->data['permission']['xname'],$data['success'],$pid,$this->data['selfaccount'],$this->data['selflevel']);
              // 刪除資料
              $this->admin_crud->delete($this->table, array('xaccount'=> $value));
            } //else $data['error'] = '權限不夠';
          }
          break;
        case 'release':
          $data['success'] = '啟用';
          foreach ($array as $value) {
            // 判斷該使用者權限  // 不可更改比自己權限高的群組
            $bool = $this->admin_crud->check_user_level($this->table, $value, $this->data['selfaccount'], $this->data['selflevel']);
            if($bool===true) {
              $pid = $this->admin_crud->row($this->admin_crud->query_where($this->table, array('xaccount'=> $value)))->pid;
              $this->admin_crud->update($this->table, $pid, array(
                'xpublish'=> 'yes',
                'xmodify'=> $now
              ));
              $this->track->trackingDoing($this->table,'xaccount',$this->data['permission']['xname'],$data['success'],$pid,$this->data['selfaccount'],$this->data['selflevel']);
            }
          }
          break;
        case '_release':
          $data['success'] = '取消啟用';
          foreach ($array as $value) {
            // 判斷該使用者權限  // 不可更改比自己權限高的群組
            $bool = $this->admin_crud->check_user_level($this->table, $value, $this->data['selfaccount'], $this->data['selflevel']);
            if($bool===true) {
              $pid = $this->admin_crud->row($this->admin_crud->query_where($this->table, array('xaccount'=> $value)))->pid;
              $this->admin_crud->update($this->table, $pid, array(
                'xpublish'=> 'no',
                'xmodify'=> $now
              ));
              $this->track->trackingDoing($this->table,'xaccount',$this->data['permission']['xname'],$data['success'],$pid,$this->data['selfaccount'],$this->data['selflevel']);
            }
          }
          break;
        // case 'home':
        //   $data['success'] = '刊登首頁';
        //   foreach ($array as $value) {
        //     $this->admin_crud->update($this->table, $value, array(
        //       'xindex'=> 'yes',
        //       'xmodify'=> $now
        //     ));
        //   }
        //   break;
        // case '_home':
        //   $data['success'] = '取消刊登首頁';
        //   foreach ($array as $value) {
        //     $this->admin_crud->update($this->table, $value, array(
        //       'xindex'=> 'no',
        //       'xmodify'=> $now
        //     ));
        //   }
        //   break;
        default:
          $data = array();
          break;
      }
    } else {
      $data['error'] = '請選擇項目';
    }
    $this->output->set_content_type('application/json')->set_output(json_encode($data));
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
    $xaccount = $this->input->post('id', true);

    $this->admin_crud->update_where($this->table, array('xaccount' => $xaccount), array($field => NULL));
  }

}

// application/controllers/admin/user/User
