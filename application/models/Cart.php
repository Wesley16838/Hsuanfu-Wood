<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->MerchantID = $this->config->item('MerchantID');
        $this->HashKey = $this->config->item('HashKey');
        $this->HashIV = $this->config->item('HashIV');
        $this->CartPayUrl = $this->config->item('ecpayCartPay');
        $this->ChkPayStatusUrl = $this->config->item('ecpayChkPay');
    }

    // 送出訂單-參數處理
    public function processCart($listData=array())
  	{
      // 變動參數
      foreach ($listData as $key => $value) { $result[$key] = $value; }
      // 固定參數
  		$result['EncryptType'] = '1'; // 請固定填入 1，使用 SHA256 加密
  		$result['MerchantID'] = $this->MerchantID; // 合作特店編號
  		$result['MerchantTradeDate'] = date('Y/m/d H:i:s'); // 交易日 [yyyy/MM/dd HH:mm:ss]
  		$result['PaymentType'] = 'aio'; // 交易類型，請固定填入 aio
  		$result['NeedExtraPaidInfo'] = 'Y'; // 傳回額外參數，例如信用卡後4碼
      // 取得驗證碼 CheckMacValue
      $postStr = $this->BuildPostString($result);
      $result['CheckMacValue'] = $this->cartChkMacValue($postStr);
      // 頁面用 url，不納入驗證碼流程，所以必須寫於最後
      $result['posturl'] = $this->CartPayUrl;
      return $result;
  	}

    // 假排程-付款狀態確認-參數處理
    public function chkPayStatus($MerchantTradeNo='',$tb='',$pid=0)
    {
      $msg = '';
      // 取得驗證碼
      $result = array(
        'MerchantID'=>$this->MerchantID,
        'MerchantTradeNo'=>$MerchantTradeNo,
        'TimeStamp'=>time()
      );
      $postStr = $this->BuildPostString($result);
      $result['CheckMacValue'] = $this->cartChkMacValue($postStr);
      // 送出請求/處理回傳值
      $returnData = $this->do_post_request($this->ChkPayStatusUrl,$result);
      $returnArr = $this->ReturnStr2Arr($returnData);
      // 進行反驗證檢查
      $return_MacValue = (isset($returnArr['CheckMacValue']))?$returnArr['CheckMacValue']:'';
      $isValid = $this->cartChkMacValue($returnData,$return_MacValue);
      if ($isValid) {
        // @@ 依據查詢結果更新訂單狀態
        /***
          TradeStatus 各狀態說明
          [
            10200047: 訂單未成立
            1:  訂單成立已付款
            0:  訂單成立未付款
          ]
        ***/
        $TradeStatus = (isset($returnArr['TradeStatus']))?$returnArr['TradeStatus']:'';
        // 訂單未成立
        if ($TradeStatus=='10200047') {
          //   $this->admin_crud->update($tb,$pid,array('orderstate'=>'訂單未成立'));
          // 若為 1 時，代表交易訂單成立已付款
        } else if($TradeStatus=='1'){
          // 由於付款方式不同，回傳參數亦不同，以兩個參數判斷是否成功付款
          $PaymentDate = (isset($returnArr['PaymentDate']))?$returnArr['PaymentDate']:'';
          $PaymentType = (isset($returnArr['PaymentType']))?$returnArr['PaymentType']:'';
          // 成功付款，並不需要更新狀態
          if ($PaymentDate!='' && $PaymentType!='') {
          // 例外狀況
          } else {
            // $this->admin_crud->update($tb,$pid,array('orderstate'=>'取消訂單'));
          }
        // 若為 0 時，代表交易訂單成立未付款
        } else if($TradeStatus==0) {
          //   $this->admin_crud->update($tb,$pid,array('orderstate'=>'取消訂單'));
        }
        // 依據查詢結果更新訂單狀態 @@
      //驗證失敗
      } else {}
      return;
    }

    // 驗證碼檢查
    function cartChkMacValue($postStr='',$return_MacValue='')
  	{
      // 進行反驗證檢查
  		if ($postStr && $return_MacValue) {
        $searchStr = '&CheckMacValue='.$return_MacValue;
        $postStr = str_replace($searchStr,'',$postStr);
  			$CheckMacValue = $this->cartGetMacValue($postStr); // 組合驗證碼
  			if($return_MacValue == $CheckMacValue) return 'true'; // 與自己組合的驗證碼相同，驗證成功
  			else return 'false'; // 反之，失敗
      // 取得驗證碼
  		} else {
  			$CheckMacValue = $this->cartGetMacValue($postStr); // 組合驗證碼
  			return $CheckMacValue;
  		}
  	}

    // 組合驗證碼
  	function cartGetMacValue($postStr='')
  	{
  		$CheckMacValue = '';
  		if ($postStr) {
  			$level1 = $this->HashKey.$postStr.$this->HashIV;
  			$level2 = urlencode($level1);
  			$level3 = strtolower($level2);
  			$level3 = str_replace('%2d', '-', $level3);
  			$level3 = str_replace('%5f', '_', $level3);
  			$level3 = str_replace('%2e', '.', $level3);
  			$level3 = str_replace('%21', '!', $level3);
  			$level3 = str_replace('%2a', '*', $level3);
  			$level3 = str_replace('%28', '(', $level3);
  			$level3 = str_replace('%29', ')', $level3);
  			// SHA256 加密
  			$level4 = hash('sha256', $level3);
  			// 轉換成大寫
  			$CheckMacValue = strtoupper($level4);
  		}
  		return $CheckMacValue;
  	}

    // 組合所需的 post字串
    function BuildPostString($array=array(),$isValid=false)
    {
      // 參數轉小寫，以便排序
      foreach ($array as $key => $value) {
        $key = strtolower($key);
        $array2[$key] = $value;
      }
      $array = $array2;
      ksort($array);
      /*$postStr = ''; $fisrt = -1;
      foreach ($array as $key => $value) { $fisrt++;
        if($fisrt!=0) $postStr .= '&';
        $postStr .= $key.'='.$value;
      }*/
      $postStr = urldecode(http_build_query($array));
      return $postStr;
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
