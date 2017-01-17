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
