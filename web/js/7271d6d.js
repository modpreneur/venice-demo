/**
 * Created by Hermi on 27.08.15.
 */
$(document).ready(function () {
    $("#label-main-navigation").click(function() {
        $("#full-menu").toggleClass("IE-full-menu");
    });
    $("#label-messenger").click(function() {
        $("#messenger-box").toggleClass("display-block");
    });
    $(".start-guide-label").click(function() {
        $(".start-guilde").toggleClass("display-block").toggleClass("IE-background");
        $(".blured").toggleClass("IE-blur");
    });
    $(".buy-modal-open").click(function() {
        $("#buy-modal").toggleClass("display-block").toggleClass("IE-background");
    });
    $(".profile-settings-edit").click(function() {
        $("#profile-photo").toggleClass("IE-profile-open");
    });
});
$(document).ready(function(){
    /*TODO: maybe it would be better to move to onReady func to scroll after images are loaded*/
    // nice workaround to easy parse url
    var parser = document.createElement('a');
    parser.href = window.location.href;

    if (parser.hash != '') {
        var oldId = parser.hash;
        var newId = parser.hash+"_hash";

        // unset target to trick browser not to jump to location
        $(oldId).attr("id",newId.substr(1));
        // nice scroll to location

        if($(newId).length !== 0){
            $('html, body').animate({
                scrollTop: $(newId).offset().top + 1 // must be + 1 !!
            }, 500);
            // set location ID back
            $(newId).attr("id",oldId.substr(1));
        }
    }
});
/**
 * Created by ondrejbohac on 01.07.15.
 */

var EditProfilePicture = {
    setFormItems : function(data){
        $("#global_user_profilePhoto_cropStartX").val(data.x);
        $("#global_user_profilePhoto_cropStartY").val(data.y);
        $("#global_user_profilePhoto_cropSize").val(data.width);
    },
    refresh : function(){
        var image = $("#image-before-upload");
        if(image.length != 0){
            $("#global_user_profilePhoto_image_file").change(function(fileEvent){
                if(fileEvent.target.files && fileEvent.target.files[0]){
                    var reader = new FileReader();
                    reader.onload = function(e){
                        image.show();
                        image.attr("src", e.target.result);
                        image.cropper("destroy");
                        image.cropper({aspectRatio: 1, crop: EditProfilePicture.setFormItems});
                    };
                    reader.readAsDataURL(fileEvent.target.files[0]);
                }
            });
            if(image.attr("src").length != 0){
                image.cropper({aspectRatio: 1, crop: EditProfilePicture.setFormItems});
            }
        }
    }
};

var EditProfile = {
    refresh: function(){

        $(".nice-scroll-parent").unbind("click").click(function(event) {
            //TODO Ondra IE fix (class "IE-profile-open")
            var target = event.target;
            setTimeout(function(){
                $('html, body').animate({
                    scrollTop: $(target).parent().parent().offset().top - 73
                }, 500);
            },500);
            return true;
        });
    },
    refreshProfile: function(){
        var hash = window.location.hash;
        if(hash != "#close"){
            setTimeout(function(){
                $('html, body').animate({
                    scrollTop: $(hash).parent().parent().offset().top - 73
                }, 500);
            },500);
        }
    }
};

$(document).ready(function () {
    EditProfile.refresh();
    EditProfilePicture.refresh();

    // $("#row_globaluserprofilephotowithdeletebutton_profilePhoto_image_file").click(function () {
    //     EditProfile.refresh();
    //     EditProfilePicture.refresh();
    // });
});
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

/**
 * Created by Hermi on 01.07.15.
 */
$(document).ready(function () {
    var radios = document.getElementsByClassName("hide-full-main-menu");
    var radioCheck;
    for(var i = 0; i < radios.length; i++) {
     	radios[i].addEventListener("click", function(event) {
        	if(this.tagName == "INPUT") {
                if(radioCheck == this){
                this.checked = false;
                radioCheck = null;
                }else{
                    radioCheck = this;
                }
           }
            else if(this.tagName == "A"){
             	for(var a = 0; a < radios.length; a++) {
                 	radios[a].checked = false;
                    radioCheck = null;
                }
            }
        })
    }
});
/**
 * Created by ondrejbohac on 20.11.15.
 */
/**
 * Created by ondrejbohac on 05.11.15.
 */
var ActionsLogger = {
    init: function(){
        $("*[data-log-click]").click(function(e){
            var target = $(e.currentTarget);
            var data = {};

            if(typeof target.attr("href") != "undefined")
                data.searchByHref = target.attr("href");

            var dataKeys = Object.keys(target.data());
            for(var i in dataKeys)
            {
                if(dataKeys[i] != "logClick" && dataKeys[i].indexOf("log") == 0){
                    var key = dataKeys[i].substring(3);
                    data[key.substr(0,1).toLowerCase() + key.substr(1)] = target.data(dataKeys[i]);
                }
            }

            ActionsLogger.logAction(target.data("log-click"),data);
        });
    },
    logAction : function(action, data){
        var dataObject = {};
        dataObject.action = action;
        if(typeof data !== "undefined")
            dataObject.data = data;
        $.ajax({
            type: "POST",
            url: $("#advanced-tracking-url").val(),
            data: dataObject,
            async: false
        }).success(function(a){
            console.log(a);
        });
    }
};

$(document).ready(ActionsLogger.init);

/**
 * Created by ondrejbohac on 29.06.15.
 */
(function () {
    function displayThings(ajaxData){
        if(ajaxData.method === "redirect"){
            window.location.href = ajaxData.url;
        }else if(ajaxData.method === "view"){
            var keys = Object.keys(ajaxData);
            delete keys["method"];

            if(typeof ajaxData.hashValue != "undefined"){
                location.hash = ajaxData["hashValue"];
                delete keys["hashValue"];
            }

            for(var i = 0;i < keys.length;i++)
            {
                var element = $("#" + keys[i]);
                if(element.hasClass("ajax-append")){
                    element.html(element.html() + ajaxData[keys[i]]);

                    element.find(".ajax-hidden").each(function(i,element){
                            $(element).slideDown();
                        });
                }else{
                    element.html(ajaxData[keys[i]]);
                }

                getDataCallback(element,"data-on-display", ajaxData[keys[i]]);
            }
        }
    }
    function displayError(parentElement){
        var message = $(parentElement).attr("data-error-message");
        if(message)
            addFlashMessage("danger",message);
    }
    function solveAfterClick(element) {
        var $element = $(element);
        if (typeof $element.attr("data-after-click") !== "undefined") {
            var text = $element.attr("data-after-click");
            if($element.nodeName == "INPUT")
                $element.val(text);
            else
                $element.text(text);
        }
        if(typeof $element.data("class-after-click") !== "undefined") {
            var text = $element.data("class-after-click");
            $element.addClass(text);
        }
        if(typeof $element.data("disable-after-click") !== "undefined") {
            $element.prop("disabled",true);
        }
    }
    function solveRemoveElement(element) {
        if(typeof $(element).attr("data-remove-element") !== "undefined"){
            $(element.attr("data-remove-element")).remove();
        }
    }
    function getDataCallback(element,dataParameterName,parameters) {
        if(typeof $(element).attr(dataParameterName) !== "undefined")
        {
            try{
                var functionSplitted = $(element).attr(dataParameterName).split(".");

                if(typeof window[functionSplitted[0]] == "object"){
                    window[functionSplitted[0]][functionSplitted[1]](parameters);
                }else{
                    window[$(element).attr(dataParameterName)](parameters);
                }
            }catch(err){

            }
        }
    }
    function revalidateListeners(){
        $(".ajax-on-change-submit").each(function(i,element){
            var $element = $(element);
            $element.unbind("change").change(function(){
                $element.parents("form").first().submit();
            });
        });
        $(".trinity-ajax").each(function (i, element) {
            var form = $(element);

            form.unbind("submit").submit(function (e) {
                e.preventDefault();

                var input1 = form.find("[data-class-after-click]");
                var input2 = form.find("[data-text-after-click]");
                solveAfterClick(input1);
                solveAfterClick(input2);

                getDataCallback(form,"data-on-submit-callback",form);

                var formData = new FormData(this);

                $.ajax({
                    url: form.attr("action"),
                    method: form.attr("method"),
                    data: formData,
                    processData: false,
                    cache: false,
                    contentType: false,
                }).done(function (data) {
                    displayThings(data);
                    getDataCallback(form,"data-ajax-done-callback",data);
                    revalidateListeners();
                }).error(function (){
                    displayError(form);
                    getDataCallback(form,"data-ajax-error-callback",form);
                    revalidateListeners();
                });

                return false;
            })
        });
        $(".trinity-load-more").each(function(i,element) {
            var $element = $(element);
            $element.unbind("click").click(function(){
                solveAfterClick($element);
                $.ajax({
                    url: $element.attr("href")
                }).done(function(data){
                    solveRemoveElement($element);
                    displayThings(data);
                    revalidateListeners();
                }).error(function(){
                    displayError();
                });
                return false;
            });
        });
    }
    $(document).ready(function () {
        revalidateListeners();
    });
})();


/**
 * Created by ondrejbohac on 29.06.15.
 */
function canPlayHSL()
{
    return document.createElement('video').canPlayType('application/vnd.apple.mpegURL') != "";
}

function saveQualityOpinion(qualityIndex)
{
    if(typeof(Storage) !== "undefined") {
        localStorage.setItem("videoQualityIndex", qualityIndex);
    }
}

function getQualityOpinion()
{
    if(typeof(Storage) !== "undefined" && localStorage.getItem("videoQualityIndex") != null) {
        return parseInt(localStorage.getItem("videoQualityIndex"));
    }
    return 2;
}

function videoResize()
{
    var screenRatio = $(window).width() / $(window).height();
    var computedHeight, computedWidth;
    if (screenRatio > (16 / 9)) { // screen is more wide than 16:9 - lock 90% height
        computedHeight = $(window).height() * (80 / 100); // 90% of window height
        computedWidth = computedHeight * (16 / 9); // aspect ratio 16:9
    } else { // screen is less wide than 16:9 - lock 90% width
        computedWidth = $(window).width() * (90 / 100); // 90% of window width
        computedHeight = computedWidth * (9 / 16); // aspect ratio 16:9
    }
    $("#modal-video-box").width(computedWidth).height(computedHeight);
    $("#close-video-button").css("left",parseInt($("#modal-video-box").css("margin-left")) + computedWidth - 40);
}

//TODO only when video plays
$(window).resize(videoResize);

function closeVideo(){
    var jw = jwplayer();
    if(typeof jw.remove != "undefined")
        jw.remove();

    $("#video-box-open").removeClass("video-modal-open");
}

$(document).ready(function () {
    $(".videoPlayButton").click(function(e){
        var video = $(this).data("video");
        var videoArray = video.split(";");
        var qualityArray = ["1080p HD", "720p HD", "360p SQ", "270 mobile"];

        var videoBox = $("#video-box-open");
        videoBox.addClass("video-modal-open");
        videoResize();

        var backgroundImage= "";
        var md = new MobileDetect(window.navigator.userAgent);
        if(md.mobile() != null) {
            backgroundImage = "https://s3-us-west-2.amazonaws.com/flofit-prod/site-resources/press+ff.jpg";
        }

        if(canPlayHSL() && videoArray.length == 5)
        {
            jwplayer("video-div").setup({
                file: videoArray[4],
                //width: videoBox.width() * 0.8,
                //height: videoBox.width() * 9 / 16 * 0.8,
                image: backgroundImage,
                aspectratio: "16:9",
                autostart: true,
                responsive: true,
                controls: true,
                models: [{type: "html5"}],
                skin: {
                    name: "vapor",
                    active: "#00477c"
                }
            });
        }else {
            var qualityOption = getQualityOpinion();
            jwplayer("video-div").setup({
                sources: [{ // Could be done dynamic
                    file: videoArray[0],
                    label: qualityArray[0],
                    "default": qualityOption == 0
                }, {
                    file: videoArray[1],
                    label: qualityArray[1],
                    "default": qualityOption == 1
                }, {
                    file: videoArray[2],
                    label: qualityArray[2],
                    "default": qualityOption == 2
                }, {
                    file: videoArray[3],
                    label: qualityArray[3],
                    "default": qualityOption == 3
                }],
                width: "100%",
                height: "100%",
                image: backgroundImage,
                aspectratio: "16:9",
                models: [{type: "html5"}],
                autostart: true,
                responsive: true,
                skin: {
                    name: "vapor",
                    active: "#00477c"
                }
            });
            jwplayer().on("levelsChanged",function(values){
                if(typeof values["currentQuality"] != "undefined"){
                    saveQualityOpinion(values["currentQuality"])
                }
            });
            jwplayer().on("ready",function(){
                videoResize();
            });
        }
    });

    var videoBox = $("#videoPlayBox");
    if(videoBox.length == 1){
        var videoContainer = videoBox.parent();
        var sources = [];
        if(videoBox.data("video-hd"))
            sources.push({file: videoBox.data("video-hd"),label: "1080p HD"});
        if(videoBox.data("video-hq"))
            sources.push({file: videoBox.data("video-hq"),label: "High quality"});
        if(videoBox.data("video-lq"))
            sources.push({file: videoBox.data("video-lq"),label: "Low quality","default": true});
        if(videoBox.data("video-mobile"))
            sources.push({file: videoBox.data("video-mobile"),label: "Mobile"});

        var previewImage = videoBox.data("preview");
        jwplayer("videoPlayBox").setup({
            sources: sources,
            image: previewImage,
            width: videoContainer.width(),
            height: videoContainer.width() * 9/16,
            aspectratio: "16:9",
            responsive: true,
            skin: {
                name: "vapor",
                active: "#00477c",
            }
        });
    }

    $(document).keyup(function(e){
        if(e.keyCode == 27)
            closeVideo();
    });

    if (window.hasOwnProperty('openVideoFromDirectLink')) { // if defined by direct link,
        openVideoFromDirectLink();
    }
});
