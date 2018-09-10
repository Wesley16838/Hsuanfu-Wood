<?php defined('BASEPATH') OR exit('No direct script access allowed');

// controllers 名稱 // 須修改
class Multisort_caty extends Admin_Controller
{
  // 基本設定
  public $MultiLevel = 1; // 類別層級(最少1層)(值=2，2層類別+1層詳細頁) // 須修改
  public $table = 'cktmp_caty'; // 資料庫table name // 須修改
  protected $jointable = 'cktmp_relative'; // 關連資料庫 // 須修改
  // 頁面設定
  public $subindexPath = 'admin/design/multisort_app'; // 須修改

  /***********
    列表頁
  ***********/
  // 表格-呈現欄位 // 須修改
  public $tableTitle = array('刊登','主標','切換');
  public $tableColumn = array('xpublish','xname','next');
  public $tableHeadSize = array('8%','80%');
  public $tableSortValue = array(true,true);
  // 表格-縮圖設定 // 須修改
  protected $imageSize = 'original'; // original、mid、small
  protected $imageArray = array('xfile1');
  // 表格-搜尋欄位 // 須修改
  public $searchTitle = array('主標');
  public $searchColumn = array('xname');
  // 表格-其他功能 // 須修改
  public $showPaging = true;
  public $showSearching = true;
  public $showMulti = true;

  function __construct()
  {
    parent::__construct();

    $this->is_logged_in(); // 判斷是否登入
    $this->load->model('MultiType', 'multitype'); // 處理類別及標籤

    // 回上一層
    if(strpos(current_url(),'/index/')>-1) {
      $split = explode('/index/',current_url());
      $preid = (count($split)>1)?$split[1]:0;
    } else $preid = 0;
    $id = $this->multitype->getbackurl($this->table,$preid);
    $this->backurl = (is_numeric($id))?($id)?$this->indexPath.'/index/'.$id:$this->indexPath:'';

    // 紀錄類別層資料表以便使用
    $this->session->set_userdata('preMainTable', $this->table);

    // 頁碼設定
    if(!$this->session->userdata('pageNumber')) {
      $this->session->set_userdata('pageNumber', 1);
    }
    $this->currentPage = $this->session->userdata('pageNumber');
  }

  public function index($preid = 0)
  {
    $data = array();

    $this->data['action'] = 'index';
    // 紀錄上層
    $this->session->set_userdata('preid', $preid);
    // 紀錄目前層級
    $this->session->set_userdata('nowlevel', $this->getLevel($preid));

    $layout['content'] = $this->load->view($this->viewPath, $this->data, true);
    $this->load->view($this->adminview, $layout);
  }

  // 讀取列表
  public function read($id = 0, $preid = 0)
  {
    $actionIndex = false; // 是否在列表頁
    // 排序:置入某筆資料
    if($id > 0 && $preid > -1) $where = array('pid !='=>$id, 'preid'=> $preid);
    // 列表:初始化ajax
    else if($id == 0 && $preid > -1) {
      $where = array('preid'=>$preid);
      $actionIndex = true;
    }
    else $where = array();

    $data['data'] = $this->admin_crud->result_array($this->admin_crud->query_where($this->table, $where, true, 'xsort', 'asc'));
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
    echo json_encode($data);
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
      $this->input->post('xname', true)
    ) {

        // 排序選項處理
        $xsort = $this->common->processSort($this->table,$this->input->post('xsort', true),$this->input->post('insertxsortpid', true));

        // DB資料
        $data = array(
          'xpublish'=> ($this->input->post('xpublish', true)) ? $this->input->post('xpublish', true) : '',
          'xname'=> $this->input->post('xname', true),
          'xcode'=> ($this->input->post('xcode', true)) ? $this->input->post('xcode', true) : '',
          'xcontent'=> ($this->input->post('xcontent')) ? $this->input->post('xcontent') : '', // 不可有第二參數移除class
          'preid'=> $this->session->userdata('preid'),
          ($id) ? 'xmodify' : 'xcreate'=> date('Y-m-d H:i:s'),
          'xsort'=> $xsort,
        );

        // 新增、更新動作
        if($id) {
          $actionMessage = '修改-第'.($this->session->userdata('nowlevel')+1).'層類別';
          $this->admin_crud->update($this->table, $id, $data);
        }
        else {
          $actionMessage = '新增-第'.($this->session->userdata('nowlevel')+1).'層類別';
          $id = $this->admin_crud->create($this->table, $data);
        }
        $this->track->trackingDoing($this->table,'xname',$this->data['permission']['xname'],$actionMessage,$id,$this->data['selfaccount'],$this->data['selflevel']);

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
            // 判斷是否為最後一層
            if((int)($this->session->userdata('nowlevel')+1) == $this->MultiLevel) {
              // 是否有關連
              $this->db->from($this->table);
              $this->db->join($this->jointable,"$this->jointable.ftypepid = $this->table.pid");
              $this->db->where("$this->jointable.ftypepid", $value);
              $relativeArray = $this->admin_crud->get();
              // 不可刪
              if(count($relativeArray) > 0) {
                $data['error'] = '不可刪除';
              } else {
                $data['success'] = '刪除-第'.$this->MultiLevel.'層類別';
                $this->track->trackingDoing($this->table,'xname',$this->data['permission']['xname'],$data['success'],$value,$this->data['selfaccount'],$this->data['selflevel']);
                // 刪除項目
                $this->admin_crud->delete($this->table, array('pid'=>$value));
              }
            } else {
              // 查看除了自己上層是否有其他類別
              $selfArray = $this->admin_crud->result_array($this->admin_crud->query_where($this->table, array('pid'=> $value)));
              if(count($selfArray) > 0) {
                // 取得上層
                $preid = $selfArray[0]['preid'];
                $preOtherArray = $this->admin_crud->result_array($this->admin_crud->query_where($this->table, array('preid'=> $preid, 'pid !='=> $value)));
                // 有找到上層其他類別可以轉移
                if(count($preOtherArray) > 0) {
                  $subArray = $this->admin_crud->result_array($this->admin_crud->query_where($this->table, array('preid'=> $value)));
                  // 有子類別
                  if(count($subArray) > 0) {
                    // 更新子類別 preid 為上層其他類別的第一個類別的 pid
                    $otherpreid = $preOtherArray[0]['pid'];
                    // $data['error'] = '有子類別，轉移至:'.$otherpreid."|";
                    $data['error'] = '不可刪除';
                    // 刪除項目
                    // $this->admin_crud->delete($this->table, array('pid'=>$value));
                  // 沒有子類別
                  } else {
                    $data['success'] = '刪除-第'.($this->session->userdata('nowlevel')+1).'層類別';
                    $this->track->trackingDoing($this->table,'xname',$this->data['permission']['xname'],$data['success'],$value,$this->data['selfaccount'],$this->data['selflevel']);
                    // 刪除項目
                    $this->admin_crud->delete($this->table, array('pid'=>$value));
                  }
                // 上層沒有其他類別
                } else {
                  if ($preid==0) {
                    // 刪除項目
                    $this->admin_crud->delete($this->table, array('pid'=>$value));
                  } else $data['error'] = '不可刪除';
                }
              }
            }
          }
          break;
        case 'release':
          $data['success'] = '刊登-第'.($this->session->userdata('nowlevel')+1).'層類別';
          foreach ($array as $value) {
            $this->admin_crud->update($this->table, $value, array(
              'xpublish'=> 'yes',
              'xmodify'=> $now
            ));
            $this->track->trackingDoing($this->table,'xname',$this->data['permission']['xname'],$data['success'],$value,$this->data['selfaccount'],$this->data['selflevel']);
          }
          break;
        case '_release':
          $data['success'] = '取消刊登-第'.($this->session->userdata('nowlevel')+1).'層類別';
          foreach ($array as $value) {
            $this->admin_crud->update($this->table, $value, array(
              'xpublish'=> 'no',
              'xmodify'=> $now
            ));
            $this->track->trackingDoing($this->table,'xname',$this->data['permission']['xname'],$data['success'],$value,$this->data['selfaccount'],$this->data['selflevel']);
          }
          break;
        case 'home':
          $data['success'] = '刊登首頁-第'.($this->session->userdata('nowlevel')+1).'層類別';
          foreach ($array as $value) {
            $this->admin_crud->update($this->table, $value, array(
              'xindex'=> 'yes',
              'xmodify'=> $now
            ));
            $this->track->trackingDoing($this->table,'xname',$this->data['permission']['xname'],$data['success'],$value,$this->data['selfaccount'],$this->data['selflevel']);
          }
          break;
        case '_home':
          $data['success'] = '取消刊登首頁-第'.($this->session->userdata('nowlevel')+1).'層類別';
          foreach ($array as $value) {
            $this->admin_crud->update($this->table, $value, array(
              'xindex'=> 'no',
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

  // 取得目前層級
  public function getLevel($preid = 0)
  {
    $count = 0;
    // 使用者以選擇一個類別
    if($preid != 0) {
      $count ++;
      // 取得上層資訊
      $query = $this->admin_crud->query_where($this->table, array('pid'=>$preid));
      if($query->num_rows() > 0) {
        $parent = $query->row()->preid;
        // 有上層類別
        if($parent != 0) {
          $count +=$this->getLevel($parent);
        }
      }
    }
    return $count;
  }

  // 更新檔案欄位值
  public function saveFile()
  {
    $field = $this->input->post('field', true);
    $id = $this->input->post('id', true);

    $this->admin_crud->update($this->table, $id, array($field => NULL));
  }

}

// application/controllers/
