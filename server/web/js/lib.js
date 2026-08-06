"use strict"; 
function warningAlert(text) {
    swal( {
        title:"Warning!", 
        text:text, 
        type:"warning", 
        showCancelButton:false,
        confirmButtonText:"OK",
        buttonsStyling:false,
        confirmButtonClass:"btn btn-md btn-login",
        allowOutsideClick:false
    }); 
}
function validateEmail(){
    var valid = true;
   if (!/^.*([a-z0-9])([@]).*([a-z0-9])(\.).*([a-z])(\.)?(.*([a-z]))?$/.test($("#email").val())){
            var mistakes = ($("#email").val()=="") ? "Email is empty!" : "The email is incorect, you have type email correct!";
            valid = false;
            warningAlert(mistakes);
        } 
        return valid;
} 
function validateUserName(){
    var valid = true;
   if (!/^.*([a-z0-9])([@]).*([a-z0-9])(\.).*([a-z])(\.)?(.*([a-z]))?$/.test($("#username").val())){
            var mistakes = ($("#username").val()=="") ? "Username is empty!" : "The username is incorect, you have type username correct!";
            valid = false;
            warningAlert(mistakes);
        } 
        return valid;
} 
function validatePhone(){
    var valid = true;
    if (!/^(0)((([1-9])|((5)([0-9]))))(\-)?.*([0-9])$/.test($("#phone").val())){
        var mistakes = ($("#phone").val()=="") ? "Phone number is empty!" : "The phone is incorect, you have use only numbersrs and hyphens!";
        valid = false;
        warningAlert(mistakes);
    }
    return valid;
}
function validateName(){
    var valid = true;
    if (!/^([A-Z]).*([a-z])([ ])([A-Z]).*([a-z])$/.test($("#name").val())){
        var mistakes = ($("#name").val()=="") ? "Name is empty!" : "The name is incorect, you have use only characters first leters have by capital!";
        valid=false;
        warningAlert(mistakes);
    } 
    return valid;
}
function validateFileIfExists(){
    var valid=true;
     if($("#file")[0].files.length === 0 ){
        var mistakes = "Image not selected";
         valid = false;
         warningAlert(mistakes);
     }
     return valid;
}
function validateCourseName(){
    var valid = true;
    if(!/^.*([a-zA-Z0-9 ])$/.test($("#name").val())){
        var mistakes = ($("#name").val()=="") ? "Name is empty!" : "you have use only numbersrs and letters! \n";
        valid =false;
        warningAlert(mistakes);
    }
    return valid;
}
function validateCourseDescription(){
    var valid=true;
    if($("#description").val()==="",$("#description").val().length>500){
        var mistakes = ($("#phone").val()=="") ? "description is empty!" : "The description length is incorect max length is 500 characters the description can't by empty";
        valid = false;
        warningAlert(mistakes);
    }
    return valid;
}
function validateRole(){
    var valid = true;
    if (!($("#role").val()==="owner" || $("#role").val()==="manager" || $("#role").val()==="sales")){
        valid = false;
        var mistakes = ($("#role").val()=="") ? "Role is empty." : "The role incorect.";
        warningAlert(mistakes);
    }
    return valid;
}
function validatePassword(){
    var valid = true;
    if ($("#password").val().length<8){
        valid = false;
        var mistakes = ($("#password").val()=="") ? "Password is empty." : "The password too short.";
        warningAlert(mistakes);
    }
    return valid;
}
function validateFileProp(formType){
    if( formType === "student" || formType === "administrator"){
        var maxFileSize = 500000;
        var maxWidth = 250;
        var maxHeight = 250;
        var maxFileSizeText = " 500 KB ";
    } else{
        var maxFileSize = 1000000;
        var maxWidth = 350;
        var maxHeight = 350;
        var maxFileSizeText = " 1 MB ";
    }
    var valid = true;
    if( $("#file")[ 0 ].files.length===1){
        var files=$("#file")[ 0 ].files;
        var file=files[ 0 ];
        if (/(png)|(jpg)|(jpeg)|(gif)/.test(file.type.toLowerCase()) && file.size <= maxFileSize) {
            var reader = new FileReader(); 
            reader.addEventListener("load", function () {
                var img = new Image(); 
                img.src = reader.result; 
                img.addEventListener("load", function() {
                    if (img.height > maxHeight || img.height > maxWidth) {
                        warningAlert("Too large image!");
                        valid = false; 
                    }
                }, false); 
            }, false);     
        }else {
            if (file.size > maxFileSize) {
                valid = false;
                warningAlert("You only can't to load images larger" + maxFileSizeText + "!"); 
            }else {
                valid = false;
                warningAlert("You only can to load png, jpg, jpeg, gif images!"); 
            }
        }
    }
    return valid; 
}