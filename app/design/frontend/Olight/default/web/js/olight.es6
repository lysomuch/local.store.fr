require(['jquery'], function ($) {
    const olight = {
        route: function (roteJson) {
            let page;
            for (page in roteJson) {
                if (jQuery(page).length) {
                    roteJson[page]();
                }
            }
        },
        toggle: function (x, y, ev) {
            let theBtn = $(x);
            let theP = $(y);
            theBtn.each(
                function (i) {
                    $(this).click(function (e) {
                            if (ev == true) {
                                e.stopPropagation()
                            }
                            // console.log(e.target);
                            $(this).toggleClass("closeUp");
                            theP.eq(i).slideToggle();
                        }
                    )
                }
            )
        },
        async_toggle: function (x, p, y, ev) {
            $(document).on("click", x, function (e) {
                if (ev == true) {
                    e.stopPropagation()
                }
                $(this).toggleClass("closeUp").parents(p).find(y).slideToggle();
            })
        },
        addIn: function addIn(x, y) {
            $(x).each(function () {
                    $(this).click(function () {
                        $(this).addClass(y).siblings().removeClass(y)
                    })
                }
            )
        },
        isMob: window.innerWidth < 768,
        getDevices: function () {
            return window.innerWidth;
        },
        rem: function () {
            let w = window.innerWidth;
            if (w > 767) {
                $("html").css("font-size", w / 1920 * 100);
            }
            else {
                $("html").css("font-size", w / 320 * 50);
            }
        },
        swiper: function (width, obj) {
            if ($(window).width() <= width) {
                $(obj).find('li').each(function () {
                    $(this).addClass('swiper-slide');
                });
                require(['swiper'], function () {
                    var accountNavigation = new Swiper(obj, {
                        slidesPerView: 'auto'
                    });
                });
            } else {
                $(obj).find('li').each(function () {
                    $(this).removeClass('swiper-slide');
                });
            }
        },
        addCart: function (startObj, imgObj) {
            let offset = $('.minicart-wrapper').offset();
            let productImg = imgObj.attr('src');
            let startoffset = startObj.offset();
            let flyer = $("<img src='" + productImg + "' class='fly_img' />");
            require(['flyer'], function () {
                flyer.fly({
                    start: {
                        left: startoffset.left,
                        top: startoffset.top - $(document).scrollTop()
                    },
                    end: {
                        left: offset.left,
                        top: offset.top,
                        width: 20,
                        height: 20
                    },
                    onEnd: function () {
                        this.destory();
                    }
                });
            });
        },
        lazy: function () {
            require(['lazyload'], function () {
                $("img").lazyload({effect: "show"});
            });
        }
    };
    $(function () {
        pagePublic();
        addToWishlist();
        handleMessage();
        olight.route({
            ".cms-index-index": pageHome,
            ".customer-account-index": accountIndex,
            ".catalog-product-view": productView,
            ".checkout-cart-index": pageCart,
            ".cms-page-view": cmsPage,
            // ".flashsales-view-index": flashPage,
            ".catalog-category-view": pageList,
            ".account": allAccount,
            ".checkout-index-index": pageCheckout,
        });
    });

    function pagePublic() {
        $('.panel-header').find('a.showcart').click(function () {
            if ($(window).width() < 768) {
                let url = $(this).attr('href');
                $(location).prop('href', url);
            }
        });
        $(".right-float ul li.toTop").click(function () {
            $('body,html').animate({scrollTop: 0}, 500);
            return false;
        });

        require(['vue'], function (Vue) {

            let navVue = new Vue({
                el: '#olight-nav',
                data: {
                    nav: [],
                    hot: [],
                },
                methods: {},
                created: function () {
                    let t = 1000 * 60 * 5 * 1,//过期时间5min
                        hasNav = (typeof localStorage.olight_nav != undefined) && (new Date().getTime() - localStorage.nav_date < t);
                    if (hasNav) {
                        // console.log("已经获取菜单数据");
                        this.nav = JSON.parse(localStorage.getItem('olight_nav')).nav;
                        this.hot = JSON.parse(localStorage.getItem('olight_nav')).hot;
                    }
                    else {
                        $.get(`/catalog/nav/ajax`, function (res) {
                            localStorage.olight_nav = JSON.stringify(res.result);
                            localStorage.nav_date = new Date().getTime();
                            this.nav = JSON.parse(localStorage.getItem('olight_nav')).nav;
                            this.hot = JSON.parse(localStorage.getItem('olight_nav')).hot;
                        }.bind(this))
                    }
                },
                mounted: function () {
                    this.$nextTick(function () {
                        olight.toggle(".action.nav-toggle", "#olight-nav");
                        $(navVue.$refs.menuitem).hover(function () {
                            $(this).addClass('active');
                            $(this).siblings().removeClass('active');
                        });
                        $('#olight-nav .hot-nav > li').hover(function () {
                            //olight.lazy
                            require(['lazyload'], function () {
                                $("img").lazyload({effect: "show"});
                            });
                        });


                    });

                },
                updated: function () {
                    $(navVue.$refs.menuitem).hover(function () {
                        $(this).addClass('active');
                        $(this).siblings().removeClass('active');
                    });
                }
            });
        });

        if ($("#time-number").length) {
            require(['vue'], function (Vue) {
                let timeVue = new Vue({
                    el: '.countdown',
                    data: {
                        startTime: initDate(startTime),
                        endTime: initDate(endTime),
                        nowTime: null,
                        isReady: false
                    },
                    methods: {},
                    filters: {
                        numDouble(v, d = false){
                            if (typeof timeVue == 'object') {
                                if (timeVue.state == 'end') {
                                    return d ? 0 : "00"
                                }
                            }
                            if ((v >= 0 && v < 10) && !d) {
                                return '0' + v
                            }
                            return v
                        }
                    },
                    created() {
                        var _this = this;
                        $.ajax({
                            url: '/customer/info/ajax',
                            method: 'GET',
                            dataType: 'json',
                            success: function (res) {
                                $('div.panel-header span.account-name').text(res.name);
                                _this.nowTime = new Date(initDate(res.currentServerTime));
                                var timer = _this.nowTime.getTime();
                                _this.isReady = true;
                                let interval = setInterval(function () {
                                    timer += 1000;
                                    _this.nowTime = new Date(timer);

                                    if (_this.nowTime - _this.endTime > 0) {
                                        clearInterval(interval);
                                    }
                                }, 1000);

                            }
                        });
                        // let interval = setInterval(function () {
                        //     this.nowTime = new Date();
                        //     // console.log(this.nowTime);
                        //     if (this.nowTime - this.endTime > 0) {
                        //         clearInterval(interval);
                        //     }
                        // }.bind(this), 1000);
                    },
                    computed: {
                        timeToStart(){
                            return this.startTime - this.nowTime
                        },
                        timeToEnd(){
                            return this.endTime - this.nowTime
                        },
                        state(){
                            let s = '';
                            if (this.timeToStart > 0) {
                                s = 'un_start'
                            }
                            else if (this.timeToEnd > 0 && this.timeToStart <= 0) {
                                s = 'start'
                            }
                            else {
                                s = 'end'
                            }
                            return s
                        },
                        day(){
                            let t = this.state == 'un_start' ? this.timeToStart : this.timeToEnd;
                            return parseInt(t / 1000 / 60 / 60 / 24)
                        },
                        hour(){
                            let t = this.state == 'un_start' ? this.timeToStart : this.timeToEnd;
                            return parseInt(t / 1000 / 60 / 60 % 24);
                        },
                        minute(){
                            let t = this.state == 'un_start' ? this.timeToStart : this.timeToEnd;
                            return parseInt(t / 1000 / 60 % 60);
                        },
                        seconds(){
                            let t = this.state == 'un_start' ? this.timeToStart : this.timeToEnd;
                            return parseInt(t / 1000 % 60);
                        },
                        text(){
                            let t = '';
                            if (this.state == 'un_start') {
                                t = 'STARTING IN'
                            }
                            else if (this.state == 'start') {
                                t = 'ENDING IN'
                            }
                            return t
                        },
                        startAt(){
                            let t = this.startTime,
                                day = t.toDateString().slice(0, -4),
                                // time = t.toTimeString().slice(0, 5);
                                h = t.getHours(),
                                mm = t.getMinutes(),
                                str;
                            if (h > 12) {
                                h -= 12;
                                str = " PM";
                            } else {
                                str = " AM";
                            }
                            let time = h + ':' + mm + str;
                            return `${day} <b>${time}</b>`
                        },
                        endAt(){
                            let t = this.endTime,
                                day = t.toDateString().slice(0, -4),
                                time = t.toTimeString().slice(0, 5);
                            return day + '| ' + time
                        }
                    }
                });
            });
        }


        if (olight.isMob) {
            olight.toggle(".footer_links .links>.block-title", ".footer_links .links>ul");
        }

//fly


        // lazyload
        // olight.lazy

        // contact us
        let isSend = true;
        $('.right-float').find('li .icon-box').click(function () {
            let formBox = $(this).next('.floatbox');
            let current = $(this).parents('li');
            let parent = $(this).parent('li');
            let url = '';

            if (parent.hasClass('facebook-contact')) {
                url = '/contact/index/facebook';
            } else if (parent.hasClass('contact-us')) {
                url = '/contact/index/ajax';
            }

            if (!formBox.html() && url) {
                if (isSend) {
                    isSend = false;
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'html',
                        success: function (html) {
                            formBox.append(html)
                            current.removeClass('hideFloat').siblings().addClass('hideFloat');
                            isSend = true;
                        }
                    });
                }

            } else {
                if (current.hasClass('hideFloat')) {
                    current.removeClass('hideFloat').siblings().addClass('hideFloat');
                } else {
                    current.addClass('hideFloat');
                }
            }
        });

        $('.right-gather').click(function () {
            var elem = $('.right-items').parent('.right-float');
            if (!elem.hasClass('expand')) {
                elem.addClass('expand');
            } else {
                elem.removeClass('expand');
            }
        });

        setTimeout(function () {
            $('.right-float').removeClass('expand');
        }, 3000);

        // newsletter ajax
        $('button.subscribe').click(function () {
            let subUrl = $(this).parents('form').attr('action');
            let email = $('input#newsletter').val();
            if (!email) {
                $('#newsletter-error').text('This is a required field.').attr('class', 'mage-error').show();
            } else {
                $.ajax({
                    url: subUrl,
                    type: 'POST',
                    data: {email: email},
                    dataType: 'json',
                    success: function (data) {
                        if (data.status != "ERROR") {
                            $('#newsletter').val('');
                            $('#newsletter-error').attr('class', 'message success').show();
                        } else {
                            $('#newsletter-error').attr('class', 'mage-error').show();
                        }
                        $('#newsletter-error').text(data.msg);
                    }
                });
            }
            return false;
        });
    }

    function pageHome() {
        //olight.bannerHeight;
        //$(window).resize(function(){
        //    olight.bannerHeight;
        //});

        //require(['fullpage'], function () {
        //    let runPage,
        //        interval,
        //        autoPlay;
        //    autoPlay = function (to) {
        //        clearTimeout(interval);
        //        interval = setTimeout(function () {
        //            runPage.go(to);
        //        }, 5000);
        //
        //    };
        //    runPage = new FullPage({
        //        id: 'pageContain',
        //        slideTime: 100,
        //        effect: {
        //            transform: {
        //                translate: 'none',
        //                scale: [1, 1],
        //                rotate: [0, 0]
        //            },
        //            opacity: [0, 1]
        //        },
        //        mode: 'touch,nav:navBar',
        //        easing: [0, .93, .39, .98],
        //        callback: function (index, thisPage) {
        //            index = index + 1 > 2 ? 0 : index + 1;
        //            autoPlay(index);
        //        }
        //    });
        //    interval = setTimeout(function () {
        //        runPage.go(runPage.thisPage() + 1);
        //    }, 5000);
        //
        //});


        // let dataInit = {
        //     lists: [
        //         {
        //             title: "TEST PRODUCT",
        //             url: "",
        //             products: [
        //                 {
        //                     img: 'https://www.olightstore.com/image/cache/catalog/flashlights/olight-pl2/gun-flashlight-olight-pl-2-4-262x262.jpg',
        //                     url: '',
        //                     name: 'Olight M2T Warrior',
        //                     price: '$78.99',
        //                     final_price: '$78.99',
        //                     rating: 77
        //                 },
        //                 {
        //                     img: 'https://www.olightstore.com/image/cache/catalog/flashlights/olight-pl2/gun-flashlight-olight-pl-2-4-262x262.jpg',
        //                     url: '',
        //                     name: 'Olight M2T Warrior',
        //                     price: '$78.99',
        //                     final_price: '$78.99',
        //                     rating: 77
        //                 },
        //                 {
        //                     img: 'https://www.olightstore.com/image/cache/catalog/flashlights/olight-pl2/gun-flashlight-olight-pl-2-4-262x262.jpg',
        //                     url: '',
        //                     name: 'Olight M2T Warrior',
        //                     price: '$78.99',
        //                     final_price: '$78.99',
        //                     rating: 77
        //                 }
        //             ]
        //         },
        //         {
        //             title: "TEST PRODUCT",
        //             url: "",
        //             products: [
        //                 {
        //                     img: 'https://www.olightstore.com/image/cache/catalog/flashlights/olight-pl2/gun-flashlight-olight-pl-2-4-262x262.jpg',
        //                     url: '',
        //                     name: 'Olight M2T Warrior',
        //                     price: '$78.99',
        //                     final_price: '$78.99',
        //                     rating: 77
        //                 },
        //                 {
        //                     img: 'https://www.olightstore.com/image/cache/catalog/flashlights/olight-pl2/gun-flashlight-olight-pl-2-4-262x262.jpg',
        //                     url: '',
        //                     name: 'Olight M2T Warrior',
        //                     price: '$78.99',
        //                     final_price: '$78.99',
        //                     rating: 77
        //                 },
        //                 {
        //                     img: 'https://www.olightstore.com/image/cache/catalog/flashlights/olight-pl2/gun-flashlight-olight-pl-2-4-262x262.jpg',
        //                     url: '',
        //                     name: 'Olight M2T Warrior',
        //                     price: '$78.99',
        //                     final_price: '$78.99',
        //                     rating: 77
        //                 },
        //                 {
        //                     img: 'https://www.olightstore.com/image/cache/catalog/flashlights/olight-pl2/gun-flashlight-olight-pl-2-4-262x262.jpg',
        //                     url: '',
        //                     name: 'Olight M2T Warrior',
        //                     price: '$78.99',
        //                     final_price: '$78.99',
        //                     rating: 77
        //                 },
        //                 {
        //                     img: 'https://www.olightstore.com/image/cache/catalog/flashlights/olight-pl2/gun-flashlight-olight-pl-2-4-262x262.jpg',
        //                     url: '',
        //                     name: 'Olight M2T Warrior',
        //                     price: '$78.99',
        //                     final_price: '$78.99',
        //                     rating: 77
        //                 },
        //
        //             ]
        //         }
        //     ]
        // };


        // require(['vue', 'axios'], function (Vue, axios) {
        //     let homeVue = new Vue({
        //         el: '#home-vue',
        //         data: dataInit,
        //         created: function () {
        //         },
        //     });
        // })
        $('.pc-banner .banner-down-button').click(function () {
            $('html, body').animate({scrollTop: $(window).height()}, 700);
        });
    }

    function pageList() {
        olight.toggle(".filter-title", ".filter-content");
        olight.toggle("#toolbar-amount", ".toolbar-sorter");


        $('button.tocart').click(function () {
            olight.addCart($(this), $(this).parents('li').find('img'));
        });

        // add to wishlist
        $.ajax({
            url: '/wishlist/index/ajax',
            method: 'GET',
            dataType: 'json',
            contentType: 'application/json; charset=UTF-8',
            success: function (data) {
                $('.products-grid ol li').each(function () {
                    let currentItem = $(this);
                    let dataId = currentItem.attr('data-id');
                    $.each(data, function (key, item) {
                        if (dataId == item) {
                            currentItem.find('a.towishlist').addClass('added');
                        }
                    });


                });
            }

        });

    }

    function pageCart() {
        changeQty(".add_more", ".del_less");
        $('.gift-remove').click(function () {
            let r = confirm("Are you sure you want to do this?")
            if (r == true) {
                $(this).siblings(".action-delete").click();
            }
            else {

            }
        });

        //无选中时默认选中第一个
        let timer;
        timer = setInterval(function () {
            if ($("dl.items.methods").find("input.radio").length) {
                if (!$("dl.items.methods").find("input.radio:checked").length) {
                    $("dl.items.methods").find("input.radio").eq(0).click()
                }
                clearInterval(timer)
            }
        }, 500)
    }

    function productView() {
        $('.product-info-main .number-box span').click(function () {
            let inputBox = $(this).siblings('input');
            if ($(this).hasClass('less')) {
                if (parseInt(inputBox.val()) - 1 <= 0) {
                    inputBox.val(1);
                } else {
                    inputBox.val(parseInt(inputBox.val()) - 1);
                }
            } else {
                inputBox.val(parseInt(inputBox.val()) + 1);
            }
        });
        $('.product-info-main').find('.action.primary').before($('.product-info-main .product-addto-links').html());
        $('.product.info.detailed ul li').first().addClass('active');
        $('.product.info.detailed .product.data.items > .item.content').first().addClass('active');
        //olight.toggle('.product.info.detailed ul li', '.product.data.items > .item.content');
        $('.product.info.detailed ul li').each(function () {
            $(this).click(function () {
                $(this).addClass('active').siblings().removeClass('active');
                $('.product.data.items').find('.item.content').eq($(this).index()).addClass('active').siblings().removeClass('active');
                //console.log($(this_id).html())
                //$(this_id).addClass('active').siblings().removeClass('active');

            });
        })
        //checkout button
        $('span.toCheckout').click(function () {
            $("input[name='checkout']").removeAttr('disabled');
            $('body').data('toCheckout', 'toCheckout');
            $(this).parents('form').submit();
        });

        //bundle
        olight.toggle("#bundle-slide", ".bundle-options-wrapper");
        $('.box-tocart div.qty').after($("#bundle-slide"));
        //related product upsell
        olight.swiper(1300, '.products-related');
        $(window).resize(function () {
            olight.swiper(1300, '.products-related');
        });
        olight.swiper(1300, '.products-upsell');
        $(window).resize(function () {
            olight.swiper(1300, '.products-upsell');
        });
        olight.swiper(767, '.proudct-infor-tab');
        $(window).resize(function () {
            olight.swiper(767, '.proudct-infor-tab');
        });
//fly
        $('button.tocart').click(function () {
            olight.addCart($(this), $('.fotorama__active').find('img.fotorama__img'));
        });

        olight.toggle(".btn-title", ".accessories_product");
        changeQty(".add_more", ".del_less");

        $(document).on('mouseleave', '.magnify-lens', function () {
            $(this).addClass('magnify-hidden');
            $('.magnifier-preview').addClass('magnify-hidden');
        })
    }

    function accountIndex() {
        $('.page-title-wrapper').after("<div class='content-box'></div>");
        $('.content-box').append($('.block.account-nav').clone()).find('.content ').removeClass('account-nav-content');
    }

    function flashPage() {
        olight.toggle(".btn-title", ".accessories_product");
        changeQty(".add_more", ".del_less");
    }

    function cmsPage() {
        olight.toggle('.item-title', '.item-content');
    }

    function allAccount() {
        olight.swiper(1300, '.account-nav-content');
        $(window).resize(function () {
            olight.swiper(1300, '.account-nav-content');
        });
    }

    function pageCheckout() {
        //无选中时默认选中第一个
        let timer;
        timer = setInterval(function () {
            if ($("#checkout-shipping-method-load").find("input.radio").length) {
                if (!$("#checkout-shipping-method-load").find("input.radio:checked").length) {
                    $("#checkout-shipping-method-load").find("input.radio").eq(0).click()
                }
                clearInterval(timer)
            }
        }, 500)

    }

    /*-----------------------------------------------------------------*/

    function changeQty(add, del) {
        $(document).on("click", add, function () {
            let obj = $(this).siblings("input");
            obj.val(parseInt(obj.val()) + 1);
        })
        $(document).on("click", del, function () {
            let obj = $(this).siblings("input");
            obj.val(parseInt(obj.val()) - 1);
            if ((obj).val() < 1) {
                obj.val(1);
            }
        })
    }

    function scaler(x, y, z) {
        let cssIn = $(x).width();
        let cssOut = $(y).width();
        let scaleNum = cssOut / cssIn;
        $(x).css(
            {
                "transform": "scale(" + scaleNum + ")",
                "transform-origin": z,
                "-webkit-transform": "scale(" + scaleNum + ")",
                "-webkit-transform-origin": z,
                "-ms-transformtransform": "scale(" + scaleNum + ")",
                "-ms-transformtransform-origin": z,
                "-moz-transformtransform": "scale(" + scaleNum + ")",
                "-moz-transformtransform-origin": z
            }
        );
    }

    function zoomer(x, y) {
        let cssIn = $(x).width();
        let cssOut = $(y).width();
        let scaleNum = cssOut / cssIn;
        $(x).css(
            {
                "zoom": scaleNum
            }
        );
    }

    function browser() {
        let info = navigator.userAgent,
            browser = {
                b: '',
                v: ''
            };
        if (info.indexOf('Firefox') != -1) {
            //ff
            browser.b = 'f';
            browser.v = ''
        }

        else if (info.indexOf('Chrome') != -1) {
            //google
            browser.b = 'c';
            browser.v = ''
        }

        else if (info.indexOf('Safari') != -1) {
            //Safari
            browser.b = 's';
            browser.v = ''
        }

        return browser
    }

    function initDate(str) {
        /*2018-05-30 00:00:00=>["2018", "04", "30", "00", "00", "00"]*/
        let arr = str.replace(/-/g, ':').replace(' ', ':').split(':');
        arr[1]--;
        return new Date(...arr)
    }

    function addToWishlist() {
        $('.addtowishlist').click(function (e) {
            e.stopPropagation();
            e.preventDefault();

            var _this = $(this);
            var timer;
            var data = _this.data('post-wishlist');
            var formKey = $('input[name=form_key]').val();
            var parent = _this.parents('.product-item-info');
            var popup = parent.find('.info-popup');
            var imgObj = parent.find('.product-image-photo')

            if (!isLoggedIn) {
                window.location.href = '/customer/account/login';
                return false;
            }

            if (_this.hasClass('added')) {
                return false;
            }

            $.ajax({
                type: 'post',
                url: data.action,
                data: {
                    form_key: formKey,
                    product: data.data.product,
                    uenc: data.data.uenc
                },
                success: function (res) {
                    if (res.code == 200) {
                        _this.addClass('added');
                        popup.text(res.msg).animate({
                            bottom: 0
                        }, 2000);

                        let offset = $('#extend-info .extend-wish').offset();
                        let productImg = imgObj.attr('src');
                        let startoffset = _this.offset();
                        let flyer = $("<img src='" + productImg + "' class='fly_img' />");
                        require(['flyer'], function () {
                            flyer.fly({
                                start: {
                                    left: startoffset.left,
                                    top: startoffset.top - $(document).scrollTop()
                                },
                                end: {
                                    left: offset.left,
                                    top: offset.top,
                                    width: 20,
                                    height: 20
                                },
                                onEnd: function () {
                                    this.destory();
                                }
                            });
                        });

                        clearTimeout(timer);

                        timer = setTimeout(function () {
                            popup.animate({
                                bottom: '-100px'
                            }, 800, function () {
                                popup.text('');
                            });
                        }, 5000);
                    }

                },
                error: function (err) {
                    console.log(err);
                }

            });
        });

    }

    function handleMessage() {
        $('.contact-message').click(function () {
            $zopim(function () {
                $zopim.livechat.window.show();
            });
        });
    }

    //获取已登录用户基本信息
    function getCustomerBaseInfo() {
        $.ajax({
            url: '/customer/info/ajax',
            method: 'GET',
            dataType: 'json',
            success: function (res) {
                $('div.panel-header span.account-name').text(res.name);
            }
        });
    }

    getCustomerBaseInfo();


});