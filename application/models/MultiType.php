<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MultiType extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    // 處理類別排序
    function processTypeSort($table, $field, $ftypepid, $post_xsort, $post_insertxsortpid)
    {
      $xsort = $post_xsort;
      if($post_xsort == 'first') {
        $where[$field] = $ftypepid;
        $xsort = $this->admin_crud->query_min_sort($table, $where);
      }
      if($post_xsort == 'last') { // 加入至該類別中最後一個
        $where[$field] = $ftypepid;
        $xsort = $this->admin_crud->query_max_sort($table, $where);
      }
      if($post_xsort == 'insert')  {
        $where[$field] = $ftypepid;
        $where['fappid'] = $post_insertxsortpid;
        $xsort = $this->admin_crud->query_max_sort($table, $where);

        $this->db->set('xsort', 'xsort+1', FALSE)
        ->where('xsort >=', $xsort)
        ->where('ftypepid', $ftypepid)
        ->update($table);
      }
      return $xsort;
    }

    // 判斷陣列是否有新增或移除
    function compareArray($table, $field, $fappid, $post)
    {
      // 取得舊/新資料
      $array = $this->admin_crud->result_array($this->admin_crud->query_where($table, array('fappid'=>$fappid)));
      if(count($array) > 0) {
        foreach ($array as $value) { $old[] = $value[$field]; }
      } else $old = array();

      if(count($post) > 0) $new = array_filter($post);
      else $new = array();

      // 比較陣列
      if(count($new) > 0 && count($old) > 0) {
        $add = array_diff($new,$old); // 新增
        $remove = array_diff($old,$new); // 減少
      } else if(count($new) == 0 && count($old) > 0) {
        $add = array();
        $remove = $old;
      } else {
        $add = $new;
        $remove = array();
      }
      return array($add,$remove);
    }

    // 處理勾選式類別
    function processTypeCheckbox($table, $field, $ftypepid, $fappid, $xsort, $post_ftypepid)
    {
      // 判斷陣列是否有新增或移除
      $array = $this->compareArray($table, $field, $fappid, $post_ftypepid);
      $add = $array[0]; $remove = $array[1];

      // 有新增項目
      if(count($add) > 0) {
        foreach ($add as $value) {
          // 排序處理:加入至該類別中第一個
          $where[$field] = $value;
          $xsort = $this->admin_crud->query_min_sort($table, $where);

          $this->admin_crud->create($table, array(
            $field=> $value,
            'fappid'=> $fappid,
            'xsort'=> $xsort,
            'xcreate'=> date('Y-m-d H:i:s'),
          ));
        }
      }
      // 有項目被刪除
      if(count($remove) > 0) {
        foreach ($remove as $value) {
          $this->admin_crud->delete($table, array(
            $field=> $value,
            'fappid'=> $fappid,
          ));
        }
      }
      // 未更動項目:更動排序
      if(count($add) == 0 && count($remove) == 0) {
        $where[$field] = $ftypepid;
        $where['fappid'] = $fappid;
        $this->admin_crud->update_where($table, $where, array(
          $field=> $ftypepid,
          'fappid'=> $fappid,
          'xsort'=> $xsort,
          'xmodify'=> date('Y-m-d H:i:s'),
        ));
      }
      return;
    }

    // 處理標籤
    function processTag($table, $field, $fappid, $post)
    {
      // 判斷陣列是否有新增或移除
      $array = $this->compareArray($table, $field, $fappid, $post);
      $add = $array[0]; $remove = $array[1];

      // 有新增項目
      if(count($add) > 0) {
        foreach ($add as $value) {
          $this->admin_crud->create($table, array(
            $field=> $value,
            'fappid'=> $fappid,
            'xcreate'=> date('Y-m-d H:i:s'),
          ));
        }
      }
      // 有項目被刪除
      if(count($remove) > 0) {
        foreach ($remove as $value) {
          $this->admin_crud->delete($table, array(
            $field=> $value,
            'fappid'=> $fappid,
          ));
        }
      }
      return;
    }

    // 取得選單
    function getTypeMenu($table, $preIDArray = array(0), $count = 0)
    {
      $result = array();
      if($this->session->userdata('nowlevel')) {
        while ($count < $this->session->userdata('nowlevel')) {
          $this->db->from($table)
                   ->where_in('preid', $preIDArray)
                   ->order_by('xsort');
          $result = $this->admin_crud->get();

          if(count($result) > 0) {
            $preIDArray = array();
            foreach ($result as $value) {
              $preIDArray[] = $value['pid'];
            }
            // 目前層級未達最底層
            if($this->session->userdata('nowlevel') != $count) {
              $count++;
            }
          }
        }
      }
      return $result;
    }

    // 組合陣列
    function array2str($array)
    {
      $string = ',';
      if(count($array)>0) {
        for ($i=0; $i < count($array); $i++) {
          $string .= $array[$i].',';
        }
      }
      return $string;
    }

    // 非關聯標籤處理
    function processxTag($table,$field,$post)
    {
      $xtag = ',';
      if(count($post)>0) {
        for ($i=0; $i < count($post); $i++) {
          $array = $this->admin_crud->row($this->admin_crud->query_where($table,array($field=>$post[$i],'xpublish'=>'yes')));
          if(count($array)>0) $xtag .= $array->pid;
          else {
            $tagxsort = $this->common->processSort($table,'first','');
            $xtag .= $this->admin_crud->create($table,array(
              'xpublish'=>'yes',
              'xtitle'=>$post[$i],
              'xcreate'=>date('Y-m-d H:i:s'),
              'xsort'=>$tagxsort,
            ));
          }
          $xtag .= ',';
        }
      }
      return $xtag;
    }

    // 回上一頁
    function getbackurl($tb,$preid=0)
    {
      $this->db->from($tb)->where('pid',$preid);
      $array = $this->admin_crud->get();
      $id = (count($array)>0)?$array[0]['preid']:'';
      return $id;
    }
}

// application/models/
