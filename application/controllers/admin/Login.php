<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Admin_Controller
{

  function __construct()
  {
    parent::__construct();

    $this->logged_in();
  }

  public function index()
  {
    $data = array();
    $list = $this->admin_crud->read('xfile1','admin_logo');
    if(count($list)>0 && $list[0]['xfile1']) $this->data['img'] = $this->common->addroot($list[0]['xfile1']);
    else $this->data['img'] = '';
    $this->load->view('admin/login_view', $this->data);
  }

  public function check()
  {
    if(
      $this->input->post('Username', true) &&
      $this->input->post('Password', true) &&
      $this->config->item('validresult') == 'yes'
    ) {
      $array = $this->admin_crud->checkUser($this->input->post('Username', true));

      if(count($array) > 0) {
        $Account = $array['acc'];
        $Password = $array['psw'];
        $AccessKey = $array['key'];
        $GroupID = $array['group'];

        $bool = $this->admin_crud->checkaccpsw($this->input->post('Password', true),$Password,$AccessKey);
        if($bool) {
          $data['success'] = site_url($this->defaultURL);
          $query2 = $this->admin_crud->query_where('admin_group', array('pid'=> $GroupID));
          if($query2->num_rows() > 0) {
            $Level = $query2->row()->xlevel;
            $this->track->trackingDoing('','','','Login','',$Account,$Level);
          }
        } else {
          $data['error'] = '登入失敗';
          $this->track->trackingDoing('','','','Login 密碼錯誤','',$this->input->post('Username', true),'');
        }
      }
      else {
        $data['error'] = '登入失敗';
      }

    }
    else $data['error'] = '登入失敗';

    $this->output->set_content_type('application/json')->set_output(json_encode($data));

  }
}

// application/controllers/admin/Login
