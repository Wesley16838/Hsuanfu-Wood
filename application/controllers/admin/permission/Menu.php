<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends Admin_Controller
{

  public $table = 'admin_menu'; // 資料庫table name

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

  public function index($root = 0, $currentPreID = 0)
  {
    $data = array();

    $this->data['action'] = 'index';

    // 最上層的類別ID
    $this->data['root'] = $root;

    // 前一層的類別ID，如果沒有目前的ID就以最上層類別ID作為類別ID
    if($currentPreID == 0) $currentPreID = $root;

    // 紀錄前一層及最上層的類別ID，以便使用
    $this->session->set_userdata('menu_root', $root);
    $this->session->set_userdata('menu', $currentPreID);

    // 選單
    $this->data['menu'] = $menu = $this->admin_crud->result_array($this->admin_crud->query_where($this->tb_lang, array('xpublish'=>'yes'),true,'xsort'));
    // 目前選單active
    $this->data['tabactive'] = $active = ($this->session->taba)?$this->session->taba:'';
    $xcode = (count($menu)>0)?$menu[0]['xcode']:'';
    if(!$active) $this->session->set_userdata('taba',$xcode);
    $this->data['langArray'] = '';
    foreach ($this->data['menu'] as $value) {
      $this->data['langArray'] .= $value['xcode'];
      $this->data['langArray'] .= ',';
    }

    // 麵包屑
    $this->data['nav'] = $this->nav($currentPreID);

    $layout['content'] = $this->load->view($this->viewPath, $this->data, true);
    $this->load->view($this->adminview, $layout);
  }

  // 麵包屑 (count 用來判斷是否為最後一層麵包屑)
  public function nav($preID = 0, $count = 0, $action = 'index')
  {
    $nav = '';

    // 使用者以選擇一個類別
    if($preID != 0) {

      // 取得資訊
      $query = $this->admin_crud->query_where($this->table, array('pid'=>$preID));

      if($query->num_rows() > 0) {

        $id = $query->row()->pid;
        $title = $query->row()->xname;
        $dbPreID = $query->row()->preid;

        // 該<li>在最後一層麵包屑，並且在列表介面時，才呈現<strong> css
        if($count == 0 && $action == 'index') $li = "<li class='active'><strong>$title</strong></li>"; // 最後一層麵包屑<strong>
        else $li = "<li>$title</li>";

        // 有上層類別
        if($dbPreID != 0) {
          $count ++;
          // 上層類別 + 目前類別
          $nav .= $this->nav($dbPreID, $count).$li;
        } else {
          $nav .= $li;
        }
      }
    }

    return $nav;
  }

  // 節點風格
  public function NodeStyle($type)
  {
    $class = array(
      'ol'=> 'dd-list', //'',
      'liCollapsed'=> 'dd-item',//'mjs-nestedSortable-branch mjs-nestedSortable-collapsed',
      'li'=> 'dd-item',//' mjs-nestedSortable-leaf',
      'handle'=> 'dd-handle',//'',
      'collapse'=> '<button data-action="collapse" type="button" style="display: none;">Collapse</button><button data-action="expand" type="button" style="display: none;">Expand</button>',//'<span class="disclose"><span></span></span>',
      'btnCreate'=> 'btn btn-default btn-xs pull-right',
      'btnCreateText'=> "<i class='fa fa-plus'></i>",
      'btnUpdate'=> 'btn btn-default btn-xs pull-right',
      'btnUpdateText'=> "<i class='fa fa-pencil'></i>",
      'btnDelete'=> 'btn btn-default btn-xs pull-right',
      'btnDeleteText'=> "<i class='fa fa-remove'></i>",
      'btnImport'=> 'btn btn-primary btn-xs pull-right',
      'btnImportText'=> "<i class='fa fa-plus'></i>",
    );

    if($type == 'json') $this->output->set_content_type('application/json')->set_output(json_encode($class));
    else return $class;
  }

  // 建立樹狀(不含匯入)
  public function makeTreeNotImport($menu, $preID, $dep,  $depth = 0, $limitDepth = 3, $class) {

    $html = '';
    $html .= "\n";
    $html .= str_repeat("\t", $dep);

    // 判斷層級
    if($preID != 0) $depth++;
    else $depth = 0;

    if($depth < $limitDepth) {

      if($depth == 0) $html .= "";
      else $html .= "<ol class='".$class['ol']."'>\n";

       foreach ($menu[$preID] as $item) {

         // 需修改範圍 //
         $id = $item['pid'];
         $title = $item['xname'];

        if (isset($menu[$item['pid']])) $html .= "<li class='".$class['liCollapsed']." dep$depth' id='list_$id'><div class='".$class['handle']."'>".$class['collapse']."$title ";
        else $html .= "<li class='".$class['li']." dep$depth' id='list_$id'><div class='".$class['handle']."'>".$class['collapse']."$title ";

        $html .= generatebutton('normal', $class['btnDelete'], "javascript:sortable_delete($id)", $this->data['permission']['DeleteAction'], $class['btnDeleteText']);
        $html .= generatebutton('normal', $class['btnUpdate'], "javascript:sortable_update($id)", $this->data['permission']['UpdateAction'], $class['btnUpdateText']);
        if($depth != $limitDepth-1) $html .= generatebutton('normal', $class['btnCreate'], "javascript:sortable_create($id)", $this->data['permission']['CreateAction'], $class['btnCreateText']);
        $html .= "</div>";
        // 需修改範圍 //

         // 有子類別
         if (isset($menu[$item['pid']])) {
           $html .= $this->makeTreeNotImport($menu, $item['pid'],$dep, $depth, $limitDepth, $class);
           $dep++;
         }

         $html .= "\n\t</li>\n";

       }
       if($depth == 0) $html .= "";
       else $html .= "</ol>\n";
     }

    return $html;
  }


  // 讀取節點
  public function readNode($lang)
  {
    $data = '';
    $array = array();

    $this->db->from($this->table)
      ->like('xlang',$lang)
      ->order_by('xsort');
    $list = $this->admin_crud->get(); $fisrt = 0;
    if(count($list) > 0) { $count = -1;
      foreach ($list as $value) { $count++;
        if($count==0) $fisrt = $value['preid'];
        $array[$value['preid']][] = $value;
      }
      $data['list'] = $this->makeTreeNotImport($array, 0, 0, 0, $this->maxDepth, $this->NodeStyle('array'));
    }

    $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }

  // 建立節點
  public function createNode()
  {
    $xname = $this->input->post('inputval', true);
    $preid = $this->input->post('id', true);

    $where['preid'] = $preid;
    $xsort = $this->admin_crud->query_max_sort($this->table, $where);

    // 第一層
    if($preid == 0) $xtype = 'Menu';
    else {
      // 上層為Sub改為MENU // $this->admin_crud->update($this->table, $preid, array('xtype'=> 'Menu'));
      // 建立該層為Sub
      $xtype = 'Sub';
    }

    $insertID = $this->admin_crud->create($this->table, array(
      'xname'=> $xname,
      'xlang'=> $this->session->taba,
      'xtype'=> $xtype,
      'preid'=> $preid,
      'xcreate'=>date('Y-m-d H:i:s'),
      'xsort'=> $xsort,
    ));
    $this->track->trackingDoing($this->table,'xname',$this->data['permission']['xname'],'新增',$insertID,$this->data['selfaccount'],$this->data['selflevel']);
  }

  // 新增/更新介面表單
  public function form($id = NULL)
  {
    $this->data['menu'] = $this->admin_crud->result_array($this->admin_crud->query_where($this->tb_lang, array('xpublish'=>'yes'),true,'xsort'));
    // 更新介面
    if(isset($id)) {

      $this->data['action'] = 'update';

      $array = $this->admin_crud->query_where($this->table, array('pid'=>$id))->result_array();
      $this->data['list'] = $array[0];

      if($this->data['list']['xtype'] == 'Multi') {
        $this->data['list']['xmulti'] = 'yes';
      } else {
        $this->data['list']['xmulti'] = 'no';
      }

    // 新增介面
    } else {

      $this->data['action'] = 'create';

      // 初始化，所有欄位
      $this->data['list'] = array();
    }

    // 麵包屑
    $this->data['nav'] = $this->nav($id, 0, $this->data['action']);

    $layout['content'] = $this->load->view($this->viewPath, $this->data, true);
    $this->load->view($this->adminview, $layout);
  }

  // 更新節點
  public function updateNode($id = NULL)
  {

    if(
      $this->input->post('xname', true)
    ) {
        $result = $this->admin_crud->row($this->admin_crud->query_where($this->table, array('pid'=>$id)));

        if(count($result)>0) {
          $preID = $result->preid;
          $oldxtype = $result->xtype;
        }

        // 型態處理
        $xmulti = $this->input->post('xmulti', true);
        // 多層級
        if($xmulti == 'yes') {
          $xtype = 'Multi';
        } else {
          // 多層級改為單層級
          if($oldxtype == 'Multi') {
            // 在第一層
            if($preID == 0) $xtype = 'Menu';
            // 在其他層
            else {
              // 上層為Sub改為MENU // $this->admin_crud->update($this->table, $preid, array('xtype'=> 'Menu'));
              $xtype = 'Sub';
            }
          // 資料不變
          } else {
            $xtype = $oldxtype;
          }
        }

        // DB資料
        $data = array(
          'xicon'=> ($this->input->post('xicon', true)) ? $this->input->post('xicon', true) : NULL,
          'xname'=> $this->input->post('xname', true),
          'xlang'=> ($this->input->post('xlang', true)) ? $this->input->post('xlang', true) : NULL,
          'xpage'=> ($this->input->post('xpage', true)) ? $this->input->post('xpage', true) : NULL,
          'xtype'=> $xtype,
          'preid'=> $preID,
          'xmodify'=> date('Y-m-d H:i:s'),
        );

        // 新增、更新動作
        $this->admin_crud->update($this->table, $id, $data);
        $this->track->trackingDoing($this->table,'xname',$this->data['permission']['xname'],'修改',$id,$this->data['selfaccount'],$this->data['selflevel']);
    }
  }

  // 刪除節點
  public function deleteNode()
  {
    $id = $this->input->post('id', true);

    // 子類別處理
    $query2 = $this->admin_crud->query_where($this->table, array('preid'=>$id));

    if($query2->num_rows() > 0) {

      $data['error'] = '尚有子類別，不可刪除';

    } else {

      // 刪除關聯
      $this->admin_crud->delete('admin_permission', array('MenuID'=>$id));

      // 刪除主類別
      $this->track->trackingDoing($this->table,'xname',$this->data['permission']['xname'],'刪除',$id,$this->data['selfaccount'],$this->data['selflevel']);
      $this->admin_crud->delete($this->table, array('pid'=>$id));

      $data['success'] = '刪除項目成功';
    }

    $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }

  // 排序
  public function sortNode()
  {
    $sortdata = $this->input->post('data', true);

    $this->saveSort($sortdata, 0);

  }

  // 排序-存入資料庫
  public function saveSort($menu, $preID) {

    $sort = 0;

    foreach ($menu as $value) {

      $id = $value['id'];

      $sort++;

      // 取得舊資料
      $row = $this->admin_crud->row($this->admin_crud->query_where($this->table, array('pid'=> $id)));
      if(count($row) > 0) $oldxtype  = $row->xtype;
      else $oldxtype = '';

      if($oldxtype == 'Multi') $type = $oldxtype;
      else if($preID == 0 || isset($value['children'])) $type = 'Menu';
      else $type = 'Sub';

      $this->admin_crud->update($this->table, $id, array(
        'preid'=> $preID,
        'xtype'=> $type,
        'xsort'=> $sort,
      ));

      if(isset($value['children'])) {
        $this->saveSort($value['children'], $id);
      }
    }
    return true;
  }
  // 紀錄目前tab
  public function recordtab($value)
  {
    $data = $value;
    $this->session->set_userdata('taba',$value);
    return $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }

}

// application/controllers/admin/permission/Menu
