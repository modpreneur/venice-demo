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