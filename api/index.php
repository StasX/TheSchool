<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;

require 'vendor/autoload.php';
$app = new Slim\App();
$container = $app->getContainer();
$container['upload_directory'] = '../upload/';

$app->post('/login', function ($request, $response) {
    $userData = $request->getParsedBody();
    $pass = $userData["pass"];
    require_once 'validators.php';
    if (validateUserData($userData["user"], $pass)) {
        require_once 'Modules/Connector.php';
        $connector = Connector::getInstace();
        $admin = $connector->getCurentUser($userData["user"], $pass);
        if ($admin !== NULL) {

            require_once 'token.php';
            require_once 'htmlPreparator.php';
            $response->getBody()->write('{"title":"School" , "body":"' . getSchool($admin) . '","token" : "' . createToken($admin) . '"}');
        } else {
            $response->getBody()->write("Username or password is incorect");
        }
    } else {
        $response->getBody()->write("Username or password is incorect");
    }
    return $response;
});

$app->get('/school/{token}', function ($request, $response, $args) {
    require_once 'token.php';
    $user = parseToken($args["token"]);
    if ($user != NULL) {
        $userName = $user->getEmail();
        $pass = $user->getPass();
        require_once 'validators.php';
        if (validateUserData($userName, $pass)) {
            require_once 'Modules/Connector.php';
            $connector = Connector::getInstace();
            $admin = $connector->getCurentUser($userName, $pass);
            if ($admin !== NULL) {
                require_once 'htmlPreparator.php';
                $response->getBody()->write('{"title":"School" , "body":"' . getSchool($admin) . '"}');
            }
        }
    }
    return $response;
});

$app->get('/administration/{token}', function ($request, $response, $args) {
    require_once 'token.php';
    $user = parseToken($args["token"]);
    if ($user != NULL) {
        $userName = $user->getEmail();
        $pass = $user->getPass();
        require_once 'Modules/Connector.php';
        $connector = Connector::getInstace();
        $admin = $connector->getCurentUser($userName, $pass);
        if ($admin !== NULL && $admin->role != "sale") {
            require_once 'htmlPreparator.php';
            $response->getBody()->write('{"title":"Administration" , "body":"' . getAdministration($admin) . '"}');
        }
    }
    return $response;
});

$app->post('/createStudent', function ($request, $response) {
    $directory = $this->get('upload_directory');
    $studentData = $request->getParsedBody();
    require_once 'token.php';
    $user = parseToken($studentData["token"]);
    if ($user != NULL) {
        $userName = $user->getEmail();
        $pass = $user->getPass();
        require_once 'validators.php';
        require_once 'Modules/Connector.php';
        if (validateUserData($userName, $pass)) {
            $connector = Connector::getInstace();
            if ($connector->validateUser($userName, $pass) && (validateStudentData($studentData{"name"}, $studentData["phone"], $studentData["email"], 0))) {
                $name = $studentData["name"];
                $phone = $studentData["phone"];
                $email = $studentData["email"];
                if (isset($studentData["course"])) {
                    $courses = $studentData["course"];
                } else {
                    $courses = array();
                }
                $uploadedFiles = $request->getUploadedFiles();
                $uploadedFile = $uploadedFiles['image'];
                if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                    require_once 'file_manipulations.php';
                    $image = moveUploadedFile($directory, $uploadedFile);
                }
                $connector->createStudent($name, $phone, $email, ("../upload/" . $image), $courses);
                $students = $connector->getAllStudents();
                $response->getBody()->write(json_encode($students));
            }
        }
    }
    return $response;
});

$app->post('/createCourse', function ($request, $response) {
    $directory = $this->get('upload_directory');
    $courseData = $request->getParsedBody();
    require_once 'token.php';
    $user = parseToken($courseData["token"]);
    if ($user != NULL) {
        $userName = $user->getEmail();
        $pass = $user->getPass();
        require_once 'validators.php';
        require_once 'Modules/Connector.php';
        if (validateUserData($userName, $pass)) {
            $connector = Connector::getInstace();
            if ($connector->validateUser($userName, $pass) && (validateCourseData($courseData{"name"}, $courseData["description"], 0))) {
                $name = $courseData["name"];
                $description = $courseData["description"];
                $uploadedFiles = $request->getUploadedFiles();
                $uploadedFile = $uploadedFiles['image'];
                if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                    require_once 'file_manipulations.php';
                    $image = moveUploadedFile($directory, $uploadedFile);
                }
                $connector->createCourse($name, $description, ("../upload/" . $image));
                $response->getBody()->write(json_encode($connector->getAllCourses()));
            }
        }
    }
    return $response;
});

$app->post('/createAdministrator', function ($request, $response) {
    $directory = $this->get('upload_directory');
    $administratorData = $request->getParsedBody();
    require_once 'token.php';
    $user = parseToken($administratorData["token"]);
    if ($user != NULL) {
        $userName = $user->getEmail();
        $pass = $user->getPass();
        require_once 'validators.php';
        require_once 'Modules/Connector.php';
        if (validateUserData($userName, $pass)) {
            $connector = Connector::getInstace();
            $admin = $connector->getCurentUser($userName, $pass);
            if ($admin !== NULL && $admin->role !== "sales" && $admin->role !== "sales") {
                if (validateAdministratorData($administratorData{"name"}, $administratorData["phone"], $administratorData["email"], $administratorData["role"], $administratorData["password"])) {
                    $name = $administratorData["name"];
                    $role = $administratorData["role"];
                    $password = $administratorData["password"];
                    $phone = $administratorData["phone"];
                    $email = $administratorData["email"];
                    $uploadedFiles = $request->getUploadedFiles();
                    $uploadedFile = $uploadedFiles['image'];
                    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                        require_once 'file_manipulations.php';
                        $image = moveUploadedFile($directory, $uploadedFile);
                    }
                    $connector->createAdministrator($name, $role, $phone, $email, hash("sha256", $password), ("../upload/" . $image));
                    $administrators = $connector->getAllAdministrators($admin->role === "owner");
                    $response->getBody()->write(json_encode($administrators));
                }
            }
        }
    }
    return $response;
});

$app->post('/updateStudent', function ($request, $response) {
    $directory = $this->get('upload_directory');
    $studentData = $request->getParsedBody();
    require_once 'token.php';
    $user = parseToken($studentData["token"]);
    if ($user != NULL) {
        $userName = $user->getEmail();
        $pass = $user->getPass();
        require_once 'validators.php';
        require_once 'Modules/Connector.php';
        if (validateUserData($userName, $pass)) {
            $connector = Connector::getInstace();
            if ($connector->validateUser($userName, $pass) && (validateStudentData($studentData{"name"}, $studentData["phone"], $studentData["email"], $studentData["id"]))) {
                $id = $connector->getRealStudentId($studentData["id"]);
                $name = $studentData["name"];
                $phone = $studentData["phone"];
                $email = $studentData["email"];
                $courses = ( isset($studentData["course"]) ) ? $courses = $studentData["course"] : array();
                $uploadedFiles = $request->getUploadedFiles();
                if (isset($uploadedFiles['image'])) {
                    $connector->deleteStudentImage($id);
                    $uploadedFile = $uploadedFiles['image'];
                    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                        require_once 'file_manipulations.php';
                        $image = moveUploadedFile($directory, $uploadedFile);
                        $image = "../upload/" . $image;
                    }
                } else {
                    $image = "";
                }
                $connector->updateStudent($name, $phone, $email, $image, $id, $courses);
                $students = $connector->getAllStudents();
                $response->getBody()->write(json_encode($students));
            }
        }
    }

    return $response;
});

$app->post('/updateCourse', function ($request, $response) {
    $directory = $this->get('upload_directory');
    $courseData = $request->getParsedBody();
    require_once 'token.php';
    $user = parseToken($courseData["token"]);
    if ($user != NULL) {
        $userName = $user->getEmail();
        $pass = $user->getPass();
        require_once 'validators.php';
        require_once 'Modules/Connector.php';
        if (validateUserData($userName, $pass)) {
            $connector = Connector::getInstace();
            if ($connector->validateUser($userName, $pass) && (validateCourseData($courseData{"name"}, $courseData["description"], $courseData["id"]))) {
                $id = $connector->getRealCourseId($courseData["id"]);

                $name = $courseData["name"];
                $description = $courseData["description"];
                $uploadedFiles = $request->getUploadedFiles();
                if (isset($uploadedFiles['image'])) {
                    $connector->deleteCourseImage($id);
                    $uploadedFile = $uploadedFiles['image'];
                    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                        require_once 'file_manipulations.php';
                        $image = moveUploadedFile($directory, $uploadedFile);
                        $image = "../upload/" . $image;
                    }
                } else {
                    $image = "";
                }
                $connector->updateCourse($name, $description, $image, $id);
                $courses = $connector->getAllCourses();
                $response->getBody()->write(json_encode($courses));
            }
        }
    }

    return $response;
});

$app->post('/updateAdministrator', function ($request, $response) {
    $directory = $this->get('upload_directory');
    $administratorData = $request->getParsedBody();
    require_once 'token.php';
    $user = parseToken($administratorData["token"]);
    if ($user != NULL) {
        $userName = $user->getEmail();
        $pass = $user->getPass();
        require_once 'validators.php';
        require_once 'Modules/Connector.php';
        if (validateUserData($userName, $pass)) {
            $connector = Connector::getInstace();
            $admin = $connector->getCurentUser($userName, $pass);
            if ($admin !== NULL && $admin->role !== "sales") {
                $user = parseToken($administratorData["token"]);
                if (validateAdministratorData($administratorData["name"], $administratorData["phone"], $administratorData["email"], $administratorData["role"], $administratorData["password"], $administratorData["id"])) {
                    $id = $connector->getRealAdministratorId($administratorData["id"]);
                    $name = $administratorData["name"];
                    $phone = $administratorData["phone"];
                    $email = $administratorData["email"];
                    $role = $administratorData["role"];
                    $password = $administratorData["password"];
                    if ($administratorData["role"] !== "owner" && $admin->role != "owner") {
                        $role = $administratorData["role"];
                    }

                    $password = $administratorData["password"];
                    $uploadedFiles = $request->getUploadedFiles();
                    if (isset($uploadedFiles['image'])) {
                        $connector->deleteAdministratorImage($id);
                        $uploadedFile = $uploadedFiles['image'];
                        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                            require_once 'file_manipulations.php';
                            $image = moveUploadedFile($directory, $uploadedFile);
                        }
                    } else {
                        $image = "";
                    }
                    $connector->updateAdministrator($name, $phone, $email, ("../upload/" . $image), $role, $password, $id);
                    $adminIsOwner = ($admin->role === "owner");
                    $administrators = $connector->getAllAdministrators($adminIsOwner);
                    $response->getBody()->write(json_encode($administrators));
                }
            }
        }
    }
    return $response;
});

$app->post('/deleteStudent', function ($request, $response) {
    $studentData = $request->getParsedBody();
    require_once 'token.php';
    $user = parseToken($studentData["token"]);
    if ($user != NULL) {
        $userName = $user->getEmail();
        $pass = $user->getPass();
        require_once 'validators.php';
        require_once 'Modules/Connector.php';
        if (validateUserData($userName, $pass)) {
            $connector = Connector::getInstace();
            if ($connector->validateUser($userName, $pass)) {
                $id = $connector->getRealStudentId($studentData["id"]);
                $connector->deleteStudentImage($id);
                $connector->deleteStudent($id);
                $students = $connector->getAllStudents();
            }
        }
    }
    $response->getBody()->write(json_encode($students));
    return $response;
});

$app->post('/deleteCourse', function ($request, $response) {
    $courseData = $request->getParsedBody();
    require_once 'token.php';
    $user = parseToken($courseData["token"]);
    if ($user != NULL) {
        $userName = $user->getEmail();
        $pass = $user->getPass();
        require_once 'validators.php';
        require_once 'Modules/Connector.php';
        $connector = Connector::getInstace();
        $admin = $connector->getCurentUser($userName, $pass);
        if ($admin !== NULL && $admin->role !== "sales") {
            if ($connector->validateUser($userName, $pass)) {
                $id = $connector->getRealCourseId($courseData["id"]);
                $connector->deleteCourseImage($id);
                $connector->deleteCourse($id);
                $courses = $connector->getAllCourses();
            }
        }
    }
    $response->getBody()->write(json_encode($courses));
    return $response;
});

$app->post('/deleteAdministrator', function ($request, $response) {
    $administratorData = $request->getParsedBody();
    require_once 'token.php';
    $user = parseToken($administratorData["token"]);
    if ($user != NULL) {
        $userName = $user->getEmail();
        $pass = $user->getPass();
        require_once 'validators.php';
        require_once 'Modules/Connector.php';
        if (validateUserData($userName, $pass)) {
            $connector = Connector::getInstace();
            $admin = $connector->getCurentUser($userName, $pass);
            if ($admin !== NULL && $admin->role !== "sales") {
                $id = $connector->getRealAdministratorId($administratorData["id"]);
                $connector->deleteAdministratorImage($id);
                $connector->deleteAdministrator($id, $admin->email);
                $administrators = $connector->getAllAdministrators($admin->role === "owner");
            }
        }
    }
    $response->getBody()->write(json_encode($administrators));
    return $response;
});

$app->get('/getStudents/{token}', function ($request, $response, $args) {
    require_once 'token.php';
    $user = parseToken($args["token"]);
    if ($user != NULL) {
        $userName = $user->getEmail();
        $pass = $user->getPass();
        require_once 'validators.php';
        require_once 'Modules/Connector.php';
        if (validateUserData($userName, $pass)) {
            $connector = Connector::getInstace();
            if ($admin = $connector->validateUser($userName, $pass)) {
                $students = $connector->getAllStudents();
                $response->getBody()->write(json_encode($students));
            }
        }
    }
    return $response;
});

$app->get('/getCourses/{token}', function ( $request, $response, $args ) {
    require_once 'token.php';
    $user = parseToken($args["token"]);
    if ($user != NULL) {
        $userName = $user->getEmail();
        $pass = $user->getPass();
        require_once 'validators.php';
        require_once 'Modules/Connector.php';
        if (validateUserData($userName, $pass)) {
            $connector = Connector::getInstace();
            if ($connector->validateUser($userName, $pass)) {
                $courses = $connector->getAllCourses();
                $response->getBody()->write(json_encode($courses));
            }
        }
    }
    return $response;
});

$app->get('/getAdministrators/{token}', function ( $request, $response, $args) {
    require_once 'token.php';
    $user = parseToken($args["token"]);
    if ($user != NULL) {
        $userName = $user->getEmail();
        $pass = $user->getPass();
        require_once 'validators.php';
        require_once 'Modules/Connector.php';
        if (validateUserData($userName, $pass)) {
            $connector = Connector::getInstace();
            $admin = $connector->getCurentUser($userName, $pass);
            if ($admin !== NULL) {
                $adminIsOwner = ($admin->role === "owner");
                $administrators = $connector->getAllAdministrators($adminIsOwner);
                $response->getBody()->write(json_encode($administrators));
            }
        }
    }
    return $response;
});

$app->get('/getStudent/{id}/{token}', function ($request, $response, $args) {
    require_once 'token.php';
    $user = parseToken($args["token"]);
    if ($user != NULL) {
        $userName = $user->getEmail();
        $pass = $user->getPass();
        require_once 'validators.php';
        require_once 'Modules/Connector.php';
        if (validateUserData($userName, $pass)) {
            $connector = Connector::getInstace();
            if ($admin = $connector->validateUser($userName, $pass)) {
                $id = $connector->getRealStudentId($args["id"]);
                $student = $connector->getStudent($id);
                $courses = $connector->getCoursesOfStudent($id);
                require_once 'htmlPreparator.php';
                $response->getBody()->write(getStudentInfo($student, $courses));
            }
        }
    }
    return $response;
});

$app->get('/getCourse/{id}/{token}', function ($request, $response, $args) {
    require_once 'token.php';
    $user = parseToken($args["token"]);
    if ($user != NULL) {
        $userName = $user->getEmail();
        $pass = $user->getPass();
        require_once 'validators.php';
        require_once 'Modules/Connector.php';
        if (validateUserData($userName, $pass)) {
            $connector = Connector::getInstace();
            $admin = $connector->getCurentUser($userName, $pass);
            if ($admin !== null) {
                $id = $connector->getRealCourseId($args["id"]);
                $course = $connector->getCourse($id);
                $students = $connector->getStudentsFromCourse($id);
                require_once 'htmlPreparator.php';
                $response->getBody()->write(getCourseInfo($course, $students, ($admin->role === "sales")));
            }
        }
    }
    return $response;
});

$app->get('/getStudentForm/{id}/{token}', function ($request, $response, $args) {
    require_once 'token.php';
    $user = parseToken($args["token"]);
    if ($user != NULL) {
        $userName = $user->getEmail();
        $pass = $user->getPass();
        require_once 'validators.php';
        require_once 'Modules/Connector.php';
        if (validateUserData($userName, $pass)) {
            $connector = Connector::getInstace();
            if ($admin = $connector->validateUser($userName, $pass)) {
                $id = $connector->getRealStudentId($args["id"]);
                $student = $connector->getStudent($id);
                $courses = $connector->getNamesOfCourseOfStudent($id);
                $allCourses = $connector->getAllCourses();
                require_once 'htmlPreparator.php';
                $response->getBody()->write(getStudentForm($student, $courses, $allCourses));
            }
        }
    }
    return $response;
});

$app->get('/getCourseForm/{id}/{token}', function ($request, $response, $args) {
    require_once 'token.php';
    $user = parseToken($args["token"]);
    if ($user != NULL) {
        $userName = $user->getEmail();
        $pass = $user->getPass();
        require_once 'validators.php';
        require_once 'Modules/Connector.php';
        if (validateUserData($userName, $pass)) {
            $connector = Connector::getInstace();
            if ($connector->validateUser($userName, $pass)) {
                $id = $connector->getRealCourseId($args["id"]);
                $course = $connector->getCourse($id);
                $students = $connector->getStudentsFromCourse($id);
                require_once 'htmlPreparator.php';
                $response->getBody()->write(getCourseForm($course, $students));
            }
        }
    }
    return $response;
});

$app->get('/getNewStudentForm/{token}', function ($request, $response, $args) {
    require_once 'token.php';
    $user = parseToken($args["token"]);
    if ($user != NULL) {
        $userName = $user->getEmail();
        $pass = $user->getPass();
        require_once 'validators.php';
        if (validateUserData($userName, $pass)) {
            require_once 'Modules/Connector.php';
            $connector = Connector::getInstace();
            $admin = $connector->getCurentUser($userName, $pass);
            if ($admin !== NULL) {
                require_once 'htmlPreparator.php';
                $response->getBody()->write(getNewStudentForm($connector->getAllCourses()));
            }
        }
    }
    return $response;
});

$app->get('/getAdministrator/{id}/{token}', function ( $request, $response, $args) {
    require_once 'token.php';
    $user = parseToken($args["token"]);
    if ($user != NULL) {
        $userName = $user->getEmail();
        $pass = $user->getPass();
        require_once 'validators.php';
        require_once 'Modules/Connector.php';
        if (validateUserData($userName, $pass)) {
            $connector = Connector::getInstace();
            $admin = $connector->getCurentUser($userName, $pass);
            if ($admin !== NULL) {
                $adminIsOwner = ($admin->role === "owner");
                $id = $connector->getRealAdministratorId($args["id"]);
                $administrator = $connector->getAdministrator($id, $adminIsOwner);
                if ($administrator != NULL) {
                    require_once 'htmlPreparator.php';
                    $response->getBody()->write(getAdministratorForm($administrator, $administrator->email === $admin->email));
                }
            }
        }
    }
    return $response;
});

$app->get('/renewToken/{token}', function($request, $response, $args) {
    require_once 'token.php';
    $user = parseToken($args["token"]);
    if ($user != NULL) {
        $userName = $user->getEmail();
        $pass = $user->getPass();
        require_once 'validators.php';
        require_once 'Modules/Connector.php';
        if (validateUserData($userName, $pass)) {
            $connector = Connector::getInstace();
            $admin = $connector->getCurentUser($userName, $pass);
            if ($admin !== NULL) {
                $response->getBody()->write(createToken($admin));
            }
        }
    }
    return $response;
});
$app->run();
