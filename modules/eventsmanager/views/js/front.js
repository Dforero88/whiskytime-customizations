/**
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FMM Modules
 *  @copyright FME Modules 2024
 *  @license   Single domain
 */

$(document).ready(function() {
    // Product Page Seat Map Add to cart button
    hidebtn = $("#hide_btn").val();
    if (hidebtn == 1) {
        $('.add').find(".add-to-cart").attr("disabled", "true");
        $('#add_to_cart').find(".exclusive").attr("disabled", "true");
    }
    $('#save_map_pro').click(function() {

        var event_id = $('#idevent_ajax').val();

        var aqty = $('#' + event_id + '_quantityajax').val();
        var sqty = $('#' + event_id + '_select_seat').val();
        if (sqty < 1) {
            return false;
        }
        id_product = $("#event_info_" + event_id + "_id_product").val();
        id_customer = $("#event_info_" + event_id + "_id_customer").val();
        id_cart = $("#event_info_" + event_id + "_id_cart").val();
        var reserve_seat = reserve2();
        var reserve_seat_num = reserve_num();
        var ajax_url = $("#ajax_url").val();
        $.ajax({
            url: ajax_url,
            method: "post",
            data: {
                id_event: event_id,
                ajax: 1,
                id_product: id_product,
                id_customer: id_customer,
                id_cart: id_cart,
                reserve_seat: reserve_seat,
                reserve_seat_num: reserve_seat_num,
                sqty: sqty,
                action: 'updateCustomerTicketSeatCart'
            },
            dataType: "json",
            success: function(data) {
                if (data == 1) {
                    $("#update_message").show().delay(8500).fadeOut();
                    $("#select_seat_" + event_id).attr("comp", "1");
                    setTimeout(function() {
                        window.location.reload(1);
                    }, 1000);
                } else {
                    $("#cart_message").show().delay(8500).fadeOut();
                }

            }
        });
    });
    
    if (is_all && is_all == 'All') {
        $('#event-paginator').hide();
    }

    // Width
    var widthscreen = $(window).width();
    if (widthscreen < 470) {
        $("tr.mat_event_single_holder td:first-child").css("display", "none");
        $("tr.mat_event_single_holder td:nth-child(2)").css("display", "none");
    }
    // Map mouseover
    if (show_map_hover == 1) {
        $(document).on('mousemove', function(e) {
            cursorX = e.pageX;
            cursorY = e.pageY;
        });
        $(".mapThiss").each(function() {
            var dPlace = $(this).attr("place");
            var dZoom = $(this).attr("zoom");
            var dText = $(this).html();
            $(this).html('<a onmouseover="mapThis.show(this);" style="text-decoration:none; border-bottom:1px dotted #999" href="http://maps.google.com/maps?key=' + static_map_key + '&q=' + dPlace + '&z=' + dZoom + '">' + dText + '</a>');
        });
    }

    // Event Slider
    setValues();
    $('.noclass').fancybox();
    $('#fme-events-slider').sliderPro({
        width: set_width,
        height: set_height,
        fade: true,
        fadeDuration: 2000,
        arrows: set_arrows,
        buttons: set_buttons,
        fullScreen: true,
        fadeFullScreen: true,
        smallSize: 500,
        mediumSize: 1000,
        largeSize: 3000,
        thumbnailArrows: true,
        autoplay: set_autoplay,
        breakpoints: {
            800: {
                thumbnailsPosition: 'bottom',
                thumbnailWidth: 270,
                thumbnailHeight: 100
            },
            500: {
                orientation: 'vertical',
                thumbnailsPosition: 'bottom',
                thumbnailWidth: 120,
                thumbnailHeight: 50
            }
        },
    });
    // Buy Ticket Link
    $("#buy_ticketslink").click(function() {
        $('html,body').animate({
                scrollTop: $(".ticket_section").offset().top
            },
            'slow');
    });
    isPaymentClicked();
    $('#checkout #payment-confirmation').prepend("<p id='payment_error' style='display:none;' class='alert alert-warning warning'>Error: Kindly First complete Ticket Information</p>");
    $('#checkout #payment-confirmation').prepend("<p id='payment_error_update' style='display:none;' class='alert alert-warning warning'>Error: Kindly First Press Update Details Button</p>");

    $("#payment-confirmation").click(function(e) {

        var req_phone = $("#req_phone").val();
        var update_btn_fmm = $("#update_btn_fmm").val();
        var allow_order = $('#allow_order').val();
        var events_in_cart = $('#events_in_cart').val();
        var map_event_in_cart = $('#map_event_in_cart').val();
        var checkArr = new Array();
        var temp_current = events_in_cart.split(',');
        var count = 0;
        $.each(temp_current, function(key, value) {
            var comp = $('#select_seat_' + value).attr('comp');
            if (comp) {
                count++;
            }
        });
        //validate order all data was submit before order or not
        if (count != map_event_in_cart) {
            e.preventDefault;
            $("#payment_error").show().delay(1500).fadeOut();
            return false;
        }

        if (req_phone != 0) {
            if (allow_order > 0) {
                e.preventDeault;
                $("#payment_error").show().delay(1500).fadeOut();
                return false;
            }
        } else {
            if (req_phone == 0 && update_btn_fmm == 0) {
                e.preventDeault;
                $("#payment_error_update").show().delay(1500).fadeOut();
                return false;
            }

        }

    });
    $("#customer_info_submit").click(function() {

        $("#update_btn_fmm").val(1);
        var id_event_in_cart = $("#events_in_cart").val();
        var products_in_cart = $("#products_in_cart").val();
        var req_phone = $("#req_phone").val();

        var res = id_event_in_cart.split(",");
        var res_pro = products_in_cart.split(",");

        var array_data = [];
        var products_in_cart = $("#products_in_cart").val();
        var count = 0;
        var days_id = [];
        for (i = 0; i < res.length; i++) {
            var days_id = [];
            var sub_array = [];
            id_event = $("#event_info_" + res[i] + "_id_event").val();
            id_product = $("#event_info_" + res[i] + "_id_product").val();
            id_product = $("#event_info_" + res[i] + "_id_product_" + res_pro[i]).val();

            id_customer = $("#event_info_" + res[i] + "_id_customer").val();
            id_guest = $("#event_info_" + res[i] + "_id_guest").val();
            quantity = $("#event_info_" + res[i] + "_quantity").val();
            quantity = $("#event_info_" + res[i] + "_quantity_" + res_pro[i]).val();
            id_cart = $("#event_info_" + res[i] + "_id_cart").val();
            customer_name = $("#event_info_" + res[i] + "_customer_name").val();
            customer_phone = $("#event_info_" + res[i] + "_customer_phone").val();



            $('input[id="days_' + id_event + '"]:checked').each(function() {
                // var val = this.value+'-'+id_event;
                var val = this.value;
                days_id.push(val);
            })

            if (!customer_phone) {
                count++;
                $("#allow_order").val(count);
            } else if (!customer_name) {
                count++;
                $("#allow_order").val(count);
            }

            // sub_array.push('id_event: ' + id_event);
            // sub_array.push('id_product: '+ id_product);
            // sub_array.push('id_customer: '+ id_customer);
            // sub_array.push('id_guest: '+ id_guest);
            // sub_array.push('quantity: '+ quantity);

            sub_array.push(id_event);
            sub_array.push(id_product);
            sub_array.push(id_customer);
            sub_array.push(id_guest);
            sub_array.push(quantity);
            sub_array.push(id_cart);
            sub_array.push(customer_name);
            sub_array.push(customer_phone);
            sub_array.push(products_in_cart);
            sub_array.push(days_id);

            array_data.push(sub_array);
        }
        if (count >= 1 && req_phone == 1) {
            $("#error_info").show().delay(1500).fadeOut();
            return false;
        } else {
            $("#allow_order").val(count);
        }
        var id_event = $("#id_event").val();
        var quantity = $("#quantity").val();
        var id_product = $("#id_product").val();
        var id_customer = $("#id_customer").val();
        var id_guest = $("#id_guest").val();

        var ajax_url = $("#ajax_url").val();
        $.ajax({
            url: ajax_url,
            method: "post",
            data: {
                array_data: array_data,
                ajax: 1,
                action: 'updateCustomerTicket'
            },
            dataType: "json",
            success: function(data) {
                $("#done_message").show().delay(1500).fadeOut();

                $("#id_2").attr("disabled", true);
            }
        });
    });

    $('#save_map').click(function() {

        var id_event = $('#idevent_ajax').val();
        var event_product_id = $('#ideventproduct_ajax').val();
        var aqty = $('#' + id_event + '_quantityajax').val();
        var sqty = $('#' + id_event + '_select_seat').val();
        if (aqty != sqty) {
            $("#error_message").show().delay(1500).fadeOut();
            $("#t_seats").text(aqty);
            return false;
        }
        id_product = $("#event_info_" + id_event + "_id_product").val();
        id_customer = $("#event_info_" + id_event + "_id_customer").val();
        id_cart = $("#event_info_" + id_event + "_id_cart").val();

        //id_product =  $("#event_info_"+event_product_id+"_event_product_id").val();


        var reserve_seat = reserve2();
        var reserve_seat_num = reserve_num();
        var ajax_url = $("#ajax_url").val();
        $.ajax({
            url: ajax_url,
            method: "post",
            data: {
                id_event: id_event,
                ajax: 1,
                id_product: id_product,
                event_product_id: event_product_id,
                id_customer: id_customer,
                id_cart: id_cart,
                reserve_seat: reserve_seat,
                reserve_seat_num: reserve_seat_num,
                action: 'updateCustomerTicketSeat'
            },
            dataType: "json",
            success: function(data) {
                $("#update_message").show().delay(8500).fadeOut();
                $("#select_seat_" + id_event).attr("comp", "1");
            }
        });

    });

});

function openSelectMapPro(id_event, quantity, id_product) {
    var id_event = id_event;
    var id_product = id_product;
    id_customer = $("#event_info_" + id_event + "_id_customer").val();
    id_cart = $("#event_info_" + id_event + "_id_cart").val();
    var ajax_url = $("#ajax_url").val();
    var wait_min = $("#wait_min").val();

    $.ajax({
        url: ajax_url,
        method: "post",
        data: {
            id_event: id_event,
            wait_min: wait_min,
            ajax: 1,
            id_product: id_product,
            id_cart: id_cart,
            id_customer: id_customer,
            action: 'getSeatMap'
        },
        dataType: "json",
        success: function(data) {
            $('#show_map').html('<input type="hidden" id="' + id_event + '_select_seat" value="0"><input type="hidden" id="idevent_ajax" value="' + id_event + '"><input type="hidden" id="' + id_event + '_quantityajax" value="' + quantity + '"><table border="1" id="row_col_table">' + data[0] + '</table>');
            $('#openMap').show();
            var str_all = data[1];
            var str_current = data[2];

            var temp = new Array();
            var temp_current = new Array();
            var temp_current = '';
            var count = 0;
            var temp = str_all.split(',');
            if (str_current) {
                var temp_current = str_current.split(',');
            }


            $.each(temp, function(key, value) {
                $('#td_' + value).addClass('selectedSeat');
            });
            $.each(temp_current, function(key, value) {
                count++;
                $('#td_' + value).removeClass('selectedSeat');
                $('#td_' + value).addClass('selectingSeat');
            });

            $("#" + id_event + "_select_seat").val(count);
        }
    });

}

$(document).on("click", ".sello", function() {
    var select_qty = 0;
    var event_id = $("#idevent_ajax").val();

    var select_qty = $("#" + event_id + "_select_seat").val();

    if ($(this).hasClass('selectedSeat')) {
        // swal("Already Reserved");
        if (typeof swal !== 'undefined' && swal) {
            Swal.fire({
                position: 'top-end',
                type: 'warning',
                title: "Already Reserved",
                icon: "warning",
                showConfirmButton: false,
                timer: 1500
            });
        }
    } else {
        if ($(this).hasClass('selectingSeat')) {
            select_qty--;
            $("#" + event_id + "_select_seat").val(select_qty);
            $(this).toggleClass('selectingSeat');
        } else {
            $(this).toggleClass('selectingSeat');
            select_qty++;
            $("#" + event_id + "_select_seat").val(select_qty);
        }
        return;
    }
});

function openSelectMap(id_event, quantity, id_product, event_product_id) {
    var id_event = id_event;
    var id_product = id_product;
    var event_product_id = event_product_id;

    id_customer = $("#event_info_" + id_event + "_id_customer").val();
    id_cart = $("#event_info_" + id_event + "_id_cart").val();
    var ajax_url = $("#ajax_url").val();
    var wait_min = $("#wait_min").val();

    $.ajax({
        url: ajax_url,
        method: "post",
        data: {
            id_event: id_event,
            wait_min: wait_min,
            ajax: 1,
            id_product: id_product,
            id_cart: id_cart,
            id_customer: id_customer,
            action: 'getSeatMap'
        },
        dataType: "json",
        success: function(data) {
            $('#show_map').html('<input type="hidden" id="' + id_event + '_select_seat" value="0"><input type="hidden" id="idevent_ajax" value="' + id_event + '"><input type="hidden" id="ideventproduct_ajax" value="' + event_product_id + '"><input type="hidden" id="' + id_event + '_quantityajax" value="' + quantity + '"><table border="1" id="row_col_table">' + data[0] + '</table>');
            $('#openMap').modal('show');
            var str_all = data[1];
            var str_current = data[2];
            var temp = new Array();
            var temp_current = new Array();
            var temp_current = '';
            var count = 0;
            var temp = str_all.split(',');

            if (str_current) {
                var temp_current = str_current.split(',');
            }


            $.each(temp, function(key, value) {
                $('#td_' + value).addClass('selectedSeat');
            });
            $.each(temp_current, function(key, value) {
                count++;
                $('#td_' + value).removeClass('selectedSeat');
                $('#td_' + value).addClass('selectingSeat');
            });

            $("#" + id_event + "_select_seat").val(count);
        }
    });

}


// $('#save_map').click(function() {

//     var id_event = $('#idevent_ajax').val();
//     var event_product_id = $('#ideventproduct_ajax').val();
//     var aqty = $('#' + id_event + '_quantityajax').val();
//     var sqty = $('#' + id_event + '_select_seat').val();
//     if (aqty != sqty) {
//         $("#error_message").show().delay(1500).fadeOut();
//         $("#t_seats").text(aqty);
//         return false;
//     }
//     id_product = $("#event_info_" + id_event + "_id_product").val();
//     id_customer = $("#event_info_" + id_event + "_id_customer").val();
//     id_cart = $("#event_info_" + id_event + "_id_cart").val();

//     //id_product =  $("#event_info_"+event_product_id+"_event_product_id").val();


//     var reserve_seat = reserve2();
//     var reserve_seat_num = reserve_num();
//     var ajax_url = $("#ajax_url").val();
//     $.ajax({
//         url: ajax_url,
//         method: "post",
//         data: {
//             id_event: id_event,
//             ajax: 1,
//             id_product: id_product,
//             event_product_id: event_product_id,
//             id_customer: id_customer,
//             id_cart: id_cart,
//             reserve_seat: reserve_seat,
//             reserve_seat_num: reserve_seat_num,
//             action: 'updateCustomerTicketSeat'
//         },
//         dataType: "json",
//         success: function(data) {
//             $("#update_message").show().delay(8500).fadeOut();
//             $("#select_seat_" + id_event).attr("comp", "1");
//         }
//     });


// });

function reserve2() {
    var str = [];
    $.each($('#row_col_table tr td.selectingSeat'), function(index, value) {
        str.push($(this).attr('title'));
    });
    return str.join(',');
    // var count = $('#row_col_table tr td.selectingSeat').length;
    // return count;
}

function reserve_num() {
    var str2 = [];
    $.each($('#row_col_table tr td.selectingSeat'), function(index, value) {
        str2.push($(this).text());
    });
    return str2.join(',');
}


function isPaymentClicked() {
    $('#HOOK_PAYMENT .payment_module').prepend("<p id='payment_error' style='display:none;' class='alert alert-warning warning'>Error: Kindly First complete Ticket Information</p>");
    $(".payment_module").click(function(e) {
        var allow_order = $('#allow_order').val();
        var req_phone = $("#req_phone").val();
        var events_in_cart = $('#events_in_cart').val();
        var map_event_in_cart = $('#map_event_in_cart').val();
        var update_btn_fmm = $("#update_btn_fmm").val();
        var checkArr = new Array();
        var temp_current = events_in_cart.split(',');
        var count = 0;
        $.each(temp_current, function(key, value) {
            var comp = $('#select_seat_' + value).attr('comp');
            if (comp) {
                count++;
            }
        });
        if (count != map_event_in_cart) {
            e.preventDefault;
            $("#payment_error").show().delay(1500).fadeOut();
            return false;
        }

        if (req_phone != 0) {
            if (allow_order > 0) {
                e.preventDeault;
                $("#payment_error").show().delay(1500).fadeOut();
                return false;
            }
        } else {
            if (req_phone == 0 && update_btn_fmm == 0) {
                e.preventDeault;
                $("#payment_error_update").show().delay(1500).fadeOut();
                return false;
            }

        }

    });
}

function stringToJSON(string) {
    var pairs = string.split('&');
    var result = {};
    pairs.forEach(function(pair) {
        pair = pair.split('=');
        result[pair[0]] = decodeURIComponent(pair[1] || '').replace(/\+/g, ' ');
    });
    return JSON.parse(JSON.stringify(result));
}

function myMap(lati, lngi) {
    var mapProp = {
        center: new google.maps.LatLng(lati, lngi),
        zoom: 9,
    };

    var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
    var myCenter = {
        lat: lati,
        lng: lngi
    };

    var marker = new google.maps.Marker({
        position: myCenter
    });

    marker.setMap(map);
    marker.setAnimation(google.maps.Animation.DROP);

    $('#openMapgoogle').modal('show');
    $('html,body').animate({
            scrollTop: $("#openMapgoogle").offset().top
        },
        'slow');
}

function myCustomFunction() {
    $('#save_map').click(function() {

        var id_event = $('#idevent_ajax').val();
        var event_product_id = $('#ideventproduct_ajax').val();
        var aqty = $('#' + id_event + '_quantityajax').val();
        var sqty = $('#' + id_event + '_select_seat').val();
        if (aqty != sqty) {
            $("#error_message").show().delay(1500).fadeOut();
            $("#t_seats").text(aqty);
            return false;
        }
        id_product = $("#event_info_" + id_event + "_id_product").val();
        id_customer = $("#event_info_" + id_event + "_id_customer").val();
        id_cart = $("#event_info_" + id_event + "_id_cart").val();

        //id_product =  $("#event_info_"+event_product_id+"_event_product_id").val();


        var reserve_seat = reserve2();
        var reserve_seat_num = reserve_num();
        var ajax_url = $("#ajax_url").val();
        $.ajax({
            url: ajax_url,
            method: "post",
            data: {
                id_event: id_event,
                ajax: 1,
                id_product: id_product,
                event_product_id: event_product_id,
                id_customer: id_customer,
                id_cart: id_cart,
                reserve_seat: reserve_seat,
                reserve_seat_num: reserve_seat_num,
                action: 'updateCustomerTicketSeat'
            },
            dataType: "json",
            success: function(data) {
                $("#update_message").show().delay(8500).fadeOut();
                $("#select_seat_" + id_event).attr("comp", "1");
            }
        });


    });
    $("#customer_info_submit").click(function() {
        $("#update_btn_fmm").val(1);
        var id_event_in_cart = $("#events_in_cart").val();
        var req_phone = $("#req_phone").val();

        var res = id_event_in_cart.split(",");
        var array_data = [];
        var products_in_cart = $("#products_in_cart").val();
        var count = 0;
        for (i = 0; i < res.length; i++) {
            var sub_array = [];
            id_event = $("#event_info_" + res[i] + "_id_event").val();
            id_product = $("#event_info_" + res[i] + "_id_product").val();
            id_customer = $("#event_info_" + res[i] + "_id_customer").val();
            id_guest = $("#event_info_" + res[i] + "_id_guest").val();
            quantity = $("#event_info_" + res[i] + "_quantity").val();

            id_cart = $("#event_info_" + res[i] + "_id_cart").val();
            customer_name = $("#event_info_" + res[i] + "_customer_name").val();
            customer_phone = $("#event_info_" + res[i] + "_customer_phone").val();

            if (!customer_phone) {
                count++;
                $("#allow_order").val(count);
            } else if (!customer_name) {
                count++;
                $("#allow_order").val(count);
            }

            sub_array.push(id_event);
            sub_array.push(id_product);
            sub_array.push(id_customer);
            sub_array.push(id_guest);
            sub_array.push(quantity);
            sub_array.push(id_cart);
            sub_array.push(customer_name);
            sub_array.push(customer_phone);
            sub_array.push(products_in_cart);

            array_data.push(sub_array);
        }
        if (count >= 1 && req_phone == 1) {
            $("#error_info").show().delay(1500).fadeOut();
            return false;
        } else {
            $("#allow_order").val(count);
        }
        var id_event = $("#id_event").val();
        var quantity = $("#quantity").val();
        var id_product = $("#id_product").val();
        var id_customer = $("#id_customer").val();
        var id_guest = $("#id_guest").val();

        var ajax_url = $("#ajax_url").val();
        $.ajax({
            url: ajax_url,
            method: "post",
            data: {
                array_data: array_data,
                ajax: 1,
                action: 'updateCustomerTicket'
            },
            dataType: "json",
            success: function(data) {
                $("#done_message").show().delay(1500).fadeOut();

                $("#id_2").attr("disabled", true);
            }
        });
    });

}

function cart_update(id_product) {
    var qty = $(".qty_pro_fmm" + id_product).val();
    var ajax_cart = $("#fmm_cartlink").val();
    $.ajax({
        url: ajax_cart,
        method: "post",
        data: {
            qty: qty,
            id_product: id_product,
            ajax: true,
            action: 'update'
        },
        dataType: "json",
        success: function(data) {
            if (data['hasError'] == true) {
                $.simplyToast('warning', 'Product is Not Available with this Quantity');
            } else {
                $.simplyToast('success', 'Product successfully added to your shopping cart');
            }

        }
    });
}

function fmmup(id_product) {

    var qty = $(".qty_pro_fmm" + id_product).val();
    var newVal = parseFloat(qty) + 1;

    $(".qty_pro_fmm" + id_product).val(newVal);

}

function fmmdown(id_product) {

    var qty = $(".qty_pro_fmm" + id_product).val();
    if (qty < 1) {
        return false;
    }
    var newVal = parseFloat(qty) - 1;

    $(".qty_pro_fmm" + id_product).val(newVal);

}

// setting default values for slider
function setValues() {
    if (!set_width)
        set_width = 960;
    else if (!set_height)
        set_height = 500;
    if (set_arrows == 1)
        set_arrows = true;
    else if (set_arrows == 0)
        set_arrows = false;

    if (set_buttons == 1)
        set_buttons = true;
    else if (set_buttons == 0)
        set_buttons = false;

    if (set_thumbnailArrows == 1)
        set_thumbnailArrows = true;
    else if (set_thumbnailArrows == 0)
        set_thumbnailArrows = false;

    if (set_autoplay == 1)
        set_autoplay = true;
    else if (set_autoplay == 0)
        set_autoplay = false;
}
// Intilize Map
function initializeMap() {
    var geocoder;
    var map;
    var address = $(".mapThis").attr("place");
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(-34.397, 150.644);
    var myOptions = {
        zoom: 10,
        center: latlng,
        mapTypeControl: true,
        mapTypeControlOptions: { style: google.maps.MapTypeControlStyle.DROPDOWN_MENU },
        navigationControl: true,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    if (geocoder) {
        geocoder.geocode({ 'address': address }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
                    map.setCenter(results[0].geometry.location);

                    var infowindow = new google.maps.InfoWindow({
                        content: '<b>' + address + '</b>',
                        size: new google.maps.Size(150, 50)
                    });

                    var marker = new google.maps.Marker({
                        position: results[0].geometry.location,
                        map: map,
                        title: address
                    });
                    google.maps.event.addListener(marker, 'click', function() {
                        infowindow.open(map, marker);
                    });

                } else {}
            } else {

            }
        });
    }
}
// Map on hover
if (show_map_hover == 1) {
    var mapThis = function() {
        var tt;
        var errorBox;
        return {
            show: function(v) {
                if (tt == null) {
                    var pNode = v.parentNode;
                    pPlace = jQuery(pNode).attr("place");
                    pZoom = parseInt(jQuery(pNode).attr("zoom"));
                    pText = jQuery(v).html();
                    tt = document.createElement('div');
                    jQuery(tt).html('<a href="https://maps.google.com/maps?key=' + static_map_key + '&q=' + pPlace + '&z=11" target="new"><img border=0 src="https://maps.google.com/maps/api/staticmap?key=' + static_map_key + '&center=' + pPlace + '&zoom=' + pZoom + '&size=300x300&sensor=false&format=png&markers=color:blue|' + pPlace + '"></a>');
                    jQuery(tt).on('mouseover', function() { mapHover = 1; });
                    jQuery(tt).on('mouseout', function() { mapHover = 0; });
                    jQuery(tt).on('mouseout', mapThis.hide);
                    document.body.appendChild(tt);
                }
                fromleft = cursorX;
                fromtop = cursorY;
                fromleft = fromleft - 25;
                fromtop = fromtop - 25;
                tt.style.cssText = "position:absolute; left:" + fromleft + "px; top:" + fromtop + "px; z-index:999; display:block; padding:1px; margin-left:5px; background-color:#333; width:302px; -moz-box-shadow:0 1px 10px rgba(0, 0, 0, 0.5);";
                tt.style.display = 'block';
            },
            hide: function() {
                tt.style.display = 'none';
                tt = null;
            }
        };
    }();
}
// Unavailable Ticket
function unAvaliable(ticket) {
    $('#ticket-unavailable').show().html(ticket + unavailable).fadeOut(10000);
}

// Seat map product page 
function openSelectMapProduct(id_event, quantity, id_product, event_product_id) {
    var id_event = id_event;
    var id_product = id_product;
    var event_product_id = event_product_id;

    id_customer = $("#event_info_" + id_event + "_id_customer").val();
    id_cart = $("#event_info_" + id_event + "_id_cart").val();
    var ajax_url = $("#ajax_url").val();
    var wait_min = $("#wait_min").val();

    $.ajax({
        url: ajax_url,
        method: "post",
        data: {
            id_event: id_event,
            wait_min: wait_min,
            ajax: 1,
            id_product: id_product,
            id_cart: id_cart,
            id_customer: id_customer,
            action: 'getSeatMap'
        },
        dataType: "json",
        success: function(data) {
            $('#show_map').html('<input type="hidden" id="' + id_event + '_select_seat" value="0"><input type="hidden" id="idevent_ajax" value="' + id_event + '"><input type="hidden" id="ideventproduct_ajax" value="' + event_product_id + '"><input type="hidden" id="' + id_event + '_quantityajax" value="' + quantity + '"><table border="1" id="row_col_table">' + data[0] + '</table>');

            var str_all = data[1];
            var str_current = data[2];
            var temp = new Array();
            var temp_current = new Array();
            var temp_current = '';
            var count = 0;
            var temp = str_all.split(',');

            if (str_current) {
                var temp_current = str_current.split(',');
            }


            $.each(temp, function(key, value) {
                $('#td_' + value).addClass('selectedSeat');
            });
            $.each(temp_current, function(key, value) {
                count++;
                $('#td_' + value).removeClass('selectedSeat');
                $('#td_' + value).addClass('selectingSeat');
            });

            $("#" + id_event + "_select_seat").val(count);
        }
    });

}

//Hook paymentTop openSelectMapProduct
function openSelectMapP(id_event, quantity, id_product, event_product_id) {
    var id_event = id_event;
    var id_product = id_product;
    var event_product_id = event_product_id;

    id_customer = $("#event_info_" + id_event + "_id_customer").val();
    id_cart = $("#event_info_" + id_event + "_id_cart").val();
    var ajax_url = $("#ajax_url").val();
    var wait_min = $("#wait_min").val();

    $.ajax({
        url: ajax_url,
        method: "post",
        data: {
            id_event: id_event,
            wait_min: wait_min,
            ajax: 1,
            id_product: id_product,
            id_cart: id_cart,
            id_customer: id_customer,
            action: 'getSeatMap'
        },
        dataType: "json",
        success: function(data) {
            $('#show_map').html('<input type="hidden" id="' + id_event + '_select_seat" value="0"><input type="hidden" id="idevent_ajax" value="' + id_event + '"><input type="hidden" id="ideventproduct_ajax" value="' + event_product_id + '"><input type="hidden" id="' + id_event + '_quantityajax" value="' + quantity + '"><table border="1" id="row_col_table">' + data[0] + '</table>');
            $('#openMap').modal('show');
            var str_all = data[1];
            var str_current = data[2];
            var temp = new Array();
            var temp_current = new Array();
            var temp_current = '';
            var count = 0;
            var temp = str_all.split(',');

            if (str_current) {
                var temp_current = str_current.split(',');
            }


            $.each(temp, function(key, value) {
                $('#td_' + value).addClass('selectedSeat');
            });
            $.each(temp_current, function(key, value) {
                count++;
                $('#td_' + value).removeClass('selectedSeat');
                $('#td_' + value).addClass('selectingSeat');
            });

            $("#" + id_event + "_select_seat").val(count);
        }
    });

}

// Sort events for modern theme

function sortUnorderedGrid(ul, sortDescending) {
    if (typeof ul == "string")
        ul = document.getElementById(ul);

    var lis = ul.getElementsByTagName("li");
    var vals = [];

    for (var i = 0, l = lis.length; i < l; i++)
        vals.push(lis[i].innerHTML);

    vals.sort();

    if (sortDescending)
        vals.reverse();

    for (var i = 0, l = lis.length; i < l; i++)
        lis[i].innerHTML = vals[i];
}

function sortGridDate() {
    var container = $("#gridli");
    var items = $(".grid");

    items.each(function() {
        // Convert the string in 'data-event-date' attribute to a more
        // standardized date format
        var BCDate = $(this).attr("data-event-date").split("-");
        var standardDate = BCDate[1] + " " + BCDate[0] + " " + BCDate[2];
        standardDate = new Date(standardDate).getTime();
        $(this).attr("data-event-date", standardDate);

    });

    items.sort(function(a, b) {
        a = parseFloat($(a).attr("data-event-date"));
        b = parseFloat($(b).attr("data-event-date"));
        return a > b ? -1 : a < b ? 1 : 0;
    }).each(function() {
        container.prepend(this);
    });
}
if (fmm_theme == 1 && controller == 'events' && event_id == null) {
    function fmmSortDate() {
        var desc = false;
        sortGridDate("gridli", desc);
    }

    function fmmSortName() {
        var desc = false;
        sortUnorderedGrid("gridli", desc);
        desc = !desc;
        return false;
    }

    function fmmSortListDate() {
        var desc = false;
        sortListDate("listli", desc);
    }

    function fmmSortListName() {
        var desc = false;
        sortUnorderedList("listli", desc);
        desc = !desc;
        return false;
    }

}

// function sortUnorderedList(ul, sortDescending) {
//     if (typeof ul == "string")
//         ul = document.getElementById(ul);

//     var lis = ul.getElementsByClassName("list");
//     var vals = [];

//     for (var i = 0, l = lis.length; i < l; i++)
//         vals.push(lis[i].innerHTML);

//     vals.sort();

//     if (sortDescending)
//         vals.reverse();

    // for (var i = 0, l = lis.length; i < l; i++)
    //     lis[i] = vals[i];
// }

//changed
function sortUnorderedList(ul, sortDescending) {
    if (typeof ul === "string")
        ul = document.getElementById(ul);

    var lis = Array.from(ul.querySelectorAll("li.list"));
    console.log(lis);

    lis.sort(function(a, b) {
        var valA = a.getAttribute("data-ex") || '';
        var valB = b.getAttribute("data-ex") || '';
        return valA.localeCompare(valB); // For string sort
    });

    if (sortDescending)
        lis.reverse();

    lis.forEach(li => ul.removeChild(li));

    let firstNonList = Array.from(ul.children).find(li => !li.classList.contains("list"));

    lis.forEach(li => {
        if (firstNonList) {
            ul.insertBefore(li, firstNonList);
        } else {
            ul.appendChild(li);
        }
    });
    
    // Append sorted elements back to the UL
    // lis.forEach(function(li) {
    //     ul.appendChild(li);
    // });
    // for (var i = 0, l = lis.length; i < l; i++)
    //     ul.appendChild(lis[i]);
}


function sortListDate() {
    var container = $("#listli");
    var items = $(".list");

    items.each(function() {
        // Convert the string in 'data-event-date' attribute to a more
        // standardized date format
        var BCDate = $(this).attr("data-event-date").split("-");
        var standardDate = BCDate[1] + " " + BCDate[0] + " " + BCDate[2];
        standardDate = new Date(standardDate).getTime();
        $(this).attr("data-event-date", standardDate);

    });

    items.sort(function(a, b) {
        a = parseFloat($(a).attr("data-event-date"));
        b = parseFloat($(b).attr("data-event-date"));
        return a > b ? -1 : a < b ? 1 : 0;
    }).each(function() {
        container.prepend(this);
    });
}