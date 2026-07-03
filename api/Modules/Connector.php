<?php

final class Connector {

    private $connection;
    private static $instance = null;

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    private function __construct() {
        $this->connection = mysqli_connect("localhost", "root", "", "theschool");
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function __destruct() {
        if ($this->connection) {
            mysqli_close($this->connection);
            $this->connection = false;
        }
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public static function getInstace() {
        if (self::$instance === null) {
            self::$instance = new Connector();
        }
        return self::$instance;
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function getRealStudentId($id) {
        if ($this->connection) {
            $operation_result = 0;
            if ($result = mysqli_query($this->connection, "SELECT Student_ID FROM students GROUP BY Student_ID")) {
                while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                    $rows[] = $row[0];
                }
                $operation_result = $rows[$id];
            }
        }
        return $operation_result;
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function getRealCourseId($id) {
        $operation_result = 0;
        if ($this->connection) {
            if ($result = mysqli_query($this->connection, "SELECT Course_ID FROM courses GROUP BY Course_ID")) {
                while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                    $rows[] = $row[0];
                }
                $operation_result = $rows[$id];
            }
        }
        return $operation_result;
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function getRealAdministratorId($id) {
        $operation_result = 0;
        if ($this->connection) {
            if ($result = mysqli_query($this->connection, "SELECT Administrator_ID FROM administrators GROUP BY Administrator_ID")) {
                while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                    $rows[] = $row[0];
                }
                $operation_result = $rows[$id];
            }
        }
        return $operation_result;
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function getCurentUser($user, $pass) {
        if ($this->connection) {
            $email = mysqli_real_escape_string($this->connection, $user);
            $password = mysqli_real_escape_string($this->connection, $pass);
            $stmt = mysqli_stmt_init($this->connection);
            mysqli_stmt_prepare($stmt, "SELECT Email, Name, Role, Phone, Password, Image FROM administrators WHERE Email = ? AND Password = ?");
            mysqli_stmt_bind_param($stmt, "ss", $email, $password);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $email, $name, $role, $phone, $password, $image);
            if (mysqli_stmt_fetch($stmt)) {
                require_once 'Administrator.php';
                $result = new Administrator($name, $role, $phone, $email, $password, $image);
            } else {
                $result = NULL;
            }
            mysqli_stmt_close($stmt);
        }
        return $result;
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function validateUser($user, $pass) {
        $result = false;
        if ($this->connection) {
            $email = mysqli_real_escape_string($this->connection, $user);
            $password = mysqli_real_escape_string($this->connection, $pass);
            $stmt = mysqli_stmt_init($this->connection);
            mysqli_stmt_prepare($stmt, "SELECT Email, Password FROM administrators WHERE Email = ? AND Password = ?");
            mysqli_stmt_bind_param($stmt, "ss", $email, $password);
            mysqli_stmt_execute($stmt);
            if (mysqli_stmt_fetch($stmt)) {
                $result = true;
            } else {
                $result = false;
            }
            mysqli_stmt_close($stmt);
        }
        return $result;
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function getAllStudents() {
        $students = array();
        if ($this->connection) {
            if ($result = mysqli_query($this->connection, "SELECT Name, Phone, Email, Image FROM students", MYSQLI_USE_RESULT)) {
                require_once 'Student.php';
                if (mysqli_field_count($this->connection)) {
                    while ($student = mysqli_fetch_assoc($result)) {
                        array_push($students, new Student($student['Name'], $student["Phone"], $student["Email"], $student["Image"]));
                    }
                }
                mysqli_free_result($result);
            }
        }
        return $students;
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function getAllCourses() {
        $courses = array();
        if ($this->connection) {
            if ($result = mysqli_query($this->connection, "SELECT Name, Description, Image FROM courses")) {
                require_once 'Course.php';
                if (mysqli_field_count($this->connection)) {
                    while ($course = mysqli_fetch_assoc($result)) {
                        array_push($courses, new Course($course['Name'], $course["Description"], $course["Image"]));
                    }
                }
                mysqli_free_result($result);
            }
        }
        return $courses;
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function getAllAdministrators($adminIsOwner) {
        $administrators = array();
        if ($this->connection) {
            if ($adminIsOwner) {
                $result = mysqli_query($this->connection, "SELECT Name, Role, Phone, Email, Password, Image FROM administrators", MYSQLI_USE_RESULT);
            } else {
                $result = mysqli_query($this->connection, "SELECT Name, Role, Phone, Email, Password, Image FROM administrators WHERE Role != 'owner'", MYSQLI_USE_RESULT);
            }
            if ($result) {
                $administrators = array();
                require_once 'Administrator.php';
                if (mysqli_field_count($this->connection)) {
                    while ($administrator = mysqli_fetch_assoc($result)) {
                        array_push($administrators, new Administrator($administrator['Name'], $administrator["Role"], $administrator["Phone"], $administrator["Email"], $administrator["Password"], $administrator["Image"]));
                    }
                }
                mysqli_free_result($result);
            }
            return $administrators;
        }
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function getStudent($id) {
        $queryResult = NULL;
        if ($this->connection) {
            $id = mysqli_real_escape_string($this->connection, $id);
            $stmt = mysqli_stmt_init($this->connection);
            mysqli_stmt_prepare($stmt, "SELECT Name, Phone, Email, Image FROM students WHERE Student_ID=?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            if ($result = mysqli_stmt_get_result($stmt)) {
                require_once 'Student.php';
                if (mysqli_field_count($this->connection)) {
                    $student = mysqli_fetch_assoc($result);
                    $queryResult = new Student(
                            $student['Name'], $student["Phone"], $student["Email"], $student["Image"]
                    );
                }
                mysqli_free_result($result);
            }
            mysqli_stmt_close($stmt);
        }
        return $queryResult;
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function getCoursesOfStudent($id) {
        $courses = array();
        if ($this->connection) {
            $id = mysqli_real_escape_string($this->connection, $id);
            $stmt = mysqli_stmt_init($this->connection);
            mysqli_stmt_prepare($stmt, "SELECT DISTINCT courses.Name, courses.Image FROM courses INNER JOIN school ON  courses.Course_ID = school.Course_ID INNER JOIN students ON school.Student_ID = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            if ($result = mysqli_stmt_get_result($stmt)) {
                if (mysqli_field_count($this->connection)) {
                    $courses = array();
                    require_once 'Course.php';
                    while ($course = mysqli_fetch_assoc($result)) {
                        array_push($courses, new Course($course['Name'], null, $course['Image']));
                    }
                }
                mysqli_free_result($result);
            }
            mysqli_stmt_close($stmt);
        }
        return $courses;
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function getNamesOfCourseOfStudent($id) {
        $courses = array();
        if ($this->connection) {
            $id = mysqli_real_escape_string($this->connection, $id);
            $stmt = mysqli_stmt_init($this->connection);
            mysqli_stmt_prepare($stmt, "SELECT DISTINCT courses.Name, courses.Image FROM courses INNER JOIN school ON  courses.Course_ID = school.Course_ID INNER JOIN students ON school.Student_ID = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            if ($result = mysqli_stmt_get_result($stmt)) {
                if (mysqli_field_count($this->connection)) {
                    require_once 'Course.php';
                    while ($course = mysqli_fetch_assoc($result)) {
                        array_push($courses, $course['Name']);
                    }
                }
                mysqli_free_result($result);
            }
            mysqli_stmt_close($stmt);
        }
        return $courses;
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function getStudentsFromCourse($id) {
        $students = array();
        if ($this->connection) {
            $id = mysqli_real_escape_string($this->connection, $id);
            $stmt = mysqli_stmt_init($this->connection);
            mysqli_stmt_prepare($stmt, 'SELECT DISTINCT students.Name, students.Image FROM students INNER JOIN school ON  students.Student_ID = school.Student_ID INNER JOIN courses ON school.course_ID = ?');
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            if ($result = mysqli_stmt_get_result($stmt)) {
                if (mysqli_field_count($this->connection)) {
                    require_once 'Student.php';
                    while ($student = mysqli_fetch_assoc($result)) {
                        array_push($students, new Student(
                                $student['Name'], null, null, $student["Image"]));
                    }
                }
            }
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
        }
        return $students;
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function getCourse($id) {
        $queryResult = NULL;
        if ($this->connection) {
            $id = mysqli_real_escape_string($this->connection, $id);
            $stmt = mysqli_stmt_init($this->connection);
            mysqli_stmt_prepare($stmt, "SELECT Name, Description, Image FROM courses WHERE Course_ID=?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            if ($result = mysqli_stmt_get_result($stmt)) {
                require_once 'Course.php';
                if (mysqli_field_count($this->connection)) {
                    $course = mysqli_fetch_assoc($result);
                    $queryResult = new Course($course['Name'], $course["Description"], $course["Image"]
                    );
                }
            }
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
        }
        return $queryResult;
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function getAdministrator($id, $adminIsOwner) {
        $queryResult = NULL;
        if ($this->connection) {
            $id = mysqli_real_escape_string($this->connection, $id);
            $stmt = mysqli_stmt_init($this->connection);
            ($adminIsOwner) ?
                            mysqli_stmt_prepare($stmt, "SELECT Name, Role, Phone, Email, Password, Image FROM administrators WHERE Administrator_ID=?") :
                            mysqli_stmt_prepare($stmt, "SELECT Name, Role, Phone, Email, Password, Image FROM administrators WHERE Administrator_ID=? AND Role != 'owner'");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            if ($result = mysqli_stmt_get_result($stmt)) {
                require_once 'Administrator.php';
                if (mysqli_field_count($this->connection)) {
                    $administrator = mysqli_fetch_assoc($result);
                    $queryResult = new Administrator(
                            $administrator['Name'], $administrator["Role"], $administrator["Phone"], $administrator["Email"], $administrator["Password"], $administrator["Image"]
                    );
                }
                mysqli_free_result($result);
            }
            mysqli_stmt_close($stmt);
        }
        return $queryResult;
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function createStudent($name, $phone, $email, $image, $courses) {
        if ($this->connection) {
            $name = mysqli_real_escape_string($this->connection, $name);
            $phone = mysqli_real_escape_string($this->connection, $phone);
            $email = mysqli_real_escape_string($this->connection, $email);
            $image = mysqli_real_escape_string($this->connection, $image);
            $stmt = mysqli_stmt_init($this->connection);
            mysqli_stmt_prepare($stmt, "SELECT Email FROM students WHERE Email=?");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            if (mysqli_stmt_fetch($stmt)) {
                $operation_result = false;
            } else {
                mysqli_stmt_reset($stmt);
                mysqli_stmt_prepare($stmt, "INSERT INTO students ( Email, Name, Phone, Image ) VALUES ( ?, ?, ?, ? )");
                mysqli_stmt_bind_param($stmt, "ssss", $email, $name, $phone, $image);
                mysqli_stmt_execute($stmt);
                if (mysqli_stmt_affected_rows($stmt)) {
                    $studentId = mysqli_insert_id($this->connection);
                    foreach ($courses as $course) {
                        mysqli_stmt_reset($stmt);
                        $course = mysqli_real_escape_string($this->connection, $course);
                        mysqli_stmt_prepare($stmt, "INSERT INTO school ( Student_ID, Course_ID ) VALUES( ?, ( SELECT Course_ID FROM courses WHERE  name = ? ) )");
                        mysqli_stmt_bind_param($stmt, "is", $studentId, $course);
                        mysqli_stmt_execute($stmt);
                    }
                }
            }
            mysqli_stmt_close($stmt);
        }
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function createCourse($name, $description, $image) {
        if ($this->connection) {
            $name = mysqli_real_escape_string($this->connection, $name);
            $description = mysqli_real_escape_string($this->connection, $description);
            $image = mysqli_real_escape_string($this->connection, $image);
            $stmt = mysqli_stmt_init($this->connection);
            mysqli_stmt_prepare($stmt, "INSERT INTO courses ( Name, Description, Image ) VALUES(?,?,?)");
            mysqli_stmt_bind_param($stmt, "sss", $name, $description, $image);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function createAdministrator($name, $role, $phone, $email, $password, $image) {
        if ($this->connection) {
            $name = mysqli_real_escape_string($this->connection, $name);
            $role = mysqli_real_escape_string($this->connection, $role);
            $phone = mysqli_real_escape_string($this->connection, $phone);
            $email = mysqli_real_escape_string($this->connection, $email);
            $password = mysqli_real_escape_string($this->connection, $password);
            $image = mysqli_real_escape_string($this->connection, $image);
            $stmt = mysqli_stmt_init($this->connection);
            mysqli_stmt_prepare($stmt, "SELECT Email FROM administrators WHERE Email=?");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            if (mysqli_stmt_fetch($stmt)) {
                $operation_result = false;
            } else {
                mysqli_stmt_reset($stmt);
                mysqli_stmt_prepare($stmt, "INSERT INTO administrators (Email, Name, Role, Phone, Password, Image) VALUES(?,?,?,?,?,?)");
                mysqli_stmt_bind_param($stmt, "ssssss", $email, $name, $role, $phone, $password, $image);
                mysqli_stmt_execute($stmt);
                if (mysqli_stmt_affected_rows($stmt)) {
                    $operation_result = true;
                } else {
                    $operation_result = false;
                }
            }
            mysqli_stmt_close($stmt);
        }
        return $operation_result;
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function updateStudent($name, $phone, $email, $image, $id, $courses) {
        if ($this->connection) {
            $name = mysqli_real_escape_string($this->connection, $name);
            $phone = mysqli_real_escape_string($this->connection, $phone);
            $email = mysqli_real_escape_string($this->connection, $email);
            $image = mysqli_real_escape_string($this->connection, $image);
            $id = mysqli_real_escape_string($this->connection, $id);
            $stmt = mysqli_stmt_init($this->connection);
            if ($image !== "") {
                mysqli_stmt_prepare($stmt, "UPDATE students SET Name = ?, Phone=?, Email=?, Image=? WHERE Student_ID=?");
                mysqli_stmt_bind_param($stmt, "sssss", $name, $phone, $email, $image, $id);
            } else {
                mysqli_stmt_prepare($stmt, "UPDATE students SET Name = ?, Phone=?, Email=? WHERE Student_ID=?");
                mysqli_stmt_bind_param($stmt, "ssss", $name, $phone, $email, $id);
            }
            mysqli_stmt_execute($stmt);
            mysqli_stmt_reset($stmt);
            mysqli_stmt_prepare($stmt, "DELETE FROM school WHERE Student_ID = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            if (count($courses)) {
                foreach ($courses as $course) {
                    mysqli_stmt_reset($stmt);
                    $course = mysqli_real_escape_string($this->connection, $course);
                    mysqli_stmt_prepare($stmt, "INSERT INTO school (Student_ID, Course_ID ) VALUES( ?, ( SELECT Course_ID FROM courses WHERE  name = ? ) )");
                    mysqli_stmt_bind_param($stmt, "is", $id, $course);
                    mysqli_stmt_execute($stmt);
                }
            }
        }
        mysqli_stmt_close($stmt);
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function updateCourse($name, $description, $image, $id) {
        if ($this->connection) {
            $id = mysqli_real_escape_string($this->connection, $id);
            $name = mysqli_real_escape_string($this->connection, $name);
            $description = mysqli_real_escape_string($this->connection, $description);
            $image = mysqli_real_escape_string($this->connection, $image);
            $stmt = mysqli_stmt_init($this->connection);
            if ($image !== "") {
                mysqli_stmt_prepare($stmt, "UPDATE courses SET Name = ?, Description = ?, Image = ? WHERE Course_ID = ?");
                mysqli_stmt_bind_param($stmt, "ssss", $name, $description, $image, $id);
            } else {
                mysqli_stmt_prepare($stmt, "UPDATE courses SET Name = ?, Description = ? WHERE Course_ID = ?");
                mysqli_stmt_bind_param($stmt, "sss", $name, $description, $id);
            }
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function updateAdministrator($name, $phone, $email, $image, $role, $password, $id) {
        if ($this->connection) {
            $id = mysqli_real_escape_string($this->connection, $id);
            $email = mysqli_real_escape_string($this->connection, $email);
            $name = mysqli_real_escape_string($this->connection, $name);
            $phone = mysqli_real_escape_string($this->connection, $phone);
            $role = mysqli_real_escape_string($this->connection, $role);
            $password = mysqli_real_escape_string($this->connection, $password);
            $image = mysqli_real_escape_string($this->connection, $image);
            $stmt = mysqli_stmt_init($this->connection);
            if ($image !== "") {
                mysqli_stmt_prepare($stmt, "UPDATE administrators SET Name = ?, Role = ?, Phone = ?, Email = ?, Password = ?, Image = ? WHERE Administrator_ID = ?");
                mysqli_stmt_bind_param($stmt, "sssssss", $name, $role, $phone, $email, $password, $image, $id);
            } else {
                mysqli_stmt_prepare($stmt, "UPDATE administrators SET Name = ?, Role = ?, Phone = ?, Email = ?, Password = ? WHERE Administrator_ID = ?");
                mysqli_stmt_bind_param($stmt, "ssssss", $name, $role, $phone, $email, $password, $id);
            }
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function deleteStudentImage($id) {
        if ($this->connection) {
            $id = mysqli_real_escape_string($this->connection, $id);
            $stmt = mysqli_stmt_init($this->connection);
            mysqli_stmt_prepare($stmt, "SELECT Image FROM students WHERE Student_ID = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $img = mysqli_stmt_get_result($stmt);
            $old_image = mysqli_fetch_assoc($img);
            require_once 'file_manipulations.php';
            deleteImage($old_image['Image']);
            mysqli_free_result($img);
            mysqli_stmt_close($stmt);
        }
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function deleteCourseImage($id) {
        if ($this->connection) {
            $id = mysqli_real_escape_string($this->connection, $id);
            $stmt = mysqli_stmt_init($this->connection);
            mysqli_stmt_prepare($stmt, "SELECT Image FROM courses WHERE Course_ID = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $img = mysqli_stmt_get_result($stmt);
            $old_image = mysqli_fetch_assoc($img);
            require_once 'file_manipulations.php';
            deleteImage($old_image['Image']);
            mysqli_free_result($img);
            mysqli_stmt_close($stmt);
        }
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function deleteAdministratorImage($id) {
        if ($this->connection) {
            $id = mysqli_real_escape_string($this->connection, $id);
            $stmt = mysqli_stmt_init($this->connection);
            mysqli_stmt_prepare($stmt, "SELECT Image FROM administrators WHERE Administrator_ID = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $img = mysqli_stmt_get_result($stmt);
            $old_image = mysqli_fetch_assoc($img);
            require_once 'file_manipulations.php';
            deleteImage($old_image['Image']);
            mysqli_free_result($img);
            mysqli_stmt_close($stmt);
        }
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function deleteStudent($id) {
        if ($this->connection) {
            $id = mysqli_real_escape_string($this->connection, $id);
            $stmt = mysqli_stmt_init($this->connection);
            mysqli_stmt_prepare($stmt, "DELETE FROM school  WHERE Student_ID = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_reset($stmt);
            mysqli_stmt_prepare($stmt, "DELETE FROM students  WHERE Student_ID = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function deleteCourse($id) {
        if ($this->connection) {
            $id = mysqli_real_escape_string($this->connection, $id);
            $stmt = mysqli_stmt_init($this->connection);
            mysqli_stmt_prepare($stmt, "DELETE FROM school  WHERE Course_ID = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_reset($stmt);
            mysqli_stmt_prepare($stmt, "DELETE FROM courses  WHERE Course_ID = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    /* ----------------------------------------------------------------------------------------------------------------------------------------------------- */

    public function deleteAdministrator($id, $email) {
        if ($this->connection) {
            $id = mysqli_real_escape_string($this->connection, $id);
            $email = mysqli_real_escape_string($this->connection, $id);
            $stmt = mysqli_stmt_init($this->connection);
            mysqli_stmt_prepare($stmt, "DELETE FROM administrators  WHERE Administrator_ID = ? AND Email != ?");
            mysqli_stmt_bind_param($stmt, "is", $id, $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

}
