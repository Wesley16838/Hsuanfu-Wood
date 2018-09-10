<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Style extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
    /*
      $tb 資料表、$field 欄位名稱、
      $styleT 哪種樣板 [text-A|text-B|text-C|pic-top-A|pic-top-B|pic-top-C|pic-left|pic-right]
      $postValArr post欄位值
    */
    // input欄位轉化成 html
    public function input2html($tb='',$styleT='',$postValArr=array())
    {
      // 回傳 tmp1 = 未有切割標籤的 html、tmp2 = 有切割標籤的 html
      $mixArr = array('','');
      if ($tb) {
        // 取得對應的樣板 class
        $styleArr = $this->admin_crud->result_array($this->admin_crud->query_where($tb,array('xpublish'=>'yes','xtitle'=>$styleT)));
        // 有資料
        if(count($styleArr)>0) {
          $value = $styleArr[0];
          $xwrap_fs = $value['xwrap-fs'];
          $xwrap = $value['xwrap'];
          $xrow = $value['xrow'];
          $xdivimg = $value['xdivimg'];
          $xdivtxt = $value['xdivtxt'];
          // 組合 html 及 class
          $tmp2 = $tmp = '<div class="'.$xwrap_fs.'" xbg_img>
            <div class="'.$xwrap.'">
              <div class="'.$xrow.'">';
          // 沒圖片時候給<div> | 有圖片時格外加上class及參數ximg1
          if(!$xdivimg) $tmp2 = $tmp .= '<div></div>';
          else $tmp2 = $tmp .= '<div class="'.$xdivimg.'">ximg1</div>';
          $tmp2 = $tmp .= '<div class="'.$xdivtxt.'">
                  <h2>xedit_title</h2>
                  <p>xedit_desc</p>
                </div>
              </div>
            </div>
          </div>
          ';
          // 切割標籤(多個 | 1個 | 0個)
          if (strpos($value['xvar'],',')>-1) {
            $compare = explode(',',$value['xvar']);
          } else if($value['xvar']){
            $compare = array($value['xvar']);
          } else {
            $compare = array();
          }
          // 將 post欄位值 取代掉參數，並加上切割標籤
          foreach ($compare as $key => $value) {
            $start = '('.$value.')'; $end = '(/'.$value.')';
            // 處理背景圖資料 (不為空值才進行處理)
            if ($value=='xbg_img' && $postValArr[$key]!='') {
              $val = $this->ImgAddHtml('bg',$postValArr[$key]);
              $val2 = $this->ImgAddHtml('bg',$start.$postValArr[$key].$end);
            // 處理小圖資料 (不為空值才進行處理)
            } else if ($value=='ximg1' && $postValArr[$key]!='') {
              $val = $this->ImgAddHtml('',$postValArr[$key]);
              $val2 = $this->ImgAddHtml('',$start.$postValArr[$key].$end);
            } else {
              $val = $postValArr[$key];
              $val2 = $start.$postValArr[$key].$end;
            }
            $tmp = str_replace($value,$val,$tmp);
            $tmp2 = str_replace($value,$val2,$tmp2);
          }
          $mixArr[0] = $tmp;
          $mixArr[1] = $tmp2;
        }
      }
      return $mixArr;
    }
    // html切割成 input欄位
    public function html2input($tb='',$styleT='',$content='')
    {
      // 回傳各切割標籤取出欄位值
      $result = array();
      if ($tb) {
        // 取得切割標籤
        $compare = $this->getCompare($tb,$styleT);
        // 透切割標籤取出欄位值
        foreach ($compare as $key => $value) {
          $start = '('.$value.')'; $end = '(/'.$value.')';
          if (strpos($content,$start)>-1 && strpos($content,$end)>-1) {
            $spl = explode($start,$content);
            $spl2 = explode($end,$spl[1]);
            $result[] = $spl2[0];
          }
        }
      }
      return $result;
    }
    /*
      $type 背景圖、小圖 [bg|'']
      $imgVal 圖片路徑
    */
    // 圖片加工
    public function ImgAddHtml($type='',$imgVal='')
    {
      $imgVal = $this->common->addroot($imgVal); // 給根目錄
      switch ($type) {
        case 'bg': // 背景圖加上style屬性
          return 'style="background-image: url('.$imgVal.');"';
          break;
        default: // 小圖加上img標籤
          return '<img src="'.$imgVal.'">';
          break;
      }
    }
    // 取得切割標籤
    public function getCompare($tb='',$styleT='')
    {
      $styleArr = $this->admin_crud->result_array($this->admin_crud->query_where($tb,array('xpublish'=>'yes','xtitle'=>$styleT)));
      $compare = array();
      if (count($styleArr)>0) {
        $value = $styleArr[0];
        // 切割標籤(多個 | 1個 | 0個)
        if (strpos($value['xvar'],',')>-1) {
          $compare = explode(',',$value['xvar']);
        } else if($value['xvar']){
          $compare = array($value['xvar']);
        }
      }
      return $compare;
    }
}

// application/models/
