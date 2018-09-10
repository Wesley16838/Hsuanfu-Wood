<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Administer extends Admin_Controller
{

  function __construct()
  {
    parent::__construct();
    $this->lang = $this->config->item('defaultlang');
  }

  public function index()
  {
  }

  public function logout()
  {
    $this->session->unset_userdata('access_key');
    $this->session->unset_userdata('MenuID');
    $data = base_url().'admin';
    return $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }

  public function menu($id=0)
  {
    if(is_numeric($id) && $id!=0) {
      $this->session->set_userdata('MenuID', $id);
      $this->common->resetPageNum();
    } else if($id) {
      $this->common->resetPageNum();
    }
    $this->session->set_userdata('otherPageUrl', '');
    $this->session->set_userdata('preName', '');
  }

  // 取得範本
  public function getTemplates($tmp)
  {
    $table_caty = 'cktmp_caty';
    $table_app = 'cktmp_app';
    $jointable = 'cktmp_relative';

    $this->db->select("$table_app.xtitle as title, $table_app.xsubtitle as description, $table_app.xfile1 as image, $table_app.xcontent as html,");
    $this->db->from($table_app);
    $this->db->join($jointable, "$jointable.fappid = $table_app.pid");
    $this->db->join($table_caty, "$table_caty.pid = $jointable.ftypepid");
    $this->db->where("$table_caty.xcode", $tmp);
    $this->db->where("$table_caty.xpublish", 'yes');
    $this->db->where("$table_app.xpublish", 'yes');
    $this->db->order_by("$jointable.xsort");
    $data = $this->admin_crud->get();

    return $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }

  public function getHtml($tmp)
  {
    $data['header'] = ''; $data['footer'] = ''; $data['css'] = '';

    $table_caty = 'cktmp_caty';
    $result = $this->admin_crud->row($this->admin_crud->query_where($table_caty, array('xcode'=> $tmp,'xpublish'=>'yes'),true,'xsort'));

    if(count($result)>0) {
      $array = explode('xcontent',$result->xcontent);
      if(count($array) > 0) $data['header'] = $array[0];
      if(count($array) > 1) $data['footer'] = $array[1];
    }
    return $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }

  public function getCss()
  {
    $data['css'] = '';

    $string = explode('/',$this->input->post('url',true));
    $split = explode('_',$string[2]);
    if(count($split)>0) $lang = $split[0];
    else $lang = '';

    $table = 'cktmp_css';
    $result = $this->admin_crud->result_array($this->admin_crud->query_where($table,array('xlang'=>$lang)));
    if(count($result)>0) $data['css'] = explode(',',$result[0]['xpath']);
    else {
      $result = $this->admin_crud->read('xpath',$table,'xsort');
      if(count($result)>0) $data['css'] = explode(',',$result[0]['xpath']);
    }
    return $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }

  // 取得圖片限制的長寬
  public function getImageLimit($tableName, $fieldName, $custom = '')
  {
    $data = array(); $tablename2 = '';

    $split = explode('_', $tableName);
    for ($i=1; $i < count($split); $i++) {
      $tablename2 .= '_'.$split[$i];
    }
    if(!$custom) $tableName = $this->lang.$tablename2;

    $this->db->select('tips_relative_tb.xwidth, tips_relative_tb.xheight');
    $this->db->from('tips_tb');
    $this->db->join('tips_relative_tb', 'tips_relative_tb.fpid = tips_tb.pid');
    $this->db->where('tips_tb.xtablename', $tableName);
    $this->db->where('tips_tb.xfieldname', $fieldName);
    $array = $this->admin_crud->get();

    if(count($array) > 0) {
      foreach ($array as $value) {
        if($value['xwidth'] && $value['xheight']) $data[] = $value['xwidth'].'x'.$value['xheight'];
      }
    }
    return $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }

  // 檢查副檔名是否可以合法
  public function checkImagetype($tableName, $fieldName, $type, $custom = '')
  {
    $data = false; $tablename2 = '';
    $split = explode('_', $tableName);
    for ($i=1; $i < count($split); $i++) {
      $tablename2 .= '_'.$split[$i];
    }
    if(!$custom) $tableName = $this->lang.$tablename2;

    $list = $this->admin_crud->result_array($this->admin_crud->query_where('tips_tb',array('xtablename'=>$tableName,'xfieldname'=>$fieldName)));
    if(count($list)>0) {
      $validtype = $list[0]['xfiletype'];
      $array = $this->common->getImagetype($validtype);
      if(strpos($array[1],strtolower($type))>-1) $data = true;
    } else $data = true;
    return $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }

  // 紀錄目前的url
  public function currenturl()
  {
    if($custom = $this->input->post('custom', true)) {
      $this->session->set_userdata($custom.'id', $this->input->post('id', true));
      $this->session->set_userdata($custom.'url', $this->input->post('url'));
      $this->session->set_userdata($custom.'num', 1);
    } else {
      $this->session->set_userdata('ofkpid', $this->input->post('id', true));
      $this->session->set_userdata('otherPageUrl', $this->input->post('url'));
      $this->common->resetPageNum();
    }
    $data = base_url().$this->input->post('url');
    $this->session->set_userdata('preName', $this->input->post('name'));
    return $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }

  // 開啟/關閉設定
  public function offauto($tb='',$field='',$return='')
  {
    $data = array(); $result = 'no';

    if($tb && $field) {
      $tb = $this->common->splittb($tb);

      $list = $this->admin_crud->result_array($this->admin_crud->query_where($this->tb_setting,array('xtb'=>$tb,'xfield'=>$field)));
      if($return) {
        if(count($list)>0) $result = $list[0]['xoff'];
        return $this->output->set_content_type('application/json')->set_output(json_encode($result));
      }
      // 第一次為關閉
      if (count($list)==0) {
        $xsort = $this->common->processSort($this->tb_setting,'last',0);
        $result = 'yes';
        $data = array(
          'xtb'=>$tb,
          'xfield'=>$field,
          'xoff'=>$result,
          'xcreate'=>date('Y-m-d H:i:s'),
          'xsort'=>$xsort,
        );
        $this->admin_crud->create($this->tb_setting,$data);
      // 已有開關過
      } else {
        $id = $list[0]['pid'];
        $result = $newxoff = ($list[0]['xoff']=='no')?'yes':'no';
        $data = array(
          'xoff'=>$newxoff,
          'xmodify'=>date('Y-m-d H:i:s'),
        );
        $this->admin_crud->update($this->tb_setting,$id,$data);
      }
    }
    return $this->output->set_content_type('application/json')->set_output(json_encode($result));
  }

  // SEO自動化 - 接值
  public function AutoUpdSeo($tb)
  {
    $data = array();
    $input = $this->input->post('input',true);
    $output = $this->input->post('output',true);
    $isurl = $this->input->post('isurl',true);
    if($tb && $input && $output && $isurl) {
      $inputArray = explode(',',$input);
      $outputArray = explode(',',$output);
      $isurlArray = explode(',',$isurl);
      $outval = '';
      // 取出資料庫值
      $list = $this->admin_crud->read('*',$tb);
      // 處理欄位
      foreach ($isurlArray as $key => $value) {
        $inputfield = $inputArray[$key];
        $outputfield = $outputArray[$key];
        // 判斷欄位是否要同步
        $xtb = $this->common->splittb($tb);
        $settingArray = $this->admin_crud->result_array($this->admin_crud->query_where($this->tb_setting,array('xtb'=>$xtb,'xfield'=>$outputfield)));
        $xoff = (count($settingArray)>0)?$settingArray[0]['xoff']:'no';
        // 同步才進行資料更新
        if($xoff=='no') $data[] = $this->processAutoField($tb,$list,$inputfield,$outputfield,$value);
      }
    }
    return $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }
  // SEO自動化 - 資料處理
  public function processAutoField($tb,$list,$inputfield,$outputfield,$isurl)
  {
    foreach ($list as $key => $value) {
      $pid = $value['pid'];
      $inputval = $value[$inputfield];
      $updateV = true; // 是否可以更新
      // 進行url欄位處理
      if($isurl=="true") {
        $outval = trim($inputval);
        $outval = $this->common->removeAllSpace($outval);
        $outval = $this->common->clearString($outval);
        // 格式正確且不重複才可以新增
        if($this->common->processUrl($tb,$pid,$outputfield,$outval)) {
          $updateV = false;
          // 清空 url欄位
          $this->admin_crud->update($tb,$pid,array($outputfield=>''));
        }
      } else $outval = $inputval;
      // 更新DB
      if($updateV) $this->admin_crud->update($tb,$pid,array($outputfield=>$outval));
    }
    return;
  }
}

// application/controllers/admin/Administer
