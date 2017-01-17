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