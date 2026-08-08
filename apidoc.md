# API Documentation

| Description | Method + Path | Request / Response |
|---|---|---|
| Login to the system | **POST** `/login` | **Request:**<br>`pass: string`<br>`user: string`<br><br>**Response — success:**<br>`data:`<br>&nbsp;&nbsp;`title: string`<br>&nbsp;&nbsp;`body: string (HTML)`<br>&nbsp;&nbsp;`token: string`<br><br>**Response — failure:**<br>`data:`<br>&nbsp;&nbsp;`warning: string` |
| Get school page | **GET** `/school/{token}` | **Request:**<br>`token: string`<br><br>**Response:**<br>`data:`<br>&nbsp;&nbsp;`title: string`<br>&nbsp;&nbsp;`body: string (HTML)` |
| Get administration page | **GET** `/administration/{token}` | **Request:**<br>`token: string`<br><br>**Response:**<br>`data:`<br>&nbsp;&nbsp;`title: string`<br>&nbsp;&nbsp;`body: string (HTML)` |
| Create student | **POST** `/createStudent` | **Request:**<br>`token: string`<br>`name: string`<br>`phone: string`<br>`email: string`<br>`course: array`<br>`image: file`<br><br>**Response:**<br>`JSON array (all students)` |
| Create course | **POST** `/createCourse` | **Request:**<br>`token: string`<br>`name: string`<br>`description: string`<br>`image: file`<br><br>**Response:**<br>`JSON array (all courses)` |
| Create administrator | **POST** `/createAdministrator` | **Request:**<br>`token: string`<br>`name: string`<br>`role: string`<br>`password: string`<br>`phone: string`<br>`email: string`<br>`image: file`<br><br>**Response:**<br>`JSON array (all administrators)` |
| Update student | **POST** `/updateStudent` | **Request:**<br>`token: string`<br>`name: string`<br>`phone: string`<br>`email: string`<br>`course: array`<br>`image: file`<br><br>**Response:**<br>`JSON array (all students)` |
| Update course | **POST** `/updateCourse` | **Request:**<br>`token: string`<br>`name: string`<br>`description: string`<br>`image: file`<br><br>**Response:**<br>`JSON array (all courses)` |
| Update administrator | **POST** `/updateAdministrator` | **Request:**<br>`token: string`<br>`name: string`<br>`role: string`<br>`password: string`<br>`phone: string`<br>`email: string`<br>`image: file`<br><br>**Response:**<br>`JSON array (all administrators)` |
| Delete student | **POST** `/deleteStudent` | **Request:**<br>`token: string`<br>`id: int`<br><br>**Response:**<br>`JSON array (all students)` |
| Delete course | **POST** `/deleteCourse` | **Request:**<br>`token: string`<br>`id: int`<br><br>**Response:**<br>`JSON array (all courses)` |
| Delete administrator | **POST** `/deleteAdministrator` | **Request:**<br>`token: string`<br>`id: int`<br><br>**Response:**<br>`JSON array (all administrators)` |
| Get all students | **GET** `/getStudents/{token}` | **Request:**<br>`token: string`<br><br>**Response:**<br>`JSON array (all students)` |
| Get all courses | **GET** `/getCourses/{token}` | **Request:**<br>`Id: int`<br>`token: string`<br><br>**Response:**<br>`JSON array (all courses)` |
| Get all administrators | **GET** `/getAdministrators/{token}` | **Request:**<br>`token: string`<br><br>**Response:**<br>`JSON array (all administrators)` |
| Get student information screen | **GET** `/getStudent/{id}/{token}` | **Request:**<br>`Id: int`<br>`token: string`<br><br>**Response:**<br>`Student information screen (HTML)` |
| Get course information screen | **GET** `/getCourse/{id}/{token}` | **Request:**<br>`Id: int`<br>`token: string`<br><br>**Response:**<br>`Course information screen (HTML)` |
| Get "Edit student" screen | **GET** `/getStudentForm/{id}/{token}` | **Request:**<br>`Id: int`<br>`token: string`<br><br>**Response:**<br>`"Edit student" screen (HTML)` |
| Get "Edit course" screen | **GET** `/getCourseForm/{id}/{token}` | **Request:**<br>`Id: int`<br>`token: string`<br><br>**Response:**<br>`"Edit course" screen (HTML)` |
| Get "New student" screen | **GET** `/getNewStudentForm/{token}` | **Request:**<br>`token: string`<br><br>**Response:**<br>`"New student" screen (HTML)` |
| Get "Edit administrator" screen | **GET** `/getAdministrator/{id}/{token}` | **Request:**<br>`Id: int`<br>`token: string`<br><br>**Response:**<br>`"Edit administrator" screen (HTML)` |
| Renew token | **GET** `/renewToken/{token}` | **Request:**<br>`token: string`<br><br>**Response:**<br>`{}` |
