"use strict";
function newFormPreparation(subject){
    switch(subject){
        case "student": {
            var path = getCurentPath() + "api/getNewStudentForm/" + $.cookie("token"); 
            $.get(path, function(data) {
                if(data!=""){  
                    $("#main-container").html(data);
                    $("form").submit(function(event){
                        event.preventDefault();
                        if(validateName() && validateEmail() && validatePhone() && validateFileIfExists() ){
                            postDataToServer(null,this,(getCurentPath() + "api/createStudent"),"student",1);
                        }
                    })
                    $("#save").click(function(){
                        $("form").trigger("submit");
                    });
                    validateAndDisplayImage( 250 ); 
                }
            });
            break;
        }   case "course" : {
            $("#main-container").html("<b>Add Course</b></div><div class = 'row'><hr class = 'col-sm-12 col-xs-12'></div><div class = 'row'>" +
            "<button class = 'btn btn-sm btn-green btn-sm' id = 'save'>Save&nbsp;<b class = 'glyphicon glyphicon-ok'></b></button></div><form id = 'courses-form'><div class = 'row'>" +
            "<div class = 'input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'><label for = 'name' class = 'input-group-addon labels'>Name</label>" +
            "<input type = 'text' name = 'name' id = 'name' class = 'form-control' spellcheck = 'false' /></div></div><div class = 'row'>" +
            "<div class = 'input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'><label for = 'description' class = 'input-group-addon'>Description</label>" +
            "<textarea name = 'description' id = 'description' class = 'form-control'  spellcheck = 'false' rows = '5' maxlength = '270'></textarea></div></div><div class = 'row'>" +
            "<img src = '' width = '300' height = '300' id = 'image-upload' style = 'visibility:hidden' /></div><div><span class = 'btn btn-sm btn-file'><span>Choise image</span>" +
            "<input type = 'file' name = 'file' id = 'file'></span></div></div></div></form>");
            $("form").submit(function(event){
                event.preventDefault();
                if(validateCourseName() && validateCourseDescription() && validateFileIfExists()){
                    postDataToServer(null,this,(getCurentPath() + "api/createCourse"),"course",1);
                }
            });
            validateAndDisplayImage( 350 );
            $("#save").click(function(){
                $("form").trigger("submit");
            });
            break;
        }   case "administrator" : {
            $("#main-container").html("<div class = 'row'><b>Edit Administrator</b></div><div class = 'row'><hr class = 'col-sm-12 col-xs-12'></div><div class = 'row'>" +
            "<button class = 'btn btn-sm btn-green' id = 'save'>Save&nbsp;<b class = 'glyphicon glyphicon-ok'></b></button></div><form enctype = 'multipart/form-data' id = 'administrators-form'>" +
            "<div class = 'row'><div class = 'input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'><label for = 'name' class = 'input-group-addon'>Name</label>" +
            "<input type = 'text' name = 'name' id = 'name' class = 'form-control' spellcheck = 'false' /></div></div><div class = 'row'>"+
            "<div class = 'input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'><label for = 'phone' class = 'input-group-addon'>Phone</label>" +
            "<input type = 'tel' name = 'phone' id = 'phone' class = 'form-control' /></div></div><div class = 'row'>" +
            "<div class = 'input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'><label for = 'email' class = 'input-group-addon'>Email</label>" +
            "<input type = 'email' name = 'email' id = 'email' class = 'form-control' spellcheck = 'false' /></div></div><div class = 'row'>" +
            "<div class = 'input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'><label for = 'role' class = 'input-group-addon'>Role</label>" +
            "<input type = 'text' name = 'role' id = 'role' class = 'form-control' spellcheck = 'false' /></div></div><div class = 'row'>" +
            "<div class = 'input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'><label for = 'password' class = 'input-group-addon'>Password</label>" +
            "<input type = 'password' name = 'password' id = 'password' class = 'form-control' spellcheck = 'false' /></div></div><div class = 'row'>" +
            "<img src = '' width = '250' height = '250' id = 'image-upload' style = 'visibility: hidden' /></div><div><span class = 'btn btn-xs btn-file'><span>Choise image</span>" +
            "<input type = 'file' name = 'file' id = 'file' /></span></div></div></div></form>");        
            validateAndDisplayImage( 250 );
            $("form").submit( function( event ){
                event.preventDefault();
                if(validateName() && 
                validatePhone() &&
                validateEmail() &&
                validateRole() &&
                validatePassword() &&
                validateFileIfExists()){
                    postDataToServer(null,this,(getCurentPath() + "api/createAdministrator"),"administrator",1);
                }
            });
            $("#save").click(function(){
                $("form").trigger("submit");
            });
            break;
        }
    }
}
function deleteDialog(subject,id){
    var txt = (subject !=="course") ? "him" : "it";
    swal({
        title: 'Are you sure you want to delete ' + subject + '?',
        text: "You won't be able to recover "+txt+"!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete!',
        cancelButtonText: 'No, cancel!',
        confirmButtonClass: 'btn btn-sm btn-red',
        cancelButtonClass: 'btn btn-sm btn-green',
        buttonsStyling: false
      }).then(function () {
        switch(subject){
            case "student" : {
                var path = getCurentPath() + "api/deleteStudent";
                if(id!=null){
                    $.post(path, { id : id, token:$.cookie("token") }, function(data){
                        if(data!==""){
                            getStudents(JSON.parse(data));        
                            get("course");
                        }
                    });
                }
                break;
            }   case "course" : {
                var path = getCurentPath() + "api/deleteCourse";
                if(id!=null){
                    $.post(path, { id : id, token:$.cookie("token") }, function(data){
                        if(data!==""){
                            get("student");
                            getCourses(JSON.parse(data));   
                        }
                    });
                }
                break;
            }   case "administrator" : {
                var path = getCurentPath() + "api/deleteAdministrator";
                if(id!=null){
                    $.post(path, { id : id, token:$.cookie("token") }, function(data){
                        if(data!==""){
                            getAdministrators(JSON.parse(data));        
                        }
                    });
                }
                break;
            }
        }
    }, function(){/* Do nothiing */});
}
function clickDialog(subject,id,source){
    swal({
        title: 'Are you sure you want to continue?',
        text: "All changes will be dismissed!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, continue!',
        cancelButtonText: 'No, cancel!',
        confirmButtonClass: 'btn btn-sm btn-green',
        cancelButtonClass: 'btn btn-sm btn-red',
        buttonsStyling: false
}).then(function(){
        if(source === "list" ){
            switch(subject){
                case "student" : {
                    getStudent(id);
                    break;
                }   case "course" : {
                    getCourse(id);
                    break;
                }   case "administrator" : {
                    getAdministrator(id);
                    break;
                }
            }
        } else{
            newFormPreparation(subject);
        }  
    }, function(){/* Do nothiing */});
}
function validateAndDisplayImage( size ){
    if( size === 250 ){
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
    $("#file").change(function(){
        if( $("#file").val() !== ""){
            if( $("#file")[ 0 ].files.length===1){
                var files=$("#file")[ 0 ].files;
                var file=files[ 0 ];
                if (/(png)|(jpg)|(jpeg)|(gif)/.test(file.type.toLowerCase()) && file.size <= maxFileSize) {
                    var reader = new FileReader(); 
                    reader.addEventListener("load", function () {
                        var img = new Image(); 
                        img.src = reader.result; 
                        img.addEventListener("load", function() {
                            if (img.height <= maxHeight && img.height <= maxWidth) {
                                var photo = $("#image-upload")[ 0 ]; 
                                photo.src = reader.result; 
                            } else {
                                warningAlert("Too large image!");
                                return false; 
                            }
                        }, false); 
                    }, false); 
                    if (file) {
                        reader.readAsDataURL(file); 
                    }
                    $("#image-upload").css("visibility", "visible");
                }else {
                    if (file.size > maxFileSize) {
                        warningAlert("You only can't to load images larger" + maxFileSizeText + "!"); 
                    }else {
                        warningAlert("You only can to load png, jpg, jpeg, gif images!"); 
                    }
                    $("#file").val(""); 
                }
            }   else if($("#file")[ 0 ].files.length > 1){
                warningAlert("You can load only one image!"); 
            } else{
                warningAlert("Image not selected!");
            }
        }
    });
}
function addLogout(){
    $("#logout").click(function(event){
        event.preventDefault();
        $.removeCookie("token");
        window.location.href=getCurentPath()+"web/";
    }); 
}
function get(subject){
    switch(subject){
        case "student" : {
            var path = getCurentPath() + "api/getStudents/" + $.cookie("token"); 
            $.get(path, function(data) {
                if (data !== "") {
                    getStudents(JSON.parse(data)); 
                }
            });
            break;
        }   case "course" : {
                var path = getCurentPath() + "api/getCourses/" + $.cookie("token"); 
                $.get(path, function(data) {
                    if (data !== "") {
                        getCourses(JSON.parse(data)); 
                    }
                }); 
            break;
        }   case "administrator" : {
                var path = getCurentPath() + "api/getAdministrators/" + $.cookie("token"); 
                $.get(path, function(data) {
                    if (data !== "") {
                        getAdministrators(JSON.parse(data));
                    }
                });
            break;
        }
    }
}
function getStudents(students){
    var countOfStudents = students.length; 
    $("#main-container").html("<h4 class = ' col-sm-6 col-xs-6 '>Total " + countOfStudents + " students exists</h4>"); 
    $("#students-container").html("");
    $.each(students, function(index,student) {
        $("#students-container").append("<div class = 'row'><div class = 'container col-sm-1 col-xs-1'><img alt = '"+
        this.name + " image' hight = '62' width = '62' src = '" +
        this.image + "'></div><div class = 'container col-sm-10 col-xs-10'><div class = 'row'><div class = 'col-sm-9 col-xs-9 bolder'>" + this.name +
        "</div></div><div class = 'row'><div class='col-sm-9 col-xs-9'>" + this.phone + "</div></div><div class = 'row'><div class = 'col-sm-9 col-xs-9'>" + this.email + "</div></div></div>");
        $("#students-container > .row:last-child").click(function(){
            ( $( "form" ).length === 0 ) ? getStudent( $( "#students-container > .row" ).index( this ) ) : 
            clickDialog( "student" , $( "#students-container > .row" ).index( this ), "list");

        });
    }, this); 
}
function getCourses(courses){
    var countOfCourses = courses.length; 
    $("#main-container").append("<h4 class = ' col-sm-6 col-xs-6 '>Total " + countOfCourses + " courses exists</h4>"); 
    $("#courses-container").html("");
    $.each(courses, function(index,course) {
        $("#courses-container").append("<div class = 'row'><div class = 'container col-sm-1 col-xs-1'><img alt = '" +
        this.name + " image' hight = '62' width = '62' src = '" +
        this.image + "'></div><div class = 'container col-sm-10 col-xs-10'><div class = 'row'><div class = 'col-sm-9 col-xs-9 bolder'>" + this.name +
        "</div></div></div></div>");
        $("#courses-container > .row:last-child").click(function(){
            ( $( "form" ).length === 0 ) ? getCourse( $( "#courses-container > .row" ).index( this ) ) : 
            clickDialog( "course" , $( "#courses-container > .row" ).index( this ), "list");
        });
    }, this); 
}
function getAdministrators(administrators){
    var countOfAdministrators = administrators.length; 
    $("#main-container").html("<h4>Total " + countOfAdministrators + " administrators exists</h4>"); 
    $("#administrators-container").html("");
    $.each(administrators, function(index,admin) {
        $("#administrators-container").append("<div class = 'row'><div class = 'container col-sm-1 col-xs-1'><img alt = '" +
        this.name + " image' hight = '62' width = '62' src = '" +
        this.image + "'></div><div class = 'container col-sm-10 col-xs-10'><div class = 'row'><div class = 'col-sm-8 col-xs-8 bolder'>" + this.name +
        "</div><div class = 'col-sm-4 col-xs-4'>" + this.role +
        "</div></div><div class = 'col-sm-9 col-xs-9'>" + this.phone + "</div><div class = 'col-sm-9 col-xs-9'>" + this.email + "</div></div></div>");
        $("#administrators-container > .row:last-child").click(function(){
            ( $("form").length === 0 ) ? getAdministrator( $("#administrators-container > .row").index( this ) ) : 
            clickDialog("administrator", $("#administrators-container > .row").index( this ), "list");
        });
    }, this); 
}
function postDataToServer(id, form, path, formType,action){//action 0 is update. action 1 is create
        var expire = new Date(); 
        expire.setTime(expire.getTime() + 120000);
         $.cookie("id", id, {expires: expire});
         $.cookie("action" , action, {expires: expire});
    var formData = new FormData(form);
    var hash = sessionStorage.getItem("hash");
    if(hash){
           formData.set("password", hash );
           sessionStorage.removeItem('hash');
    }
    if(id!=null){
        formData.append("id", id);
    }
    formData.append("token", $.cookie("token"));
    if($("#file")[0].files.length===1 && validateFileProp(formType)){
        formData.append("image", $("#file")[0].files[0], $("#file")[0].files[0].fileName);
    }
    switch(formType){
        case("student") : {
                $.ajax({
                    url: path,
                    type: "POST",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(data){
                        if(data !== ""){
                            var students = JSON.parse(data);
                            getStudents(students);
                            if($.cookie("action") == "0"){  
                                getStudent(parseInt($.cookie("id")));
                            }   else{
                                getStudent((students.length-1));
                            }
                            $.removeCookie("action");
                            $.removeCookie("id"); 
                        }      
                    }
                });
            break;
        }   case("course") : {
            $.ajax({
                url: path,
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(data){
                    if(data !== ""){
                        var courses = JSON.parse(data);
                        getCourses(courses);
                        if($.cookie("action") == "0"){
                            getCourse(parseInt($.cookie("id")));
                        }   else{
                            getCourse((courses.length-1));
                        }
                    }
                    $.removeCookie("action");
                    $.removeCookie("id"); 
                }
            });        
            break;
        }   case("administrator") : {
            $.ajax({
                url: path,
                type: "POST",
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(data){
                    if(data !== ""){
                        getAdministrators(JSON.parse(data));
                    }        
                }
            });                    
            break;
        }

    }
}
function getStudent(id){
var path = getCurentPath() + "api/getStudent/" + id +"/" + $.cookie("token");
    $.get(path,function(data){
        if(data!=""){
            $("#main-container").html(data);
            $("#edit").click({id:id},function(){
                var path = getCurentPath() + "api/getStudentForm/" + id +"/" + $.cookie("token");
                $.get(path,function(data){
                    if(data!=""){
                        $("#main-container").html(data);
                        $("form").submit(function(event){
                            event.preventDefault();
                            if(validateName() && validatePhone() && validateEmail()){
                                postDataToServer(id,this, (getCurentPath() + "api/updateStudent"),"student",0);
                            }
                        });
                        $("#save").click(function(){
                            $("form").trigger("submit");
                        });
                        $("#delete").click({id:id},function(){
                            deleteDialog("student",id);
                        });
                        validateAndDisplayImage( 250 );
                    }
                });
            });
        }
    });
}
function getCourse(id){
    var path = getCurentPath() + "api/getCourse/" + id +"/" + $.cookie("token");
    $.get(path,function(data){
        if(data!=""){
            $("#main-container").html(data);
            $("#edit").click(function(){
                var path = getCurentPath() + "api/getCourseForm/" + id +"/" + $.cookie("token");
                $.get(path,function(data){
                    if(data!=""){
                        $("#main-container").html(data);
                        $("form").submit(function(event){
                            event.preventDefault();
                            if(validateCourseName() && validateCourseDescription()){
                                postDataToServer(id,this, (getCurentPath() + "api/updateCourse"),"course",0);
                            }
                        });
                        $("#save").click(function(){
                            $("form").trigger("submit");
                        });
                        $("#delete").click({id:id},function(){
                            deleteDialog("course",id);
                        });
                        validateAndDisplayImage( 350 );
                    }
                });
            }); 
        }
    });
}
function getAdministrator(id){
    var path = getCurentPath() + "api/getAdministrator/" + id +"/" + $.cookie("token");
    $.get(path,function(data){
        if(data!=""){
            $("#main-container").html(data);
            $("form").submit(function(event){
                event.preventDefault();
                if(validateName() && 
                validatePhone() &&
                validateEmail() &&
                validateRole() &&
                validatePassword()){
                    postDataToServer(id,this, getCurentPath() + "api/updateAdministrator","administrator",0);
                }
            });
            $("#save").click(function(){
                $("form").trigger("submit");
            });
            $("#delete").click({id:id},function(){
                deleteDialog("administrator",id);
            });
            validateAndDisplayImage( 250 );
        }
        $("#password").change(function(){
            sessionStorage.setItem("hash",CryptoJS.SHA256($(this).val()).toString(CryptoJS.enc.Hex))
        });
    });
}
function addSchoolEvents(doc) {
    $("title").text(doc.title); 
    $("body").html(doc.body);
    addLogout();
    get("student");
    setTimeout(function(){
        get("course");
    },300);
    $("#administration").click(function () {
        var path = getCurentPath() + "api/administration/" + $.cookie( "token" ); 
        $.get(path, function(data) {
            addAdministrationEvents(JSON.parse(data)); 
        });
    }); 
    $("#add-students").click(function () {
        ($("form").length === 0 ) ? newFormPreparation( "student" ) : clickDialog( "student", null, "button" );
    });    
    $("#add-courses").click(function () {
        ($("form").length === 0 ) ? newFormPreparation( "course" ) : clickDialog( "course", null, "button" );
    }); 
}
function addAdministrationEvents(doc) {
    $("title").text(doc.title); 
    $("body").html(doc.body);
    addLogout();
    get("administrator");
    $("#school").click(function () {
        var path = getCurentPath() + "api/school/" + $.cookie("token"); 
        $.get(path, function(data) {
            if (data != "") {
                addSchoolEvents(JSON.parse(data)); 
            }
        }); 
    }); 
    $("#add-administrator").click(function(){
        ($("form").length === 0 ) ? newFormPreparation("administrator") : clickDialog( "administrator", "button" );
    });
}