<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Frontend extends Public_Controller
{

  function __construct()
  {
    parent::__construct();

    $this->load->library('mailer');
		$this->load->library('recaptcha');
    $this->prefix = $this->session->prefix;
  }

  public function getrecaptch()
	{
    // https://developers.google.com/recaptcha/docs/language
		$data = $this->recaptcha->getWidget().$this->recaptcha->getScriptTag(array('hl' => 'zh-TW'));
		return $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

  public function send($type)
  {
    $data = array('error'=>''); $body = ''; $insertid = 0; $table = '';
    $xmailArray = array(); $mailsetArray = array(); $mailset = array();

    $recaptcha = $this->input->post('g-recaptcha-response');
    $domain = strpos(base_url(),'localhost');
    if (!empty($recaptcha) || $domain>-1) {
      // 驗證驗證碼
      $response = $this->recaptcha->verifyResponse($recaptcha);
      if (isset($response['success']) and $response['success'] === true || $domain>-1) {
        // smtp
        $mailsetArray = $this->admin_crud->read('*', 'mailset_tb');
        if(count($mailsetArray)>0) $mailset = $mailsetArray[0];
        // 主旨
        $xsubject = $this->input->post('xsubject', true);
        if($xsubject) {
          switch ($type) {
            default:
              $tb_subject = '';
              break;
          }
          if($tb_subject) {
            $this->db->select("$tb_subject.xsubject, $tb_subject.xmailsubject, $tb_subject_xmail.xmail")
            ->from($tb_subject_xmail)
            ->join($tb_subject, "$tb_subject.pid = $tb_subject_xmail.fsubjectpid")
            ->where("$tb_subject.xpublish", 'yes')
            ->where("$tb_subject_xmail.fsubjectpid", $xsubject);
            $xmailArray = $this->admin_crud->get();
            $to_xsubject = (count($xmailArray)>0)?$xmailArray[0]['xsubject']:'';
            $to_xmailsubject = (count($xmailArray)>0)?$xmailArray[0]['xmailsubject']:'';

            $to_xsubjectmail = ''; $count = -1;
            foreach ($xmailArray as $value) { $count++;
              $to_xsubjectmail .= $value['xmail'];
              if($count!=count($xmailArray)-1) $to_xsubjectmail .= ',';
            }
          }
        }
        // 處理表單

      } else $data['error'] = "驗證失敗";
    } else $data['error'] = "請進行驗證";
    // 寄信
    if(!$data['error']) {
      $bool = $this->mailer->send_mail($mailset, $xmailArray, $body);
      if($bool) $data['success'] = true;
      else {
        $data['error'] = '表單送出失敗，請再次填寫!';
        if($table && $insertid) $this->admin_crud->update($table,$insertid,array('xerror'=>'寄信失敗'));
      }
    }
    return $this->output->set_content_type('application/json')->set_output(json_encode($data));
  }

  // 短路徑
  public function removeimg($fpath)
  {
    $root = 'uploads/';
    if(strpos($fpath,$root)>-1) { // 縮圖會附上uploads
      $fileArray = explode($root,$fpath);
      if(count($fileArray)>1) $fpath = $fileArray[1];
    }
    return $fpath;
  }
  // 加上根目錄
  public function addroot($fpath)
  {
    $addroot = 'uploads/'.$fpath; // 加上uploads目錄
    return $addroot;
  }
  // 下載
  public function download() {
    if (isset($_GET['path']) && $_GET['path']) {
      // 路徑
      $fpath = $_GET['path'];
      $fpath = $this->removeimg($fpath); // 短路徑
      $xfile = $this->addroot($fpath); // 加上根目錄
      $filepath = base_url($xfile); // 完整路徑
      $filesize = filesize($xfile);
      // 自製檔名
      if(isset($_GET['name']) && $_GET['name']!='') {
        $fname = $_GET['name'];
        $fname = $fname.'.'.pathinfo($fpath, PATHINFO_EXTENSION);
        // 判斷檔名是不是中文
        if (mb_strlen($fname, 'Big5') != strlen($fname) ) {
          $fname = iconv('UTF-8', 'Big5', $fname);
        }
        $filename = $fname;
      // 使用檔案原檔名
      } else {
        $filename = basename($filepath);
      }
      $this->load->helper('download');
      $data = file_get_contents($xfile);
      force_download($filename, $data);
    } else {
      $this->load->helper('download');
      $data = file_get_contents('no file');
      force_download('error', $data);
    }
  }
}

// application/controllers/common/Frontend
