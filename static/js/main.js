$(function(){
    sidebar();
    pickControl();
    $('[data-toggle="tooltip"]').tooltip();
    actualHeight();
    logout();
    slipSearch();
    mobilesearch();
})

// $(window).on('load', function(){ loader(); })

$.fn.extend({
    toggleText: function(a, b){
        return this.text(this.text() == b ? a : b);
    }
});

function loader() {
    $('#main-loader').fadeToggle(100);
}

function getCookie(name) {
    var v = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
    return v ? v[2] : null;
}

function setCookie(name, value, days) {
    var d = new Date;
    d.setTime(d.getTime() + 24*60*60*1000*days);
    document.cookie = name + "=" + value + ";path=/;expires=" + d.toGMTString();
}

function deleteCookie(name) { setCookie(name, '', -1); }

function sidebar() {

    if(getCookie('side-off')) {
        $('.sidebar, body').addClass('off');
        resizeTables();
    }

    $('.sidebar-dropdown').on('click', function(){
        var target = $(this).data('target');
        var h = $(this).parent().outerHeight() * $(target).children('li').length;
        if($(target).hasClass('active')) {
            $(target).animate({ 'height' : 0 }, 300);
            $(target).removeClass('active');
            return
        }
        $(target).addClass('active');
        $(target).animate({ 'height' : h }, 300)
        return
    })

    $('.sidebar-trigger').on('click', function(){
        $('.sidebar').toggleClass('active');
        $('#side-icon').toggleText('menu', 'close');
    })

    var current_path = window.location.pathname.split('/').pop();
    
    $(".sidebar a[href*='"+current_path+"']").each(function(){

        $(this).closest("li").addClass("active");
        var el = $(this).parents('.dropdown').attr('id');        
        var h = $(this).parent().outerHeight() * ($(this).parent().siblings('li').length + 1 );

        if(typeof el !== "undefined") { 

            $('#'+el).css({ 'height' : h })
        }
    })

    /* sidebar cookie based toggle */

    $('.side-toggler').on('click', function() {
        if(getCookie('side-off')) { 
            deleteCookie('side-off');
            $('.sidebar').removeClass('no-transtion');
            $('.body').removeClass('no-transtion');
            $('.sidebar, body').removeClass('off');
            $('#side-toggle').text('short_text');
        } else {
            setCookie('side-off', 'true', 7);
            $('.sidebar').removeClass('no-transtion');
            $('.body').removeClass('no-transtion');
            $('.sidebar, body').addClass('off');
            $('#side-toggle').text('menu');
        }
        resizeTables();
    })
    
    $('.dark-toggler').on('click', function() {
        if(getCookie('wms-dark')) { 
            deleteCookie('wms-dark');
            $("#dark-css").remove();
            $('.dark-toggler').text('brightness_high');
        } else {
            setCookie('wms-dark', 'true', 7);
            $("head").append("<link id=\"dark-css\" rel=\"stylesheet\" href=\"/wms/static/css/dark.css?v=2\">");
            $('.dark-toggler').text('nights_stay');
        }
    })

    $( "#maintenance-trigger" ).on('click', function() {
        var up =  $("#maintenance-dropdown-ico").html();
        if(up == "arrow_drop_down") {
            $("#maintenance-dropdown-ico").html("arrow_drop_up");
        } else {
            $("#maintenance-dropdown-ico").html("arrow_drop_down");
        }
    })

    $("#report-trigger").on('click', function() {
        var up = $("#report-dropdown-ico").html();
        if(up == "arrow_drop_down") {
            $("#report-dropdown-ico").html("arrow_drop_up");
        } else {
            $("#report-dropdown-ico").html("arrow_drop_down");
        }
    })

    $.ripple(".btn", {
        debug: false,
        on: 'mousedown',
        opacity: 0.4,
        color: "auto",
        multi: true,
        duration: 0.7,
        rate: function(pxPerSecond) {
            return pxPerSecond;
        },
        easing: 'linear' 
    });

}

function resizeTables() {
    $.each($('table').not('.un-dt'), function() {
        $(this).DataTable().columns.adjust().draw();
    })
}


function pickControl(){
    $('.pick-control').on('click', function(){

        var pickToast = { 'duration': 4000,'position': 'top','align': 'right','class' : 'bg-danger','zindex': 99999 };

        var type = $(this).data('type');
        var $target = $($(this).data('target'));
        var max = parseFloat($target.attr('max'));
        var currentValue = parseFloat($target.val());

        switch(type) {
            case "+":
                currentValue++;
                break;
            case "-":
                currentValue--;
                break;
            case "?":
                $.Toast("Quantity is locked", pickToast);
                return
        }

        if(Number.isNaN(currentValue)) { currentValue = 1; }
        if(currentValue < 1) {  currentValue = 1; }
        if(max >= 0) { if(max < currentValue) { currentValue = max; } }
        
        $target.val(currentValue);
        return
    })
}

function confirmed(callback, str = 'Press Yes to continue', ok = 'Yes', no = 'Cancel', param = false){

    $('#confirm-message').html(str);
    $ok = $('#confirm-ok'); $no = $('#confirm-no');
    $ok.html(ok); $no.html(no);
    $modal = $('#confirm-modal');
    $modal.modal('show');

    $ok.off('click');
    $no.off('click');

    $no.on('click', function(){ $modal.modal('hide');});
    $ok.on('click', function(){ 
        $modal.modal('hide');
        if(param){
            callback(param); 
        } else {
            callback();
        }
    })
    
    return
}

function actualHeight(){
    $('ul[data-simplebar]').css({
        'height' : window.innerHeight - 120
    })
}

function logout(){
    $('a.logout').on('click', function(event){
        event.preventDefault();
        confirmed(logoutNow, "Logout current session?", "Logout", "Cancel");
    })

    $.each($('.modal button.close span'), function(){
        $(this).html('clear').addClass("material-icons").css({
            'outline' : 'none !important'
        });
    })
}

function logoutNow(){
    window.location.href="logout.php";
    return
}

function slipSearch(){
    var $result = $('#slip-result');
    
    $('#slip-search').on('keyup', function(){
        var search = $.trim($(this).val());
        
        if(search == "") { $result.fadeOut(100); return; }
        if(search.length <= 4) { $result.fadeOut(100); return; }

        var offset = $(this).offset();

        $.ajax({
            url: 'controller/controller.checking.php?mode=search&s='+search,
            type: "GET",
            processData: false,
            contentType: false,
            success: function(data) { 
                var data = JSON.parse(data);
                if(data.code == '1') { $result.find('ul').html(data.view); }
                else { $result.find('ul').html('<li class="list-group-item d-flex justify-content-between align-items-center"><a href="#!">Slip number not found</a></li>') }
                $result
                    .css({ 'left' : offset.left})
                    .fadeIn(100);
                return;
            }
        })
    })

    $('#slip-search').on('focusout', function(){
        $result.fadeOut(100);
    })

    return
}

function mobilesearch() {
    $('.navbar-search').on('click', function(){
        $('html, body').animate({scrollTop:(0)}, '300');
        $('.user-control').fadeToggle(500);
        $('.navbar-search > i').toggleText('search', 'close');

    })
}
