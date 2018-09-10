$(window).load(function () {
    $('.load').fadeOut(500);
    // 滾動css動態
    new WOW().init();
    //跑馬燈
})

$(function () {
    /*-------------------------------*/
    /*---手機選單---*/
    /*-------------------------------*/
    var RWD = navigator.userAgent,
        winWidth = $(window).width(),
        anoWidth = 0,
        nowstatus = 0, // 0 = mobile / 1 = desktop
        nowIndex,
        nowNavIndex,
        pos,
        filter,
        fixbar,
        winNow,
        posRef,
        jsrwd,
        body = $("html, body"),
        $mask,
        scrollNow,
        $header = $('.header'),
        $nav = $('.nav'),
        needMask = false;

    if (needMask) {
        var $mask = $('<div class="mask"></div>').hide();
        $('.page').after($mask);
    }

    //$(".video").fitVids();
    //document.createElement("picture");
    if (RWD.match(/iPhone|iPad|iPod|Android|BlackBerry/i)) {
        $('.nav-toggle').on('touchstart', MENU);
        $('.nav-transparenttoggle').on('click', MENU);
        $('.mask').on('touchstart', MENU);
    } else {
        $('.nav-toggle').on('click', MENU);
        $('.nav-transparenttoggle').on('click', MENU);
        $('.mask').on('click', MENU);
    }

    if ($('.category-nav').size() !== 0) {
        if ($('.category-nav a').eq(0).attr('href') === "#") {
            filter = $('.category-nav');
            filter.on('click', 'a', categoryNav);
            console.log('#');
        } else {
            console.log('link');

        }
    }

    if ($('.video').size() !== 0) {
        $(".video").fitVids();
    }

    if ($('.fixbar').size() !== 0) {
        // must add ".fixbar-after"
        if ($('.fixbar-after').size() === 0) {
            alert("你沒有加 fixbar-after 啦");
        } else {
            posRef = $('.fixbar-after');
            fixbar = $('.fixbar');
            $(window).scroll(FIXBAR);
        }

    }

    BGIMG();
    MENUMODE();
    $(window).scroll(function () {
        scrollNow = $(window).scrollTop();
        console.log(scrollNow);
        if (scrollNow > 150) {
            $header.addClass('fixed');
            $nav.addClass('fixed');
            $('.language').addClass('fixed');
            $('.contactlanguage').addClass('fixed');
            $('.transparentheader').addClass('fixed');
            $('.contactnav').addClass('fixed');
            $('.sub-nav').addClass('fixed');

        } else {
            $header.removeClass('fixed');
            $nav.removeClass('fixed');
            $('.language').removeClass('fixed');
            $('.contactlanguage').removeClass('fixed');
            $('.transparentheader').removeClass('fixed');
            $('.contactnav').removeClass('fixed');
            $('.sub-nav').removeClass('fixed');
            $('.sub-contactnav').removeClass('fixed');

        }
    });

    // $('.btn-search').on('click', function() {
    //     $('.searchbar').toggleClass('open');
    //     $('.searchbar input').focus();
    //     $(this).toggleClass('open');
    //     return false;
    // });


    // function HEADER() {

    // }


    $(window).resize(function () {
        if (needMask) {
            $mask.hide();
        }
        $('.nav-toggle').removeClass('show');
        $('.nav-transparenttoggle').removeClass('show');
        $('.page').removeClass('show');
        $('html,body').removeClass('show');
        $('.nav').removeClass('show');
        $('.hassub > .submenu').removeAttr('style');
        $('.hassub').removeClass('open');
        MENUMODE();
        //console.log($('#js-rwd').width());
        delay(function () {
            BGIMG();
            //console.log('window size change');
        }, 200);
    });

    $('.hassub').hover(function () {
        $('.searchbar').removeClass('open');
    });
    $('.nav-close').on('click', MENU);

    // $(document).click(function(event) {
    //     if (!$(event.target).closest(".searchbar, .btn-search").length) {
    //         $('.searchbar').removeClass('open');
    //         //console.log('not searchbar')
    //     }
    // });
    $(".lazy").lazyload({
        threshold: 500,
        effect: "fadeIn"
    });

    var nowTag = 0,
        newTagIndex;
    $('.tags').on('click', 'a', function () {
        newTagIndex = $(this).index('.tags a');
        if (newTagIndex != 2) {
            console.log(newTagIndex);
            $(this).addClass('current');
            $('.tags a').eq(nowTag).removeClass('current');
            $('.tags-contents').eq(nowTag).removeClass('show');
            $('.tags-contents').eq(newTagIndex).addClass('show');
            nowTag = newTagIndex;
            //alert('abc');
            return false;
        }

    })

    // $('.tags-wrap').on('click', '.tags a', function() {
    //     newTagIndex = $(this).index('.tags a');
    //     if (newTagIndex != 2) {
    //         console.log(newTagIndex);
    //         $(this).addClass('current');
    //         $('.tags a').eq(nowTag).removeClass('current');
    //         $('.tags-content').eq(nowTag).removeClass('show');
    //         $('.tags-content').eq(newTagIndex).addClass('show');
    //         nowTag = newTagIndex;
    //         //alert('abc');
    //         return false;
    //     }

    // })

    function categoryNav() {
        nowNavIndex = $(this).index('.category-nav a');
        //var nowcount = $('.category-nav a').size() - 1;
        //console.log(nowNavIndex);
        //nowIndex--;
        var newpos = $('.list-wtb').eq(nowNavIndex).offset();
        //console.log('now position = ' + newpos.top);
        body.stop(true, false).animate({
            scrollTop: newpos.top - 70
        }, 500);
        return false;
    }

    function FIXBAR() {
        pos = posRef.offset();
        winNow = $(window).scrollTop();
        //console.log(pos.top + " / " + winNow);
        if (winNow > pos.top) {
            fixbar.addClass('fixed');
        } else {
            fixbar.removeClass('fixed');
        }
    }

    function MENU() {
        if (needMask) {
            $mask.fadeToggle(300);
        }
        $('.page').toggleClass('show');
        $('html,body').toggleClass('show');
        $('.nav-toggle').toggleClass('show');
        $('.nav-transparenttoggle').toggleClass('show');
        $('.nav').toggleClass('show');
        $('.contactnav').toggleClass('show');
        //$header.toggleClass('fixed');
        return false;
    }

    $('.hassub').on('click', SUBMENU);
    $('.hasthird').on('click', 'li', THIRDMENU);

    function SUBMENU() {
        //var thisClassName = $(this).attr('class');
        //alert();
        //  && $(this).hasClass('open') === false
        if (nowstatus === 0) {
            $(this).toggleClass('open').find('.submenu').slideToggle();
            $(this).siblings('.hassub').removeClass('open').find('.submenu').slideUp();
        }
    }

    function THIRDMENU() {
        //alert($(this).hasClass('title'));
        if (nowstatus === 0) {
            if ($(this).hasClass('title')) {
                var $box = $(this).parent('ul');
                var minimumHeight = $(this).height() + 1;
                var currentHeight = $box.innerHeight();
                var autoHeight = $box.css('height', 'auto').innerHeight();
                //console.log($box);
                //console.log("currentHeight = " + currentHeight);
                //console.log("autoHeight = " + autoHeight);
                $box.siblings('ul').animate({ height: minimumHeight }, function () {
                    $(this).removeAttr('style');
                    $(this).removeClass('current');
                });
                if ($box.hasClass('current')) {
                    $box.removeClass('current');
                    //console.log('has current')
                } else {
                    $box.addClass('current');
                    //console.log('no current')
                }

                $box.css('height', currentHeight).animate({
                    height: (currentHeight == autoHeight ? minimumHeight : autoHeight)
                })
                //$box.animate({ height: currentHeight },300);


                // get height with auto applied
                //var autoHeight = $box.css('height', 'auto').innerHeight();
                // reset height and revert to original if current and auto are equal
                /*$box.css('height', currentHeight).animate({
                    height: (currentHeight == autoHeight ? minimumHeight : autoHeight)
                })*/
                return false;
            }

        }

    }

    function MENUMODE() {
        jsrwd = $('#js-rwd').width();
        if (jsrwd === 0) {
            nowstatus = 0;
            //console.log('mobile menu');
        } else {
            nowstatus = 1;
            //console.log('desktop menu');
        }

    }


    /*-------------------------------*/
    /*---editor 移除圖片尺寸---*/
    /*-------------------------------*/
    if ($('.edit').size() !== 0) {
        $('.edit img').removeAttr('style');
        $('img.big').unwrap('p');
        //
    }
    if ($('.section-nav').size() !== 0) {
        SectionNavCheck();
        $('.section-nav').on('click', 'a', goSECTION);
    }

    if ($('.pos-middle').size() !== 0) {
        var pm = $('.pos-middle');
        var pmh = 0 - pm.outerHeight(false) / 2;
        //console.log(cnah);
        pm.css('margin-top', pmh);
    }

    var delay = (function () {
        var timer = 0;
        return function (callback, ms) {
            clearTimeout(timer);
            timer = setTimeout(callback, ms);
        };
    })();


    function BGIMG() {
        winWidth = $(window).width();
        //MENUMODE(winWidth);
        if ($('.bg').size() !== 0 && winWidth > 842) {
            //console.log($('.bg').size());
            //console.log('winWidth = ' + winWidth + 'screen width:' + anoWidth);
            $('.bg').each(function (index, el) {
                var bgpic = 'url("' + $(this).find('img').attr('data-src') + '")';
                $(this).css({
                    'background-image': bgpic
                });
                //console.log(index + '/' + bgpic);
            });
        } else {
            $('.bg').css("background-image", "");
            //console.log('winWidth = ' + winWidth + 'screen width:' + anoWidth);
        }

    }


    //$('.section-nav').on('click', 'a', goTHERE);
    function SectionNavPosition() {
        var cna = $('.section-nav');
        var cnah = 0 - cna.outerHeight(false) / 2;
        cna.css('margin-top', cnah);

    }

    function SectionNavCheck() {
        var specSize = $('.spec').size();
        var downloadSize = $('.download').size();

        if (specSize === 1 && downloadSize === 1) {
            $('.section-nav a').eq(2).remove();
        }
        if (downloadSize === 0 && specSize === 0) {
            $('.section-nav a:eq(2), .section-nav a:eq(3)').remove();
        } else if (downloadSize === 0 && specSize !== 0) {
            $('.section-nav a').eq(3).remove();

        }
        SectionNavPosition();

    }

    function goSECTION() {
        nowIndex = $(this).index('.section-nav a');
        var nowsize = $('.section-nav a').size() - 1;
        if (nowIndex !== nowsize) {
            //console.log(nowIndex);
            //nowIndex--;
            var newpos = $('.bookmark').eq(nowIndex).offset();
            //console.log('now position = ' + newpos.top);
            body.stop(true, false).animate({
                scrollTop: newpos.top
            }, 500);

        } else {
            //console.log(nowIndex);
            body.stop(true, false).animate({
                scrollTop: 0
            }, 500);

        }

        return false;
    }


});

function globalDevice(e) {
    var RWD = navigator.userAgent;
    if (RWD.match(/iPhone|iPad|iPod|Android|BlackBerry/i)) {
        return "1";
    } else {
        return "0";
    }
}