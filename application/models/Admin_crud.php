<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_crud extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
    // 新增(資料表、[欄位=>欄位值])
    // 回傳 insert_id
    public function create($table, $data)
    {
      $this->db->insert($table, $data);
      return $this->db->insert_id();
    }
    // 讀取(select欄位、資料表、排序欄位、排序優先權、限制筆數開始、限制筆數結尾)
    // 回傳 result
    public function read($select = '*', $table, $orderby = 'pid', $sc = 'asc', $start = 0, $limitnum = 0)
    {
      $this->db->select($select);
      $this->db->from($table);
      $this->db->order_by($orderby, $sc);
      if((int)$start > -1 && (int)$limitnum > 0) $this->db->limit($limitnum, $start); // 筆數,從哪裡開始
      $result = $this->get();
      return $result;
    }
    // 更新(資料表、主鍵、[欄位=>欄位值])
    public function update($table, $id, $data)
    {
      $this->db->where('pid', $id)->update($table, $data);
    }
    // 更新(資料表、{[欄位 條件]=欄位值}、[欄位=>欄位值])
    public function update_where($table, $where, $data)
    {
      $this->db->where($where)->update($table, $data);
    }
    // 刪除(資料表、[欄位=>欄位值])
    public function delete($table, $array)
    {
      $this->db->delete($table, $array);
    }
    // 刪除多筆(資料表、欄位、欄位值)
    public function delete_in($table, $field, $value)
    {
      $this->db->where_in($field, $value)->delete($table);
    }
    // 條件搜尋(資料表、[欄位=>欄位值]、排序欄位、排序優先權、限制筆數開始、限制筆數結尾)
    // 回傳 query
    public function query_where($table, $array, $sort = false, $orderby = 'xsort', $sc = 'asc', $start = 0, $limitnum = 0)
    {
      if($sort) $this->db->order_by($orderby, $sc);
      if((int)$start > -1 && (int)$limitnum > 0) $this->db->limit($limitnum, $start); // 筆數,從哪裡開始
      $query = $this->db->get_where($table, $array);
      return $query;
    }
    // 最大排序(資料表、{[欄位 條件]=欄位值})
    // 回傳 sort+1
    public function query_max_sort($table, $where = NULL)
    {
      if($where) $sort = $this->db->select_max('xsort')->where($where)->get($table)->row()->xsort;
      else $sort = $this->db->select_max('xsort')->get($table)->row()->xsort;
      return $sort+1;
    }
    // 最小排序(資料表、{[欄位 條件]=欄位值})
    // 回傳 sort-1
    public function query_min_sort($table, $where = NULL)
    {
      if($where) $sort = $this->db->select_min('xsort')->where($where)->get($table)->row()->xsort;
      else $sort = $this->db->select_min('xsort')->get($table)->row()->xsort;
      return $sort-1;
    }
    // 回傳 result
    public function get()
    {
      $query = $this->db->get();
      $result = $this->result_array($query);
      return $result;
    }
    // ($query)
    // 回傳 result_array
    public function result_array($query)
    {
      if($query->num_rows() > 0) $result = $query->result_array();
      else $result = array();
      return $result;
    }
    // ($query)
    // 回傳 row
    public function row($query)
    {
      if($query->num_rows() > 0) $result = $query->row();
      else $result = array();
      return $result;
    }
    // 驗證
    public function checkUser($account,$noP=false)
    {
      $boolArray = array();

      $where['xaccount'] = $account;
      if(!$noP) $where['xpublish'] = 'yes';

      $result = $this->row($this->query_where('admin_user', $where));
      if(count($result) > 0) {
        $boolArray = array(
          'id'=>$result->pid,
          'acc'=>$result->xaccount,
          'psw'=>$result->xpassword,
          'key'=>$result->xaccesskey,
          'group'=>$result->GroupID,
        );
      }
      return $boolArray;
    }
    // 驗證
    public function checkaccpsw($psw,$dbpsw,$AccessKey)
    {
      $bool = '';
      if(md5($psw.$GLOBALS['crypt']) == $dbpsw) {
        $this->session->set_userdata('access_key', $AccessKey);
        $this->session->set_userdata('MenuID', 14);
        $bool = true;
      }
      return $bool;
    }
    // 加密
    public function cryptpsw($var1)
    {
      return md5($var1.$GLOBALS['crypt']);
    }
    // 加密
    public function cryptkey($var1, $var2)
    {
      return md5($var1.$var2.$GLOBALS['crypt2']);
    }
    // 判斷金鑰
    public function checkauthorize($getdata = false)
    {
      $boolArray = array();

      $result = $this->row($this->query_where('admin_user', array('xaccesskey'=> $this->session->userdata('access_key'))));
      if(count($result) > 0) {
        if($this->cryptkey($result->pid,$result->xaccount) == $this->session->userdata('access_key')) {
          $boolArray[] = true;
          if($getdata) {
            $boolArray = array(
              'ID'=>$result->pid,
              'account'=>$result->xaccount,
              'password'=>$result->xpassword,
              'group'=>$result->GroupID,
              'nickname'=>$result->xnickname,
              'pic'=>$result->xpic,
              'jobtitle'=>$result->xjobtitle,
            );
          }
        }
      }
      return $boolArray;
    }
    // 取得權限
    public function get_user_level($table, $account)
    {
      $query = $this->db->select('admin_group.xlevel')
              ->from($table)
              ->join('admin_group', "admin_group.pid = $table.GroupID", 'left')
              ->where('xaccount', $account);
      $array = $this->admin_crud->get();
      return $array;
    }
    // 判斷權限(可改)(低於本身)
    public function check_level($table, $pid, $selflevel)
    {
      $query = $this->query_where($table, array('pid'=> $pid));

      if($query->num_rows() > 0) $selectLevel = $query->row()->xlevel;
      else $selectLevel = '';

      // 不可刪除比自己權限高的群組
      if($selectLevel != '' && $selectLevel > $selflevel) {
        $bool = true;
      }else{
        $bool = '權限不夠';
      }
      return $bool;
    }
    // 判斷權限(可改)(等於或低於本身)
    public function check_level_equal($table, $pid, $selflevel)
    {
      $query = $this->query_where($table, array('pid'=> $pid));

      if($query->num_rows() > 0) $selectLevel = $query->row()->xlevel;
      else $selectLevel = '';

      if($selectLevel != '' && $selectLevel >= $selflevel) {
        $bool = true;
      }else{
        $bool = '權限不夠';
      }
      return $bool;
    }
    // 判斷權限(可改)(本身)(低於等於本身)
    public function check_user_level($table, $account, $selfaccount, $selflevel)
    {
      $array = $this->get_user_level($table, $account);

      if(count($array) > 0) {
        if($array[0]['xlevel'] != '') $selectLevel = $array[0]['xlevel'];
        else $selectLevel = '';

        if(
          $selectLevel != '' && $selfaccount == $account /*&& $selectLevel != 0*/ ||  // (不可改最高管理員)
          $selectLevel != '' && $selflevel <= $selectLevel /*&& $selectLevel != 0*/  // (不可改最高管理員)
        ) {
          $bool = true;
        } else {
          $bool = '權限不夠';
        }
      }
      return $bool;
    }
    // 處理拖曳排序
    public function sorting($tb='',$row=array(),$start=0,$where=array(),$field='')
    {
      $end = 0; $current = 0;
      $objcount = count($row);
      if ($tb && $objcount>0 && count($where)==0) {
        $search_startpid = $row[0]->id;
        $search_endpid = $row[$objcount-1]->id;
        // 取出第一筆xsort
        $fisrtArray = $this->admin_crud->result_array($this->admin_crud->query_where($tb, array('pid'=>$search_startpid), true, 'xsort', 'asc'));
        $fisrtxsort = $fisrtArray[0]['xsort'];
        // 取出最後一筆xsort
        $lastArray = $this->admin_crud->result_array($this->admin_crud->query_where($tb, array('pid'=>$search_endpid), true, 'xsort', 'asc'));
        $lastxsort = $lastArray[0]['xsort'];
        // 更新除了本頁列表之外的排序(排序在第一筆之前)
        $beforeArray = $this->admin_crud->result_array($this->admin_crud->query_where($tb, array('pid !='=>$search_startpid,'xsort <='=>$fisrtxsort), true, 'xsort', 'desc'));
        foreach ($beforeArray as $key => $value) {
          $current = $start-$key; // 從第一筆索引開始減
          $this->admin_crud->update($tb, $value['pid'], array('xsort' => $current));
        }
        // 更新除了本頁列表之外的排序(排序在最後一筆之後)
        $afterArray = $this->admin_crud->result_array($this->admin_crud->query_where($tb, array('pid !='=>$search_endpid,'xsort >='=>$lastxsort), true, 'xsort', 'asc'));
        foreach ($afterArray as $key => $value) {
          $current = $start+$objcount+$key+1; // 從最後一筆索引開始加
          $this->admin_crud->update($tb, $value['pid'], array('xsort' => $current));
        }
        // 更新本頁排序
        foreach ($row as $key => $value) {
          $this->admin_crud->update($tb, $value->id, array('xsort' => $start+$key+1));
        }
      } else if ($tb && $objcount>0 && count($where)>0 && $field) {
        $search_startpid = $row[0]->id;
        $search_endpid = $row[$objcount-1]->id;
        // 取出第一筆xsort
        $fisrtwhere = $where;
        $fisrtwhere[$field] = $search_startpid;
        $fisrtArray = $this->admin_crud->result_array($this->admin_crud->query_where($tb, $fisrtwhere, true, 'xsort', 'asc'));
        $fisrtxsort = $fisrtArray[0]['xsort'];
        // 取出最後一筆xsort
        $lastArray = $this->admin_crud->result_array($this->admin_crud->query_where($tb, array("$field"=>$search_endpid), true, 'xsort', 'asc'));
        $lastxsort = $lastArray[0]['xsort'];
        // 更新除了本頁列表之外的排序(排序在第一筆之前)
        $BF1WHERE = $where;
        $BF1WHERE["$field !="] = $search_startpid;
        $BF1WHERE['xsort <='] = $fisrtxsort;
        $beforeArray = $this->admin_crud->result_array($this->admin_crud->query_where($tb, $BF1WHERE, true, 'xsort', 'desc'));
        foreach ($beforeArray as $key => $value) {
          $current = $start-$key; // 從第一筆索引開始減
          $bfwhere[$field] = $value[$field];
          $this->admin_crud->update_where($tb, $bfwhere, array('xsort' => $current));
        }
        // // 更新除了本頁列表之外的排序(排序在最後一筆之後)
        $AF1WHERE = $where;
        $AF1WHERE["$field !="] = $search_startpid;
        $AF1WHERE['xsort >='] = $lastxsort;
        $afterArray = $this->admin_crud->result_array($this->admin_crud->query_where($tb, $AF1WHERE, true, 'xsort', 'asc'));
        foreach ($afterArray as $key => $value) {
          $current = $start+$objcount+$key+1; // 從最後一筆索引開始加
          $afwhere = $where;
          $afwhere[$field] = $value[$field];
          $this->admin_crud->update_where($tb, $afwhere, array('xsort' => $current));
        }
        // 更新本頁排序
        foreach ($row as $key => $value) {
          $where[$field] = $value->id;
          $this->admin_crud->update_where($tb, $where, array('xsort' => $start+$key+1));
        }
      }
      return;
    }
}

// application/models/Admin_crud
