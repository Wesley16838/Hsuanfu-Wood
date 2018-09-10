<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Common extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->lang = $this->config->item('defaultlang');
        $this->table_record = 'record_import';
    }

    function getMenu($tb,$lang='',$group=0)
    {
      $menuData = array();
      if($group == 1) {
        $this->db->from($tb)
          ->where("$tb.xlang",$lang)
          ->order_by("$tb.preid", 'asc')
          ->order_by("$tb.xsort", 'asc');
        $menuData = $this->admin_crud->get();
        for ($i=0; $i < count($menuData); $i++) {
          $menuData[$i]['RankAction'] = 1;
          $menuData[$i]['ReadAction'] = 1;
        }
      } else {
        $query = $this->db->select("$tb.*, admin_permission.RankAction, admin_permission.ReadAction, admin_permission.CreateAction, admin_permission.UpdateAction, admin_permission.DeleteAction")
          ->from($tb)
          ->join('admin_permission', "admin_permission.MenuID = $tb.pid", 'left')
          ->where("$tb.xlang",$lang)
          ->where('admin_permission.GroupID', $group)
          ->order_by("$tb.preid", 'asc')
          ->order_by("$tb.xsort", 'asc');
        $menuData = $this->admin_crud->get();
      }
      $count = -1;
      foreach ($menuData as $value) { $count++;
        $preid = $value['preid']; $menuData[$count]['prename'] = '';
        if($preid!=0) {
          $row = $this->admin_crud->row($this->admin_crud->query_where($tb,array('pid'=>$preid)));
          if(count($row)) $menuData[$count]['prename'] = $row->xname;
        }
      }
      return $menuData;
    }

    function getMenuPermission($tb_menu,$tb_p,$selfgroup,$xlang)
    {
      // 不可看到權限比自己高
      if($selfgroup == 1) {
        $query = $this->db->select("$tb_menu.*, $tb_p.RankAction")
          ->from($tb_menu)
          ->join($tb_p, "$tb_p.MenuID = $tb_menu.pid", 'left')
          ->like("$tb_menu.xlang", $xlang)
          ->group_by("$tb_menu.pid")
          ->order_by("$tb_menu.xsort", 'asc');
      } else {
        $query = $this->db->select("$tb_menu.*, $tb_p.RankAction")
          ->from($tb_menu)
          ->join($tb_p, "$tb_p.MenuID = $tb_menu.pid", 'left')
          ->where("$tb_p.GroupID", $selfgroup)
          ->like("$tb_menu.xlang", $xlang)
          ->order_by("$tb_menu.xsort", 'asc');
      }
      return $this->admin_crud->get($query);
    }

    // 產生左側子選單(多層級-最多2層，超過就沒有class)
    function SubLeftMenuInfinite($menu, $preid, $dep, $depth = 0, $maxDepth = 3) {

      $html = '';
      $html .= "\n";
      $html .= str_repeat("\t", $dep);

      // 判斷層級
      if($preid != 0) $depth++;
      // else $depth = 0;

      if($depth < $this->menuMaxDepth-1) {

        switch ($depth) {
          case '0':
            $class = "nav nav-third-level elise-s collapse";
            break;
          case '1':
            $class = "nav nav-fourth-level elise-s collapse";
            break;
          case '2':
            $class = "nav nav-fifth-level elise-s collapse";
            break;
          default:
            $class = "nav nav-default-level elise-s collapse";
            break;
        }

         foreach ($menu[$preid] as $item) {

           $id = $item['pid'];
           $title = $item['xname'];
           $link = base_url()."$this->indexPath/index/$id";
           $menuid = $item['MenuID'];

           if($item['ReadAction'] == 0) $show = ''; else $show = 1;

           if($show) {
            $havesub = (isset($menu[$item['pid']]))?true:false;
            $html .= "<li><a href='$link' onclick='javascript:redirect(\"$link\")'>$title";
            if($havesub) $html .= '<span class="fa arrow"></span></a>';
            else $html .= '</a>';
            if($havesub) $html .= "<ul class='$class'>";

            // 有子類別
            if (isset($menu[$item['pid']])) {
              $html .= $this->SubLeftMenuInfinite($menu, $item['pid'], $dep, $depth, $this->menuMaxDepth);
              $dep++;
            }
            if($havesub) $html .= "</ul>";
          }

           $html .= "\n\t</li>\n";
         }
       }
      return $html;
    }

    // 權限控管
    function getPermission($group, $page, $action = '', $var1 = NULL, $defaultURL) {
      $result = array();
      if($page=='dashboard') { // 所有人都可以首頁權限
        $this->session->set_userdata('otherPageUrl','');
      } else if($group == 1) {
        $lang = $this->searchlang;
        $this->db->from($this->tb_menu);
        $this->db->where('xpage',$page);
        if($lang) $this->db->where('xlang',$lang);
        $this->db->order_by('xsort');
        $array = $this->admin_crud->get();
        $num = count($array);
        if($num>0) $result = $array[0];
        // else redirect($defaultURL);
        $result['CreateAction'] = 1;
        $result['UpdateAction'] = 1;
        $result['DeleteAction'] = 1;
      } else {
        $query = $this->db->select("$this->tb_menu.*, admin_permission.RankAction, admin_permission.ReadAction, admin_permission.CreateAction, admin_permission.UpdateAction, admin_permission.DeleteAction")
                          ->from($this->tb_menu)
                          ->join('admin_permission', "admin_permission.MenuID = $this->tb_menu.pid", 'left')
                          ->where('admin_permission.GroupID', $group)
                          ->where("$this->tb_menu.xpage", $page)
                          ->order_by("$this->tb_menu.xsort", 'asc');

        $array = $this->admin_crud->get($query);

        // 取得該頁面權限
        if(count($array) > 0) {

          $result = $array[0];

          // 節點action判斷方式
          if(strpos($action, 'Node') > -1) {
            // 判斷是否可以 read
            if($result['ReadAction'] == 0) redirect($defaultURL);
            // 判斷是否可以 create 介面
            else if(strpos($action, 'form') > -1 && !$var1 && $result['CreateAction'] == 0) redirect($defaultURL);
            else if(strpos($action, 'create') > -1 && $result['CreateAction'] == 0) redirect($defaultURL);
            // 判斷是否可以 update 介面
            else if(strpos($action, 'form') > -1 && $var1 && $result['UpdateAction'] == 0) redirect($defaultURL);
            else if(strpos($action, 'update') > -1 && $result['UpdateAction'] == 0) redirect($defaultURL); // permission 沒有 $var1
            // 判斷是否可以 delete
            else if(strpos($action, 'delete') > -1 && $result['DeleteAction'] == 0) redirect($defaultURL);
            // 判斷是否可以 sort
            else if(strpos($action, 'sort') > -1 && $result['UpdateAction'] == 0) redirect($defaultURL);

          // 匯入
          } else if(strpos($action, 'import') > -1) {
            // 判斷是否可以 read
            if($result['ReadAction'] == 0) redirect($defaultURL);
            // 判斷是否可以 delete
            else if(strpos($action, 'delete') > -1 && $result['DeleteAction'] == 0) redirect($defaultURL);
            // 判斷是否可以 sort
            else if(strpos($action, 'sort') > -1 && $result['UpdateAction'] == 0) redirect($defaultURL);

          // 一般 CRUD
          } else {
            // 判斷是否可以 read
            if($result['ReadAction'] == 0) redirect($defaultURL);
            // 判斷是否可以 create 介面
            else if(strpos($action, 'form') > -1 && !$var1 && $result['CreateAction'] == 0) redirect($defaultURL);
            else if(strpos($action, 'save') > -1 && !$var1 && $result['CreateAction'] == 0) redirect($defaultURL);
            // 判斷是否可以 update 介面
            else if(strpos($action, 'form') > -1 && $var1 && $result['UpdateAction'] == 0) redirect($defaultURL);
            else if(strpos($action, 'save') > -1 && $var1 && $result['UpdateAction'] == 0) redirect($defaultURL);
            // 判斷是否可以 delete
            else if(strpos($action, 'delete') > -1 && $result['DeleteAction'] == 0) redirect($defaultURL);
            // 判斷是否可以 sort
            else if(strpos($action, 'sort') > -1 && $result['UpdateAction'] == 0) redirect($defaultURL);
          }

        // 沒有該頁面的權限
        } else {
          redirect($defaultURL);
        }
      }
      return $result;
    }

    // 主選單麵包屑
    function getnav($preID = 0)
    {
      $nav = '';  $subli = '';
      // 不是第一層
      if($preID != 0) {
        // 取得資訊
        $query = $this->admin_crud->query_where($this->tb_menu, array('pid'=>$preID));

        if($query->num_rows() > 0) {
          $xname = $query->row()->xname;
          $xlang = $query->row()->xlang;
          $havelang = (!in_array($xlang, $this->filtercode))?$xlang.'/':'';
          $dbpage = $query->row()->xpage;
          $xpage = base_url().'admin/'.$havelang.$dbpage;
          $dbPreID = $query->row()->preid;
          // 連結處理
          if($dbpage=='' || $dbpage=='#') $li = "<li>$xname</li>";
          else $li = "<li><a href='$xpage'>$xname</a></li>";

          // 有上層 (+目前層級)
          if($dbPreID != 0) $nav .= $this->getnav($dbPreID).$li;
          else $nav .= $li;
        }
      }
      return $nav;
    }

    // 選單麵包屑
    function getsubnav($preID = 0,$tb='',$count=0,$first=0)
    {
      $nav = '';
      // 不是第一層，多層子層級
      if($preID != 0) {
        $id = $this->session->userdata('preMenuID');
        $xtb = $this->session->userdata('preMainTable');
        if($id && $xtb) $table = $xtb; // 多層級(一律讀取系列table)
        else $table = $this->maintable; // 單層(一律讀取自己table)

        $query = $this->admin_crud->query_where($table, array('pid'=>$preID));
        if($query->num_rows() > 0) {
          $xname = $query->row()->xname;
          $dbPreID = $query->row()->preid;
          $dbPid = $query->row()->pid;
          $typeUrl = $this->session->userdata('nowurl'); // 類別層基本連結
          $url = $typeUrl.'/index/'.$dbPid; // 組合類別層正確連結
          // 位於其他頁面的話，count加一
          if($this->session->userdata('prePage') && (bool)$this->otherPage) $count++;
          // 若位於其他頁面，需額外加上最底層麵包屑
          if($count==1) {
            $li = "<li><a href='$url'>$xname</a></li>";
            // 加上上層名稱
            if($tb && $tb != $table) { // 底層
              $array = $this->admin_crud->result_array($this->admin_crud->query_where($tb,array('pid'=>$this->session->ofkpid)));
              $mainname = (count($array)>0)?$array[0]['xtitle'].' ':'';
              // $li .= "<li><a href='".$this->indexPath."'>$mainname</a></li>"; // 不顯示<a>並加上上層名稱
              $url = $this->indexPath;
            } else if($tb && $tb == $table){ // 系列層
              $array = $this->admin_crud->result_array($this->admin_crud->query_where($table,array('pid'=>$this->session->ofkpid)));
              $mainname = (count($array)>0)?$array[0]['xname'].' ':'';
              $url = $this->indexPath;
            } else $mainname = '';
            // 最底層
            $xname = $this->session->userdata('preName');
            $li .= "<li><a href='$url'>$mainname ($xname)</a></li>"; // 不顯示<a>並加上上層名稱 $xname
          // 第一個項目
          } else if($first==0) { $li = "<li><a href='$url'>$xname</a></li>"; $first++; // 不顯示<a>
          // 若不是就只要印目前層級
          } else $li = "<li><a href='$url'>$xname</a></li>";

          // 有上層 (+目前層級)
          if($dbPreID != 0) $nav .= $this->getsubnav($dbPreID,$tb,$count,$first).$li;
          else $nav .= $li;
        }
      // 位於其他頁面，需額外加上最底層麵包屑
      } else if(strpos(current_url(),$this->session->userdata('nowurl'))<=-1 && $this->session->userdata('preName')){
        $id = $this->session->userdata('preMenuID');
        $table = $this->session->userdata('preMainTable');
        // 多層級(上層名稱)
        if($id && $table) {
          if($tb) {
            $array = $this->admin_crud->result_array($this->admin_crud->query_where($tb,array('pid'=>$this->session->ofkpid)));
            $mainname = (count($array)>0)?$array[0]['xname'].' ':'';
          } else $mainname = '';
        // 單層(上層名稱)
        } else {
          if($tb) {
            $array = $this->admin_crud->result_array($this->admin_crud->query_where($tb,array('pid'=>$this->session->ofkpid)));
            $mainname = (count($array)>0)?$array[0]['xtitle'].' ':'';
          } else $mainname = '';
        }
        $xname = $this->session->userdata('preName');
        // $nav = ""; // 不顯示<a>並加上上層名稱 <li>$mainname$xname</li>
        $nav = "<li><a href='".$this->indexPath."'>$mainname ($xname)</a></li>"; // 不顯示<a>並加上上層名稱
      }
      return $nav;
    }

    // 檢查圖片是否有限制長寬並自動建立圖片長寬限制
    function checkImageLimit($tableName, $fieldName, $custom = '')
    {
      $array = array();
      $result = false; $tablename2 = '';

      $split = explode('_', $tableName);
      for ($i=1; $i < count($split); $i++) {
        $tablename2 .= '_'.$split[$i];
      }
      if(!$custom) {
        // 預設語系+_資料表
        $tableName = $this->lang.$tablename2;

        // 判斷資料庫欄位是否存在
        $query = $this->db->query("Describe $tableName");
        foreach ($query->result_array() as $value) {
          if($value['Field'] == $fieldName) $result = true;
        }
      } else $result = true;

      if($result) {
        $this->db->select('tips_relative_tb.xwidth, tips_relative_tb.xheight');
        $this->db->from('tips_tb');
        $this->db->join('tips_relative_tb', 'tips_relative_tb.fpid = tips_tb.pid');
        $this->db->where('tips_tb.xtablename', $tableName);
        $this->db->where('tips_tb.xfieldname', $fieldName);
        $query = $this->db->get();

        if($query->num_rows() == 0) {
          $id = $this->admin_crud->create('tips_tb', array(
            'xpublish' => 'yes',
            'xtitle' => 'default',
            'xtablename' => $tableName,
            'xfieldname' => $fieldName,
            'xfiletype' => 2,
            'xfilesize' => 2100,
            'xcreate' => date('Y-m-d H:i:s'),
            'xsort' => 999999999999,
          ));

          $this->admin_crud->create('tips_relative_tb', array(
            'fpid' => $id,
            'xsize' => 'original',
            'xcreate' => date('Y-m-d H:i:s'),
          ));
        }
      } else {
        $array = $this->admin_crud->result_array($this->admin_crud->query_where('tips_tb',array('xtablename'=>$tableName,'xfieldname'=>$fieldName)));
        if (count($array)>0) {
          foreach ($array as $value) {
            $this->admin_crud->delete('tips_relative_tb',array('fpid' => $value['pid']));
            $this->admin_crud->delete('tips_tb',array('pid' => $value['pid']));
          }
        }
      }
      return true;
    }
    // 單位轉換
    function formatSizeUnitsKb($kb)
    {
      if ($kb >= 1024*1024*1024) $kb = number_format($kb / 1024*1024*1024, 0).' TB';
      elseif ($kb >= 1024*1024)  $kb = number_format($kb / 1024*1024, 0).' GB';
      elseif ($kb >= 1024) $kb = number_format($kb / 1024, 0).' MB';
      elseif ($kb > 1) $kb = $kb.' KB';
      elseif ($kb == 1) $kb = $kb.' KB';
      else $kb = '0 KB';
      return $kb;
    }
    // 取得格式資訊
    function getImageinfo($tableName, $fieldName, $custom = '')
    {
      $msg = ''; $tablename2 = '';

      $split = explode('_', $tableName);
      for ($i=1; $i < count($split); $i++) {
        $tablename2 .= '_'.$split[$i];
      }
      if(!$custom) $tableName = $this->lang.$tablename2;

      $this->db->select('tips_tb.*, tips_relative_tb.xsize, tips_relative_tb.xwidth, tips_relative_tb.xheight');
      $this->db->from('tips_tb');
      $this->db->join('tips_relative_tb', 'tips_relative_tb.fpid = tips_tb.pid');
      $this->db->where('tips_tb.xtablename', $tableName);
      $this->db->where('tips_tb.xfieldname', $fieldName);
      $this->db->where('tips_relative_tb.xsize', 'original');
      $query = $this->db->get();

      if($query->num_rows() > 0) {
        $result = $query->result_array();
        $type = $this->getImagetype($result[0]['xfiletype']);

        foreach ($result as $value) {
          $xfilesize = $this->formatSizeUnitsKb($value['xfilesize']);
$msg .= "序號: ".$value['pid']."
檔案類型: $type[0]
檔案最大限制: ".$xfilesize."
接受檔案格式: $type[1]";
if($type[0]=='圖片') {
$xwidth = (is_numeric($value['xwidth']))?$value['xwidth']:'未設定';
$xheight = (is_numeric($value['xheight']))?$value['xheight']:'未設定';
$msg .="
寬度: ".$xwidth."
高度: ".$xheight;
}

        }
      } else {
        $type = $this->getImagetype('');
$msg .= "檔案類型: $type[0]
檔案最大限制: 未設定
接受檔案格式: $type[1]";
      }
      return $msg;
    }

    function getImagetype($type)
    {
      $result = array('',''); // '.svg .7z .aiff .asf .avi .bmp .csv .doc .docx .fla .flv .gif .gz .gzip .jpeg .jpg .mid .mov .mp3 .mp4 .mpc .mpeg .mpg .ods .odt .pdf .png .ppt .pptx .pxd .qt .ram .rar .rm .rmi .rmvb .rtf .sdc .sitd .swf .sxc .sxw .tar .tgz .tif .tiff .txt .vsd .wav .wma .wmv .xls .xlsx .zip'
      switch ($type) {
        case '1': // 全部
          $result[0] = '全部';
          $result[1] = '.svg .7z .csv .doc .docx .gif .jpeg .jpg .pdf .png .ppt .pptx .txt .xls .xlsx .zip';
          break;
        case '2': // 圖片
          $result[0] = '圖片';
          $result[1] = '.svg .gif .jpeg .jpg .png';
          break;
        case '3': // 文件
          $result[0] = '文件';
          $result[1] = '.csv .doc .docx .pdf .xls .xlsx';
          break;
        default:
          $result[0] = '未設定';
          $result[1] = '未設定';
          break;
      }
      return $result;
    }

    // 新增根目錄
    function addroot($filepath)
    {
      $rootpath = $this->config->item('upload_folder');
      if(strpos($filepath,$rootpath)<=-1) {
        $filepath = $rootpath.$filepath;
      }
      return $filepath;
    }

    // 移除根目錄
    function removeroot($filepath)
    {
      $rootpath = $this->config->item('upload_folder');
      if(strpos($filepath,$rootpath)>-1) {
        $array = explode($rootpath,$filepath);
        if(count($array)>0) $filepath = $array[1];
      }
      return $filepath;
    }

    // 取得圖片路徑
    function getImagethumb($tableName, $fieldName, $xsize, $filepath, $custom = '', $ratio = false, $useType='resize')
    {
      if($filepath) {

        $folder = $this->config->item('upload_folder');
    		if(strpos($filepath,$folder)<=-1) {
    		  $array = explode('/',$filepath);
    		  $filepath2 = '';
    		  for ($i=0; $i < count($array); $i++) {
      			if($i==0) {
      				$filepath2 .= $folder;
              if($array[$i]!='.' || $array[$i]!='') {
                $filepath2 .= $array[$i];
                if($i!=count($array)-1) $filepath2 .= '/';
              }
      				else $filepath2 = $array[$i].$filepath2;
      			}
      			else {
      			  $filepath2 .= $array[$i];
      			  if($i!=count($array)-1) $filepath2 .= '/';
      			}
      		}
      		$filepath = $filepath2;
      	}

        $tablename2 = '';

        $split = explode('_', $tableName);
        for ($i=1; $i < count($split); $i++) {
          $tablename2 .= '_'.$split[$i];
        }
        if(!$custom) $tableName = $this->lang.$tablename2;

        // 取得判斷依據
        $this->db->select('tips_relative_tb.xwidth, tips_relative_tb.xheight');
        $this->db->from('tips_tb');
        $this->db->join('tips_relative_tb', 'tips_relative_tb.fpid = tips_tb.pid');
        $this->db->where('tips_tb.xtablename', $tableName);
        $this->db->where('tips_tb.xfieldname', $fieldName);
        $this->db->where('tips_relative_tb.xsize', $xsize);
        $query = $this->db->get();

        $row = $this->admin_crud->row($query);
        if(count($row) > 0) {
          $compareW = $row->xwidth;
          $compareH = $row->xheight;

          // 分解路徑
          $array = explode('/',$filepath);
          // 根目錄
          $rootPath = '.';
          // 檔名
          $filename = $array[count($array)-1];
          for ($i=0; $i < count($array)-1; $i++) {
            $rootPath .= $array[$i];
            $rootPath .= '/';
          }
          if($compareW || $compareH) {
            if($useType == 'resize') $filepath = $this->resizeImage($rootPath, $filename, $compareW, $compareH, $ratio);
            elseif($useType == 'crop') $filepath = $this->cropImage($rootPath, $filename, $compareW, $compareH, $ratio);
          }
        }
      }

      if(!$filepath) return false;
      else if($filepath && strpos($filepath,'./uploads/') > -1) return $filepath;
      else if($filepath && substr_count($filepath,".") > 2) return substr($filepath,1,strlen($filepath));
	    else return $filepath;
    }

    function resizeImage($dir, $imgSrc, $compareW, $compareH, $ratio) {
      $array = explode('.',$imgSrc);
      $filename = $array[0];
      // 副檔名大寫不壓縮 (for linux)
      if(preg_match('/^[A-Z]+$/', $array[1])) $do_nothing = true;
  	  else $do_nothing = false;
      $type = strtolower($array[1]);

      $folder = $this->config->item('upload_folder');
      if(strpos($dir,$folder)>-1) {
        $number = explode($folder,$dir);
        $dir = 'uploads/';
        for ($i=1; $i < count($number); $i++) {
          $dir.= $number[$i];
        }
      }
      $sourcename = $dir.$imgSrc;
      $sourcesize = @getimagesize($sourcename);
      $sourcewidth = $sourcesize[0];
      $sourceheight = $sourcesize[1];
      // 副檔名大寫不壓縮 (for linux)
      if($do_nothing) return $sourcename;
      // 格式若為svg不進行動作
      if(@is_file($sourcename) && $type !='svg') {
        // 新位置+新檔名 __thumbs/檔名/檔名_寬x高
        $newdir = $dir."__thumbs/$imgSrc/";
        $newname = $newdir.$filename."_".$compareW.'x'.$compareH.".$type";
        //
        if (!@is_dir($newdir)) {
  	      if (@mkdir($newdir,0777,true)) {}
  	    }
        if(!@is_file($newname)) {
          if($compareW != $sourcewidth || $compareH != $sourceheight) {
            $this->load->library('image_lib');
            $config['image_library'] = 'gd2';
            $config['source_image'] = $sourcename;
            $config['create_thumb'] = true;
            $config['maintain_ratio'] = ($compareW!=0 && $compareH!=0)?$ratio:true; // $ratio
            $config['width']     = ($compareW!=0)?$compareW:$sourcewidth; // $compareW
            $config['height']   = ($compareH!=0)?$compareH:$sourceheight; // $compareH

            if(@$this->image_lib->clear()) return $sourcename; // 縮圖錯誤，回傳原路徑 (for linux)
            if(!$this->image_lib->initialize($config)) return $sourcename; // 縮圖錯誤，回傳原路徑 (for linux)
            if(!$this->image_lib->resize()) return $sourcename; // 縮圖錯誤，回傳原路徑 (for linux)

            $thumbname = $dir.$filename.'_thumb.'.$type;
            if(@rename($thumbname,$newname))
              $thumbnail = $newname;
            else
              $thumbnail = $sourcename;
          } else $thumbnail = $sourcename;
        } else $thumbnail = $newname;
      } else $thumbnail = $sourcename;

      return $thumbnail;
    }

    function cropImage($dir, $imgSrc, $compareW, $compareH, $ratio){
        // $width = $compareW;
        // $height = $compareH;
        //
        // $this->load->library('image_lib');
        // $config['image_library'] = 'gd2';
        // $config['source_image'] = $sourcename;
        // $config['x_axis'] = (($sourcewidth-$width)/2 > 0) ? ($sourcewidth-$width)/2 : 0;
        // $config['y_axis'] = (($sourceheight-$height)/2 > 0) ? ($sourceheight-$height)/2 : 0;
        // $config['maintain_ratio'] = ($compareW!=0 && $compareH!=0)?$ratio:true; // $ratio;
        // $config['width']     = ($compareW!=0)?$compareW:$sourcewidth; // $width;
        // $config['height']   = ($compareH!=0)?$compareH:$sourceheight; // $height;
        //
        // $config['new_image'] = $newname;
        // $this->image_lib->initialize($config);
        //
        // $src = $config['new_image'];
        // $crop_image = substr($src, 2);
        // $crop_image = base_url() . $crop_image;
        // $this->image_lib->crop();
    }

    // 取得尺寸
    function getsize($filepath)
    {
      $array = array(0,0);

      if(strpos($filepath, base_url()) <= -1) $filepath = site_url($filepath);

      $explode = explode('.',$filepath);
      $type = $explode[1];
      if($type == 'svg') {
        $xml = @simplexml_load_file($filepath);
        $attr = $xml->attributes();
        $array[0] = (isset($attr->width))?$attr->width:0;
        $array[1] = (isset($attr->height))?$attr->height:0;
      } else {
        $size = @getimagesize($filepath);
        $array[0] = (isset($size[0]))?$size[0]:0;
        $array[1] = (isset($size[1]))?$size[1]:0;
      }
      return $array;
    }
    // 解決csv匯入資料中文錯誤、出現逗號問題
    function csvToArray($file, $delimiter) {
      if (($handle = fopen($file, 'r')) !== FALSE) {
        $i = 0;
        while (($lineArray = fgetcsv($handle, 4000, $delimiter, '"')) !== FALSE) {
          for ($j = 0; $j < count($lineArray); $j++) {
            setlocale(LC_ALL, 'zh_TW.BIG5');
            $arr[$i][$j] =  iconv("big5","utf-8//ignore",addslashes("$lineArray[$j]"));
          }
          $i++;
        }
        fclose($handle);
      }
      return $arr;
    }
    // 匯入csv
    function ImportCsv($file,$table,$array,$compareIndex=0)
    {
      $id = 0; $IDcount = 0;
      if($file && $table && count($array)) {
        if(!file_exists($file)) return false;
        $count = 0;
        $xcreate = date('Y-m-d H:i:s');
        // 取得欄位名稱
        $field = $compareIndex;
        // 進行匯入
        $csvlist = $this->csvToArray($file,',');
        foreach ($csvlist as $ROW) {
          $xsort = $this->admin_crud->query_max_sort($table);
          $count++;
          if($count>1){ // 從第二列開始讀
            // 判斷是否有該資料存在
            $xval = $ROW[$array[$compareIndex]];
            $this->db->from($table)->like($field,$xval,'none');
            $result = $this->admin_crud->get();
            $nums = count($result);
            if($nums==0){
              if($xval!='') {
                foreach ($array as $key => $value) {
                  if(is_numeric($value)) $data[$key] = $ROW[$value];
                  else if($value) $data[$key] = $value;
                  else $data[$key] = '';
                }
                $data['xcreate'] = $xcreate;
                $data['xmodify'] = null;
                $data['xsort'] = $xsort;
                $id = $this->admin_crud->create($table,$data);
                if($id) $IDcount++;
              }
            } else {
             $data2 = array('xfile1'=> $file, 'xtitle'=> $xval, 'xerror'=>'已有該資料', 'xcreate'=> date('Y-m-d'));
             $this->admin_crud->create($this->table_record,$data2);
            }
          }
        }
      }
      return $IDcount;
    }
    // 格外處理字元
    public function exception($value)
    {
      $value = str_replace('_x000D_', '',$value);
      $value = str_replace('﹪', '%',$value);
      return $value;
    }
    // 匯入excel
    function ImportExcel($file,$table,$array,$compareIndex='A')
    {
      $id = 0; $IDcount = 0;
      if($file && $table && count($array)) {
        if(!file_exists($file)) return false;
        $count = 0;
        $xcreate = date('Y-m-d H:i:s');

        $this->load->library('Excel');
        // 讀取檔案
        try {
          $objPHPExcel = PHPExcel_IOFactory::load($file);
        } catch(Exception $e)
        {
          $this->resp->success = FALSE;
          $this->resp->msg = 'Error Uploading file';
          return false;
          exit;
        }
        $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        // 取得欄位名稱
        $field = $compareIndex;
        $count = 0;
        foreach($allDataInSheet as $import)
        {
          $xsort = $this->admin_crud->query_max_sort($table);
          $count++;
          if($count>1){ // 從第二列開始讀
            // 判斷是否有該資料存在
            $xval = $import[$array[$compareIndex]];
            $this->db->from($table)->like($field,$xval,'none');
            $result = $this->admin_crud->get();
            $nums = count($result);
            if($nums==0){
              if($xval!='') {
                foreach ($array as $key => $value) {
                  if(preg_match('/[A-Z]|[A-Z]/',$value)) {
                    $rowval = $import[$value];
                    if($date = $this->isDate($rowval)) {
                      $data[$key] = $date;
                    } else $data[$key] = $rowval;
                  }
                  else if($value) $data[$key] = $value;
                  else $data[$key] = '';
                  $data[$key] = $this->exception($data[$key]);
                }
                $data['xcreate'] = $xcreate;
                $data['xmodify'] = null;
                $data['xsort'] = $xsort;
                $id = $this->admin_crud->create($table,$data);
                if($id) $IDcount++;
              }
            } else {
             $data2 = array('xfile1'=> $file, 'xtitle'=> $xval, 'xerror'=>'已有該資料', 'xcreate'=> date('Y-m-d'));
             $this->admin_crud->create($this->table_record,$data2);
            }
          }
        }
      }
      return $IDcount;
    }

    // 處理排序
    function processSort($table, $post_xsort, $post_insertxsortpid)
    {
      $xsort = $post_xsort;
      if($post_xsort == 'first') $xsort = $this->admin_crud->query_min_sort($table);
      if($post_xsort == 'last') $xsort = $this->admin_crud->query_max_sort($table);
      if($post_xsort == 'insert')  {
        $where['pid'] = $post_insertxsortpid;
        $xsort = $this->admin_crud->query_max_sort($table, $where);

        $this->db->set('xsort', 'xsort+1', FALSE)
        ->where('xsort >=', $xsort)
        ->update($table);
      }
      return $xsort;
    }

    // 處理日期
    function processDate($pdate,$ddate)
    {
      $msg = '';
      $validdate = $this->validregex('date', $pdate);
      if($validdate != true) $msg = $validdate;
      $validdate = $this->validregex('date', $ddate);
      if($validdate != true) $msg = $validdate;
      if($pdate && $ddate && $pdate >= $ddate) $msg =  '下刊日期須大於發佈日期';
      return $msg;
    }

    // 處理圖片
    function processImg($tb,$id,$field,$file)
    {
      $path = $this->removeroot($file);
      if($id) {
        $row = $this->admin_crud->row($this->admin_crud->query_where($tb, array('pid'=>$id)));
        if($path == '') $path = $row->$field; // 沒輸入，讀取原路徑
      }
      return $path;
    }

    // 處理url
    function processUrl($tb,$id,$filed,$url)
    {
      $msg = '';
      if($url) {
        if(!$this->checkurlvalid($url)) {
          $msg = 'URL Rewrite不可為特殊符號或僅有純數字';
        } else {
          $bool = $this->checkurlrepeat($tb, array($filed=> $url), $id);
          if($bool > 0) $msg = 'URL Rewrite不可重覆';
        }
      }
      return $msg;
    }

    // 檢查url是否合法
    function checkurlvalid($value)
    {
      $regex = '/^[0-9]*$/';
      $regex2 = '/^[0-9a-zA-Z_\x{4e00}-\x{9fff}-]+$/u';
      if (preg_match($regex, $value)) return false;
      else if (!preg_match($regex2, $value)) return false;
      return true;
    }

    // 檢查url名稱是否重複
    function checkurlrepeat($table, $data, $id='')
    {
      if($id) $data['pid !='] = $id;
      $array = $this->admin_crud->result_array($this->admin_crud->query_where($table, $data));
      return count($array);
    }
    // 次語系
    function generatesecondlang($oldprefixname, $newprefixname, $oldlangname, $newlangname)
    {
      // 確認語系是否存在
      $this->db->from($this->tb_menu)
              ->where('xlang', $newlangname)
              ->order_by('xsort');
      $checkempty = $this->admin_crud->get();

      if(count($checkempty) == 0) {
        // 第一次搜尋
        $this->db->from($this->tb_menu)
                ->where('xlang', $oldlangname)
                ->order_by('xsort');
                // ->group_by('preid');
        $menuData = $this->admin_crud->get();
        if(count($menuData) > 0) {
          // 進行次語系新增
          foreach ($menuData as $value) {
            $menu[$value['preid']][] = $value;
          }
          $a = $this->processlangmenu($menu, 0, $newlangname);
          $b = $this->processlangtable($oldprefixname, $newprefixname);
          if($a>0 && $b===true) return true;
          else {
            $this->db->delete($this->tb_menu, array('xlang' => $newlangname)); // 清空已建立資料
            return ': '.$b;
          }
        } else return ': 找不到複製項目';
      } else return ': 請清空選單項目';
    }
    // 次語系(選單)
    function processlangmenu($menu, $preid, $newlangname, $insertid=0)
    {
      $count = 0;
      $newpreid = $insertid;

      foreach ($menu[$preid] as $item) {
        $data = array(
          'xicon' => $item['xicon'],
          'xname' => $item['xname'],
          'xlang' => $newlangname,
          'xpage' => $item['xpage'],
          'xtype' => $item['xtype'],
          'preid' => $newpreid,
          'xcreate' => date('Y-m-d H:i:s'),
          'xsort' => $item['xsort'],
        );
        $insertid = $this->admin_crud->create($this->tb_menu, $data);

        $count ++;

        if (isset($menu[$item['pid']])) {
          $count += $this->processlangmenu($menu, $item['pid'], $newlangname, $insertid);
        }
      }
      return $count;
    }
    // 次語系(資料表)
    function processlangtable($oldprefixname, $newprefixname)
    {
      $tablename = $this->db->database;
      $sql = 'show tables from '.$tablename.' LIKE \''.$oldprefixname.'%\'';
      $query = $this->db->query($sql);
      $array = $this->admin_crud->result_array($query);
      if(count($array)>0) {
        foreach ($array as $value) {
          $oldtable = $value['Tables_in_'.$tablename.' ('.$oldprefixname.'%)'];
          $newtable = str_replace($oldprefixname, $newprefixname, $oldtable);
          // 確保不重複新增
          $sql = 'show tables from '.$tablename.' LIKE \''.$newtable.'%\'';
          $query = $this->db->query($sql);
          $array2 = $this->admin_crud->result_array($query);
          if(count($array2) == 0) {
            $sql = 'create table '.$newtable.' like '.$oldtable.';';
            $this->db->query($sql);
            $sql2 = 'insert '.$newtable.' select * from '.$oldtable.';';
            $this->db->query($sql2);
          }
        }
        return true;
      } else return '找不到舊資料表';
    }
    // 驗證
    function validregex($type, $value)
    {
      if($value != '') {
        switch ($type) {
          case 'date': // YYYY-mm-dd
            $date_regex = '/^(19|20)([0-9]{2})-((0|)[1-9]|1[012])-((0|)[1-9]|[12][0-9]|3[01])$/';
            if (!preg_match($date_regex, $value)) return '日期格式錯誤';
            break;
          case 'email': // xxx@xxx.xx
            $regex = "/^[A-Za-z0-9\.|\-|_]*[@]{1}[A-Za-z0-9\.|\-|_]*[.]{1}[a-z]{2,5}$/";
            if (!preg_match($regex, $value)) return '信箱格式錯誤';
            break;
          case 'psw':
            if($this->validpsw($value)!=1) return '密碼為6字元、英數混合字串';
            break;
          default:
            break;
        }
      }
      return true;
    }
    // 日期格式驗證
    function isDate($str){
      if(!preg_match_all('$\S*(?=\S*[a-z|A-Z])$', $str)) {
        $regex_YYYY_MM_DD = '/^(19|20)([0-9]{2})-((0|)[1-9]|1[012])-((0|)[1-9]|[12][0-9]|3[01])$/';
        $regex_DD_MM_YYYY = '/^((0|)[1-9]|[12][0-9]|3[01])-((0|)[1-9]|1[012])-(19|20)([0-9]{2})$/';
        $regex_YYYYMMDD = '/^(19|20)([0-9]{2})\/((0|)[1-9]|1[012])\/((0|)[1-9]|[12][0-9]|3[01])$/';
        $regex_MMDDYYYY = '/^((0|)[1-9]|1[012])\/((0|)[1-9]|[12][0-9]|3[01])\/(19|20)([0-9]{2})$/';
        if (
          preg_match($regex_YYYY_MM_DD, $str) ||
          preg_match($regex_YYYYMMDD, $str) ||
          preg_match($regex_DD_MM_YYYY, $str) ||
          preg_match($regex_MMDDYYYY, $str)
        ) {
          $time = strtotime($str);
          if($time) {
            $date = date('Y-m-d',$time);
            if($this->validregex('date', $date)==1) {
              $__y = substr($date, 0, 4);
              $__m = substr($date, 5, 2);
              $__d = substr($date, 8, 2);
              if(checkdate($__m, $__d, $__y)) {
                return $date;
              }
            }
          }
        }
      }
      return false;
    }
    // 密碼格式驗證
    function validpsw($psw) {
      if (!preg_match_all('$\S*(?=\S{6,})$', $psw))
          return '6字元';
      else if(!preg_match_all('$\S*(?=\S*[a-z|A-Z])$', $psw))
          return '字母';
      // else if(!preg_match_all('$\S*(?=\S*[a-z])$', $psw))
      //     return '小字母';
      // else if(!preg_match_all('$\S*(?=\S*[A-Z])$', $psw))
      //     return '大字母';
      else if(!preg_match_all('$\S*(?=\S*[\d])$', $psw))
          return '數值';
      // else if(preg_match_all('$\S*(?=\S*[\W])\S*$', $candidate))
      //     return '特殊';
      return TRUE;
    }
    //  SEO自動化 - 切割前綴詞
    public function splittb($tb)
    {
      $tb2 = '';
      $split = explode('_', $tb);
      for ($i=1; $i < count($split); $i++) {
        $tb2 .= '_'.$split[$i];
      }
      $tb = $this->lang.$tb2;
      return $tb;
    }
    //  SEO自動化 - auto按鈕初始化
    public function precessAutoinit($autoArray)
    {
      $result = array();
      foreach ($autoArray as $key => $value) {
        $table = $value['xtb'];
        $outputName = $value['outputName'];
        $result['IelNarray'][] = $value['inputName']; $result['OelNarray'][] = $outputName;
        $result['OelTarray'][] = $value['outputType']; $result['urlArray'][] = $value['isurl'];
        $array['xtb'] = $xtb = $this->splittb($table);
        $settingArray = $this->admin_crud->result_array($this->admin_crud->query_where($this->tb_setting,array('xtb'=>$xtb,'xfield'=>$outputName)));
        $result['xoffArray'][] = (count($settingArray)>0)?$settingArray[0]['xoff']:'no';
      }
      $array['IelNstr'] = implode(',',$result['IelNarray']);
      $array['OelNstr'] = implode(',',$result['OelNarray']);
      $array['OelTstr'] = implode(',',$result['OelTarray']);
      $array['urlStr']= implode(',',$result['urlArray']);
      $array['xoffstr'] = implode(',',$result['xoffArray']);
      return $array;
    }
    // SEO自動化 - 取代空格
    public function removeAllSpace($str) {
      if($str) return str_replace(' ',"-",$str);
      else return $str;
    }
    // SEO自動化 - 去除特殊符號
    public function clearString($str){
      $pattern = "[`~!@#$^&*()=|{}':;',\\[\\].<>/?~！@#￥……&*（）&;|{}【】‘；：”“'。，、？]";
      $regex = '/^[0-9a-zA-Z_\x{4e00}-\x{9fff}-%]+$/u';
      $result = ""; $item='';
      for ($i = 0; $i < strlen($str); $i++) {
          $item = mb_substr($str,$i,1);
          if(preg_match($regex, $item)) {
            if(!preg_match($pattern, $item)) {
              if($i!=0 && $item=='-') { // 移除重複性「-」
                if(substr($result, -1, 1)!=$item) $result = $result.$item;
              } else {
                $result = $result.$item;
              }
            }
          }
      }
      return $result;
    }
    // SEO自動化 -補上空值欄位
    public function emptyAuto($inputval='',$isurl='fasle')
    {
      if($isurl=="true") {
        $outval = trim($inputval);
        $outval = $this->removeAllSpace($outval);
        $outval = $this->clearString($outval);
      } else $outval = $inputval;
      return $outval;
    }
    // 重設頁碼
    public function resetPageNum()
    {
      $this->session->set_userdata('pageNumber', 1);
      return;
    }
    // 重建 view路徑
    public function resetPath($path='')
    {
      // 依照後台建立的語系取代成預設語系
      $this->db->from($this->tb_lang)
      ->where_not_in('xcode',$this->filtercode)
      ->order_by('xsort');
      $xshowlang = $this->admin_crud->get();
      $viewPath = $path.'_view'; // 目前路徑
      foreach ($xshowlang as $key => $value) { // 不分語系一律改為預設語系
        $viewPath = str_replace('/'.$value['xcode'].'/','/'.$this->config->item('defaultlang').'/',$viewPath);
      }
      return $viewPath;
    }
}

// application/models/
