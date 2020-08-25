
jQuery.validator.addMethod("dollarsscents", function (value, element) {
    return this.optional(element) || /^\d{0,4}(\.\d{0,2})?$/i.test(value);
}, "Please enter a numerical amount up to 2 decimal places only.");

jQuery.validator.addMethod("regex", function (value, element) {
    return this.optional(element) || /^(?=.*?[A-Z])(?=.*?[#?!@$%^&*-]).{6,}$/i.test(value);
}, "Password must be of minimum 6 characters having one capital and one special character.");


$('#login-form').validate({
    onkeyup: false,
    rules: {
        'LoginForm[email]': {required: true, email: true},
        'LoginForm[password]': {required: true},
    },
    messages: {
        'LoginForm[email]': {required: 'Please enter email.', email: 'Please enter valid email.'},
        'LoginForm[password]': {required: "Please enter password."},
    }
});
$('#password-reset-form').validate({
    onkeyup: false,
    rules: {
        'newPassword': {required: true},
        'confirmPassword': {required: true, equalTo: '#newPassword'},
    },
    messages: {
        'newPassword': {required: "Please enter password."},
        'confirmPassword': {required: "Please enter confirm password.", equalTo: 'Password and confirm password does not match.'},
    }
});
$('#reset-form').validate({
    onkeyup: false,
    rules: {
        'email1': {required: true, email: true},
    },
    messages: {
        'email1': {required: 'Please enter email.', email: 'Please enter valid email.'},
    }
});
$('#Vendor').validate({
    rules: {
        'Vendors[name]': {required: true},
        'Vendors[email]': {required: true, email: true},
        'Vendors[phone]': {required: true, minlength: 10},
        'Vendors[password]': {required: true, regex: true},
        'Vendors[password_repeat]': {required: true, equalTo: '#vendors-password'}
    },
    messages: {
        'Vendors[name]': {required: "Please enter vendor name."},
        'Vendors[email]': {required: "Please enter vendor email.", email: 'Please enter valid email.'},
        'Vendors[phone]': {required: "Please enter phone number.", minlength: 'Please enter valid phone number.'},
        'Vendors[password]': {required: 'Please enter password.'},
        'Vendors[password_repeat]': {required: 'Please enter confirm password.', equalTo: 'Password and confirm password does not match.'}
    }
});


$('#VendorUpdate1').validate({
    rules: {
        'Vendors[shop_name]': {required: true},
        'Vendors[address1]': {required: true},
        'Vendors[city]': {required: true},
        'Vendors[state]': {required: true},
        'Vendors[country_id]': {required: true},
        'Vendors[shop_description]': {required: true},
//        'Vendors[tax_vat_number]': {required: true},
        'category[]': {required: true},
    },
    messages: {
        'Vendors[shop_name]': {required: 'Please enter shop name.'},
        'Vendors[address1]': {required: 'Please enter address line 1.'},
        'Vendors[city]': {required: 'Please enter city.'},
        'Vendors[state]': {required: 'Please enter state.'},
        'Vendors[country_id]': {required: 'Please select country.'},
        'Vendors[shop_description]': {required: 'Please enter shor description.'},
//        'Vendors[tax_vat_number]': {required: 'Please enter tax/vat number.'},
        'category[]': {required: 'Please select category.'},
    }
});

$('#VendorUpdate').validate({
    rules: {
        'Vendors[password]': {regex: true},
        'Vendors[password_repeat]': {equalTo: '#vendors-password'}
    },
    messages: {
        'Vendors[password_repeat]': {equalTo: 'Password and confirm password does not match.'}
    }
});

$('#roleSave').validate({
    rules: {
        'UserRolesSearch[name]': {required: true}
    },
    messages: {
        'UserRolesSearch[name]': {required: 'Please enter role title.'}
    }
});

$('#productUpdate').validate({
    rules: {
        'product_code': {required: true},
        'vendor': {required: true},
        'name': {required: true},
        'description': {required: true},
        'sku': {required: true},
        'category_id': {required: true},
        'variationQty[]': {required: true},
        'image': {extension: "png|jpg|jpeg"},
        'featured_image_color': {extension: "png|jpg|jpeg"},
        'other_color_images[]': {extension: "png|jpg|jpeg"},
    },
    messages: {
        'product_code': {required: 'Please enter product code.'},
        'vendor': {required: 'Plese enter vendor name.'},
        'name': {required: 'Plese enter product name.'},
        'description': {required: 'Please enter vendor description.'},
        'sku': {required: 'Please enter vendor sku.'},
        'category_id': {required: 'Please enter category.'},
        'variationQty[]': {required: "Please enter qty."},
        'image': {extension: "Please upload valid file."},
        'featured_image_color': {extension: "Please upload valid file."},
        'other_color_images[]': {extension: "Please upload valid files."},
    }
});

$('#productSave').validate({
    rules: {
        'vendor': {required: true},
        'name': {required: true},
        'category': {required: true},
        'description': {required: true},
        'sku': {required: true},
        'image': {extension: "png|jpg|jpeg"},
    },
    messages: {
        'vendor': {required: 'Plese select vendor.'},
        'name': {required: 'Plese enter product name.'},
        'category': {required: 'Plese select category.'},
        'description': {required: 'Please enter product description.'},
        'sku': {required: 'Please enter product sku.'},
        'image': {extension: "Please upload valid file."},
    }
});

$('#adminUser').validate({
    onkeyup: false,
    rules: {
        'User[first_name]': {required: true},
        'User[email]': {required: true, email: true},
        'User[user_role]': {required: true},
        'User[password]': {required: true, regex: true},
        'User[confirmPassword]': {required: true, equalTo: '#password'},
    },
    messages: {
        'User[first_name]': {required: 'Plese enter full name.'},
        'User[email]': {required: 'Plese enter email.', email: 'Please enter valid email.'},
        'User[user_role]': {required: 'Please select user role.'},
        'User[password]': {required: 'Please enter password.'},
        'User[confirmPassword]': {required: 'Please enter confirm password.', equalTo: 'Password and confirm password does not match.'},
    }
});

$('#adminUserUpadte').validate({
    onkeyup: false,
    rules: {
        'User[first_name]': {required: true},
        'User[email]': {required: true, email: true},
        'User[user_role]': {required: true},
        'User[confirmPassword]': {equalTo: '#password'},
    },
    messages: {
        'User[first_name]': {required: 'Plese enter full name.'},
        'User[email]': {required: 'Plese enter email.', email: 'Please enter valid email.'},
        'User[user_role]': {required: 'Please select user role.'},
        'User[password]': {regex: true},
        'User[confirmPassword]': {equalTo: 'Password and confirm password does not match.'},
    }
});

$('#frmConfig').validate({
    onkeyup: false,
    rules: {
        'configuration[tax_rate]': {required: true, max: 100, dollarsscents: true},
        'configuration[shopping_rate]': {required: true, dollarsscents: true},
        'configuration[vendor_shipping_deadline]': {required: true},
        'configuration[estimated_delivery_days]': {required: true}
    },
    messages: {
        'configuration[tax_rate]': {required: 'Please enter tax rate.', max: 'Please enter tax rate less than or equal to {0}.', dollarsscents: 'Please enter a numerical amount up to 2 decimal places only.'},
        'configuration[shopping_rate]': {required: 'Please enter shipping rate.', dollarsscents: 'Please enter a numerical amount up to 2 decimal places only.'},
        'configuration[vendor_shipping_deadline]': {required: 'Please enter vendor shipping deadline.'},
        'configuration[estimated_delivery_days]': {required: 'Please enter estimated delivery days.'}
    }
});

$('#customerSave').validate({
    rules: {
        'User[first_name]': {required: true},
        'User[email]': {required: true, email: true},
        'User[phone]': {required: true, minlength: 10},
        'User[billing_address1]': {required: true},
        'User[billing_city]': {required: true},
        'User[billing_country]': {required: true},
        'User[confirm_password]': {equalTo: '#user-password'},
    },
    messages: {
        'User[first_name]': {required: 'Please enter full name.'},
        'User[email]': {required: 'Please enter email', email: 'Please enter valid email'},
        'User[phone]': {required: "Please enter phone number.", minlength: 'Please enter valid phone number.'},
        'User[billing_address1]': {required: 'Please enter address line 1.'},
        'User[billing_city]': {required: 'Please enter city.'},
        'User[billing_country]': {required: 'Please select country.'},
        'User[confirm_password]': {equalTo: 'Password and confirm password does not match.'},
    }
});

$('#orderUpdate').validate({
//    onkeyup:false,
    rules: {
        'txtBillingAdd1': {required: true},
        'txtBillingCity': {required: true},
        'txtBillingZip': {required: true},
        'txtBillingPhone': {required: true},
        'cmbBillingCountry': {required: true},
        'txtShippingAdd1': {required: true},
        'txtShippingCity': {required: true},
        'txtShippingZip': {required: true},
        'txtShippingPhone': {required: true},
        'cmbShippingCountry': {required: true},
    },
    messages: {
        'txtBillingAdd1': {required: "Please enter billing address."},
        'txtBillingCity': {required: "Please enter billing city."},
        'txtBillingZip': {required: "Please enter billing zipcode."},
        'txtBillingPhone': {required: "Please enter billing phone number."},
        'cmbBillingCountry': {required: "Please select billing country."},
        'txtShippingAdd1': {required: "Please enter shipping address."},
        'txtShippingCity': {required: "Please enter shipping city."},
        'txtShippingZip': {required: "Please enter shipping zipcode."},
        'txtShippingPhone': {required: "Please enter shipping phone number."},
        'cmbShippingCountry': {required: "Please select shipping country."},
    }
});

$('#shipmentUpdate').validate({
    onkeyup: false,
    rules: {
        'carrier': {required: true},
        'traking_number': {required: true},
        'shipped_date': {required: true},
        'shipment_from': {required: true},
        'shipment_to': {required: true},
//        'shipment_note': {required: true},
    },
    messages: {
        'carrier': {required: "Please enter carrier."},
        'traking_number': {required: "Please enter tracking number."},
        'shipped_date': {required: "Please select shipping date."},
        'shipment_from': {required: "Please select origin."},
        'shipment_to': {required: "Please enter destination."},
//        'shipment_note': {required: "Please enter shipping note."},
    }
});

$('#shipmentAdd').validate({
    onkeyup: false,
    rules: {
        'carrier': {required: true},
        'traking_number': {required: true},
        'shipped_date': {required: true},
        'shipment_from': {required: true},
        'shipment_to': {required: true},
//        'shipment_note': {required: true},
        'productId[]': {required: true},
    },
    messages: {
        'carrier': {required: "Please enter carrier."},
        'traking_number': {required: "Please enter tracking number."},
        'shipped_date': {required: "Please select shipped date."},
        'shipment_from': {required: "Please select origin."},
        'shipment_to': {required: "Please enter destination."},
//        'shipment_note': {required: "Please enter shipping note."},
        'productId[]': {required: "Please select product(s)."},
    }
});

$('#addCollection').validate({
    onkeyup: false,
    rules: {
        'Collection[title]': {required: true},
        'Collection[status]': {required: true},
        'Collection[image]': {extension: "png|jpg|jpeg"},
    },
    messages: {
        'Collection[title]': {required: "Please enter collection name."},
        'Collection[status]': {required: "Please select status."},
        'Collection[image]': {extension: "Please upload valid file."},
    }
});

$('#addSlider').validate({
    onkeyup: false,
    rules: {
        'Slider[title]': {required: true},
        'Slider[image]': {required: true, extension: "png|jpg|jpeg"},
        'Slider[link]': {url: true},
        'Slider[status]': {required: true}
    },
    messages: {
        'Slider[title]': {required: "Please enter slide title."},
        'Slider[image]': {required: "Please upload slide image.", extension: "Please upload valid file."},
        'Slider[link]': {url: "Please enter valid slide link."},
        'Slider[status]': {required: "Please select status."}
    },
});
$('#editSlider').validate({
    onkeyup: false,
    rules: {
        'Slider[title]': {required: true},
        'Slider[hdnImage]': {required: true, extension: "png|jpg|jpeg"},
        'Slider[image]': {extension: "png|jpg|jpeg"},
        'Slider[link]': {url: true},
        'Slider[status]': {required: true}
    },
    messages: {
        'Slider[title]': {required: "Please enter slide title."},
        'Slider[hdnImage]': {required: "Please upload slide image.", extension: "Please upload valid file."},
        'Slider[image]': {extension: "Please upload valid file."},
        'Slider[link]': {url: "Please enter valid slide link."},
        'Slider[status]': {required: "Please select status."}
    },
});


$("#sortable").sortable({
    cursor: "move",
    stop: function (event, ui) {
        var listArr = [];
        var sort = 0;
        $('#sortable tr').each(function () {
            console.log($(this).attr('id'));

            listArr[sort] = $(this).attr('id');
            sort = sort + 1;
        });
        console.log(listArr);

        var json = JSON.stringify(listArr);
        console.log(json);
        var url = location.href;
        var sep = '?';
        if (url.indexOf('?') > 0) {
            sep = '&';
        }
        $.ajax({
            type: "POST",
            url: url + sep + 'listArr=' + json,
            success: function (data) {
                console.log(data);
            }
        });
    }
});

$('#setVariationQty').click(function () {
    if ($('#setVariationQty').is(':checked')) {
        var qty = $('#variation-table .variationQty:first').val();
        $('#variation-table .variationQty').val(qty)
    }
});

$('#setVariationPrice').click(function () {
    if ($('#setVariationPrice').is(':checked')) {
        var price = $('#variation-table .variationPrice:first').val();
        $('#variation-table .variationPrice').val(price)
    }
});

$(document).ready(function () {

    $('#accountDrp').click(function () {
        if ($('#accountDrp').hasClass('active')) {
            $('#accountDrp ul').hide();
            $('#accountDrp').removeClass('open');
            $('#accountDrp').removeClass('active');
            $('#accountDrp a').removeAttr('aria-expanded');
        } else {
            $('#accountDrp ul').show();
            $('#accountDrp').addClass('open');
            $('#accountDrp').addClass('active');
            $('#accountDrp a').attr('aria-expanded', 'true');
        }
    });

    var summaryDivs = jQuery(".summary");
    for (i = 0; i < summaryDivs.length; i++) {
        jQuery(summaryDivs[i]).addClass("col-sm-6");
    }

    var paginationDivs = jQuery(".pagination");
    for (i = 0; i < paginationDivs.length; i++) {
        jQuery(paginationDivs[i]).addClass("col-sm-6");
    }

    var df = "dd/mm/yy";
    if ($('[name="OrdersSearch[order_date]"]').length > 0) {
        $('[name="OrdersSearch[order_date]"]').datepicker({dateFormat: df, maxDate: new Date()});
    }
    if ($('[name="OrdersSearch[invoice_date]"]').length > 0) {
        $('[name="OrdersSearch[invoice_date]"]').datepicker({dateFormat: df, maxDate: new Date()});
    }

    if ($('[name="OrdersSearch[actual_delivery_date]"]').length > 0) {
        $('[name="OrdersSearch[actual_delivery_date]"]').datepicker({dateFormat: df, maxDate: new Date()});
    }

    if ($('[name="OrdersSearch[paymentDate]"]').length > 0) {
        $('[name="OrdersSearch[paymentDate]"]').datepicker({dateFormat: df, maxDate: new Date()});
    }

    if ($('[name="OrdersSearch[estimate_delivery_date]"]').length > 0) {
        $('[name="OrdersSearch[estimate_delivery_date]"]').datepicker({dateFormat: df});
    }

    if ($('[name="OrdersSearch[updated_date]"]').length > 0) {
        $('[name="OrdersSearch[updated_date]"]').datepicker({dateFormat: df, maxDate: new Date()});
    }

    if ($('[name="VendorsSearch[created_date]"]').length > 0) {
        $('[name="VendorsSearch[created_date]"]').datepicker({dateFormat: df, maxDate: new Date()});
    }

    if ($('[name="UserSearch[created_at]"]').length > 0) {
        $('[name="UserSearch[created_at]"]').datepicker({dateFormat: df, maxDate: new Date()});
    }

    $('#user-birthdate').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        maxDate: '0',
        yearRange: "-100:+0",
    });

    $('#shipped_date').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        maxDate: '0',
        minDate: $('#orderDate').val(),
        yearRange: "-100:+0",
    });
//    jQuery(".summary").addClass("col-sm-6");
//    jQuery(".pagination").addClass("col-sm-6");
//    jQuery('.summary,.pagination').wrapAll('<div class="tablefooter row clearfix"></div>');

    /** product search for collection start **/
    var url = location.href;
    var sep = '?';
    if (url.indexOf('?') > 0) {
        sep = '&';
    }
    $("#productName").keyup(function () {
        $.ajax({
            type: "POST",
            url: url + sep + 'term=' + $(this).val() + '&productCategory=' + $('#productCategory').val() + '&productVendor=' + $('#productVendor').val(),
            beforeSend: function () {
                $("#productName").css("background", "#FFF no-repeat 165px");
            },
            success: function (data) {
                $("#suggesstion-box").show();
                $("#suggesstion-box").html(data);
                $("#productName").css("background", "#FFF");
            }
        });
    });

    $("#suggesstion-box").on('mousedown', function (event) {
        var ele = event.target;
        $(ele).click();
    });
    $("#productName").blur(function (event) {
        $("#suggesstion-box").hide();
    });
    /** product search for collection end **/
});
//To select product
function selectProduct(val, productId) {
    $("#productName").val(val);
    $("#productId").val(productId);
    $("#suggesstion-box").hide();
}
function addProduct() {

    var productId = $('#productId').val();
    if (productId != '') {
        var url = location.href;
        var sep = '?';
        if (url.indexOf('?') > 0) {
            sep = '&';
        }
        $.ajax({
            type: "POST",
            url: url + sep + 'productId=' + productId,
            success: function (data) {

                var jsonArr = JSON.parse(data);
                var html = '';

                html += '<tr id="row_' + jsonArr.id + '"><td>' + jsonArr.product_code + '<input type="hidden" name="collectionProduct[]" value="' + jsonArr.id + '"/></td><td><img src="' + jsonArr.featured_image + '" height="60" weight="60"/></td><td>' + jsonArr.name + '</td><td>' + jsonArr.category_name + '</td><td>' + jsonArr.vendor_name + '</td><td><a href="javascript:;" onclick=\'removeProduct("row_' + jsonArr.id + '")\'><i class="fa fa-times"></i></a></td></tr>';
//                console.log(html);
                $('#collectionProducts').append(html);
                $("#productName").val('');
                $("#productId").val('');
                $('#productCategory').val('');
                $('#productVendor').val('');
            }
        });
    }
}

function removeProduct(id) {
    if (confirm('Are you sure you want to remove this product from this collection?')) {
        $('#collectionProducts #' + id).remove();
    }

}
function isNumber(evt)
{
    var charCode = (evt.which) ? evt.which : evt.keyCode

    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 8)
        return false;

    return true;
}
function isPhone(evt)
{
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode == 8 || charCode == 32 || charCode == 37 || charCode == 39 || charCode == 40 || charCode == 41 || charCode == 45 || charCode == 46 || charCode == 43 || (charCode >= 48 && charCode <= 57)) {
        return true;
    } else {
        return false;
    }
}

function isBudget(evt, obj)
{
    var charCode = (evt.which) ? evt.which : evt.keyCode
    var str = obj.val();
    if (str.indexOf('.') > 0 && charCode == 46) {
        return false;
    }
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 8 && charCode != 37 && charCode != 39 && charCode != 9)
        return false;


    // if (charCode == 46)
    //return false;

    return true;


}
/** delete image */
function deleteImg(obj) {
    var response = confirm("Are you sure you want to delete picture?");
    if (response) {
        var id = obj.data('myval');
        var imgName = obj.data('image');
        var url = location.href

        $.ajax({
            url: url + '&p_id=' + id + '&imgName=' + imgName,
            success: function (data) {
//                console.log(data);
                window.location = location.href;
            }
        });
    } else {
        return false;
    }
}
function deleteColorImg(obj) {
    var response = confirm("Are you sure you want to delete picture?");
    if (response) {
        var id = obj.data('myval');
        var imgName = obj.data('image');
        var url = location.href
        //        console.log(id);
        $.ajax({
            url: url + '&p_id=' + id + '&colorImg=' + imgName,
            success: function (data) {
//                console.log(data);
                window.location = location.href;
            }
        });
    } else {
        return false;
    }
}

function getPageRecord(url, obj) {
    if (url.indexOf('?') > 0) {
        url = url + '&per-page=' + obj.val();
    } else {
        url = url + '?per-page=' + obj.val();
    }
    window.location = url;
}
function openTab(tab, activeTab) {
    var url = location.href;
    var tabArr = ['fromShop', 'fromAccount', 'fromProducts', 'fromOrders', 'fromEnquiry', 'fromProductInfo', 'fromVariation', 'fromRoles', 'fromUsers', 'fromShoppingCart', 'fromWishlist', 'fromShipment', 'page', 'per-page'];
    var index = tabArr.indexOf(tab); // get tab index     if (index > -1) { //remove from current tab from array
    tabArr.splice(index, 1);

    for (i = 0; i < tabArr.length; i++) { //remove other tab params from url
        var tabName = tabArr[i];
        if (url.indexOf(tabName)) {
            url = removeURLParameter(url, tabName);
        }
    }
    var finalUrl = '';
    if (tab != '') {
        finalUrl = url + '&' + tab + '=1'; // generat final url
    } else {
        finalUrl = url;
    }
    if (activeTab != '') {
        finalUrl = url + '&' + activeTab + '=1';
    }

//                            console.log(finalUrl);
    window.location.href = finalUrl;
//                            window.history.pushState('', '', finalUrl);
}

/** remove params from url */
function removeURLParameter(url, parameter) {     //prefer to use l.search if you have a location/link object
    var urlparts = url.split('?');
    if (urlparts.length >= 2) {

        var prefix = encodeURIComponent(parameter) + '=';
        var pars = urlparts[1].split(/[&;]/g);

        //reverse iteration as may be destructive
        for (var i = pars.length; i-- > 0; ) {
            //idiom for string.startsWith
            if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                pars.splice(i, 1);
            }
        }
        url = urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : "");
        return url;
    } else {
        return url;
    }
}

function actionProduct(url, remotUrl) {
    var action = $('#productAction').val();

    if (action != '') {
        var selected = '';
        $('input[name="selection[]"]:checked').each(function () {
            if (selected != '') {
                selected += ',';
            }
            selected += $(this).attr('value');
        });
        if (selected != '') {
            if (action == '2') {
                var $this = $(this)
                        , $remote = remotUrl
                        , $modal = $('<div class="modal" id="ajaxModal"><div class="modal-body"></div></div>');
                $('body').append($modal);
                $modal.modal();
                $modal.load($remote);

                /*  $("#dialog").dialog({
                 resizable: false,
                 height: "auto",
                 width: 400,
                 modal: true,
                 buttons: {
                 "Disapprove": function () {
                 $(this).dialog("close");
                 var reason = $('#disapproveReason').val();
                 $.ajax({
                 url: url + '&action=' + action + '&product_id=' + selected + '&reason=' + reason,
                 success: function (data) {
                 console.log(data);
                 window.location = location.href;
                 }
                 });
                 },
                 }
                 });*/
            } else {
                $.ajax({
                    url: url + '&action=' + action + '&product_id=' + selected,
                    success: function (data) {
                        console.log(data);
                        window.location = location.href;
                    }
                });
            }

        }

    }
}

function disapprove(url) {
    var action = '2';
    var selected = '';
    $('input[name="selection[]"]:checked').each(function () {
        if (selected != '') {
            selected += ',';
        }
        selected += $(this).attr('value');
    });
    var reason = $('#disapproveReason').val();
    if (reason == 'Other') {
        if ($('#txtOtherReason').val() == '') {
            $('#txtOtherReasonError').show();
            return false;
        }
        reason = 'other-' + $('#txtOtherReason').val();


    }
    $.ajax({
        url: url + '&action=' + action + '&product_id=' + selected + '&reason=' + reason,
        success: function (data) {
            console.log(data);
            window.location = location.href;
        }
    });
}
function approveProduct(url, id) {
    if (url != '' && id != '') {
        $.ajax({
            url: url + '&action=1' + '&product_id=' + id, success: function (data) {
                console.log(data);
                window.location = location.href;
            }
        });
    }
}
function disapproveProduct(url, id, remotUrl) {
    if (id.length > 0) {
        $('#' + id).attr('checked', 'checked');
    }

    var $this = $(this)
            , $remote = remotUrl
            , $modal = $('<div class="modal" id="ajaxModal"><div class="modal-body"></div></div>');
    $('body').append($modal);
    $modal.modal();
    $modal.load($remote);
    /* if (url != '' && id != '') {
     $("#dialog").dialog({
     resizable: false,
     height: "auto",
     width: 400,
     modal: true,
     buttons: {
     "Disapprove": function () {
     $(this).dialog("close");
     var reason = $('#disapproveReason').val();
     $.ajax({
     url: url + '&action=2' + '&product_id=' + id + '&reason=' + reason,
     success: function (data) {
     console.log(data);
     window.location = location.href;
     }      });
     },
     }
     });
     
     }*/
}

function addPaymentInfo(url, id) {

    var paymntDate = $('#payment_date').val();
    var refNum = $('#ref_num').val();
    var note = $('#payment_notes').val();
    if (paymntDate.length <= 0) {
        $('#payment_date_error').show();
    }
    if (refNum.length <= 0) {
        $('#ref_num_error').show();

    }
    if (refNum.length > 0 && paymntDate.length > 0) {
        var selected = '';
        $('input[name="selection[]"]:checked').each(function () {
            if (selected != '') {
                selected += ',';
            }
            if ($(this).is(':enabled')) {
                selected += $(this).attr('value');
            }

        });
        if (selected != '') {
            $.ajax({url: url + '&refNum=' + encodeURIComponent(refNum) + '&order_id=' + selected + '&note=' + encodeURIComponent(note) + '&date=' + paymntDate,
                success: function (data) {
                    console.log(data);
                    window.location = location.href;
                }
            });
        } else {
            alert('Please select order.');
            return false;
        }
    }

}

function showDialog(remotUrl, id) {


    if ($('#drpVendorPayment').val() != '1' && id.length <= 0) {
        return false;
    }
    if (id.length > 0) {
        $('#' + id).attr('checked', 'checked');
    }
    var selected = '';
    $('input[name="selection[]"]:checked').each(function () {
        if (selected != '') {
            selected += ',';
        }
        if ($(this).is(':enabled')) {
            selected += $(this).attr('value');
        }

    });
    if (selected == '') {
        alert('Please select order.');
        return false;
    }
    var $this = $(this)
            , $remote = remotUrl
            , $modal = $('<div class="modal" id="ajaxModal"><div class="modal-body"></div></div>');
    $('body').append($modal);
    $modal.modal();
    $modal.load($remote);
}

/** 
 function addcolor() {
 var color = $('#color_name').val();
 console.log(color.length);
 if (color.length <= '0') {
 $('#color_name_error').show();
 return false;
 } else {
 $('#color_name_error').hide();
 alert(1);
 $('#frmProductVariation').submit();
 //        document.forms['frmProductVariation'].submit();
 alert(2);
 return;
 }
 }**/
function removeRow(color, id) {
    if (confirm('Do you want to remove this color?')) {
        var url = window.location.href;

        $.ajax({
            url: url + '&remove_color=' + color, success: function (data) {
                console.log(data);
                window.location = location.href;
            }
        });
    } else {
        return false;
    }

}

function changeSize(obj) {
    console.log(obj.val());
    var size = obj.val();
    var add = 'no';
    if (obj.is(':checked')) {
        add = 'yes';
        var url = window.location.href;
        $.ajax({
            url: url + '&size=' + size + '&add=' + add, success: function (data) {
                console.log(data);
                window.location = location.href;
            }});
    } else {
        if (confirm('Do you want to remove this size?')) {
            var url = window.location.href;
            $.ajax({
                url: url + '&size=' + size + '&add=' + add,
                success: function (data) {
                    console.log(data);
                    window.location = location.href;
                }
            });
        } else {
            $('#' + obj.attr('id')).prop('checked', true);
            return false;
        }
    }

}

function saveForm(ele, type) {
    if (type != '') {
        if (type == '1') {
            $('.pageSave').val('1');
        } else {
            $('.pageSave').val('0');
        }
    }

    if (ele == 'orderUpdate') {
        var url = window.location.href;
        $.ajax({
            url: url + '&compareData=true',
            success: function (data) {
                console.log(data);
                var jsonArr = JSON.parse(data);
                console.log(jsonArr.message.length);
                if (jsonArr.message.length > 0) {
                    alert(jsonArr.message);
                    window.location = location.href;
                } else {
                    var ids = '';
                    $('.shipmentStatus').each(function (index, element) {
                        if ($(this).val() == '7' || $(this).val() == '6') {
                            if (ids != '') {
                                ids = ids + ',' + $(this).attr('id');
                            } else {
                                ids = $(this).attr('id');
                            }
                        }
                    });
                    if (ids != '') {
                        var remotUrl = $('.shipmentStatus').data('url');
                        console.log(remotUrl);
                        if (remotUrl.indexOf('?') > 0) {
                            remotUrl = remotUrl + '&product_id=' + ids;
                        } else {
                            remotUrl = remotUrl + '?product_id=' + ids;
                        }
                        var $this = $(this)
                                , $remote = remotUrl
                                , $modal = $('<div class="modal" id="ajaxModal"><div class="modal-body"></div></div>');
                        $('body').append($modal);
                        $modal.modal();
                        $modal.load($remote);
                    } else {
                        $("#" + ele).submit();
                    }
                }

            }
        });


    } else {
        $("#" + ele).submit();
    }
}

function saveOrderNote() {
    var saved = '1';
    $('.product-note').each(function (index, element) {
        var attrId = $(this).attr('id');
        var str = $(this).val();
        if (jQuery.trim(str) != '') {
            var product_id = $(this).data('id');
            $('#orderNote_' + product_id).val(str);

        } else {
            saved = '0';
            $('.' + attrId).show();
            return false;
        }
    });

    if (saved == '1') {
        $('#orderUpdate').submit();
    }

}

function submitProductForm(type) {
    if (type == '1') {
        $('.btnSave').val('1');
    } else {
        $('.btnSave').val('0');
    }

    if ($('#drpStatus').val() == '2') {
        console.log($('#drpReason').val());
        console.log($('#txtOtherReasontext').val());
        if ($('#drpReason').val() == '') {
            $('#drpReasonError').show();
            return false;
        } else {
            $('#drpReasonError').hide();
        }
        if ($('#txtOtherReasontext').val() == '') {
            $('#txtOtherReasonError').show();
            return false;
        } else {
            $('#txtOtherReasonError').hide();
        }
    } else {
        $('#drpReasonError').hide();
        $('#txtOtherReasonError').hide();
    }


    document.forms['productUpdate'].submit();
}

function saveQtyAndPrice(frm) {

    var error = 'no';
    $('.variationQty').each(function () {
        if ($(this).val() != '0' && $(this).val().length > 0) {
            $('#variationError').hide();
        } else {
            error = 'yes';
            $('#variationError').show();
            return false;
        }
    });
    $('.variationPrice').each(function () {
        if ($(this).val() != '0' && $(this).val().length > 0) {
            $('#variationError').hide();
        } else {
            error = 'yes';
            $('#variationError').show();
            return false;
        }
    });

    if (error == 'no') {
        $('#saveSizeAndFits').val('no');
        $('#saveQtyAndPrice').val('yes');
        document.forms[frm].submit();
    }

}


function saveSizeAndFits(frm) {

    var error = 'no';
    $('.fitBox').each(function () {
        var attr = $(this).attr('readonly');

        if (typeof attr !== typeof undefined && attr !== false) {
            $('#fitsError').hide();
        } else {
            if ($(this).val() != '0' && $(this).val().length > 0) {
                $('#fitsError').hide();
            } else {
                error = 'yes';
                $('#fitsError').show();
                return false;
            }
        }
        if ($(this).attr("readonly") === undefined) {

        }
    });
    if (error == 'no') {
        $('#saveSizeAndFits').val('yes');
        $('#saveQtyAndPrice').val('no');
        document.forms[frm].submit();
    }

}
function validateForm() {

    alert($(":input[type=submit]:focus")[0].name);
    //    alert($(":input[type=submit]:[clicked=true]").val()); 
    return false;
}

function getCategory(obj, url) {

    var id = obj.val();
    var sep = '?';
    if (url.indexOf('?') > 0) {
        sep = '&';
    }
    $.ajax({
        url: url + sep + 'id=' + id,
        success: function (data) {
            var jsonArr = JSON.parse(data);
            $('#drpCategory').empty();
            var html = '';
            html += '<option value="">Select Category</option>';

            for (var i = 0; i < jsonArr.length; i++) {
                html += '<option value="' + jsonArr[i].id + '">' + jsonArr[i].name + '</option>';
            }
            $('#drpCategory').append(html);
        }});
}

function checkedBox(action) {
    console.log(action);
    if ($('#' + action).is(':checked')) {
        //    if ($('#' + id).is(':checked')) {
        console.log($('.' + action));
        $('.' + action).prop('checked', true);
    } else {
        $('.' + action).prop('checked', false);
    }
}

function checkPermission() {
    var selected = '';
    $('#sectionPermission input[type="checkbox"]:checked').each(function () {
        selected = '';
        if ($(this).is(':enabled')) {
            selected = 'true';
        }
    });
    if (selected == 'true') {
        $('#permissionError').prop('style', 'display:none');
    } else {
        $('#permissionError').show();
        return false;
    }
}

function getProducts(obj) {
    var id = obj.val();
    if (id != '') {
        $('#sectionProducts').show();
        var url = window.location.href;
        if (id.indexOf('R') < 0) {

            var str = '<option value="">Select shipped to</option><option value="Administrator">Administrator</option>';
            $('#shipment_to').empty();
            $('#shipment_to').append(str);
            url = url + '&vendor_id=' + id
            $.ajax({
                url: url,
                success: function (data) {
                    var jsonArr = JSON.parse(data);
                    console.log(jsonArr);
                    $('#tblProduct').empty();
                    var html = '';
                    if (jsonArr.length > 0) {
                        for (var i = 0; i < jsonArr.length; i++) {
                            var checked = '';
                            if (jsonArr[i].selected == 'yes') {
//                                checked = ' checked="checked" disabled="disabled" ';
                            }
                            html += '<tr><td><input type="checkbox" name="productId[]" value="' + jsonArr[i].product_id + '" ' + checked + '></td><td>' + jsonArr[i].name + '</td></tr>';
                        }
                    }
                    $('#tblProduct').append(html);
                }
            });
        } else {
//            url = url + '&vendor_id=admin'
            var str = '<option value="">Select shipped to</option><option value="Consumer">Consumer</option>';
            $('#shipment_to').empty();
            $('#shipment_to').append(str);
            $('#sectionProducts').hide();
        }

    }

}
function getVendor(obj) {
    var id = obj.val();
    var url = window.location.href;
    var sep = '?';
    if (url.indexOf('?') > 0) {
        sep = '&';
    }
    $.ajax({
        url: url + sep + 'categoryId=' + id,
        success: function (data) {
            console.log(data);
            var jsonArr = JSON.parse(data);

            $('#productVendor').empty();
            var html = '';
            html += '<option value="">Select Vendor</option>';

            for (var i = 0; i < jsonArr.length; i++) {
                html += '<option value="' + jsonArr[i].id + '">' + jsonArr[i].vendor_code + ' - ' + jsonArr[i].shop_name + '</option>';
            }
            $('#productVendor').append(html);
        }
    });
}
function saveShipment(ele) {

    var shipment_from = $('#shipment_from').val();

    if (shipment_from != '' && shipment_from.indexOf('R') >= '0') {
        var selected = '';
        $('input[name="productId[]"]').each(function () {
            if ($(this).is(':checked')) {
            } else {
                selected += $(this).attr('value');
            }
        });
        if (selected != '') {
            if (confirm("This will mark the order as shipped. This operation can't be reversed. Are you sure you want to continue?")) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}

function changeReason(obj) {
    console.log(obj);
    console.log(obj.val());
    var reason = obj.val();
    if (reason != '' && reason == 'Other') {
        $('#txtOtherReason').css('display', 'block');
    } else {
        $('#txtOtherReason').css('display', 'none');
    }
}
function changeStatus(obj) {
    console.log(obj);
    console.log(obj.val());
    var status = obj.val();
    if (status == '2') {
        $('#drpReasons').css('display', 'block');
    } else {
        $('#drpReason').prop('selectedIndex', 0);
        $('#txtOtherReasontext').val('');
        $('#drpReasons').css('display', 'none');
        $('#txtOtherReason').css('display', 'none');
    }
}
