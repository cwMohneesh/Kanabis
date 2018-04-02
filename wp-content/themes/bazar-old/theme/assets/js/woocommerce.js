jQuery( document ).ready( function( $ ) {

    // grid hover
    $('body.isMobile ul.products li:not(.category)').on('styleswitch elastiautoslide', function(){
        var $this_item = $(this), to;

        $this_item.on({
            mouseenter: function() {
                if ( $this_item.hasClass('grid') ) {
                    $this_item.height( $this_item.height()-1 );
                    $this_item.find('.product-actions-wrapper').height( $this_item.find('.product-actions').height() + 20 );
                    if ( $('html').attr('id') == 'ie8' || $('html').attr('id') == 'ie9' ) {
                        $this_item.addClass('js_hover');
                    }
                    clearTimeout(to);
                }
            },
            mouseleave: function() {
                if ( $this_item.hasClass('grid') ) {
                    if ( $('html').attr('id') == 'ie8' || $('html').attr('id') == 'ie9' ) {
                        $this_item.removeClass('js_hover');
                    }
                    $this_item.find('.product-actions-wrapper').height( 0 );
                    to = setTimeout(function()
                    {
                        $this_item.css( 'height', 'auto' );
                    },700);
                }
            }
        });
    }).trigger('styleswitch');

    // shop style switcher
    $('.list-or-grid a').on( 'click', function(){
        var actual_view = $(this).attr('class').replace( '-view', '' );

        if( YIT_Browser.isIE8() ) {
            actual_view = actual_view.replace( ' last-child', '' );
        }

        $('ul.products li').removeClass('list grid').addClass( actual_view );
        $(this).parent().find('a').removeClass('active');
        $(this).addClass('active');

//         switch ( actual_view ) {
//             case 'list' :
//                 $('ul.products li:not(.classic)').css({ width:'auto', height:'auto' });
//                 $('ul.products li:not(.classic) .product-thumbnail').css({ width:'auto', height:'auto', position:'static' });
//                 $('ul.products li:not(.classic) .product-thumbnail .onsale').css({ right:0, top:0 });
//                 $('ul.products li:not(.classic) .product-meta').css({ display:'block' });
//                 $('ul.products li.added:not(.classic)').find('h3, .price').css({ display:'block' });
//                 break;
//
//             case 'grid' :
//                 $('ul.products li:not(.classic)').css({ width:'', height:'' });
//                 $('ul.products li:not(.classic) .product-meta').css({ display:'none' });
//                 $('ul.products li.added:not(.classic)').find('h3, .price').css({ display:'none' });
//                 break;
//         }

        $.cookie(yit_shop_view_cookie, actual_view);

        $('ul.products li').trigger('styleswitch');

        return false;
    });

    // add to cart
    var product;

    $('ul.products li.product .add_to_cart_button').on( 'click', function(){
        product = $(this).parents('li.product');
        product.find('.product-thumbnail').block({message: null, overlayCSS: {background: '#fff url(' + woocommerce_params.ajax_loader_url + ') no-repeat center', opacity: 0.3, cursor:'none'}});
        $('.widget.woocommerce.widget_shopping_cart a.cart_control').show();
        $('.widget.woocommerce.widget_shopping_cart a.cart_control.cart_control_empty').remove();
    });
    $('body').on( 'added_to_cart', function(){
        if( typeof product !== "undefined" ){
            if ( product.find('.thumbnail-wrapper .added').length == 0 ) {
                product.find('.thumbnail-wrapper').append('<span class="added">added</span>');
                product.find('.added').fadeIn(500);
            }
            product.find('.product-thumbnail').unblock();
        }
    });

    // variations select
    if( $.fn.selectbox !== undefined ) {
        var form = $('form.variations_form');
        var select = form.find('select');

        form.find('select').selectbox({
            effect: 'fade',
            onOpen: function() {       //console.log('open');
                //$('.variations select').trigger('focusin');
            }
        });

        var update_select = function(event){  // console.log(event);
            form.find('select').selectbox("detach");
            form.find('select').selectbox("attach");
        };

        // fix variations select
        form.on( 'woocommerce_update_variation_values', update_select);
        form.find('.reset_variations').on('click', update_select);
    }

    // add to wishlist
    var wishlist_clicked;
    $(document).on( 'click', '.yith-wcwl-add-button a', function(){
        wishlist_clicked = $(this);
        wishlist_clicked.block({message: null, overlayCSS: {background: '#fff url(' + woocommerce_params.ajax_loader_url + ') no-repeat center', opacity: 0.6, cursor:'none'}});
    });

    // wishlist tooltip
    var apply_tiptip = function() {
        if ( $(this).parent().find('.feedback').length != 0 ) {
            $(this).tipTip({
                defaultPosition: "top",
                maxWidth: 'auto',
                edgeOffset: 20,
                content: $(this).parent().find('.feedback').text()
            });
        };
    }
    $('.yith-wcwl-add-to-wishlist a').each(apply_tiptip).on('mouseenter', apply_tiptip);

    //product slider
    /*
     if( $.elastislide ) {
     $('.products-slider-wrapper').each(function(){
     if( $(this).parents('.border-box').length == 0)
     $(this).elastislide( elastislide_defaults );
     });
     }
     */
    if( $.fn.carouFredSel ) {
        $('.products-slider-wrapper').each(function(){
            $(this).imagesLoaded(function(){
                var t = $(this);
                var items = $(this).find('.products-slider').data('items');

                if( $(this).parents('.border-box').length == 0) {
                    var carouFredSel;

                    var prev = $(this).find('.es-nav-prev').show();
                    var next = $(this).find('.es-nav-next').show();

                    carouFredSelOptions_defaults.prev = prev;
                    carouFredSelOptions_defaults.next = next;


                    if( $('body').outerWidth() <= 767 ) {
                        t.find('li').each(function(){
                            $(this).width( t.width() );
                        });

                        carouFredSelOptions_defaults.items = 1;
                    } else {
                        t.find('li').each(function(){
                            $(this).attr('style', '');
                        });

                        carouFredSelOptions_defaults.items = items;
                    }

                    carouFredSel = $(this).find('.products').carouFredSel( carouFredSelOptions_defaults );

                    if ( $('body').hasClass('responsive') ) {
                        $(window).resize(function(){

                            carouFredSel.trigger('destroy', false).attr('style','');

                            if( $('body').outerWidth() <= 767 ) {
                                t.find('li').each(function(){
                                    $(this).width( t.width() );
                                });

                                carouFredSelOptions_defaults.items = 1;
                            } else {
                                t.find('li').each(function(){
                                    $(this).attr('style', '');
                                });

                                carouFredSelOptions_defaults.items = items;
                            }

                            carouFredSel.carouFredSel(carouFredSelOptions_defaults);
                            $('.es-nav-prev, .es-nav-next').removeClass('hidden').show();
                        });
                    }

                    $(document).on('feature_tab_opened', function(){ $(window).trigger('resize') } );
                }
            });
        });
        $('.es-nav-prev, .es-nav-next').removeClass('hidden').show();
    }

    // force classic sytle in the products list
    $('.responsive ul.products li.product.grid.force-classic-on-mobile').on('force_classic', function(){
        if ( $(window).width() < 768 ) {
            $(this).addClass('classic').removeClass('with-hover');
        } else {
            $(this).addClass('with-hover').removeClass('classic');
        }
    }).trigger('force_classic');

    $(window).resize(function(){
        $('.responsive ul.products li.product.grid.force-classic-on-mobile').trigger('force_classic');
    });

    //shop sidebar
    $('.woocommerce .sidebar .widget h3').prepend('<div class="minus" />');
    $('.woocommerce .sidebar .widget').on('click', 'h3', function(){
        $(this).parent().find('> *:not(h3)').slideToggle();

        if( $(this).find('div').hasClass('minus') ) {
            $(this).find('div').removeClass('minus').addClass('plus');
        } else {
            $(this).find('div').removeClass('plus').addClass('minus');
        }
    });


    //share
    $(document).on('click', '.yit_share', function(e){
        e.preventDefault();

        var share = $(this).parents('.product-actions-wrapper, .product-box').siblings('.product-share');
        var template = '<div class="popupOverlay share"></div>' +
            '<div id="popupWrap" class="popupWrap share">' +
            '<div class="popup">' +
            '<div class="border-1 border">' +
            '<div class="product-share">' +
            share.html() +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="close-popup"></div>' +
            '</div>' +
            '</div>';

        $('body').append(template);

        $('.popupWrap').center();
        $('.popupOverlay').css( { display: 'block', opacity:0 } ).animate( { opacity:0.7 }, 500 );
        $('.popupWrap').css( { display: 'block', opacity:0 } ).animate( { opacity:1 }, 500 );

        /** Close popup function **/
        var close_popup = function() {
            $('.popupOverlay').animate( {opacity:0}, 200);
            $('.popupOverlay').remove();
            $('.popupWrap').animate( {opacity:0}, 200);
            $('.popupWrap').remove();
        }

        /**
         *	Close popup on:
         *	- 'X button' click
         *   - wrapper click
         *   - esc key pressed
         **/
        $('.close-popup, .popupOverlay').click(function(){
            close_popup();
        });

        $(document).bind('keydown', function(e) {
            if (e.which == 27) {
                if($('.popupOverlay')) {
                    close_popup();
                }
            }
        });

        /** Center popup on windows resize **/
        $(window).resize(function(){
            $('#popupWrap').center();
        });
    });

    var apply_hover = function(){
        // grid hover
        $('ul.products li:not(.category)').each(function(){
            var $this_item = $(this), to;

            $this_item.on({
                mouseenter: function() {
                    if ( $this_item.hasClass('grid') ) {
                        $this_item.height( $this_item.height()-1 );
                        $this_item.find('.product-actions-wrapper').height( $this_item.find('.product-actions').height() + 20 );
                        if ( $('html').attr('id') == 'ie8' || $('html').attr('id') == 'ie9' ) {
                            $this_item.addClass('js_hover');
                        }
                        clearTimeout(to);
                    }
                },
                mouseleave: function() {
                    if ( $this_item.hasClass('grid') ) {
                        if ( $('html').attr('id') == 'ie8' || $('html').attr('id') == 'ie9' ) {
                            $this_item.removeClass('js_hover');
                        }
                        $this_item.find('.product-actions-wrapper').height( 0 );
                        to = setTimeout(function()
                        {
                            $this_item.css( 'height', 'auto' );
                        },700);
                    }
                }
            });
        });

        //shop sidebar
        if ( $(this).hasClass('widget') ) {
            $(this).find('h3').prepend('<div class="minus" />').on('click', 'h3', function(){
                $(this).parent().find('> *:not(h3)').slideToggle();

                if( $(this).find('div').hasClass('minus') ) {
                    $ery(this).find('div').removeClass('minus').addClass('plus');
                } else {
                    $(this).find('div').removeClass('plus').addClass('minus');
                }
            });
        }
    };

    $(window).on('ajax_layered_page_shown', apply_hover);
    $(document).on('ready yith-wcan-ajax-filtered', apply_hover);

    $(document).on( 'found_variation', 'form.variations_form', function( event, variation ) {
        var $product 	= $(this).parents( '.sidebar').prev( '.content').find( '.product' );

        var $sku 		= $product.find('.product_meta .sku');
        var $weight 	= $product.find('.product_weight');
        var $dimensions = $product.find('.product_dimensions');


        if ( ! $sku.attr( 'data-o_sku' ) )
            $sku.attr( 'data-o_sku', $sku.text() );

        if ( ! $weight.attr( 'data-o_weight' ) )
            $weight.attr( 'data-o_weight', $weight.text() );

        if ( ! $dimensions.attr( 'data-o_dimensions' ) )
            $dimensions.attr( 'data-o_dimensions', $dimensions.text() );


        if ( variation.sku ) {
            $sku.text( variation.sku );
        } else {
            $sku.text( $sku.attr( 'data-o_sku' ) );
        }

        if ( variation.weight ) {
            $weight.text( variation.weight );
        } else {
            $weight.text( $weight.attr( 'data-o_weight' ) );
        }

        if ( variation.dimensions ) {
            $dimensions.text( variation.dimensions );
        } else {
            $dimensions.text( $dimensions.attr( 'data-o_dimensions' ) );
        }
    })
        // On clicking the reset variation button
        .on( 'click', '.reset_variations', function( event ) {
            var $product 	= $(this).parents( '.sidebar').prev( '.content').find( '.product' );
            //$(this).closest('form.variations_form').find('.variations select').val('').change();

            var $sku 		= $product.find('.product_meta').find('.sku');
            var $weight 	= $product.find('.product_weight');
            var $dimensions = $product.find('.product_dimensions');

            if ( $sku.attr( 'data-o_sku' ) )
                $sku.text( $sku.attr( 'data-o_sku' ) );

            if ( $weight.attr( 'data-o_weight' ) )
                $weight.text( $weight.attr( 'data-o_weight' ) );

            if ( $dimensions.attr( 'data-o_dimensions' ) )
                $dimensions.text( $dimensions.attr( 'data-o_dimensions' ) );

            return false;
        } );


    $(document).on('hover', 'ul.products li .thumbnail-wrapper', function(e){
        if(e.type == 'mouseenter') {
            var img = $(this).find('img.image-hover');
            if ( img.length > 0 ) img.css({'display':'block', opacity:0}).animate({opacity:1}, 400);

        } else if ( e.type == 'mouseleave' ) {
            var img = $(this).find('img.image-hover');
            if ( img.length > 0 ) img.animate({opacity:0}, 400);
        }
    });

    if( $( 'body').hasClass( 'single-product' ) ) {
        setTimeout( function() {
            if( $.trim( $( '.usermessagea').html() ) != '' || $.trim( $( '.contact-form li div.msg-error' ).text() ) != '' ) {
                $( 'div.product-extra .woocommerce-tabs .tabs li' ).removeClass( 'active' );
                $( 'div.product-extra .woocommerce-tabs .panel').css( 'display', 'none' );
                $( 'div.product-extra .woocommerce-tabs .tabs li.info_tab' ).addClass( 'active' );
                $( '#tab-info').css( 'display', 'block' );
            }
        }, 200 );
    }
});
