<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Admin_Controller
{
  // 基本設定
  public $table = 'admin_group'; // 資料庫table name // 固定須修改

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
  }

  public function index($tag = NULL, $filed = NULL)
  {
    $data = array();

    $this->data['action'] = 'index';

    // 讀取列表 // 不可看到權限比自己高
    $this->data['data'] = $this->admin_crud->query_where($this->table, array('xlevel !='=> 0, 'xlevel >'=> $this->data['selflevel']), true, 'xlevel')->result_array();

    $layout['content'] = $this->load->view($this->viewPath, $this->data, true);
    $this->load->view($this->adminview, $layout);
  }

  // 新增/更新介面表單
  public function form($id = NULL)
  {
    // 更新介面
    if(isset($id)) {

      $this->data['action'] = 'update';

      // 讀取列表
      // 判斷該使用者權限
      $bool = $this->admin_crud->check_level($this->table, $id, $this->data['selflevel']);

      if($bool===true) {
        $array = $this->admin_crud->query_where($this->table, array('pid'=>$id))->result_array();
        $this->data['list'] = $array[0];
      } else redirect($this->indexPath);

    } else {

      $this->data['action'] = 'create';

      // 初始化，所有欄位
      $this->data['list'] = array(
        'pid' => '',
        'xname' => '',
        'xlevel' => 1,
        'xextend' => '',
      );
    }

    $layout['content'] = $this->load->view($this->viewPath, $this->data, true);
    $this->load->view($this->adminview, $layout);
  }

  // 新增/更新
  public function save($id = NULL)
  {

    if(
      $this->input->post('xname', true) &&
      (int) $this->input->post('xlevel', true) > 0
    ) {

        // 等級不可設定比自己權限大
        if($this->input->post('xlevel', true) > $this->data['selflevel']) {

          // DB資料
          $data = array(
            'xname'=> $this->input->post('xname', true),
            'xlevel'=> (int)($this->input->post('xlevel', true)),
            'xextend'=> ($this->input->post('xextend', true)) ? $this->input->post('xextend', true) : NULL,
            ($id) ? 'xmodify' : 'xcreate'=> date('Y-m-d H:i:s'),
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
          $this->track->trackingDoing($this->table,'xname',$this->data['permission']['xname'],$actionMessage,$id,$this->data['selfaccount'],$this->data['selflevel']);

        } else {
          $data['error'] = '您的權限不夠高，不可以設定高於您權限的等級';
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
            // 判斷該使用者權限 // 不可刪除比自己權限高的群組
            $bool = $this->admin_crud->check_level($this->table, $value, $this->data['selflevel']);

            // 不可刪除比自己權限高的群組
            if($bool===true) {
              $this->track->trackingDoing($this->table,'xname',$this->data['permission']['xname'],$data['success'],$value,$this->data['selfaccount'],$this->data['selflevel']);
              // 刪除關聯
              $this->admin_crud->delete('admin_permission', array('GroupID'=>$value));
              // 刪除資料
              $this->admin_crud->delete($this->table, array('pid'=>$value));
            }
          }
          break;
        // case 'release':
        //   $data['success'] = '刊登';
        //   foreach ($array as $value) {
        //     $this->admin_crud->update($this->table, $value, array(
        //       'xpublish'=> 'yes',
        //       'xmodify'=> $now
        //     ));
        //     $this->track->trackingDoing($this->table,'xname',$this->data['permission']['xname'],$data['success'],$value,$this->data['selfaccount'],$this->data['selflevel']);
        //   }
        //   break;
        // case '_release':
        //   $data['success'] = '取消刊登';
        //   foreach ($array as $value) {
        //     $this->admin_crud->update($this->table, $value, array(
        //       'xpublish'=> 'no',
        //       'xmodify'=> $now
        //     ));
        //     $this->track->trackingDoing($this->table,'xname',$this->data['permission']['xname'],$data['success'],$value,$this->data['selfaccount'],$this->data['selflevel']);
        //   }
        //   break;
        // case 'home':
        //   $data['success'] = '刊登首頁';
        //   foreach ($array as $value) {
        //     $this->admin_crud->update($this->table, $value, array(
        //       'xindex'=> 'yes',
        //       'xmodify'=> $now
        //     ));
        //     $this->track->trackingDoing($this->table,'xname',$this->data['permission']['xname'],$data['success'],$value,$this->data['selfaccount'],$this->data['selflevel']);
        //   }
        //   break;
        // case '_home':
        //   $data['success'] = '取消刊登首頁';
        //   foreach ($array as $value) {
        //     $this->admin_crud->update($this->table, $value, array(
        //       'xindex'=> 'no',
        //       'xmodify'=> $now
        //     ));
        //     $this->track->trackingDoing($this->table,'xname',$this->data['permission']['xname'],$data['success'],$value,$this->data['selfaccount'],$this->data['selflevel']);
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

}

// application/controllers/
