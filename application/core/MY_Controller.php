<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
  protected $data = array();

  function __construct()
  {
    parent::__construct();
  }
}

class Admin_Controller extends MY_Controller
{
  // 圖片
  public $filemsg = '未選擇任何檔案';
  // 預設首頁
  public $defaultURL = 'admin/dashboard';
  // 共用介面
  protected $adminview = 'templates/admin_master_view';
  public $admin_sidebar_view = 'templates/_parts/admin_master_sidebar_view';
  public $admin_nav_view = 'templates/_parts/admin_master_nav_view';
  public $admin_footer_view = 'templates/_parts/admin_master_footer_view';
  public $admin_right_sidebar_view = 'templates/_parts/admin_master_right_sidebar_view';
  public $otherPage = false;
  public $searchlang = '';
  // 選單設定
  public $tb_menu = 'admin_menu'; // 選單管理tb
  public $tb_lang = 'admin_lang'; // 選單語系tb
  public $filtercode = array('sys','module'); // 排除選單代碼
  // 選單層級
  public $menuMaxDepth = 3;
  public $textareaRow = 8;
  // 設定資料表
  public $tb_setting = 'autofield';
  // 列表呈現比數
  public $pageSize = 25;
  // 編輯器樣板
  public $template_tb = 'template_tb';

  function __construct()
  {
    parent::__construct();
    //
    $urlPath = '';
    if($xlang = $this->uri->segment(2)) {
      $sessionMenuID = (isset($this->session->MenuID))?$this->session->MenuID:0;
      $this->searchlang = (isset($_GET['lang']))?$_GET['lang']:''; // 語系
      $xfolder = $this->uri->segment(3);
      $xpage = $this->uri->segment(4);
      $var1 = '';
      // 是否有該語系存在
      $boolArray = $this->admin_crud->result_array($this->admin_crud->query_where($this->tb_lang,array('xcode'=>$xlang,'xpublish'=>'yes')));
      if(count($boolArray)>0) $this->searchlang = $xlang;
      else {
        $xfolder = $this->uri->segment(2);
        $xpage = $this->uri->segment(3);
        $var1 = $this->uri->segment(4);
      }
      $xlink = ($var1)?$var1:'';
      if($xlink && $xpage) $xlink = $xpage.'/'.$xlink;
      else if(!$xlink && $xpage) $xlink = $xpage;
      if($xlink && $xfolder) $xlink = $xfolder.'/'.$xlink;
      else if(!$xlink && $xfolder) $xlink = $xfolder;

      // 組合uri
      $uriStr = $this->uri->uri_string();
      $uriCount = $this->uri->total_segments();
      // 過濾關鍵字
      $fArray = array('form','import','export','index','memo');
      $link2 = '';
      $urlexplode = explode('/',$uriStr);
      for ($i=0; $i < count($urlexplode); $i++) {
        if(!in_array($urlexplode[$i],$fArray)) {
          $link2 .= $urlexplode[$i].'/';
        } else break;
      }
      $link2 = substr($link2,0,strlen($link2)-1);
      //
      $link4 = '';
      if($this->searchlang) {
        if(strpos($link2,$this->searchlang)>-1) {
          $link3 = explode($this->searchlang.'/',$link2);
          foreach ($link3 as $k=>$val) {
            if($k>0) $link4 .= $val.'/';
          }
        }
      } else if(strpos($link2,'admin')>-1) {
        $link3 = explode('admin/',$link2);
        foreach ($link3 as $k=>$val) {
          if($k>0) $link4 .= $val.'/';
        }
      }
      $link4 = substr($link4,0,strlen($link4)-1);
      if($link4 && $xlink && $link4 != $xlink) $xlink = $link4;

      $this->db->from($this->tb_menu);
      if($this->searchlang) $this->db->where('xlang',$this->searchlang);
      $this->db->like('xpage',$xlink,'none');
      $result_array = $this->admin_crud->get();
      //
      if (count($result_array)>0) {
        $this->session->set_userdata('MenuID',$result_array[0]['pid']);
        $xtype = $result_array[0]['xtype'];
        if($xtype == 'Multi') $this->session->set_userdata('preMenuID', $result_array[0]['pid']);
        else $this->session->set_userdata('preMenuID', '');
        $xpage = ($xlink)?$xlink:'';
        $xpage = ($this->searchlang)?$this->searchlang.'/'.$xpage:$xpage;
        $urlPath = 'admin/'.$xpage;
      } else {
        //依MenuID讀取資料庫對應網址
        $row = $this->admin_crud->row($this->admin_crud->query_where($this->tb_menu, array('pid'=> $sessionMenuID)));
        $xpage = (count($row))?$row->xpage:'';
        $xtype = (count($row))?$row->xtype:'';
        $xlink = ($this->searchlang)?$this->searchlang.'/'.$xpage:$xpage;
        $urlPath = ($xlink)?'admin/'.$xlink:'';
        //紀錄多層級上層
        if($xtype == 'Multi') $this->session->set_userdata('preMenuID', $sessionMenuID);
        else $this->session->set_userdata('preMenuID', '');

        //確認目前網址與 MenuID是否為相同網址
        if($urlPath && strpos(current_url(),$urlPath) > -1) $urlPath = $urlPath;
        else{
          if($this->searchlang) $urlArray = explode(base_url().'admin/'.$this->searchlang.'/',current_url());
          else $urlArray = explode(base_url().'admin/',current_url());
          $seach = (count($urlArray)>1)?$urlArray[1]:'';
          $row = $this->admin_crud->row($this->admin_crud->query_where($this->tb_menu, array('xpage'=>$seach)));
          // 找到對應的網址
          if(count($row)>0){
            $xpage = ($row->xpage)?$row->xpage:'';
            $xlink = ($this->searchlang)?$this->searchlang.'/'.$xpage:$xpage;
            $urlPath = 'admin/'.$xlink;

            $this->session->set_userdata('MenuID', $row->pid);
            // 重設分頁頁碼
            $this->common->resetPageNum();
            //紀錄多層級上層
            if($row->xtype == 'Multi') $this->session->set_userdata('preMenuID', $row->pid);
            else $this->session->set_userdata('preMenuID', '');
          } else {
            $preMenuID = ($this->session->preMenuID)?$this->session->preMenuID:'';
            $preMainTable = ($this->session->preMainTable)?$this->session->preMainTable:'';
            // 為多層級且位於詳細頁 || 多層級位於詳細頁下的其他頁面
            if($preMenuID && isset($this->maintable) || $preMenuID && $preMainTable) {
              $row = $this->admin_crud->row($this->admin_crud->query_where($this->tb_menu, array('pid'=> $preMenuID)));
              $xpage = (count($row)>0)?$row->xpage:'';
              $xlink = ($this->searchlang)?$this->searchlang.'/'.$xpage:$xpage;
              $urlPath = 'admin/'.$xlink;
              $this->mainurlPath = $urlPath; // 上層路徑
            }
          }
        }
      }
    }
    if (strpos(current_url(),$this->defaultURL)>-1) $urlPath = $this->defaultURL;
    // 內頁介面(parent)
    $this->indexPath = $urlPath; // 轉址網址 // 固定
    if($this->searchlang) $this->viewPath = str_replace('/'.$this->searchlang.'/','/'.$this->config->item('defaultlang').'/',$urlPath.'_view'); // 首頁 // 固定
    else $this->viewPath = $urlPath.'_view'; // 首頁 // 固定
    $this->formPath = $urlPath.'/form'; // 新增頁 // 固定

    $this->session->set_userdata('nowurl',$this->indexPath);

    // 麵包屑
    $this->navPath = $this->common->getnav($this->session->userdata('MenuID'));
  }

  // 登入用
  protected function logged_in()
  {
    // 存在session
    if($this->session->userdata('access_key')) {
      $bool = $this->admin_crud->checkauthorize();
      if(count($bool) > 0) {
        redirect($this->defaultURL);
      } else {
        $this->session->unset_userdata('access_key');
        $this->session->unset_userdata('MenuID');
      }
    } else {
      $this->session->unset_userdata('access_key');
      $this->session->unset_userdata('MenuID');
    }
  }

  // 內頁用
  protected function is_logged_in()
  {
    // 存在session
    if($this->session->userdata('access_key')) {
      $boolArray = $this->admin_crud->checkauthorize(true);
      if(count($boolArray) > 0) {
        // 使用者帳戶
        $this->data['selfID'] = $boolArray['ID'];
        $this->data['selfaccount'] = $boolArray['account'];
        $this->data['selfpassword'] = $boolArray['password'];
        $this->data['selfgroup'] = $boolArray['group'];

        $admin_logo = $this->admin_crud->read('xfile1','admin_logo');
        if(count($admin_logo)>0) $xpic = $admin_logo[0]['xfile1'];
        else $xpic = '';

        $this->data['userData'] = array(
          'xnickname' => ($boolArray['nickname']) ? $boolArray['nickname'] : '未命名',
          'xpic' => ($xpic) ? $this->common->addroot($xpic) : 'assets/admin/img/admin-logo.png',
          'xjobtitle' => ($boolArray['jobtitle']) ? $boolArray['jobtitle'] : '',
        );

        // 本身權限等級
        $this->data['selflevel'] = '';

        // 取得本身權限等級
        if($this->data['selfgroup']) {
          $query2 = $this->admin_crud->query_where('admin_group', array('pid'=> $this->data['selfgroup']));
          if($query2->num_rows() > 0) $this->data['selflevel'] = $query2->row()->xlevel;
        }

        // 有權限等級
        if($this->data['selflevel'] != '') {

          // 選單
          $this->data['LeftMenu'] = ''; $this->data['sysMenu'] = ''; $this->data['moduleMenu'] = '';
          $menu = array(); $menu2 = array(); $menu3 = array();

          $lang = ($this->searchlang)?$this->searchlang:$this->config->item('defaultlang');
          $menuData = $this->common->getMenu($this->tb_menu,$lang,$this->data['selfgroup']);
          $sysMenu = $this->common->getMenu($this->tb_menu,'sys',$this->data['selfgroup']);
          $moduleMenu = $this->common->getMenu($this->tb_menu,'module',$this->data['selfgroup']);

          if(count($menuData) > 0) {
            foreach ($menuData as $value) {
              $menu[$value['preid']][] = $value;
            }
            $this->data['LeftMenu'] = $this->makeLeftMenu($menu, 0,0);
          }
          if (count($sysMenu) > 0) {
            foreach ($sysMenu as $value) {
              $menu2[$value['preid']][] = $value;
            }
            $this->data['sysMenu'] = $menu2;
          }
          if (count($moduleMenu) > 0) {
            foreach ($moduleMenu as $value) {
              $menu3[$value['preid']][] = $value;
            }
            $this->data['moduleMenu'] = $menu3;
          }
          // 語系名稱(依照語系判斷)
          $this->db->from($this->tb_lang)
          ->where('xcode',$this->searchlang)
          ->where_not_in('xcode',$this->filtercode)
          ->order_by('xsort');
          $xshowlang = $this->admin_crud->get();
          $this->showLangName = (count($xshowlang)>0)?$xshowlang[0]['xtitle']:'';
          // 首頁沒有參數時候，會抓預設值
          if(!$this->showLangName && $this->indexPath==$this->defaultURL) {
            $this->db->from($this->tb_lang)
            ->where('xcode',$this->config->item('defaultlang'))
            ->where_not_in('xcode',$this->filtercode)
            ->order_by('xsort');
            $xshowlang = $this->admin_crud->get();
            $this->showLangName = (count($xshowlang)>0)?$xshowlang[0]['xtitle']:'';
          }
          // 語系
          $this->db->from($this->tb_lang)
            ->where_not_in('xcode',$this->filtercode)
            ->where('xpublish','yes')
            ->order_by('xsort');
          $langMenu = $this->admin_crud->get();
          $this->data['langMenu'] = array();
          foreach ($langMenu as $key => $value) {
            // 是否有選單項目
            $array = $this->admin_crud->result_array($this->admin_crud->query_where($this->tb_menu,array('xlang'=>$value['xcode'])));
            if(count($array)>0) $this->data['langMenu'][] = $value;
          }

          // 權限控管(取得頁面方式)
          $adminpage = $this->indexPath;
          if(isset($_GET['lang'])) $split = explode('admin/',$adminpage);
          else if($this->searchlang) $split = explode('admin/'.$this->searchlang.'/',$adminpage);
          else $split = explode('admin/',$adminpage);
          $page = $split[1];
          $pageArray = explode('/',$page);

          $urlSize = 2; // admin/index/action/var1
          $actionPos = $urlSize+1;
          $var1Pos = $urlSize+2;

          if(count($pageArray) > $urlSize) { // admin/folder/index/action/var1
            $actionPos++; $var1Pos++;
          }

          $action = $this->uri->segment($actionPos);
          $var1 = $this->uri->segment($var1Pos);

          $this->data['permission'] = $this->common->getPermission($this->data['selfgroup'], $page, $action, $var1, $this->defaultURL);

        // 沒有群組，找不到 level
        } else {
          $this->session->unset_userdata('access_key');
          redirect('admin');
        }
      // 不合法key
      } else {
        $this->session->unset_userdata('access_key');
        redirect('admin');
      }
    // 不存在 session
    } else redirect('admin');
  }

  // 產生左側選單
  protected function makeLeftMenu($menu, $perid, $dep, $depth = 0) {

    $html = '';
    $html .= "\n";
    $html .= str_repeat("\t", $dep);
    // 避免沒有menu[0]而造成錯誤
    if (isset($menu[$perid])) {}
    else if(!isset($menu[$perid]) && count($menu)>0) {
      foreach ($menu as $key => $value) {
        $perid = $key;
        break;
      }
    }
    else return;
    // 判斷層級
    if($perid != 0) $depth++;
    else $depth = 0;

    if($depth < $this->menuMaxDepth) { // maxDepth 僅顯示 2 層級

      switch ($depth) {
        case '0':
          $html .= "";
          break;
        case '1':
          $html .= "<ul class='nav nav-second-level collapse'>\n";
          break;
        case '2':
          $html .= "<ul class='nav nav-third-level collapse'>\n";
          break;
        case '3':
          $html .= "<ul class='nav nav-fourth-level collapse'>\n";
          break;
        default:
          $html .= "<ul class='nav nav-default-level collapse'>\n";
          break;
      }

        foreach ($menu[$perid] as $item) {

          $id = $item['pid'];
          $title = $item['xname'];
          $xpage = $item['xpage'];
          $xlang = $item['xlang'];
          $root = base_url().'admin/';
          if($xpage && $xlang) $xlink = $root.$xlang.'/'.$xpage;
          else if($xpage && !$xlang) $xlink = $root.'/'.$xpage;
          else $xlink = '';

          if($item['ReadAction'] == 0) $show = ''; else $show = 1;

          // 第一層，有子類別
          if($depth == 0 && isset($menu[$item['pid']]) && $show) $html .= "<li><a href='javascript:handler()'><span class='icon-folder'></span><span class='nav-label'>$title</span></a>";
          else {
            // 第一層，沒子類別
            if($depth == 0 && $show) {
              // 多層級
              if($item['xtype'] == 'Multi') {
                $link = ($xlink) ? $xlink : 'javascript:handler()';
                // 無論有沒有子類別，連結右側都要加「>」
                $html .= "<li><a href='$link' onclick='javascript:redirect(\"$link\",\"$id\")'><span class='icon-single'></span><span class='nav-label'>$title</span></a>";
                // 若目前正位於該多層級連結下，才呈現子類別
                if($id == $this->session->userdata('MenuID')) {
                  // (app)子類別左側選單
                  if($this->session->userdata('preMenuID') && isset($this->maintable)) $list = $this->admin_crud->read('*', $this->maintable, 'xsort', 'asc');
                  // (app)其他頁面左側選單
                  else if($this->session->userdata('preMainTable') && $this->otherPage) $list = $this->admin_crud->read('*', $this->session->userdata('preMainTable'), 'xsort', 'asc');
                  // (caty)子類別左側選單
                  else $list = $this->admin_crud->read('*', $this->table, 'xsort', 'asc');

                  if(count($list) > 0) {
                    // 有子類別，才加<ul>
                    if($depth==1) $navclass = 'nav-third-level';
                    else if($depth==2) $navclass = 'nav-fourth-level';
                    else if($depth==0) $navclass = 'nav-second-level';
                    else $navclass = '';
                    $html .= "<ul class='nav $navclass elise-m collapse'>";
                    foreach ($list as $subvalue) {
                      $subvalue['ReadAction'] = $item['ReadAction'];
                      $subvalue['MenuID'] = $id;
                      $submenu[$subvalue['preid']][] = $subvalue;
                    }
                    $html .= $this->common->SubLeftMenuInfinite($submenu, 0,0,$depth);
                    $html .= "</ul>";
                  }
                }
              // 單層級
              } else {
                $link = ($xlink) ? $xlink : 'javascript:handler()';
                $html .= "<li><a href='$link' ><span class='icon-single'></span><span class='nav-label'>$title</span></a>";
              }
            }
            // 非第一層，有link/沒有link
            else if($show) {
              // 多層級
              if($item['xtype'] == 'Multi') {
                $link = ($xlink) ? $xlink : 'javascript:handler()';
                // 無論有沒有子類別，連結右側都要加「>」
                $html .= "<li><a href='$link' onclick='javascript:redirect(\"$link\",\"$id\")'>$title<span class='fa arrow'></span></a>";
                // 若目前正位於該多層級連結下，才呈現子類別
                if($id == $this->session->userdata('MenuID')) {
                  // (app)子類別左側選單
                  if($this->session->userdata('preMenuID') && isset($this->maintable)) $list = $this->admin_crud->read('*', $this->maintable, 'xsort', 'asc');
                  // (app)其他頁面左側選單
                  else if($this->session->userdata('preMainTable') && $this->otherPage) $list = $this->admin_crud->read('*', $this->session->userdata('preMainTable'), 'xsort', 'asc');
                  // (caty)子類別左側選單
                  else $list = $this->admin_crud->read('*', $this->table, 'xsort', 'asc');

                  if(count($list) > 0) {
                    // 有子類別，才加<ul>
                    if($depth==1) $navclass = 'nav-third-level';
                    else if($depth==2) $navclass = 'nav-fourth-level';
                    else $navclass = '';
                    $html .= "<ul class='nav $navclass elise-m collapse'>";
                    foreach ($list as $subvalue) {
                      $subvalue['ReadAction'] = $item['ReadAction'];
                      $subvalue['MenuID'] = $id;
                      $submenu[$subvalue['preid']][] = $subvalue;
                    }
                    $html .= $this->common->SubLeftMenuInfinite($submenu, 0,0,$depth);
                    $html .= "</ul>";
                  }
                }
              // 單層級
              } else {
                if(!$xlink && isset($menu[$item['pid']])) {
                  $link = ($xlink) ? $xlink : '';
                  $html .= "<li><a href='javascript:handler()' >$title<span class='fa arrow'></span></a>";
                } else {
                  $link = ($xlink) ? $xlink : '';
                $html .= ($link) ? "<li><a href='".$link."' >$title</a>" : "<li>";
                }
              }
            }
          }

          // 有子類別
          if (isset($menu[$item['pid']])) {
            $html .= $this->makeLeftMenu($menu, $item['pid'], $dep, $depth);
            $dep++;
          }

          $html .= "\n\t</li>\n";
        }
        if($depth != 0) $html .= "</ul>\n";
      }

    return $html;
  }

}

class Public_Controller extends MY_Controller
{
  protected $layoutview = 'templates/master_view';

  function __construct()
  {
    parent::__construct();

    $this->load->model('front');
    // 語系
    switch ($this->uri->segment(1)) {
      case 'tw':
        $this->lang = 'zh-tw';
        $this->prefix = 'tw';
        $this->session->set_userdata('prefix',$this->prefix);
        break;
      default:
        $this->lang = 'zh-tw';
        $this->prefix = 'tw';
        break;
    }

    $this->canonical = current_url();
    $this->pagetitle = ''; $this->typetitle = '';
    $this->ogtitle = ''; $this->ogdesc = '';
    $this->showog = false;  // 顯示/隱藏 og
    // 預設og圖片
    $gaArr = $ogDefault = $this->admin_crud->result_array($this->admin_crud->query_where('otherset_gaog', array('xpublish'=> 'yes','fklang'=>$this->prefix), true,'xsort'));
    $filepath = (count($ogDefault)>0)?$ogDefault[0]['xfile3']:'';
    $GetSize = $this->front->getsize($filepath);
    $this->img = $GetSize['filepath'];
    $this->imgWidth = $GetSize['fileW'];
    $this->imgHeight = $GetSize['fileH'];
    $this->imgType = $GetSize['fileT'];
    // 預設ga
    $this->gaCode = (count($gaArr)>0)?$gaArr[0]['xgacode']:'';

    // 讀取共用SEO
    $seoArray = $this->admin_crud->result_array($this->admin_crud->query_where('seo_tb', array('xpublish'=> 'yes'), true,'xsort'));
    $seoArrayCount = count($seoArray);
    $this->seotitle = ($seoArrayCount>0)?$seoArray[0]['xseotitle']:'';
    $this->seokeyword = ($seoArrayCount>0)?$seoArray[0]['xseokeyword']:'';
    $this->seodesc = ($seoArrayCount>0)?$seoArray[0]['xseodescription']:'';
    // pagetitle
    $this->json = $this->front->getJson($this->prefix);
    $page = $this->uri->segment(2);
    $var1 = $this->uri->segment(3);
    switch ($page) {
      case 'about':
        $pagetitle = $this->front->show($this->json,'menu.about',false);
        break;
		  default:
        $pagetitle = '';
      break;
	  }
    $this->pagetitle = ($pagetitle)?$pagetitle.'-':'';
	}
}
