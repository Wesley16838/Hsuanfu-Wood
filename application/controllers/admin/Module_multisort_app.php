<?php defined('BASEPATH') OR exit('No direct script access allowed');

// controllers 名稱 // 須修改
class Module_multisort_app extends Admin_Controller
{
  // 基本設定
  public $maintable = 'multisort_caty'; // 類別資料庫 // 須修改
  public $table = 'multisort_app'; // 資料庫table name // 須修改
  protected $jointable = 'multisort_relative'; // 關連資料庫 // 須修改
  // 是否用選單式類別
  public $selectMenu = true;  // 選單式(單選)、勾選式(多選)

  /***********
    列表頁
  ***********/
  // 表格-呈現欄位 // 須修改
  public $tableTitle = array('刊登','主標','小圖'/*,'其他功能'*/);
  public $tableColumn = array('xpublish','xtitle','xfile1'/*,''*/);
  public $tableHeadSize = array('8%','80%','10%','10%');
  public $tableSortValue = array(true,true,false,false);
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
  public $otherPageName = '';
  public $otherPageLink = '';
  public $otherPageCustom = '';

  function __construct()
  {
    parent::__construct();

    $this->is_logged_in(); // 判斷是否登入
    $this->load->model('MultiType', 'multitype'); // 處理類別及標籤
    // 回上一層
    $preid = $this->session->userdata('preid');
    $id = $this->multitype->getbackurl($this->maintable,$preid);
    $this->backurl = (is_numeric($id))?$this->indexPath.'/index/'.$id:'';

    // 頁面設定
    $this->indexPath ='admin/module_multisort_app'; // 須修改
    $this->viewPath = $this->common->resetPath($this->indexPath); // 首頁 // 固定
    $this->formPath = $this->indexPath.'/form'; // 新增頁 // 固定
    // 麵包屑
    $this->subnavPath = $this->common->getsubnav($this->session->userdata('preid')); // 固定
    // 其他頁面使用
    $this->session->set_userdata('prePage', $this->indexPath);

    // 頁碼設定
    if(!$this->session->userdata('pageNumber')) {
      $this->session->set_userdata('pageNumber', 1);
    }
    $this->currentPage = $this->session->userdata('pageNumber');

    $this->customTbname = false; // 不更動資料表的前綴詞
    // 自動建立圖片長寬限制資料 (有需要再使用) // 須修改
    // $this->common->checkImageLimit($this->table, 'xfile1', $this->customTbname);
    // $this->common->checkImageLimit($this->table, 'xfile2', $this->customTbname);
    // 取得圖片資訊 // 須修改
    $this->imageinfo['xfile1'] = $this->common->getImageinfo($this->table, 'xfile1', $this->customTbname);
    $this->imageinfo['xfile2'] = $this->common->getImageinfo($this->table, 'xfile2', $this->customTbname);
    // 編輯器樣板圖 // 固定
    $this->imageinfo['xbg_img'] = $this->common->getImageinfo($this->template_tb, 'xbg_img', true);
    $this->imageinfo['ximg1'] = $this->common->getImageinfo($this->template_tb, 'ximg1', true);
  }

  public function index()
  {
    $data = array();

    $this->data['action'] = 'index';

    $layout['content'] = $this->load->view($this->viewPath, $this->data, true);
    $this->load->view($this->adminview, $layout);
  }

  // 讀取列表
  public function read($id = 0, $ftypepid = 0)
  {
    $actionIndex = false; // 是否在列表頁
    // 排序:置入某筆資料
    if($id > 0 && $ftypepid > -1) $where = array("$this->table.pid !="=>$id, "$this->jointable.ftypepid"=> $ftypepid);
    // 列表:初始化ajax
    else if($id == 0 && $ftypepid > -1) {
      $where = array("$this->jointable.ftypepid"=>$ftypepid);
      $actionIndex = true;
    }
    else $where = array();

    $this->db->select("$this->table.*");
    $this->db->from($this->table);
    $this->db->join($this->jointable, "$this->jointable.fappid = $this->table.pid");
    $this->db->where($where);
    $this->db->order_by("$this->jointable.xsort");
    $data['data'] = $this->admin_crud->get();
    // 處理列表中圖片
    if(count($data['data']) > 0 && $actionIndex && count($this->imageArray) > 0) { $count = -1;
      foreach ($data['data'] as $value) { $count++;
        for ($i=0;$i<count($this->imageArray);$i++) {
          if(isset($data['data'][$count][$this->imageArray[$i]])) {
            $data['data'][$count][$this->imageArray[$i]] = $this->common->getImagethumb($this->table, $this->imageArray[$i], $this->imageSize, $value[$this->imageArray[$i]], $this->customTbname);
          }
        }
      }
    }
    echo json_encode($data);
  }

  // 新增/更新介面表單
  public function form($id = NULL)
  {
    // 編輯器樣板列表
    $this->data['alignlist'] = $alignlist = $this->admin_crud->result_array($this->admin_crud->query_where($this->template_tb, array('xpublish'=>'yes'),true,'xsort'));
    // 一定有上層類別
    if($this->session->userdata('preid')) {

      $ftypepid = $this->session->userdata('preid');

      // 更新介面
      if(isset($id)) {

        $this->data['action'] = 'update';

        // 讀取列表
        $this->db->select("$this->table.*, $this->jointable.xsort");
        $this->db->from($this->table);
        $this->db->join($this->jointable, "$this->jointable.fappid = $this->table.pid");
        $this->db->where("$this->table.pid", $id);
        $this->db->where("$this->jointable.ftypepid", $ftypepid);
        $this->db->order_by("$this->jointable.xsort");
        $array = $this->admin_crud->get();

        if(count($array) > 0) $this->data['list'] = $array[0];

        // 編輯器樣板 (html切割成 input欄位)
        $styleT = $array[0]['xalign'];
        $content = $array[0]['xedit2'];
        $inputArr = $this->style->html2input($this->template_tb,$styleT,$content);
        $compare = $this->style->getCompare($this->template_tb,$styleT);
        foreach ($compare as $key => $value) {
          $this->data['list'][$value] = $inputArr[$key];
        }

        // 讀取選單
        $this->data['menu'] = $this->multitype->getTypeMenu($this->maintable);
        // 勾選式: 判斷已勾選選單
        if($this->selectMenu == false) {
          $count = -1;
          foreach ($this->data['menu'] as $value) {
            $count ++ ;
            $this->db->select("$this->maintable.pid");
            $this->db->from($this->maintable);
            $this->db->join($this->jointable, "$this->jointable.ftypepid = $this->maintable.pid");
            $this->db->where("$this->jointable.fappid", $id);
            $this->db->where("$this->jointable.ftypepid", $value['pid']);
            $array = $this->admin_crud->get();

            if(count($array) > 0) $this->data['menu'][$count]['checked'] = true;
            else $this->data['menu'][$count]['checked'] = false;
          }
        }

      } else {

        $this->data['action'] = 'create';

        // 讀取選單
        $this->data['menu'] = array();
      }

    } else redirect($this->indexPath);

    $layout['content'] = $this->load->view($this->viewPath, $this->data, true);
    $this->load->view($this->adminview, $layout);
  }

  // 新增/更新
  public function save($id = NULL)
  {
    if(
      $this->input->post('xtitle', true) &&
      $this->input->post('ftypepid', true)
    ) {
        // 日期處理
        $xpostdate = $this->input->post('xpostdate', true);
        $xduedate = $this->input->post('xduedate', true);
        if($msg = $this->common->processDate($xpostdate,$xduedate)) $data['error'] = $msg;

        // 圖片處理
        $xfile1 = $this->common->processImg($this->table,$id,'xfile1',$this->input->post('xfile1', true));
        $xfile2 = $this->common->processImg($this->table,$id,'xfile2',$this->input->post('xfile2', true));

        // urltitle處理
        $xurltitle = ($this->input->post('xurltitle', true)) ? $this->input->post('xurltitle', true) : '';
        if($msg = $this->common->processUrl($this->table,$id,'xurltitle',$xurltitle)) $data['error'] = $msg;

        // SEO 目的地欄位為空值時
        $xtitle = $this->input->post('xtitle', true);
        $xsubtitle = ($this->input->post('xsubtitle', true)) ? $this->input->post('xsubtitle', true) : '';
        $xseotitle = ($this->input->post('xseotitle', true)) ? $this->input->post('xseotitle', true) : '';
        $xseodescription = ($this->input->post('xseodescription', true)) ? $this->input->post('xseodescription', true) : '';
        if($xtitle && !$xurltitle) {
          $xurltitle = $this->common->emptyAuto($xtitle,'true');
          if($msg = $this->common->processUrl($this->table,$id,'xurltitle',$xurltitle)) $data['error'] = $msg;
        }
        if($xtitle && !$xseotitle) $xseotitle = $this->common->emptyAuto($xtitle,'false');
        if($xsubtitle && !$xseodescription) $xseodescription = $this->common->emptyAuto($xsubtitle,'false');

        // 編輯器
        $xcontent = ($this->input->post('xcontent')) ? $this->input->post('xcontent') : ''; // 不可有第二參數移除class
        // 編輯器樣板處理 (欄位轉 html)
        $xalign = $this->input->post('xalign', true); $postValArr = array();
        array_push($postValArr,$this->common->removeroot($this->input->post('xbg_img', true)));
        if ($xalign!='text-A' && $xalign!='text-B' && $xalign!='text-C') {
          array_push($postValArr,$this->common->removeroot($this->input->post('ximg1', true)));
        }
        array_push($postValArr,$this->input->post('xedit_title', true));
        array_push($postValArr,$this->input->post('xedit_desc', true));
        $input2htmlArr = $this->style->input2html($this->template_tb,$xalign,$postValArr);
        $xedit1 = (count($input2htmlArr)>0)?$input2htmlArr[0]:'';
        $xedit2 = (count($input2htmlArr)>1)?$input2htmlArr[1]:'';

        if(!isset($data['error'])) {
          // DB資料
          $data = array(
            'xpublish'=> ($this->input->post('xpublish', true)) ? $this->input->post('xpublish', true) : '',
            'xindex'=> ($this->input->post('xindex', true)) ? $this->input->post('xindex', true) : '',
            'xpostdate'=> ($xpostdate) ? $xpostdate : NULL,
            'xduedate'=> ($xduedate) ? $xduedate : NULL,
            'xtitle'=> $xtitle,
            'xsubtitle'=> $xsubtitle,
            'xlink'=> ($this->input->post('xlink', true)) ? $this->input->post('xlink', true) : '',
            'xtarget'=> ($this->input->post('xtarget', true)) ? $this->input->post('xtarget', true) : '',
            'xfile1'=> $xfile1,
            'xfile2'=> $xfile2,
            'xalign' => $xalign, // 編輯器樣板
            'xcontent'=> ($xalign=='edit')?$xcontent:$xedit1, // 編輯器樣板
            'xedit2' => $xedit2, // 編輯器樣板
            'xurltitle'=> $xurltitle,
            'xseotitle'=> $xseotitle,
            'xseokeyword'=> ($this->input->post('xseokeyword', true)) ? $this->input->post('xseokeyword', true) : '',
            'xseodescription'=> $xseodescription,
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
          $this->track->trackingDoing($this->table,'xtitle',$this->data['permission']['xname'],$actionMessage,$id,$this->data['selfaccount'],$this->data['selflevel']);

          // 使用選單式類別
          if($this->selectMenu == true) {
            // 選擇類別
            $ftypepid = $this->input->post('ftypepid', true);
            // 排序選項處理
            $xsort = $this->multitype->processTypeSort($this->jointable,'ftypepid',$ftypepid,$this->input->post('xsort', true),$this->input->post('insertxsortpid', true));
            // 搜尋id
            $array = $this->admin_crud->result_array($this->admin_crud->query_where($this->jointable, array('fappid'=>$id)));

            $data = array(
              'ftypepid'=> $ftypepid,
              'fappid'=> $id,
              'xsort'=> $xsort,
              (count($array) > 0) ? 'xmodify' : 'xcreate'=> date('Y-m-d H:i:s'),
            );

            if(count($array) == 0) {
              $this->admin_crud->create($this->jointable, $data);
            } else {
              $where['fappid'] = $id;
              $this->admin_crud->update_where($this->jointable, $where, $data);
            }
          // 使用checkbox 類別
          } else {
            // 排序選項處理
            $xsort = $this->multitype->processTypeSort($this->jointable,'ftypepid',$this->session->userdata('preid'),$this->input->post('xsort', true),$this->input->post('insertxsortpid', true));
            $this->multitype->processTypeCheckbox($this->jointable,'ftypepid',$this->session->userdata('preid'),$id,$xsort,$this->input->post('ftypepid[]', true));
          }
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
            $this->track->trackingDoing($this->table,'xtitle',$this->data['permission']['xname'],$data['success'],$value,$this->data['selfaccount'],$this->data['selflevel']);
            $this->admin_crud->delete($this->table, array('pid'=>$value));
            // 刪除關連
            $this->admin_crud->delete($this->jointable, array('fappid'=>$value));
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
    $where['ftypepid'] = $this->session->userdata('preid');
    $this->admin_crud->sorting($this->jointable,$obj[0],$start,$where,'fappid');
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

  // 更改列表中的 radio 值
  public function processRadio($id,$field,$val)
  {
    $this->admin_crud->update($this->table,$id,array($field=>$val)); return;
  }
}

// application/controllers/
