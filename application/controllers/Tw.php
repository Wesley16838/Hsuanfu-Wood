<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tw extends Public_Controller {

	function __construct()
  {
    parent::__construct();
  }

	/*
		以下資料可自行運用、僅為範例
		1. seo 範例
		2. canonical 範例
		3. og 範例
	*/
	public function index()
	{
		$this->data = array();

		$this->data['list'] = $list = $this->front->result_array('sort_tb',array('xpublish'=>'yes'),true,'xsort');
		if(count($list)>0) {
			$row = $list[0];
			// seo 範例
			if($row['xseotitle']) $this->seotitle = $row['xseotitle'];
			if($row['xseokeyword']) $this->seokeyword = $row['xseokeyword'];
			if($row['xseodescription']) $this->seodesc = $row['xseodescription'];
			// end(seo 範例)

			// canonical 讀取範例
			if($text = $row['xurltitle']) $this->canonical = base_url().$this->prefix.'/product/'.$text;

			// og 範例 (需要主標、描述、圖片資訊)
			$this->showog = true;
			$filepath = $row['xfile1']; // 取出圖片欄位值
			if($filepath) { // 有上圖才會需要做處理
				$xfile1 = $this->front->getfilepath('sort_tb','xfile1',$row,'',true); // 圖片處理:圖片固定都要縮圖，設定true
				$GetSize = $this->front->getsize($xfile1); // 圖片處理:取得圖片資訊
				$xfile1 = $this->front->ckeckfilepath($xfile1); // 圖片處理:測試是否有檔案存在
				if($xfile1) { // 抓得到檔案才會有下列資訊
					$this->img = $GetSize['filepath']; // 圖路徑
					$this->imgWidth = $GetSize['fileW']; // 圖寬
					$this->imgHeight = $GetSize['fileH']; // 圖高
					$this->imgType = $GetSize['fileT']; // 圖附檔名
				}
			}
			$this->ogtitle = $row['xtitle']; // og 主標
			$this->ogdesc = $row['xsubtitle']; // og 描述
			// end(og 範例)
		}

    $layout['pageContent'] = $this->load->view($this->lang.'/index', $this->data, true);
    $this->load->view($this->layoutview, $layout);
	}

}
