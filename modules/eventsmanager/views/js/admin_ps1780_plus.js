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
    $(function() {
        $('#event_end_date').datetimepicker({ dateFormat: 'yy-mm-dd' });
        $('#event_start_date').datetimepicker({ dateFormat: 'yy-mm-dd' });
        $('#event_streaming_start_time').datetimepicker({ dateFormat: 'yy-mm-dd' });
        $('#event_streaming_end_time').datetimepicker({ dateFormat: 'yy-mm-dd' });
    });
    // Create Table for seatmaps
    createTable();
    // Pdf 
    $("#pdf_status_off").click(function() {
        $("#show_form").show();
    });
    $("#pdf_status_on").click(function() {
        $("#show_form").hide();
    });
    // SeatMap Selection
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
            return;
        } else {
            select_qty++;
            $("#" + event_id + "_select_seat").val(select_qty);
            $(this).toggleClass('selectingSeat');
        }
    }
    $("#search_fmmid").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#table-fme_customer_details tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    $('input:radio[name="EVENTS_THEME"]').change(function() {
        if ($(this).val() == '1') {
            $("#fieldset_1_1").hide();
        }
        if ($(this).val() == '0') {
            $("#fieldset_1_1").show();
        }
    });
    if (controller == 'AdminEvents') {
        setTimeout(function() {
            tinyMCE.init({
                selector: ".default-editor",
                plugins: 'align colorpicker link table media placeholder lists advlist code table autoresize',
                browser_spellcheck: true,
                skin: 'prestashop',
                language: iso_tiny_micy,
                relative_urls: false,
                entity_encoding: 'raw',
                convert_urls: false
                //theme: "modern",
                //plugins: "",
                //theme_advanced_buttons1: "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
                //theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,,|,forecolor,backcolor",
                //theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl,|,fullscreen",
                //theme_advanced_buttons4: "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,pagebreak",
                //theme_advanced_toolbar_location: "top",
                //theme_advanced_toolbar_align: "left",
                //theme_advanced_statusbar_location: "bottom",
                //theme_advanced_resizing: false,
                ////content_css: css_content,
                //document_base_url: base_url,
                //width: "600",
                //height: "auto",
                //font_size_style_values: "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
                //template_external_list_url: "lists/template_list.js",
                //external_link_list_url: "lists/link_list.js",
                //external_image_list_url: "lists/image_list.js",
                //media_external_list_url: "lists/media_list.js",
                //elements: "nourlconvert",
                //entity_encoding: "raw",
                //convert_urls: false,
                //language: iso_tiny_micy
            });
        }, 3000);
    }
});

$(document).on('click', ".cancel_customer_data", function() {
    var result = confirm("Are you Sure?");
    if (result) {
        var id = $(this).attr('id_customer');
        var ajax_url = $("#ajax_url").val();
        $.ajax({
            url: ajax_url,
            method: "post",
            data: {
                id: id,
                ajax: 1,
                action: 'cancelCustomerRecord'
            },
            dataType: "json",
            success: function(data) {

            }
        });
        // swal("Updated Successfully");
        if (typeof swal !== 'undefined' && swal) {
            Swal.fire({
                position: 'top-end',
                type: 'success',
                title: "Updated Successfully",
                icon: "success",
                showConfirmButton: false,
                timer: 1500
            });
        }
    }

});


$(document).on('click', ".ok_customer_data", function() {
    var result = confirm("Are you Sure?");
    if (result) {
        var id = $(this).attr('id_customer');
        var ajax_url = $("#ajax_url").val();
        $.ajax({
            url: ajax_url,
            method: "post",
            data: {
                id: id,
                ajax: 1,
                action: 'okCustomerRecord'
            },
            dataType: "json",
            success: function(data) {

            }
        });
        // swal("Updated Successfully");
        if (typeof swal !== 'undefined' && swal) {
            Swal.fire({
                position: 'top-end',
                type: 'success',
                title: "Updated Successfully",
                icon: "success",
                showConfirmButton: false,
                timer: 1500
            });
        }
    }

});

$(document).on('click', ".delete_customer_data", function() {
    var result = confirm("Are you Sure?");
    if (result) {
        var id = $(this).attr('id_customer');
        var ajax_url = $("#ajax_url").val();
        $.ajax({
            url: ajax_url,
            method: "post",
            data: {
                id: id,
                ajax: 1,
                action: 'deleteCustomerRecord'
            },
            dataType: "json",
            success: function(data) {

            }
        });
        $(this).closest("tr").remove();
    }

});

function reserveBookedSelectMap(id_event, id_product, event_product_id, quantity) {
    $('#seat_maphide').remove();
    $('#table-fme_customer_details').remove();

    var id_event = id_event;
    var event_product_id = event_product_id;
    var id_product = id_product;
    var ajax_url = $("#ajax_url").val();
    $.ajax({
        url: ajax_url,
        method: "post",
        data: {
            id_event: id_event,
            ajax: 1,
            id_product: id_product,
            action: 'getSeatMapAdmin'
        },
        dataType: "json",
        success: function(data) {
            $('#show_map').html('<input type="hidden" id="ideventproduct_ajax" value="' + event_product_id + '"><input type="hidden" id="ajax_id_product" value="' + id_product + '"><input type="hidden" id="' + id_event + '_select_seat" value="0"><input type="hidden" id="idevent_ajax" value="' + id_event + '"><input type="hidden" id="' + id_event + '_quantityajax" value="' + quantity + '"><table border="1" id="row_col_table">' + data[0] + '</table>');            $('#openMap').modal('show');
            var str_all = data[1];
            var str_current = null;
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


function save_map() {
    var id_event = $('#idevent_ajax').val();
    var id_product = $('#ajax_id_product').val();
    var customer_name = $('#event_info_customer_name').val();
    var customer_phone = $('#event_info_customer_phone').val();
    var customer_email = $('#event_info_customer_email').val();
    var event_product_id = $('#ideventproduct_ajax').val();
    var aqty = $('#' + id_event + '_quantityajax').val();
    var sqty = $('#' + id_event + '_select_seat').val();
    var reserve_seat = reserve2();
    var zero = 0;
    var reserve_seat_num = reserve_num();
    var ajax_url = $("#ajax_url").val();
    if (aqty != sqty) {
        $("#error_message").show().delay(500).fadeOut();
        return false;
    }
    if (!customer_phone || !customer_name || !customer_email) {
        $("#req_message").show().delay(500).fadeOut();
        return false;
    }
    $.ajax({
        url: ajax_url,
        method: "post",
        data: {
            id_event: id_event,
            ajax: 1,
            id_product: id_product,
            customer_phone: customer_phone,
            customer_email: customer_email,
            customer_name: customer_name,
            sqty: sqty,
            event_product_id: event_product_id,
            id_customer: zero,
            id_cart: zero,
            reserve_seat: reserve_seat,
            reserve_seat_num: reserve_seat_num,
            action: 'updateCustomerTicketSeatAdmin'
        },
        dataType: "json",
        success: function(data) {
            if (data == 1) {
                $("#update_message").show().delay(1500).fadeOut();
            } else {
                $("#fill_message").show().delay(1500).fadeOut();
            }

        }
    });
}

function reserve2() {
    var str = [];
    // $.each($('#row_col_table tr td').attr("contenteditable", 'true'), function(index, value) {
    //     str.push($(this).attr('title'));
    // });
    $('#row_col_table tr td').filter(function() {
        return $(this).attr("contenteditable") === 'true';
    }).each(function(index, value) {
        str.push($(this).attr('title'));
    });

    return str.join(',');
}

function reserve_num() {
    var str2 = [];
    // $.each($('#row_col_table tr td.selectingSeat'), function(index, value) {
    //     str2.push($(this).text());
    // });
    $('#row_col_table tr td').filter(function() {
        return $(this).attr("contenteditable") === 'true';
    }).each(function(index, value) {
        str2.push($(this).attr('title'));
    });
    return str2.join(',');
}

function openBookedSelectMap() {
    var id_event = $("#id_event").val();
    var id_product = $("#id_product").val();
    var ajax_url = $("#ajax_url").val();
    $.ajax({
        url: ajax_url,
        method: "post",
        data: {
            id_event: id_event,
            ajax: 1,
            id_product: id_product,
            action: 'getSeatMapAdmin'
        },
        dataType: "json",
        success: function(data) {
            var str_all = data[1];
            var temp = new Array();
            var temp_current = new Array();
            var temp_current = '';
            var count = 0;
            var temp = str_all.split(',');

            $.each(temp, function(key, value) {
                $('#td_' + value).addClass('selectedSeat');
            });
        }
    });

}

// Create Table for seatmap
$(document).on('click', ".sello", function() {
    var td = $(this).text();
    $(this).attr("contenteditable", 'true');
    var selected_value = $(this).attr("contenteditable", 'true').attr('title');
    var id_event = $("#idevent_ajax").val();
    var contenteditableCount = $('table [contenteditable="true"]').length;
    $('#' + id_event + '_select_seat').val(contenteditableCount);
});

function show1() {
    document.getElementById('seat_combine').style.display = 'none';
}

function show2() {
    document.getElementById('seat_combine').style.display = 'block';
}


function createTable() {
    $("#row_col_table").empty();
    $("#update_table_button").show();
    var count = 1;
    var rn = $('#seat_rows').val();
    var cn = $('#seat_col').val();

    for (var r = 0; r < parseInt(rn, 10); r++) {
        var x = document.getElementById('row_col_table').insertRow(r);
        for (var c = 0; c < parseInt(cn, 10); c++) {
            // x was tr
            var y = x.insertCell(c);
            y.setAttribute("id", 'td_' + count);
            y.setAttribute("title", +count);
            y.innerHTML = count;
            var count = count + 1;
            $("#row_col_table td").addClass("sello");
        }
    }
}


function updateTable() {
    $('td').each(function() {
        if ($(this).text() == '') {
            $(this).css('background', '#e10e3be6');
            $(this).addClass('fmm_disabled');
            $(this).attr('contenteditable', 'false');
            $(this).removeClass('sello');
        } else if ($(this).text() == 'vip' || $(this).text() == 'VIP') {
            $(this).css('background', '#33d733');
            $(this).addClass('selectedSeat');
            $(this).attr('contenteditable', 'false');
        } else if ($(this).text() != '') {
            $(this).css('background', '#ecf3f6');
            $(this).removeClass('fmm_disabled');
            $(this).addClass('sello');
            $(this).attr('contenteditable', 'false');
        }
    });
    var mysave = $('#row_col_table').html();
    $('#hidden_seats_table').val(mysave);
}