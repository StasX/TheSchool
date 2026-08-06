"use strict"; 
function getCurentPath() {
    return (window.location.href.substring((window.location.href.length - 1 )) === "#")?
    window.location.href.substring(0, (window.location.href.length - 5 )) :
    window.location.href.substring(0, (window.location.href.length - 4 )); 
}
function validateLoginForm() { 
        var wrong = validateUserName() && validatePassword();
    return wrong; 
}
$(document).ready(function () {
    $("form").submit(function (e) {
        e.preventDefault(); 
        if (validateLoginForm()) {
            var pass = $("#password").val(); 
            var hash = CryptoJS.SHA256(pass).toString(CryptoJS.enc.Hex);
            var path = getCurentPath() + "api/login"; 
            $.post(path,  {user:$("#username").val(), pass:hash}, function(data) {
                if (data === "Username or password is incorect") {
                    warningAlert(data); 
                }else {
                    var doc = JSON.parse(data); 
                    var expire = new Date(); 
                    expire.setTime(expire.getTime() + 420000); //token will expired on 7 minuts
$.cookie('token', doc.token,  {expires:expire }); 
                    var polling = setInterval(function () {//request for new token every 6 minutes
var path = getCurentPath() + "api/renewToken/" + $.cookie("token"); 
                        $.get(path, function(data) {
                            if (data != "") {
                                var expire = new Date(); 
                                expire.setTime(expire.getTime() + 420000); //token will expired on 7 minuts
$.removeCookie('token'); 
                                $.cookie('token', data,  {expires:expire }); 
                            }
                        }); 
                    }, 360000); 
                    addSchoolEvents(doc, getCurentPath()); 
                }
            }); 
        }
    }); 
}); 