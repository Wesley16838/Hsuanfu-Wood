<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Track extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    function trackingDoing($table,$fieldName,$pageTitle,$actionMessage,$actionID,$adminName,$level)
    {
      $table_track = 'tracking_tb';

      // 取出紀錄保留月份數
  		$TrackSaveTime = 1;

      // 取出被更改欄位主標
      if($actionID) {
        $array = $this->admin_crud->result_array($this->admin_crud->query_where($table, array('pid'=>$actionID)));
    		if(isset($array[0][$fieldName])) $actionMessage .= "：".stripslashes($array[0][$fieldName]);
        if($pageTitle) $pageTitle = $this->getlang($table).$pageTitle;
      }

      // 進行操作紀錄
      $this->admin_crud->create($table_track, array(
        'xadmin' => ($adminName) ? $adminName : '',
        'xpage' => ($pageTitle) ? $pageTitle : '',
        'xaction' => ($actionMessage) ? $actionMessage : '',
        'xactionID' => ($actionID) ? $actionID : 0,
        'xcreate'=> date('Y-m-d H:i:s'),
      ));

      // 刪除距目前日期一個月以上的紀錄
			$myear = date("Y");
			$mmonth = date("m");
			$mday = date("d");
			$mHour = date("H");
			$mMin = date("i");
			$mSec = date("s");
			$off_date = date("Y-m-d H:i:s",mktime($mHour,$mMin,$mSec,$mmonth-$TrackSaveTime,$mday,$myear));

      $this->admin_crud->delete($table_track, array('xcreate <'=>$off_date));

      return true;
    }

    public function searchWord($xtb,$xid,$list=array(),$choose=array())
    {
      $lang = $this->getlang($xtb);
      if($lang) {
        $array = explode('_',$xtb);
        $lang = $array[0]; $table = $lang.'_search';

        $array2 = $this->admin_crud->row($this->admin_crud->query_where($table,array('xtb'=>$xtb,'xid'=>$xid)));
        if(count($array2)>0) {$id = $array2->pid; $dbsort = $array2->xsort;} else $id = 0;

        $xsort = ($id)?$dbsort:$this->admin_crud->query_min_sort($table);
        $xkeyword = array();
        foreach ($list as $key => $value) {
          foreach ($choose as $sub) {
            if($key==$sub && $value !='') $xkeyword[] = $value;
          }
        }
        $xkeyword = implode(',',$xkeyword);

        $data = array(
          'xtb' => $xtb,
          'xid' => $xid,
          'xkeyword' => $xkeyword,
          'xcreate'=> date('Y-m-d H:i:s'),
          'xsort'=>$xsort
        );
        if($id) $this->admin_crud->update($table,$id,$data);
        else $this->admin_crud->create($table,$data);
      }
      return true;
    }

    function getlang($table)
    {
      $lang = '';
      $array = explode('_',$table);
      if(count($array)>0) {
        $result = $this->admin_crud->result_array($this->admin_crud->query_where($this->tb_lang,array('xcode'=>$array[0],'xpublish'=>'yes')));
        if(count($result)>0) {
          $lang = $result[0]['xtitle'].' - ';
        }
      }
      return $lang;
    }
}

// application/models/
