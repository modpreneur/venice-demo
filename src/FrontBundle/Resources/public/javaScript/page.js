/**
 * Shared On ready function in whole project
 */
$(document).ready(function(){
    //$.smartbanner();

    $(".copy-to-clipboard").each(function(i,element){
        var zeroClipItem = new ZeroClipboard(element);
        zeroClipItem.on("aftercopy",function(){
            $(element).html("Copied <i class=\"ff-ok\"></i>");
            setTimeout(function(){
                $(element).html("<i class=\"ff-link-arrow\"></i> Copy");
            },2000);
        });
    });

    $('a[href="openSupportFancybox"]').click(function(e){
        e.preventDefault();
        $(".fancybox-support").click();
        return false;
    });

    $('.fancybox').fancybox();
    disablePrivacyForms();

    //Scrolling
    $(".nice-scroll").click(function(event) {
        var id = $(event.target).attr("href");
        $('html, body').animate({
            scrollTop: $(id).offset().top + 1 // must be + 1 !!
        }, 500);
        return false;
    });

    //Attach Main Header handler
    fixMainHeader();
    $(window).scroll(fixMainHeader);


    $(".fancyimage").fancybox({
        type        : 'image',
        openEffect  : 'none',
        closeEffect : 'none'
    });

    $('.button-email').click(function (e){
        e.preventDefault();
        $('.sign-up-form').addClass('show');
        $('.wrapper').addClass('open-form');
        $('.button-email').css("display", "none");
    });
});


/**
 * Adds new Flash message
 * @param type
 * @param text
 */
function addFlashMessage(type, text){
    var message = flashMessageExample.replace(/TYPE/g,type).replace(/ID/g,Math.random()).replace(/TEXT/g,text);
    $("#flashMessages").append(message);
}

function newsletterFormSubmit(form){
    setTimeout(function(){
        $(form).parent().find(".label-place").html("<span>Saving</span>");
    },500);
}

function paymentsFormSubmit(form){
    $(form).parent().find("input.tton").first().val("Canceling");
}

function privacyFormAjaxDone(message){
    disablePrivacyForms();
}

function closeDashboardMobileAdv()
{
    $("#dashboard-mobile-adv").slideUp();
    $("#widget-homepage").removeClass("widget-both");
}

function closeDashboardMobileQSG()
{
    $("#dashboard-mobile-qsg").slideUp();
    $("#widget-homepage").removeClass("widget-both");
}

function disablePrivacyForms(){
    var isPublicProfile = $("#privacysettingspublicprofile_publicProfile").val();

    if(isPublicProfile == 1){
        $("#privacyBlock").removeClass("disabled");
    }else{
        $("#privacyBlock").addClass("disabled");
    }
}

/**
 * Fix main header based on scroll position
 */
function fixMainHeader(){
    if ($(window).scrollTop() > 1) {
        $('.fix-header-only').css("background-color", "rgba(0, 71, 124, 0.87)");
    } else {
        $('.fix-header-only').css("background-color", "rgba(0, 71, 124, 0)");
    }
}
