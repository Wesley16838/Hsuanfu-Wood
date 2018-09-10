<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->MerchantID = $this->config->item('MerchantID');
        $this->IHashKey = $this->config->item('InvoiceHashKey');
        $this->IHashIV = $this->config->item('InvoiceHashIV');
        $this->InvoiceUrl = $this->config->item('ecpayInvoice');
        $this->InvoiceSearchUrl = $this->config->item('ecpayInvoiceSearch');
        $this->LoveCodeUrl = $this->config->item('ecpayLoveCode');
        $this->InvoiceInvalidUrl = $this->config->item('ecpayInvoiceInvalid');
    }

    // 開立發票
    public function makeInvoice($listData=array())
    {
      // 不納入驗證碼參數
      $filterArr = array('ItemName','ItemWord','InvoiceRemark');
      // 需要進行 urlencode 的參數
      $urleArr = array('CustomerName','CustomerAddr','CustomerEmail');
      // 固定參數
      $listData['TimeStamp'] = time();
      $listData['MerchantID'] = $this->MerchantID;
      $listData['TaxType'] = '1'; // 課稅類別 [1:應稅 | 2:零稅率 | 3:免稅 | 9: 若為混合應稅與免稅時(限收銀機發票無法分辨時使用，且需通過申請核可)]
      $listData['InvType'] = '07'; // 字軌類別 [07:一般稅額 | 08:特種稅額計]
      $listData['vat'] = '1'; // 商品單價是否含稅 [1:含稅 | 0:未稅]
      // 變動參數
      foreach ($listData as $key => $value) {
        if(!in_array($key,$filterArr)) {
          if(in_array($key,$urleArr)) $postDataArr[$key] = urlencode($value);
          else $postDataArr[$key] = $value;
        }
        $result[$key] = $value;
      }
      // 取得驗證碼
      $postStr = $this->BuildPostString($postDataArr);
      $result['CheckMacValue'] = $this->invoiceChkMacValue($postStr);
      // 送出請求/處理回傳值
      $returnData = $this->do_post_request($this->InvoiceUrl,$result);
      $returnArr = $this->ReturnStr2Arr($returnData);
      // 進行反驗證檢查
      $return_MacValue = (isset($returnArr['CheckMacValue']))?$returnArr['CheckMacValue']:'';
      $isValid = $this->invoiceChkMacValue($returnData,$return_MacValue);
      // 通過驗證回傳，反之不回傳
      if ($isValid) return $returnArr;
      else return array();
    }

    // 查詢發票
    public function getInvoiceInfo($listData=array())
    {
      // 變動參數
      foreach ($listData as $key => $value) { $result[$key] = $value; }
      // 固定參數
      $result['TimeStamp'] = time();
      $result['MerchantID'] = $this->MerchantID;
      // 取得驗證碼
      $postStr = $this->BuildPostString($result);
      $result['CheckMacValue'] = $this->invoiceChkMacValue($postStr);
      // 送出請求/處理回傳值
      $returnData = $this->do_post_request($this->InvoiceSearchUrl,$result);
      $returnArr = $this->ReturnStr2Arr($returnData);
      // 進行反驗證檢查
      $return_MacValue = (isset($returnArr['CheckMacValue']))?$returnArr['CheckMacValue']:'';
      $isValid = $this->invoiceChkMacValue($returnData,$return_MacValue);
      // 通過驗證回傳，反之不回傳
      if ($isValid) return $returnArr;
      else return array();
    }

    // 檢查愛心碼正確性
    public function chkLoveCode($listData=array())
    {
      $bool = false;
      // 變動參數
      foreach ($listData as $key => $value) { $result[$key] = $value; }
      if (isset($listData['LoveCode']) && $listData['LoveCode']!='') {
        // 固定參數
        $result['TimeStamp'] = time();
        $result['MerchantID'] = $this->MerchantID;
        // 取得驗證碼
        $postStr = $this->BuildPostString($result);
        $result['CheckMacValue'] = $this->invoiceChkMacValue($postStr);
        // 送出請求/處理回傳值
        $returnData = $this->do_post_request($this->LoveCodeUrl,$result);
        $returnArr = $this->ReturnStr2Arr($returnData);
        // 進行反驗證檢查
        $return_MacValue = (isset($returnArr['CheckMacValue']))?$returnArr['CheckMacValue']:'';
        $isValid = $this->invoiceChkMacValue($returnData,$return_MacValue);
        if($isValid) {
          // 回傳處理
          if(count($returnArr)>0) {
            $RtnCode = (isset($ReturnArr['RtnCode']))?$ReturnArr['RtnCode']:''; // 請求狀態
            $RtnMsg = (isset($ReturnArr['RtnMsg']))?$ReturnArr['RtnMsg']:''; // 訊息
            $IsExist = (isset($ReturnArr['IsExist']))?$ReturnArr['IsExist']:''; // 愛心碼是否存在
            if ($RtnCode=='1' && $IsExist='Y') $bool = true;
          }
        }
      }
      return $bool;
    }

    // 作廢發票
    public function invalidInvoice($listData=array())
    {
      // 變動參數
      foreach ($listData as $key => $value) { $result[$key] = $value; }
      // 固定參數
      $result['TimeStamp'] = time();
      $result['MerchantID'] = $this->MerchantID;
      // 取得驗證碼
      $postStr = $this->BuildPostString($result);
      $result['CheckMacValue'] = $this->invoiceChkMacValue($postStr);
      // 送出請求/處理回傳值
      $returnData = $this->do_post_request($this->InvoiceInvalidUrl,$result);
      $returnArr = $this->ReturnStr2Arr($returnData);
      // 進行反驗證檢查
      $return_MacValue = (isset($returnArr['CheckMacValue']))?$returnArr['CheckMacValue']:'';
      $isValid = $this->invoiceChkMacValue($returnData,$return_MacValue);
      // 通過驗證回傳，反之不回傳
      if ($isValid) return $returnArr;
      else return array();
    }

    // 組合所需的 post字串
    function BuildPostString($array=array(),$isValid=false)
    {
      if ($isValid) {
        // 參數轉小寫，以便排序
        foreach ($array as $key => $value) {
          // $key = strtolower($key);
          // echo $key.'<br>';
          $array2[$key] = $value;
        }
        $array = $array2;
      }
      // print_r('<pre>'.print_r($array,true).'</pre>');
      ksort($array);
      $postStr = urldecode(http_build_query($array));
      return $postStr;
    }

    // 驗證碼檢查
    function invoiceChkMacValue($postStr='',$return_MacValue='')
    {
      // 進行反驗證檢查
      if ($postStr && $return_MacValue) {
        $searchStr = '&CheckMacValue='.$return_MacValue;
        $postStr = str_replace($searchStr,'',$postStr);
        $CheckMacValue = $this->invoiceGetMacValue($postStr); // 組合驗證碼
        if($return_MacValue == $CheckMacValue) return 'true'; // 與自己組合的驗證碼相同，驗證成功
        else return 'false'; // 反之，失敗
        // 取得驗證碼
      } else {
        $CheckMacValue = $this->invoiceGetMacValue($postStr);
        return $CheckMacValue;
      }
    }

    // 組合驗證碼
    function invoiceGetMacValue($postStr='')
    {
      $CheckMacValue = '';
      if ($postStr) {
        $level1 = $this->IHashKey.$postStr.$this->IHashIV; // step2
        $level2 = urlencode($level1); // step3
        $level3 = strtolower($level2); // step4
        $level3 = str_replace('%21', '!', $level3);  // step5
        $level3 = str_replace('%2a', '*', $level3);
        $level3 = str_replace('%28', '(', $level3);
        $level3 = str_replace('%29', ')', $level3);
        $level4 = md5($level3); // step6
        $CheckMacValue = strtoupper($level4); // step7
      }
      return $CheckMacValue;
    }

    // 使用 stream_context_create 傳送 post
    function do_post_request($url, $postdata = false, $files = false)
    {
        $destination = $url;
        $eol = "\r\n";
        $data = '';
        $mime_boundary=md5(time());
        $data .= '--' . $mime_boundary . $eol;
        //Collect Postdata
        if ($postdata) {
            foreach($postdata as $key => $val) {
                $data .= "--$mime_boundary\n";
                $data .= "Content-Disposition: form-data; name=".$key."\n\n".$val."\n";
            }
        }
        $data .= "--$mime_boundary\n";
        if ($files) {
            foreach ($files as $key => $content) {
                $data .= 'Content-Disposition: form-data; name="' .
                          $key . '"; filename="' . $key . '"' . $eol;
                $data .= 'Content-Type: application/octet-stream' . $eol;
                $data .= 'Content-Transfer-Encoding: binary' . $eol . $eol;
                $data .= $content . $eol;
            }
        }
        $data .= "--" . $mime_boundary . "--" . $eol . $eol; // finish with two eol's!!
        $params = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: multipart/form-data; boundary=' .
                    $mime_boundary . $eol,
                'content' => $data
            )
        );
        $ctx = stream_context_create($params);
        $response = @file_get_contents($destination, FILE_TEXT, $ctx);
        return $response;
    }

    // 將回傳值轉為字串
    function ReturnStr2Arr($str='')
    {
      $result = array();
      if(strpos($str,'&')>-1) {
        $array1 = explode('&',$str);
        foreach ($array1 as $key => $value) {
          if(strpos($value,'=')>-1) {
            $array2 = explode('=',$value);
            if(count($array2)>1) $result[$array2[0]] = $array2[1];
          }
        }
      }
      return $result;
    }

}

// application/models/
