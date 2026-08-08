<?php

function getSchool($admin) {
    $html = "<nav class = 'navbar navbar-default'><div class = 'container-fluid'><div class = 'navbar-header'>" .
            "<img alt = 'Logo' src = 'img/logo.png' class = 'img-rounded logo' height = '50' width = '67'></div><div class = 'collapse navbar-collapse'>" .
            "<ul class = 'nav navbar-nav'><li class = 'active'><a href = '#' id = 'school'>School</a></li>";
    if ($admin->role !== 'sales') {
        $html .= "<li><a href = '#' id = 'administration'>Administration</a></li>";
    }
    $html .= "</ul><div class = 'container col-md-3 col-sm-4 col-xs-6' style = 'float:right;'><div class = 'row'><img alt = 'manager image' src = '" . $admin->image .
            "' width = '50' height = '50' style = 'border-radius: 50%; float:right;' /><div class = 'container col-lg-6 col-md-8 col-sm-9 col-xs-9 nav-user-info-container' style = 'float:left'>" .
            "<div class = 'row'>" . $admin->name . '&nbsp;' . $admin->role . "</div><div class = 'row'><a href = '#' id = 'logout'>Logout</a></div></div></div></div></div></div></nav>";
    $html .= "<div class = 'container-fluid'><div class = 'container col-sm-6 col-xs6' style = 'float:left'>" .
            "<div class = 'container col-sm-6 col-xs-6'  style = 'float:left'><div class = 'row'><div class = 'container col-sm-11 col-xs-11 col-sm-offset-1 col-xs-offset-1'>" .
            "<div class = 'row'><div class = 'col-sm-8 col-xs-8 header'>Courses</div><div class = 'col-sm-1 col-xs-1'><button class = 'btn btn-xs' id = 'add-courses'>" .
            "<span class = 'glyphicon glyphicon-plus'></span></button></div></div></div></div><div class = 'row'><hr class = 'col-sm-10 col-xs-10 col-sm-offset-1 col-xs-offset-1'>" .
            "</div><div class = 'row'><div class = 'container col-sm-12 col-xs-12' id = 'courses-container'></div></div></div><div class = 'container col-sm-6 col-xs-6' style = 'float:right'>" .
            "<div class = 'row'><div class = 'container col-sm-11 col-xs-11 col-sm-offset-1 col-xs-offset-1'><div class = 'row'><div class = 'col-sm-8 col-xs-8 header'>Students" .
            "</div><div class = 'col-sm-1 col-xs-1'><button class = 'btn btn-xs' id = 'add-students'><span class = 'glyphicon glyphicon-plus'></span></button>" .
            "</div></div></div></div><div class = 'row'><hr class = 'col-sm-10  col-xs-10 col-sm-offset-1 col-xs-offset-1'></div><div class = 'row'>" .
            "<div class = 'container col-sm-12 col-xs-12' id = 'students-container'></div></div></div></div><div class = 'container col-sm-6 col-xs-6' id = 'main-container'><div class = 'row'>" .
            "<div class = 'container col-sm-6 col-xs-6' style = 'float:left'></div><div class = 'container col-sm-6 col-xs-6' style = 'float:right'></div></div></div></div>";
    return $html;
}

function getAdministration($admin) {
    $html = "<nav class = 'navbar navbar-default'><div class = 'container-fluid'><div class = 'navbar-header'>" .
            "<img alt = 'Logo' src = 'img/logo.png' class = ' img-rounded logo' height = '50' width = '67'></div><div class = 'collapse navbar-collapse'>" .
            "<ul class = 'nav navbar-nav'><li><a href = '#' id = 'school'>School</a></li><li class = 'active'><a href = '#' id = 'administration'>Administration</a></li>" .
            "</ul><div class = 'container col-md-3 col-sm-4 col-xs-6' style = 'float:right;'><div class = 'row'><img alt = 'manager image' src = '" . $admin->image .
            "' width = '50' height = '50' style = 'border-radius: 50%; float:right;' /><div class = 'container col-lg-6 col-md-8 col-sm-9 col-xs-9 nav-user-info-container' style = 'float:left'>" .
            "<div class = 'row'>" . $admin->name . '&nbsp;' . $admin->role . "</div><div class = 'row'><a href = '#' id = 'logout'>Logout</a></div></div></div></div></div></div></nav>" .
            "<div class = 'container-fluid'><div class = 'container col-lg-3 col-md-3 col-sm-3 col-xs-3'><div class = 'row'><div class = 'container col-sm-11 col-xs-11 col-sm-offset-1  col-xs-offset-1'>" .
            "<div class = 'row'><div class = 'col-sm-9 col-xs-9'>Administrators</div><div class = 'col-sm-1 col-xs-1'><button class = 'btn btn-xs' id = 'add-administrator'><span class = 'glyphicon glyphicon-plus'>" .
            "</span></button></div></div></div></div><div class = 'row'><hr class = 'col-sm-9 col-xs-9 col-sm-offset-1 col-xs-offset-1'></div><div class = 'row'>" .
            "<div class = 'container col-sm-12 col-xs-12' id = 'administrators-container'></div></div></div><div class = 'container col-sm-9 col-xs-9' id = 'main-container'></div></div>";
    return $html;
}

function getNewStudentForm($courses) {
    $html = "<div class = 'row'>
                    <b>Add Student</b>
                 </div>
                 <div class = 'row'>
                    <hr class = 'col-sm-12 col-xs-12'>
                </div>
                <div class = 'row'>
                    <button class = 'btn btn-sm btn-green btn-sm' id = 'save'>
                        Save&nbsp;<b class = 'glyphicon glyphicon-ok'></b>
                    </button>
                </div>
                <form id = 'students-form'>
                    <div class = 'row'>
                        <div class = 'input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'>
                            <label for = 'name' class = 'input-group-addon'>Name</label>
                            <input type = 'text' name = 'name' id = 'name' class = 'form-control' spellcheck = 'false' />
                        </div>
                    </div>
                    <div class = 'row'>
                        <div class = 'input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'>
                            <label for = 'phone' class = 'input-group-addon'>Phone</label>
                            <input type = 'tel' name = 'phone' id = 'phone' class = 'form-control' />
                        </div>
                    </div>
                    <div class = 'row'>
                        <div class = 'input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'>
                            <label for = 'email' class = 'input-group-addon'>Email</label>
                            <input type = 'email' name = 'email' id = 'email' class = 'form-control' spellcheck = 'false' />
                        </div>
                    </div>
                    <div class = 'row'>
                        <img src = '' width = '250' height = '250' id = 'image-upload' style = 'visibility: hidden' />
                    </div>
                    <div class = 'row'>
                        <span class = 'btn btn-sm btn-file'>
                            <span>Choise image</span>
                            <input type = 'file' name = 'file' id = 'file'>
                        </span>
                    </div>
                    <div class = 'row'>
                    <div class = 'container col-sm-offset-1 col-xs-offset-1 col-sm-10 col-xs-10'>
                        <div class = 'col-sm-2 col-xs-2'><b>Courses</b></div>
                        <div class = 'container col-sm-10 col-xs-10'>
                            <span>";
    for ($i = 0; $i < count($courses); $i++) {
        $html .= "<div class = 'col-sm-6'><input type = 'checkbox' name = 'course[" . $i . "]' style = 'float' value = '" . $courses[$i]->name . "' />" .
                $courses[$i]->name . "</div>";
    }
    return $html . "</span></div></div></div></form>";
}

function getStudentForm($student, $courses, $allCourses) {
    $html = "<div class = 'row'>
                <b>Edit Student</b>
           </div>
           <div class = 'row'>
                <hr class = 'col-sm-12 col-xs-12'>
           </div>
           <div class = 'row'>
                <button class = 'btn btn-sm btn-green' id = 'save'>
                    Save&nbsp;
                    <b class = 'glyphicon glyphicon-ok'></b>
                </button>
                <button class = 'btn btn-sm btn-red' id = 'delete'>
                    Delete&nbsp;
                    <b class = 'glyphicon glyphicon-remove'></b>
                </button>
            </div>
            <form id = 'students-form'>
                <div class = 'row'>
                    <div class = 'input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'>
                        <label for = 'name' class = 'input-group-addon'>Name&nbsp;</label>
                        <input type = 'text' name = 'name' id = 'name' value = '" . $student->name . "' class = 'form-control' spellcheck = 'false' />
                    </div>
                </div>
                <div class = 'row'>
                    <div class = 'input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'>
                        <label for = 'phone' class = 'input-group-addon'>Phone</label>
                        <input type = 'tel' name = 'phone' id = 'phone' value = '" . $student->phone . "' class = 'form-control' />
                    </div>
                </div>
                <div class = 'row'>
                    <div class = 'input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'>
                        <label for = 'email' class = 'input-group-addon'>Email&nbsp;</label>
                        <input type = 'email' name = 'email' id = 'email' value = '" . $student->email . "' class = 'form-control' spellcheck = 'false' />
                    </div>
                </div>
                <div class = 'row'>
                    <img src = '" . $student->image . "' width = '250' height = '250' id = 'image-upload' />
                </div>
                <div class = 'row'>
                    <span class = 'btn btn-sm btn-file'>
                        <span>Choise image</span>
                        <input type = 'file' name = 'file' id = 'file'>
                    </span>
                </div>
                <div class = 'row'>
                    <div class = 'container col-sm-offset-1 col-xs-offset-1 col-sm-10 col-xs-10'>
                        <div class = 'col-sm-2 col-xs-2'><b>Courses</b></div>
                            <div class = 'container col-sm-10 col-xs-10' id = 'courses'>";
    for ($i = 0; $i < count($allCourses); $i++) {
        $checked = in_array($allCourses[$i]->name, $courses) ? 'checked = "checked"' : '';
        $html .= "<div class = 'col-sm-6'>
                            <span>
                            <input type = 'checkbox' name = 'course[" . $i . "]' style = 'float' value = '" . $allCourses[$i]->name . "' " . $checked . " />" .
                $allCourses[$i]->name .
                "</span>
                        </div>";
    }
    return $html.="</div></div></form>";
}

function getCourseForm($course, $students) {
    $delete = (count($students) === 0) ? "<button class = 'btn btn-sm btn-red' id = 'delete'>Delete&nbsp;<b class = 'glyphicon glyphicon-remove'></b></button>" : "";
    $html = "<div class = 'row'>
                <b>Edit Course</b>
            </div>
            <div class = 'row'>
                <hr class = 'col-sm-12 col-xs-12'>
            </div>
            <div class = 'row'>
                <button class = 'btn btn-sm btn-green btn-sm' id = 'save'>
                    Save&nbsp;
                    <b class = 'glyphicon glyphicon-ok'></b>
                </button>" .
            $delete .
            "</div>
            <form id = 'courses-form'>
                <div class = 'row'>
                    <div class = 'input-group col-sm-6 col-xs-12 col-sm-offset-2'>
                        <label for = 'name' class = 'input-group-addon labels'>Name</label>
                        <input type = 'text' name = 'name' id = 'name' value = '" . $course->name . "' class = 'form-control' spellcheck = 'false' />
                    </div>
                </div>
                <div class = 'row'>
                    <div class = 'input-group col-sm-6 col-xs-12 col-sm-offset-2'>
                        <label for = 'description' class = 'input-group-addon'>Description</label>
                        <textarea name = 'description' id = 'description' class = 'form-control' spellcheck = 'false' rows = '5' maxlength = '500'>" . $course->description . "</textarea>
                    </div>
                </div>
                <div class = 'row'>
                    <img src = '" . $course->image . "' width = '300' height = '300' id = 'image-upload' />
                </div>
                <div>
                    <span class = 'btn btn-sm btn-file'>
                        <span>Choise image</span>
                        <input type = 'file' name = 'file' id = 'file' />
                    </span>
                </div>
            </form>
            <h2 class='row'>Total " . count($students) . " students taking this course</h2>";

    return $html;
}

function getAdministratorForm($admin, $stat) {
    $deleteButton = ($stat) ? "" : "<button class = 'btn btn-sm btn-red' id = 'delete'>
                                        Delete&nbsp;<b class = 'glyphicon glyphicon-remove'></b>
                                  </button>";
    $html = "<div class = 'row'>
                <b>Edit Administrator</b>
            </div>
            <div class = 'row'>
                <hr class = 'col-sm-12 col-xs-12'>
            </div>
            <div class = 'row'>
                <button class = 'btn btn-sm btn-green' id = 'save'>
                    Save&nbsp;
                    <b class = 'glyphicon glyphicon-ok'></b>
                </button>
                $deleteButton
            </div>
            <form id = 'students-form'>
                <div class = 'row'>
                    <div class = 'input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'>
                        <label for = 'name' class = 'input-group-addon'>Name&nbsp;</label>
                        <input type = 'text' name = 'name' id = 'name' value = '" . $admin->name . "' class = 'form-control' spellcheck = 'false' />
                    </div>
                </div>
                <div class = 'row'>
                    <div class = 'input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'>
                        <label for = 'phone' class = 'input-group-addon'>Phone</label>
                        <input type = 'tel' name = 'phone' id = 'phone' value = '$admin->phone' class = 'form-control' />
                    </div>
                </div>
                <div class = 'row'>
                    <div class = 'input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'>
                        <label for = 'email' class = 'input-group-addon'>Email&nbsp;</label>
                        <input type = 'email' name = 'email' id = 'email' value = '$admin->email' class = 'form-control' spellcheck = 'false' />
                    </div>
                </div>
                <div class = 'row'>
                    <div class = 'input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'>
                        <label for = 'role' class = 'input-group-addon'>Role&nbsp;</label>
                        <input type = 'text' name = 'role' id = 'role' value = '$admin->role' class = 'form-control' spellcheck = 'false' />
                    </div>
                </div>
                <div class = 'row'>
                    <div class = 'input-group col-sm-8 col-xs-8 col-sm-offset-2 col-xs-offset-2'>
                        <label for = 'password' class = 'input-group-addon'>Password</label>
                        <input type = 'password' name = 'password' id = 'password' value='$admin->password' class = 'form-control' spellcheck = 'false' />
                    </div>
                </div>
                <div class = 'row'>
                    <img src = '$admin->image' width = '250' height = '250' id = 'image-upload' />
                </div>
                <div class = 'row'>
                    <span class = 'btn btn-sm btn-file'>
                        <span>Choise image</span>
                    <input type = 'file' name = 'file' id = 'file'>" . "</span>
                </div>
            </form>";
    return $html;
}

function getStudentInfo($student, $courses) {
    $html = '<div class = "row">
                <b>Student</b>
             </div>
            <div class = "row">
                <hr class = "col-sm-12 col-xs-12">
            </div>
            <div class = "row">
                <button class = "btn btn-sm" id = "edit">Edit</button>
            </div>
            <div class = "row">
                <div class = "container col-sm-6 col-xs-12">
                    <img src = "' . $student->image . '" height = "250" width = "250" alt = "' . $student->name . ' image" />
                </div>
                <div class = "container col-sm-6 col-xs-12">
                    <h1 class="row">' . $student->name . '</h1>
                    <h4 class="row">' . $student->phone . '</h4>
                    <h4 class="row">' . $student->email . '</h4>
                </div>
            </div>
            <div class = "row">  
                <div class = "container col-sm-6 col-xs-6" id="members-list">';
    for ($i = 0; $i < count($courses); $i++) {
        $html .= '<div class="row"><div class = "col-sm-4 col-xs-4"><img alt = "course ' . $courses[$i]->name . '" src = "' . $courses[$i]->image . '" width = "50" height = "50" /></div><h3 class = "col-sm-8 col-xs-8" >' .
                $courses[$i]->name . '</h3></div>';
    }
    return $html . '</div></div>';
}

function getCourseInfo($course, $students, $is_sales) {
    $button = (!$is_sales) ? '<button class = "btn btn-sm" id = "edit">Edit</button>' : "";
    $html = '<div class = "row">
                    <b>Course</b>
                </div>
                <div class = "row">
                    <hr class = "col-sm-12 col-xs-12">
                </div>
                <div class = "row">' .
            $button .
            '</div>
            <div class = "row" >
                <div class = "container col-sm-6 col-xs-6">
                    <img src = "' . $course->image . '" height = "300" width = "300" alt = "' . $course->name . ' image" />
                </div>
                <div class="container col-sm-6 col-xs-6">
                    <h1 class="row">' . $course->name . '</h1>
                    <h4 class="row">' . $course->description . '</h4>
                </div>
            </div>
            <div class="row">
                <div class="container col-sm-6 col-xs-6" id="members-list">';
    for ($i = 0; $i < count($students); $i++) {
        $html .= '<div class="row"><div class="col-sm-4 col-xs-4"><img alt="' . $students[$i]->name . '" width="50" height="50" src="' . $students[$i]->image . '" /></div><h3 class="col-sm-8 col-xs-8">' . $students[$i]->name . '</h3></div>';
    }
    return $html . '</div></div>';
}
