"use strict";
function addBindings(form){
    $("#file").change(function (event) {
            validateAndDisplayImage($("#file")[0].files);
    });
    $("#save").click(function(){
        validateAndSend(form,1);
    });
    /*$("#delete").bind("click",function(){
        validateAndSend(form,3);
    });*/
} 
function createStudentForm(){
   $("#main-container").html("<div class='row'><b>Add Student</b></div><div class='row'><hr class='col-sm-12 col-xs-12'></div><div class='row'><button class='btn btn-success btn-sm' id='save'>Save&nbsp;<b class='glyphicon glyphicon-ok'></b></button></div><form id='students-form'><div class='row'><div class='input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'><label for='name' class='input-group-addon'>Name&nbsp;</label><input type='text' name='name' id='name' class='form-control' spellcheck='false' /></div></div><div class='row'><div class='input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'><label for='phone' class='input-group-addon'>Phone</label><input type='tel' name='phone' id='phone' class='form-control' /></div></div><div class='row'><div class='input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'><label for='email' class='input-group-addon'>Email&nbsp;</label><input type='email' name='email' id='email' class='form-control' spellcheck='false' /></div></div><div class='row'><div class='input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'><label for='file' class='input-group-addon'>Image</label><div><img src='' width='250' height='250' id='image-upload' /></div><div><span class='btn btn-sm btn-file'><span class='fileinput-new'>Select image</span><input type='file' name='file' id='file'></span></div></div></div></form>"); 
    addBindings(1);

}
function createCourseForm(){
    $("#main-container").html("<b>Add Course</b></div><div class='row'><hr class='col-sm-12 col-xs-12'></div><div class='row'><button class='btn btn-success btn-sm' id='save'>Save&nbsp;<b class='glyphicon glyphicon-ok'></b></button></div><form id='courses-form'><div class='row'><div class='input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'><label for='name' class='input-group-addon labels'>Name&nbsp;</label><input type='text' name='name' id='name' class='form-control' spellcheck='false' /></div></div><div class='row'><div class='input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'><label for='description' class='input-group-addon'>Description</label><textarea name='description' id='description' class='form-control'  spellcheck='false' ></textarea></div></div><div class='row'><div class='input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'><label for='file' class='input-group-addon labels'>Image</label><div><img src='' width='300' height='300' id='image-upload' /></div><div><span class='btn btn-sm btn-file'><span class='fileinput-new'>Select image</span><input type='file' name='file' id='file'></span></div></div></div></form>");  
    addBindings(2);
}

$(document).ready(function () {
    $("#add-students").click(function () {
        $("form").length===1 ? warningDialog(1) :  createStudentForm();
    });    
    $("#add-courses").click(function () {
        $("form").length===1 ? warningDialog(2) :  createCourseForm();
    });    
});  