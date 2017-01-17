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

