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
