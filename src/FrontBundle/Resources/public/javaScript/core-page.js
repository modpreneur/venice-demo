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