jQuery(function () {
	jQuery.ajaxSetup({
		headers: {
			'X-CSRF-Token': jQuery('meta[name="csrf-token"]').attr('content')
		}
	});
});

jQuery(document).on('click', '.btn-delete', function (e) {
    e.preventDefault();
    var url = jQuery(this).attr('href');
    swal({
        title: "Delete Data",
        text: "Are You Sure You Want To Delete Data",
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function () {
        jQuery.ajax({
            type: 'DELETE',
            data: {  method : 'DELETE' },
            dataType: 'JSON',
            url: url,
            success: function (data) {
                if (data.response == 1) {
                    swal({
                        title: "Done",
                        text: data.msg,
                        type: "success",
                        closeOnConfirm: true
                    }, function () {
                        if (typeof data.redirect != 'undefined') {
                            location.replace(data.redirect);
                        } else {
                            loadDataTable();
                        }
                    });
                } else if (data.response == 2) {
                    swal("Oops...", data.msg, "error");
                }
            },
        });
    });
});

jQuery(document).ready(function (){
    jQuery(document).on('click', '.btn-change-status', function (e) {
        e.preventDefault();
        var url = jQuery(this).attr('href');
        swal({
            title: "Update Data",
            text: "Are You Sure To Update?",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        }, function () {
            jQuery.ajax({
                type: 'PATCH',
                data: {  method : 'PATCH' },
                dataType: 'JSON',
                url: url,
                success: function (data) {
                    if (data.response == 1) {
                        swal({
                            title: "Done",
                            text: data.msg,
                            type: "success",
                            closeOnConfirm: true
                        }, function () {
                            if (typeof data.redirect != 'undefined') {
                                location.replace(data.redirect);
                            } else {
                                loadDataTable();
                            }
                        });
                    } else if (data.response == 2) {
                        swal("Oops...", data.msg, "error");
                    }
                },
            });
        });
    });
});

jQuery(document).on('click', '.btn-send-password-link', function (e) {
    e.preventDefault();
    var url = jQuery(this).attr('href');
    swal({
        title: "Forgot Password",
        text: "Are You Sure Want to Send Link For Forgot Password?",
        type: "info",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function () {
        jQuery.ajax({
            type: 'PATCH',
            data: {  method : 'PATCH' },
            dataType: 'JSON',
            url: url,
            success: function (data) {
                if (data.response == 1) {
                    swal({
                        title: "Done",
                        text: data.msg,
                        type: "success",
                        closeOnConfirm: true
                    }, function () {
                        if (typeof data.redirect != 'undefined') {
                            location.replace(data.redirect);
                        } else {
                            loadDataTable();
                        }
                    });
                } else if (data.response == 2) {
                    swal("Oops...", data.msg, "error");
                }
            },
        });
    });
});

var current_url = window.location.href;
jQuery('.sidebar, #navbar-collapse').find('a').each(function () {
    if (current_url == jQuery(this).attr('href')) {
        jQuery(this).parents('li').addClass('active');
    }
});

function loadDataTable() {
    dTable.ajax.reload( null, false );
}

function userValidation() {
    var status = true;
    if (jQuery('.firstName').val() === '') {
        jQuery('.first_name_err').html('Please Enter Firstname');
        jQuery('.firstName').focus();
        status = false;
    }

    if (jQuery('.lastName').val() === '') {
        jQuery('.last_name_err').html('Please Enter Lastname');
        jQuery('.lastName').focus();
        status = false;
    }

    if (jQuery('.password').val() === '') {
        jQuery('.password_err').html('Please Enter password');
        jQuery('.password').focus();
        status = false;
    } else if (jQuery('.password').length > 8) {
        jQuery('.password_err').html('The password must be at least 8 characters');
        jQuery('.password').focus();
        status = false;
    }

    if (jQuery('.email').val() === '') {
        jQuery('.email_err').html('Please Enter Email Address');
        jQuery('.email').focus();
        status = false;
    } else {
        var regex = new RegExp(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);
        if (!regex.test(String(jQuery('.email').val()).toLowerCase())) {
            jQuery('.email_err').html('Please Enter Valid Email Address');
            jQuery('.email').focus();
            status = false;
        }
    }

    return status;
}

function categoryValidation() {
    var status = true;
    if (jQuery('.title').val() === '') {
        jQuery('.title_err').html('Please Enter Category Title');
        jQuery('.title').focus();
        status = false;
    }

    return status;
}
