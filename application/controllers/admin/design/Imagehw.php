<?php defined('BASEPATH') OR exit('No direct script access allowed');

// controllers 名稱 // 須修改
class Imagehw extends Admin_Controller
{
  // 基本設定
  public $table = 'tips_tb'; // 資料庫table name // 須修改
  public $jointable = 'tips_relative_tb'; // 關連資料庫 // 須修改

  /***********
    列表頁
  ***********/
  // 表格-呈現欄位 // 須修改
  public $tableTitle = array('主標','原尺寸(格式提醒.縮圖用)','中尺寸(等比例縮圖用)','小尺寸(等比例縮圖用)');
  public $tableColumn = array('xtitle','original','mid','small');
  public $tableHeadSize = array('50%','15%','15%','15%');
  public $tableSortValue = array(true,false,false,false);
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
    // 處理列表中尺寸
    if(count($data['data']) > 0) {
      $this->db->select("$this->jointable.fpid, $this->jointable.xsize, $this->jointable.xwidth, $this->jointable.xheight")
              ->from($this->jointable)
              ->join($this->table, "$this->table.pid = $this->jointable.fpid", 'left')
              ->order_by("$this->table.xsort");

      $filesizeArray = $this->admin_crud->get();

      foreach ($filesizeArray as $filesize) { $str = '';
        if($filesize['xwidth'] && $filesize['xheight']) $str = $filesize['xwidth'].' x '.$filesize['xheight'];
        else if($filesize['xwidth']) $str = '寬度: '.$filesize['xwidth'];
        else if($filesize['xheight']) $str = '高度: '.$filesize['xheight'];
        if($filesize['xsize'] == 'original') $array[$filesize['fpid']]['original'] = $str;
        if($filesize['xsize'] == 'mid') $array[$filesize['fpid']]['mid'] = $str;
        if($filesize['xsize'] == 'small') $array[$filesize['fpid']]['small'] = $str;
      }

      $count = -1;
      foreach ($data['data'] as $list) {  $count ++;
        $data['data'][$count]['original'] = (isset($array[$list['pid']]['original']))?$array[$list['pid']]['original']:'';
        $data['data'][$count]['mid'] = (isset($array[$list['pid']]['mid']))?$array[$list['pid']]['mid']:'';
        $data['data'][$count]['small'] = (isset($array[$list['pid']]['small']))?$array[$list['pid']]['small']:'';
      }
    }
    return $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }

  // 新增/更新介面表單
  public function form($id = NULL)
  {
    // 初始化，所有欄位
    $defaultArray = array();

    // 更新介面
    if(isset($id)) {

      $this->data['action'] = 'update';

      // 讀取列表
      $this->db->select("$this->table.*, $this->jointable.xsize, $this->jointable.xwidth, $this->jointable.xheight");
      $this->db->from($this->table);
      $this->db->join($this->jointable, "$this->jointable.fpid = $this->table.pid");
      $this->db->where("$this->jointable.fpid", $id);
      $array = $this->admin_crud->get();

      if(count($array) > 0) {
        foreach ($array as $value) {

          $defaultArray['pid'] = $value['pid'];
          $defaultArray['xpublish'] = $value['xpublish'];
          $defaultArray['xtitle'] = $value['xtitle'];
          $defaultArray['xtablename'] = $value['xtablename'];
          $defaultArray['xfieldname'] = $value['xfieldname'];
          $defaultArray['xfiletype'] = $value['xfiletype'];
          $defaultArray['xfilesize'] = $value['xfilesize'];
          $defaultArray['xsort'] = $value['xsort'];

          switch ($value['xsize']) {
            case 'original':
              $defaultArray['xoriginalW'] = $value['xwidth'];
              $defaultArray['xoriginalH'] = $value['xheight'];
              break;
            case 'mid':
              $defaultArray['xmidW'] = $value['xwidth'];
              $defaultArray['xmidH'] = $value['xheight'];
              break;
            case 'small':
              $defaultArray['xsmallW'] = $value['xwidth'];
              $defaultArray['xsmallH'] = $value['xheight'];
              break;
            default:
              break;
          }
        }
      }

    } else {
      $this->data['action'] = 'create';
    }

    $this->data['list'] = $defaultArray;

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
        $xsort = $this->common->processSort($this->table,$this->input->post('xsort', true),$this->input->post('insertxsortpid', true));

        // DB資料
        $data = array(
          'xpublish'=> ($this->input->post('xpublish', true)) ? $this->input->post('xpublish', true) : '',
          'xtitle'=> $this->input->post('xtitle', true),
          'xtablename'=> ($this->input->post('xtablename', true)) ? $this->input->post('xtablename', true) : '',
          'xfieldname'=> ($this->input->post('xfieldname', true)) ? $this->input->post('xfieldname', true) : '',
          'xfiletype'=> ($this->input->post('xfiletype', true)) ? $this->input->post('xfiletype', true) : '',
          'xfilesize'=> ($this->input->post('xfilesize', true)) ? $this->input->post('xfilesize', true) : '',
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

        // original
        $xsize = 'original';
        $postdata = $this->processWH($this->input->post('xoriginalW', true),$this->input->post('xoriginalH', true));
        $this->processSize($xsize,$postdata,$id);

        // middle
        $xsize = 'mid';
        $postdata = $this->processWH($this->input->post('xmidW', true),$this->input->post('xmidH', true));
        $this->processSize($xsize,$postdata,$id);

        // small
        $xsize = 'small';
        $postdata = $this->processWH($this->input->post('xsmallW', true),$this->input->post('xsmallH', true));
        $this->processSize($xsize,$postdata,$id);


    } else {
      $data['error'] = '欄位未輸入';
    }
    $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }

  public function processWH($width=0,$height=0)
  {
    $data = array(
      'width'=>$width,
      'height'=>$height
    );
    return $data;
  }

  public function processSize($xsize,$postdata = array(),$id)
  {
    $xwidth = (isset($postdata['width'])) ? $postdata['width'] : '';
    $xheight = (isset($postdata['height'])) ? $postdata['height'] : '';

    // 舊資料
    $oldData = $this->admin_crud->row($this->admin_crud->query_where($this->jointable, array('fpid' => $id, 'xsize'=> $xsize), false));

    // DB資料
    $data = array(
      'fpid'=> $id,
      'xsize'=> $xsize,
      'xwidth'=> $xwidth,
      'xheight'=> $xheight,
      (count($oldData) > 0) ? 'xmodify' : 'xcreate'=> date('Y-m-d H:i:s'),
    );
    if(count($oldData) > 0) $this->admin_crud->update_where($this->jointable, array('fpid' => $id, 'xsize'=> $xsize), $data);
    else $this->admin_crud->create($this->jointable, $data);
    return;
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
            // 刪除關連
            $this->admin_crud->delete($this->jointable, array('fpid'=>$value));
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
}

// application/controllers/
