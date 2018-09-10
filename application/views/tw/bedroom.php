<body>
    <!--start nav.html-->
    <?php $this->load->view($this->lang.'/include/nav.php');?>
    <!--end nav.html-->
    <!--start header.html-->
    <?php $this->load->view($this->lang.'/include/header.php');?>
    <!--end header.html-->
    <div class="language"><a href="#" class="text">CH/EN</a></div>
      <?php foreach ($product_info_list as $key => $value):
              $xtitle = $value['xtitle'];
              $xsubtitle = ($value['xsubtitle'])?nl2br($value['xsubtitle']):'';
              $xfile1 = $this->front->getfilepath($this->prefix.'_index_banner','xfile1',$value,'',true);
      ?>
        <div class="bedroomPage" style="background-image: url(<?=$xfile1?>);">
            <div class="linearGradient"></div>
        </div>
        <div class="bedroomMain">
            <div class="content wrap" style="transform: skewY(-5deg);">
                <h2><?=$xtitle?></h2>
                <p><?=$xsubtitle?></p>
            </div>
        </div>
      <?php endforeach; ?>
    <div class="bedroomFurniture" style="background-image: url(../../../../assets/img/bedroom/furnitureNew.png)">
        <div class="flex wrap">
            <p>對於安全的考量，我們以嚴格的標準選用合適的高品質新型配件，減少五金零件的使用並降低製造成本，以最佳的預算滿足客戶對產品的需求。</p>
        </div>
    </div>
    <div class="bedroomFurniture-image"><img src="../../../../assets/img/bedroom/furniture.jpg"></div>
    <div class="bedroomFurniture-text wrap">
        <p>對於安全的考量，我們以嚴格的標準選用合適的高品質新型配件，減少五金零件的使用並降低製造成本，以最佳的預算滿足客戶對產品的需求。</p>
    </div>
    <div class="category" style="background-image: url(../../../../assets/img/bedroom/bed.jpg);"><img class="no-display" src="../../../../assets/img/bedroom/bed.jpg">
        <div class="sec wrap">
            <div class="icon">
                <div class="icon-sec"><img src="../../../../assets/img/bedroom/one.jpg">
                    <p>床</p>
                </div>
                <div class="icon-sec"><img src="../../../../assets/img/bedroom/two.jpg">
                    <p>衣櫃</p>
                </div>
                <div class="icon-sec"><img src="../../../../assets/img/bedroom/three.jpg">
                    <p>櫃子</p>
                </div>
                <div class="icon-sec"><img src="../../../../assets/img/bedroom/four.jpg">
                    <p>書架</p>
                </div>
                <div class="icon-sec"><img src="../../../../assets/img/bedroom/five.jpg">
                    <p>化妝桌</p>
                </div>
                <div class="icon-sec"><img src="../../../../assets/img/bedroom/six.jpg">
                    <p>椅子</p>
                </div>
            </div>
        </div>
    </div>
    <p class="gallery-title">臥室傢俱鑑賞</p>
    <div class="gallery wrap">
        <figure itemprop="associatedMedia" itemscope="" class="wow fadeInDown gallery-cell"><a href="../../../../assets/img/bedroom/one.jpg" itemprop="contentUrl" data-size="450x300"><img src="../../../../assets/img/bedroom/one.jpg"></a></figure>
        <figure itemprop="associatedMedia" itemscope="" class="wow fadeInDown gallery-cell"><a href="../../../../assets/img/bedroom/two.jpg" itemprop="contentUrl" data-size="450x300"><img src="../../../../assets/img/bedroom/two.jpg"></a></figure>
        <figure itemprop="associatedMedia" itemscope="" class="wow fadeInDown gallery-cell"><a href="../../../../assets/img/bedroom/three.jpg" itemprop="contentUrl" data-size="450x300"><img src="../../../../assets/img/bedroom/three.jpg"></a></figure>
        <figure itemprop="associatedMedia" itemscope="" class="wow fadeInDown gallery-cell"><a href="../../../../assets/img/bedroom/four.jpg" itemprop="contentUrl" data-size="450x300"><img src="../../../../assets/img/bedroom/four.jpg"></a></figure>
        <figure itemprop="associatedMedia" itemscope="" class="wow fadeInDown gallery-cell"><a href="../../../../assets/img/bedroom/five.jpg" itemprop="contentUrl" data-size="450x300"><img src="../../../../assets/img/bedroom/five.jpg"></a></figure>
        <figure itemprop="associatedMedia" itemscope="" class="wow fadeInDown gallery-cell"><a href="../../../../assets/img/bedroom/six.jpg" itemprop="contentUrl" data-size="450x300"><img src="../../../../assets/img/bedroom/six.jpg"></a></figure>
    </div>
    <div class="center">
        <div>
            <!--<figure itemprop="associatedMedia" itemscope class="wow fadeInDown ">
 <a href="../../../../assets/img/bedroom/one.jpg" itemprop="contentUrl" data-size="230x150">--><img src="../../../../assets/img/bedroom/one.jpg">
            <!--</a>
 </figure>-->
        </div>
        <div>
            <!--<figure itemprop="associatedMedia" itemscope class="wow fadeInDown">
 <a href="../../../../assets/img/bedroom/two.jpg" itemprop="contentUrl" data-size="230x150">--><img src="../../../../assets/img/bedroom/two.jpg">
            <!--</a>
 </figure>-->
        </div>
        <div>
            <!--<figure itemprop="associatedMedia" itemscope class="wow fadeInDown">
 <a href="../../../../assets/img/bedroom/three.jpg" itemprop="contentUrl" data-size="230x150">--><img src="../../../../assets/img/bedroom/three.jpg">
            <!--</a>
 </figure>-->
        </div>
        <div>
            <!--<figure itemprop="associatedMedia" itemscope class="wow fadeInDown">
 <a href="../../../../assets/img/bedroom/four.jpg" itemprop="contentUrl" data-size="230x150">--><img src="../../../../assets/img/bedroom/four.jpg">
            <!--</a>
 </figure>-->
        </div>
        <div>
            <!--<figure itemprop="associatedMedia" itemscope class="wow fadeInDown">
 <a href="../../../../assets/img/bedroom/five.jpg" itemprop="contentUrl" data-size="230x150">--><img src="../../../../assets/img/bedroom/five.jpg">
            <!--</a>
 </figure>-->
        </div>
        <div>
            <!--<figure itemprop="associatedMedia" itemscope class="wow fadeInDown">
 <a href="../../../../assets/img/bedroom/six.jpg" itemprop="contentUrl" data-size="230x150">--><img src="../../../../assets/img/bedroom/six.jpg">
            <!--</a>
 </figure>-->
        </div>
    </div>
    <div class="other-furniture"><img src="../../../../assets/img/bedroom/arrowline.svg">
        <div class="detail">
            <p>Other Furniture</p><a href="#"><img src="../../../../assets/img/bedroom/kitchen1.svg">廚房</a> <a href="#"><img src="../../../../assets/img/bedroom/livingroom.svg">客廳</a> <a href="#"><img src="../../../../assets/img/bedroom/room.svg">書房/辦公傢俱</a></div>
    </div>
    <!--start photoswipe.html-->
    <!--photoswipe-->
    <?php $this->load->view($this->lang.'/include/photoswipe.php');?>
    <!--end photoswipe.html-->
    <!--start footer.html-->
    <?php $this->load->view($this->lang.'/include/footer.php');?>
    <!--end footer.html-->
    <!--start loading.html-->
    <?php $this->load->view($this->lang.'/include/loading.php');?>
    <!--end loading.html-->
    <script src="../../../../assets/js/main.min.js"></script>
    <script src="../../../../assets/js/photoswipe.min.js"></script>
    <script src="../../../../assets/js/slick.min.js"></script>
</body>
<script type="text/javascript">
    $(document).ready(function() {
        $('.center').slick({
            centerMode: true,
            centerPadding: '60px',
            slidesToShow: 1,
        });
    });
</script>

</html>
