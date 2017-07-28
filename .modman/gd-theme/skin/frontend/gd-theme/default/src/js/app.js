// Foundation JavaScript
// Documentation can be found at: http://foundation.zurb.com/docs
jQuery(document).foundation();

jQuery(document).ready(function () {
    
    //Target specific browser
    //Uncomment if you need it
    // if (!!navigator.userAgent.match(/Trident\/7\./)) {
    //     jQuery('body').addClass('ie-target');
    // }
    // if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) {
    //     jQuery('body').addClass('safari-target');
    // }

    // if (navigator.userAgent.toLowerCase().indexOf('chrome') > -1) {
    //     jQuery('body').addClass('chrome-target');
    // }

    // Show Modal for Store View
    //if (jQuery.cookie('store_view') == null) {
    //    jQuery.cookie('store_view', 'yes', {expires: 7, path: '/'});
    //    setTimeout(function () {
    //        jQuery("#store_view").foundation('reveal', 'open');
    //    }, 3000);
    //}

    // Alert-Box auto_close
    jQuery('.callout.messages').slideDown({
        duration: 1500,
        complete: function () {
            jQuery('.callout.messages').delay(7000).slideUp(1500);
        }
    });

    // Product page / wishlist - quantity increase/decrease
    jQuery('.quantity .input-group').append('<span class="input-group-label plus"><i id="add1" class="plus fa fa-plus" /></span>').prepend('<span class="input-group-label minus"><i id="minus1" class="minus fa fa-minus" /></span>');
    jQuery('.quantity .plus').click(function () {
        var currentVal = parseInt(jQuery(".qty").val());
        if (!currentVal || currentVal == "" || currentVal == "NaN") currentVal = 0;
        jQuery(".qty").val(currentVal + 1);
    });

    jQuery('.quantity .minus').click(function () {
        var currentVal = parseInt(jQuery(".qty").val());
        if (currentVal == "NaN") currentVal = 0;
        if (currentVal > 1) {
            jQuery(".qty").val(currentVal - 1);
        }
    });

    //Grid / List view
    jQuery('.view-mode strong.grid').after('<i class="fa fa-th"></i>');
    jQuery('.view-mode strong.list').after('<i class="fa fa-align-justify"></i>');

    jQuery('.view-mode a.list').each(function () {
        if (jQuery(this).text() == 'List')
            jQuery(this).text('');
        jQuery(this).append('<i class="fa fa-align-justify"></i>');
    });

    jQuery('.view-mode a.grid').each(function () {
        if (jQuery(this).text() == 'Grid')
            jQuery(this).text('');
        jQuery(this).append('<i class="fa fa-th"></i>');
    });

    //Scroll to top
    jQuery('.cookie-container').after('<div class="scrollToTop"></div>');
    jQuery('.scrollToTop').append('<i class="fa fa-chevron-circle-up fa-2x"></i>');
    jQuery(window).scroll(function () {
        if (jQuery(this).scrollTop() > 100) {
            jQuery('.scrollToTop').fadeIn();
        } else {
            jQuery('.scrollToTop').fadeOut();
        }
    });

    //Click event to scroll to top
    jQuery('.scrollToTop').click(function () {
        jQuery('html, body').animate({scrollTop: 0}, 800);
        return false;
    });

    //CookieBar
    jQuery('.cookie-container').cookieBar({closeButton: '.close-cookie'});

    //owl.carousel
    //Sort random function for owl.carousel
    function random(owlSelector) {
        owlSelector.children().sort(function () {
            return Math.round(Math.random()) - 0.5;
        }).each(function () {
            jQuery(this).appendTo(owlSelector);
        });
    }

    //Home Slider Top Products
    var $slider1 = jQuery('.top-products-slider');
    $slider1.on('initialize.owl.carousel', function (event) {
        var selector = jQuery('.top-products-slider');
        random(selector);
    });
    jQuery('.top-products-slider').owlCarousel({
        items: 4,
        loop: true,
        nav: false,
        dots: false,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        responsiveClass: true,
        responsive: {
            0: {items: 1},
            600: {items: 2},
            1000: {items: 3},
            1100: {items: 4},
        }
    });
    //Home Slider credentials
    var $slider2 = jQuery('.credentials-slider');
    $slider2.on('initialize.owl.carousel', function (event) {
        var selector = jQuery('.credentials-slider');
        random(selector);
    });
    jQuery('.credentials-slider').owlCarousel({
        items: 8,
        loop: true,
        nav: false,
        dots: false,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        responsiveClass: true,
        responsive: {
            0: {items: 2},
            600: {items: 5},
            1000: {items: 8}
        }
    });

    //Footer Slider user reviews
    var $slider3 = jQuery('.reviews-slider');
    $slider3.on('initialize.owl.carousel', function (event) {
        var selector = jQuery('.reviews-slider');
        random(selector);
    });
    jQuery('.reviews-slider').owlCarousel({
        items: 1,
        loop: true,
        nav: false,
        dots: false,
        autoplay: true,
        autoplayHoverPause: true,
        autoplayTimeout: 5000,
        autoHeight: true
    });
    var $customEvents = $('#custom-events');
    jQuery('.product-img-box').lightGallery({
        iframeMaxWidth: '100%',
        videoMaxWidth: '1140px',
        selector: '.item',
        thumbnail: true,
        hash: false
    });

    //Newsletter
    var EsNewsSubscribers = {

        cookieLiveTime: 100,
        cookieName: 'newssubscriber',
        baseUrl: '',

        setCookieLiveTime: function(value)
        {
            this.cookieLiveTime = value;
        },

        setCookieName: function(value)
        {
            this.cookieName = value;
        },

        setBaseUrl: function(url)
        {
            this.baseUrl = url;
        },

        getBaseUrl: function(url)
        {
            return this.baseUrl;
        },

        createCookie: function() {
            var days = this.cookieLiveTime;
            var value = 1;
            var name = this.cookieName;
            if (days) {
                var date = new Date();
                date.setTime(date.getTime()+(days*24*60*60*1000));
                var expires = "; expires="+date.toGMTString();
            }
            else var expires = "";
            document.cookie = escape(name)+"="+escape(value)+expires+"; path=/";
        },

        readCookie: function(name) {
            var name = this.cookieName;
            var nameEQ = escape(name) + "=";
            var ca = document.cookie.split(';');
            for(var i=0;i < ca.length;i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return unescape(c.substring(nameEQ.length,c.length));
            }
            return null;
        },

        boxClose: function()
        {
            var popup = new Foundation.Reveal(jQuery('#esns'));
            popup.close();
        },

        boxOpen: function()
        {
            var popup = new Foundation.Reveal(jQuery('#esns'));
            popup.open();
        }
    };

    jQuery(function() {
        if (EsNewsSubscribers.readCookie() != 1) {
            //EsNewsSubscribers.createCookie();
            //EsNewsSubscribers.boxOpen();
        }
        jQuery('#esns_submit').click(function(){
            var email = jQuery('#esns_email').val();
            jQuery.post(EsNewsSubscribers.getBaseUrl()+'newsletter/subscriber/newajax/', {'email':email}, function(resp) {
                if (resp.errorMsg) {
                    jQuery('#esns_box_subscribe_response_error').html('<p>'+resp.errorMsg+'</p><hr>');
                } else {
                    jQuery('#esns_box_subscribe_response_error').html('');
                    jQuery('#esns_box_subscribe_response_success').html('<p>'+resp.successMsg+'</p><hr>');
                    jQuery('#esns_box_subscribe_form').css('display','none');
                    jQuery('#esns_box_subscribe_response_success').css('display','block');
                    setTimeout('EsNewsSubscribers.boxClose()', 5000)
                }
            });
        });
    });

});