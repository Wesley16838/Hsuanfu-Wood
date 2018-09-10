<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Permission extends Admin_Controller
{
  public $table = 'admin_permission'; // 資料庫table name

  /***********
    列表頁
  ***********/
  // 表格-呈現欄位
  public $tableTitle = array('刊登','首頁','主標','小圖');
  public $tableColumn = array('xpublish','xindex','xtitle','xfile1');
  public $tableHeadSize = array('8%','8%','70%','10%');
  public $tableSortValue = array(true,true,true,false);
  // 表格-搜尋欄位
  public $searchTitle = array('主標');
  public $searchColumn = array('xtitle');
  // 表格-其他功能
  public $showPaging = true;
  public $showSearching = true;
  public $showMulti = true;
  // 圖片-縮圖設定
  protected $imageSize = 'original'; // original、mid、small
  protected $imageArray = array('xfile1');
  // 其他頁面
  public $otherPage = true;

  function __construct()
  {
    parent::__construct();

    $this->is_logged_in(); // 判斷是否登入
    $this->maxDepth = $this->menuMaxDepth; // 樹狀最大限制
  }

  public function index()
  {
    $data = array();

    $this->data['action'] = 'index';

    // 讀取選單 // 只能看到比自己權限小的群組選單
    $this->data['menu'] = $this->admin_crud->query_where('admin_group', array('xlevel !='=> 0, 'xlevel >'=> $this->data['selflevel']), true, 'xlevel')->result_array();

    // tab
    $this->data['tabmenu'] = $tabmenu = $this->admin_crud->result_array($this->admin_crud->query_where($this->tb_lang, array('xpublish'=>'yes'),true,'xsort'));
    // 目前選單active
    $this->data['tabactive'] = $active = ($this->session->taba)?$this->session->taba:'';
    $xcode = (count($tabmenu)>0)?$tabmenu[0]['xcode']:'';
    if(!$active) $this->session->set_userdata('taba',$xcode);

    // 讀取列表
    $this->data['data'] = array();
    foreach ($tabmenu as $value) {
      $array = array();
      $xlang = $value['xcode'];
      $list = $this->common->getMenuPermission($this->tb_menu,$this->table,$this->data['selfgroup'],$xlang);
      if(count($list) > 0) {
        foreach ($list as $value) {
          $array[$value['preid']][] = $value;
        }
        $this->data['data'][$xlang] = $this->makeTreeTable($array, 0, 0, 0, $this->maxDepth);
      } else $this->data['data'][$xlang] = '';
    }

    $layout['content'] = $this->load->view($this->viewPath, $this->data, true);
    $this->load->view($this->adminview, $layout);
  }

  // 建立表格樹狀
  public function makeTreeTable($menu, $preID, $dep,  $depth = 0, $limitDepth = 3) {

    $html = '';
    $html .= "\n";
    $html .= str_repeat("\t", $dep);

    // 判斷層級
    if($preID != 0) $depth++;
    else $depth = 0;

    if($depth < $limitDepth) {

       foreach ($menu[$preID] as $item) {

         // 需修改範圍 //
         $pid = $item['pid'];
         $xname = $item['xname'];
         $xtype = $item['xtype'];
         if($this->data['selfgroup'] == 1) $RankAction = 1;
         else $RankAction = $item['RankAction'];

         // 有RankAction才可控制權限
         if($RankAction == 1) {

           if($depth == 0) $html .= "<tr class='treegrid-$pid'>";
           else $html .= "<tr class='treegrid-$pid treegrid-parent-$preID'>";

           $html .= "<td>$xname</td>";
           $html .= "<td>";

           if($xtype == 'Menu') {
             $html .= "權限 <input type='checkbox' name='rank$pid' onchange='SaveCheckbox(\"rank\", $pid)'> ";
             $html .= "列表 <input type='checkbox' name='list$pid' onchange='SaveCheckbox(\"list\", $pid)'> ";
             $html .= "全選 <input type='checkbox' name='all$pid' onchange='SaveCheckbox(\"all\", $pid, \"Menu\")'> ";
           } else {
             $html .= "權限 <input type='checkbox' name='rank$pid' onchange='SaveCheckbox(\"rank\", $pid)'> ";
             $html .= "列表 <input type='checkbox' name='list$pid' onchange='SaveCheckbox(\"list\", $pid)'> ";
             $html .= "新增 <input type='checkbox' name='create$pid' onchange='SaveCheckbox(\"create\", $pid)'> ";
             $html .= "修改 <input type='checkbox' name='update$pid' onchange='SaveCheckbox(\"update\", $pid)'> ";
             $html .= "刪除 <input type='checkbox' name='delete$pid' onchange='SaveCheckbox(\"delete\", $pid)'> ";
             $html .= "全選 <input type='checkbox' name='all$pid' onchange='SaveCheckbox(\"all\", $pid)'> ";
           }

           $html .= "</td></tr>";
         }
         // 需修改範圍 //

         // 有子類別
         if (isset($menu[$item['pid']])) {
           $html .= $this->makeTreeTable($menu, $item['pid'],$dep, $depth, $limitDepth);
           $dep++;
         }
       }
     }

    return $html;
  }

  // 讀取節點
  public function readNode()
  {
    $groupID = $this->input->post('val', true);

    $data = $this->admin_crud->query_where($this->table, array('GroupID'=> $groupID))->result_array();
    $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }

  // 更新節點
  public function updateNode()
  {
    if (
      $this->input->post('groupid', true) &&
      $this->input->post('menuid', true) &&
      $this->input->post('xact', true) &&
      $this->input->post('xbool', true) > -1
    ) {
      $groupID = $this->input->post('groupid', true);
      $menuID = $this->input->post('menuid', true);
      $xact = $this->input->post('xact', true);
      $xbool = (bool)$this->input->post('xbool', true);

      // 判斷該使用者權限 // 不可更改比自己權限高的權限
      $bool = $this->admin_crud->check_level('admin_group', $groupID, $this->data['selflevel']);
      if($bool !== true) {
        $data['error'] = '權限不夠';
        return $this->output->set_content_type('application/json')->set_output(json_encode($data));
      }

      $data = array(
        'GroupID'=> $groupID,
        'MenuID'=> $menuID,
      );

      // 是否刪除資料庫資料
      $delete = '';

      // 判斷 action
      switch ($xact) {
        case 'rank':
          $data['RankAction'] = $xbool;
          break;
        case 'list':
          $data['ReadAction'] = $xbool;
          break;
        case 'create':
          $data['CreateAction'] = $xbool;
          break;
        case 'update':
          $data['UpdateAction'] = $xbool;
          break;
        case 'delete':
          $data['DeleteAction'] = $xbool;
          break;
        case 'all':
          $data['RankAction'] = $xbool;
          $data['ReadAction'] = $xbool;
          $data['CreateAction'] = $xbool;
          $data['UpdateAction'] = $xbool;
          $data['DeleteAction'] = $xbool;

          if($xbool == 0) $delete = true;
          break;
        default:
          $data['RankAction'] = 0;
          $data['ReadAction'] = 0;
          $data['CreateAction'] = 0;
          $data['UpdateAction'] = 0;
          $data['DeleteAction'] = 0;

          if($xbool == 0) $delete = true;
          break;
      }

      $query = $this->admin_crud->query_where($this->table, array('GroupID'=> $groupID, 'MenuID'=> $menuID));

      // 原本有資料，更新
      if($query->num_rows() > 0) {
        $data['xmodify'] = date('Y-m-d H:i:s');
        if(!$delete) {
          $actionMessage = '修改權限';
          $this->admin_crud->update($this->table, $query->row()->pid, $data);
        }
        else {
          $actionMessage = '刪除權限';
          $this->admin_crud->delete($this->table, array('pid'=> $query->row()->pid)); // 刪除完全沒權限資料
        }

      // 沒資料，新增
      } else {
        $actionMessage = '新增權限';
        $data['xcreate'] = date('Y-m-d H:i:s');
        $this->admin_crud->create($this->table, $data);
      }
      $this->track->trackingDoing('admin_group','xname',$this->data['permission']['xname'],$actionMessage,$groupID,$this->data['selfaccount'],$this->data['selflevel']);

    }
  }

  // 紀錄目前tab
  public function recordtab($value)
  {
    $data = $value;
    $this->session->set_userdata('taba',$value);
    return $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }

}

// application/controllers/admin/permission/Permission
