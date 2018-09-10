<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Template extends MY_Controller
{

  function __construct()
  {
    parent::__construct();
    $this->load->model('style'); // 載入樣板模組
  }

  public function datachange()
  {
    $data = array();
    $data[0] = $olddata = $this->input->post('olddata',true);
    $data[1] = $newdata = $this->input->post('newdata',true);
    $data[2] = $inputtype = $this->input->post('inputtype',true);
    $data['result'] = $newdata;
    return $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }

  public function getcontent($tb='',$styleT='')
  {
    $data = array(); $postValArr = array();
    array_push($postValArr,$this->input->get('xbg_img',true));
    if ($styleT!='text-A' && $styleT!='text-B' && $styleT!='text-C') {
      array_push($postValArr,$this->input->get('ximg1',true));
    }
    array_push($postValArr,$this->input->get('xedit_title',true));
    array_push($postValArr,$this->input->get('xedit_desc',true));
    $array = $this->style->input2html($tb,$styleT,$postValArr);
    $data['result'] = (count($array)>0)?$array[0]:array();
    $this->load->view('admin/preview/content_view', $data);
  }

}

// application/controllers/common
