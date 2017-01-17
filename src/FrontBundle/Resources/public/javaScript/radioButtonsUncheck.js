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