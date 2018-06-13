var debug = true;
// Console log IE fix
if (!window.console) window.console = {};
if (!window.console.log) window.console.log = function () { };

/** Translatable resources. */
var yes = $('#trans-yes').val() || "Yes";
var no = $('#trans-no').val() || "No";

$(document).ready(function () {
    var wrapper = $('#wrapper');
    loadAPPPlugins();

    wrapper.on('click', '.btn-remove', '.remove-entity', function (e) {
        e.preventDefault();
        var $button = $(this);
        var $form = $button.closest('form.remove-entity');
        var url = $form.attr('action');
        var method = $form.find('input[name=_method]').val();

        /** Translatable resources. */
        var title = $button.data('title') || "¿Are you sure?";
        var description = $button.data('description') ||  "You will not be able to recover this entity.";

        swal({
            title: title,
            text: description,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: yes,
            cancelButtonText: no,
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(isConfirm){
            if (isConfirm) {
                $.ajax({
                    type: method,
                    url:  url
                }).success(function () {
                    reload();
                }).error(function () {
                    swal("Error", "We cannot remove this, try again later.", "error");
                });
            }
        });
    });
});

/**
 * Initializes all basic plugins for APP.
 * @return void
 * */
function loadAPPPlugins() {
    openMenuItem();
    initIChecks();
    initSelect2();
    initDataTables();
    initLaddaLoadingButtons();
    initDatePicker();
    initDateTimePicker();
    initClipBoard();
    initBootstrapTooltip();
}

/**
 * Starts Bootstrap Tooltip.
 * @param element
 * @return void
 * */
function initBootstrapTooltip(element) {
    "use strict";

    element = typeof element !== 'undefined' ? element : $(document);
    element.find('[data-toggle="tooltip"]').tooltip();
}

/**
 * Opens menu item for Inspinia theme.
 * @return void
 * */
function openMenuItem() {
    $('ul.nav-second-level li.active')
        .closest('ul.nav-second-level').addClass('collapse in')
        .closest('li.dropdown').addClass('active');
}

/**
 * Init i-checks
 * @return void
 * */
function initIChecks(element) {
    element = typeof element !== 'undefined' ? element : $(document);
    element.find('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green', radioClass: 'iradio_square-green' });
}

/**
 * Init DataTablesJS
 * @return void
 * */
function initDataTables() {
    $('.dataTables-example').DataTable();
}

/**
 * Init Select2
 * @param element
 * @return void
 * */
function initSelect2(element) {
    element = typeof element !== 'undefined' ? element : $(document);

    element.find('.select2').select2({ allowClear: true });

    element.find('.select2-users').select2({
        allowClear: false,
        escapeMarkup: function (markup) { return markup; },
        templateResult: function (data) {
            if (data.id === '') { // adjust for custom placeholder values
                return 'no user.';
            }

            var username = $(data.element).attr('data-username');
            var email = $(data.element).attr('data-email');
            var src = $(data.element).attr('data-img');
            var text = '&nbsp;&nbsp;' + data.text;
            var result = $('<span/>');
            var img = $('<img/>')
                .addClass('img-circle')
                .attr('width', '25')
                .attr('height', '25')
                .attr('src', src);

            if (username) { result.attr('title', '@' + username); }
            if (src) { result.append(img) }

            result.append(text);
            return result;
        },
        templateSelection: function (data) {
            return data.text;
        }
    });
}

/**
 * Init Ladda for loading buttons
 * @return void
 * */
function initLaddaLoadingButtons() {
    // Ladda
    var loading_button = $('.ladda-button').ladda();
    loading_button.click(function(){
        var form = $(this).closest('form') || false ;
        $(this).ladda( 'start' );
        var isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;

        if (form && !isFirefox) {
            setTimeout(function () {
                form.submit();
            }, 100);
        }
    });
}

/**
 * Init DatePickerJS
 * @return void
 * */
function initDatePicker() {
    $('.datepicker').datepicker();
}

/**
 * Init DateTimerPickerJS
 * @return void
 * */
function initDateTimePicker() {
    $('.datetimepicker').datetimepicker();
}

/**
 * Init Clipboard plugin
 * @return void
 * */
function initClipBoard() {
    new Clipboard('.btn-copy');
}

/**
 * Append to javascript console, variables etc, only if var debug = true.
 * @param object
 * @return void
 * */
function log(object) {
    if (debug) {
        console.trace(object);
    }
}
/**
 * Log info, variables etc, only if var debug = true.
 * @param object
 * @return void
 * */
function info(object) {
    if (debug) {
        console.info(object);
    }
}
/**
 * Log warnings, variables etc, only if var debug = true.
 * @param object
 * @return void
 * */
function warn(object) {
    if (debug) {
        console.warn(object);
    }
}
/**
 * Log errors, variables etc, only if var debug = true.
 * @param object
 * @return void
 * */
function log_error(object) {
    if (debug) {
        console.error(object);
    }
}
/**
 * Start timer with given name, only if var debug = true
 * @param name
 * @return void
 * */
function timer(name) {
    if (debug) {
        console.time(name);
    }
}
/**
 * Ends timer with given name, only if var debug = true
 * @param name
 * @return void
 * */
function timerEnd(name) {
    if (debug) {
        console.timeEnd(name);
    }
}

/**
 * Truncates a string
 * @param {string} str
 * @param {int} length
 * @return {string}
 *
 * */
function truncateString(str, length) {
    if (str !== null) {
        length = typeof length !== 'undefined' ? length : 80;
        return str.length > length ? str.substring(0, length - 3) + '...' : str
    }

    return '';
}

/**
 * Format date
 * @param {string} date date('c') format
 * @param {string} format
 * @return {string}
 * */
function formatDate(date, format) {
    format = typeof format !== 'undefined' ? format : 'DD.MM.YYYY h:mmA';
    return moment(date).format(format);
}

function ago(date) {
    return moment(date).fromNow();
}

/**
 * Reset and empty forms.
 * @param form
 * @param {function} callback
 * @return void
 *
 * */
function resetGracefullyTheForm(form, callback) {
    form.trigger('reset');
    var groups = form.find('div.form-group');

    $.each(groups, function (index, group) {
        $(group).find('ul.error').remove();
        $(group).removeClass('has-error');
    });

    var selects = form.find('.select2');

    if ( selects.length > 0 ) {
        selects.trigger("change");
    }

    if (typeof callback !== 'undefined') {
        callback();
    }

}

/**
 * Show error message with toastr.
 * @param {string} message Message to display.
 * @return void
 * */
function error(message) {
    toastr.error(message);
}

/**
 * Handles 2xx message status
 * @param response
 * @param form
 * @return void
 * */
function status2xx(response, form) {
    var status = response.status;
    var text = response.statusText;

    toastr.success(text);
    if (typeof form !== 'undefined') {
        var modal = form.closest('div.modal');
        modal.modal('hide');
        resetGracefullyTheForm(form);
    }
}

/**
 * Draw input validation error messages.
 * @param input
 * @param errors
 * @return void
 * */
function drawInputError(input, errors) {
    var formGroup = input.closest('div.form-group');
    formGroup.find('ul.error').remove();

    if (typeof errors !== 'undefined') {
        formGroup.addClass('has-error');
        $('<ul/>').addClass('error').appendTo(formGroup);

        $.each(errors, function (i, error) {
            $('<li/>').html(error).appendTo(formGroup.find('ul.error'));
        });

    } else {
        formGroup.removeClass('has-error');
    }
}

/**
 * handle Form errors, if exists, then show error message.
 * @param errors
 * @return void
 * */
function handleFormErrors(errors) {
    if (typeof errors !== 'undefined') {
        toastr.clear();

        $.each(errors, function (i, e) {
            error(e);
        });
    }
}

/**
 * Handles 4xx message status
 * @param response
 * @param form
 * @return void
 * */
function status4xx(response, form) {
    var responseJSON = JSON.parse(response.responseText);
    var children = responseJSON.recordset.form.children;

    $.each(children, function (index, child) {
        var errors = child.errors;
        var input = form.find('[name='+ index +']');

        if (input.length) {
            drawInputError(input, errors);
        } else {
            handleFormErrors(errors);
        }
    });
}

/**
 * Handles 5xx message status
 * @param response
 * @return void
 * */
function status5xx(response) {
    var status = response.status;
    var text = response.statusText;

    error(status + ', ' + text);
}

/**
 * Handles the API response and shows specific message type based on HTTP CODE.
 * @param response
 * @param form
 * @return void
 * */
function handleGracefullyTheResponse(response, form) {
    var status = response.status;
    var submitButton;

    if ( typeof form !== 'undefined' ) {
        submitButton = form.find('.ladda-button');
    }

    if ( status >= 200 && status <= 299 ) {
        status2xx(response, form);
    } else if (status >= 400 && status <= 499) {
        status4xx(response, form);
    } else if ( status >= 500 && status <= 599 ) {
        status5xx(response);
    }

    if ( typeof form !== 'undefined' ) {
        submitButton.ladda('stop');
    }
}
/**
 * Shows loading gif on element, if element is null, will show on body element.
 * @param element
 * @param {int} marginTop
 * @param {int} marginBottom
 *
 * */
function loading(element, marginTop, marginBottom) {
    element = typeof element !== 'undefined' ? element : $('body');
    marginTop = typeof marginTop !== 'undefined' ? marginTop : 0;
    marginBottom = typeof marginBottom !== 'undefined' ? marginBottom : 0;

    element.html($('#loadingTemplate').tmpl()).wrapInner(function() {
        return "<div style='margin-top: "+ marginTop +"px; margin-bottom:"+ marginBottom +"px;'></div>";
    });

}

/**
 * Removes loading gif from element
 * @param element
 * */
function removeLoading(element) {
    element = typeof element !== 'undefined' ? element : $('body');
    element.empty();
}

/**
 * Reload page
 * @return void
 * */
function reload() {
    window.location.href = window.location.href;
}

(function ($) {
    $.fn.serializeFormJSON = function () {

        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
})(jQuery);

// Configure Toastr
toastr.options = {
    "closeButton": true,
    "debug": false,
    "progressBar": true,
    "preventDuplicates": true,
    "positionClass": "toast-top-right",
    "onclick": null,
    "showDuration": "400",
    "hideDuration": "1000",
    "timeOut": "7000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

$.blockUI.defaults.message = $('#blockUIMessage');
$.fn.modal.Constructor.prototype.enforceFocus = function() {}