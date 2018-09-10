<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Front extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
    /*
      $tb 資料表、$field 欄位名稱、
      $list 列表，格式 array('key'=>value)、
      $df 預設圖路徑，預設空值、
      $resize 是否縮圖，預設 false、
      $size 縮圖尺寸，預設 original [original|mid|small]、
      $custom 不管語系抓取原資料表，預設 false、
      $ratio 依比例縮放，預設 false、
      $useType 使用縮圖或裁圖，預設resize [resize|crop]、
    */
    // 縮圖用
    public function getfilepath($tb,$field,$list=array(),$df='',$resize=false,$size='original', $custom = '', $ratio = false, $useType='resize')
    {
      $filepath = '';
      if($tb && $field && count($list)>0) {
        if(isset($list[$field])) { // 列表存在該欄位參數
          if($xfile = $list[$field]) { // 有值
            if($resize == true && $size) // 進行縮圖
              $filepath = $this->common->getImagethumb($tb, $field, $size, $xfile, $custom, $ratio, $useType);
            else  // 不縮圖
              $filepath = $this->common->addroot($xfile);
          }
        }
      }
      if($df && !$filepath) $filepath = $df; // 沒圖時，使用預設圖
      return $filepath;
    }
    /* 回傳參數如下
      filepath 圖路徑
      fileW 圖寬
      fileH 圖高
      fileT 圖檔型態
    */
    // 取得圖片尺寸
    public function getsize($filepath)
    {
      $array = array('filepath'=>$filepath,'fileW'=>0,'fileH'=>0,'fileT'=>'');
      // 避免根目錄不只一層，所以統一開頭為 /uploads
      $str = '/uploads';
      if(strpos($filepath,$str)>-1) {
        $splAarry = explode($str,$filepath);
        for ($i=1; $i < count($splAarry); $i++) {
          $filepath = $str.$splAarry[$i];
        }
      }
      // 移除多餘 / 或 . 以便搜尋到檔案
      if(strpos($filepath,'.')==0) $filepath = substr($filepath,1,strlen($filepath));
      if(strpos($filepath,'/')==0) $filepath = substr($filepath,1,strlen($filepath));
      if($filepath && file_exists($filepath)) {
        $explode = explode('.',$filepath);
        $type = $explode[1];
        if($type == 'svg') {
          $xml = @simplexml_load_file($filepath);
          $attr = $xml->attributes();
          $array['fileW'] = (isset($attr->width))?$attr->width:0;
          $array['fileH'] = (isset($attr->height))?$attr->height:0;
          $array['fileT'] = 'image/svg';
          $array['filepath'] = $filepath;
        } else {
          $size = @getimagesize($filepath);
          $array['fileW'] = (isset($size[0]))?$size[0]:0;
          $array['fileH'] = (isset($size[1]))?$size[1]:0;
          $array['fileT'] = (isset($size['mime']))?$size['mime']:'';
          $array['filepath'] = base_url($filepath);
        }
      }
      return $array;
    }
    // 通用簡易取得資料庫列表
    public function result_array($table,$where,$xsort='xsort')
    {
      return $this->admin_crud->result_array($this->admin_crud->query_where($table,$where,true,$xsort));
    }
    // 是否有該檔案存在
    public function ckeckfilepath($fpath)
    {
      $addroot = $this->removeroot($fpath);
      if($fpath && file_exists($addroot)) {
        return $fpath;
      } else return '';
    }
    public function removeroot($fpath)
    {
      $root = $this->config->item('upload_folder');
      $root2 = 'uploads';
      if(strpos($fpath,$root)>-1) { // 移除uploads前的根目錄
        $fileArray = explode($root,$fpath);
        if(count($fileArray)>1) $fpath = $fileArray[1];
      }
      if(strpos($fpath,$root2)>-1) { // 縮圖會附上uploads
        $fileArray2 = explode($root2,$fpath);
        if(count($fileArray2)>1) $fpath = $fileArray2[1];
      }
      $addroot = 'uploads/'.$fpath; // 加上uploads目錄
      return $addroot;
    }
    // 取得靜態介面資料
    public function getJson($lang='')
    {
      $root = 'locales';
      if(!is_dir($root)) {
        if(mkdir($root, 0755, true)) {}
      }
      $path = "$root/$lang.json";
      if(!is_file($path)) {
        $file = fopen($path,"a+");
        fclose($file);
      }
      $string = file_get_contents($path);
      $json = json_decode($string, true);
      return $json;
    }
    // 顯示JSON回傳結果
    public function show($obj=array(),$str='',$replace=true)
    {
      if($obj && $str) {
        $error = 'OBJECT'; // 回傳物件
        $strArray = explode('.',$str);
        $result = $this->isExit($obj,$strArray);
        $result = (is_array($result))?$error:$result;
        if($result=='') { // 主語系
          $result = $this->isExit(array(),$strArray);
          $result = (is_array($result))?$error:$result;
        }
        if(!$result) $result = 'EMPTY'; // 未有值
        return $result;
      }
      return;
    }
    // 檢查JSON是否有該KEY
    public function isExit($json=array(),$strArray=array(),$dep=0,$preArray=array())
    {
      $strCount = count($strArray);
      $result='';
      if($dep==0) {
        if(count($json)==0) {
          $json = $this->getJson($this->config->item('defaultlang'));
        }
        if(isset($json[$strArray[$dep]])) {
          $preArray = $result = $json[$strArray[$dep]];
          $dep = $dep+1;
          if($dep<$strCount) {
            $result = $this->isExit($json,$strArray,$dep,$preArray);
          }
        } else {
          $result = '';
        }
      } else {
        if(isset($preArray[$strArray[$dep]])) {
          $preArray = $result = $preArray[$strArray[$dep]];
          $dep = $dep+1;
          if($dep<$strCount) {
            $result = $this->isExit($json,$strArray,$dep,$preArray);
          }
        } else {
          $result = '';
        }
      }
      return $result;
    }
}

// application/models/
