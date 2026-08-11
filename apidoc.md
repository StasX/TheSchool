# API Documentation

## Web Routes

| Description | Method + Path | Request / Response |
| --- | --- | --- |
| Application home page | **GET** `/` | **Response:** `welcome` Blade view |

## Authentication

| Description | Method + Path | Request / Response |
| --- | --- | --- |
| Login to the system | **POST** `/login` | **Request:** Authentication credentials<br>**Response:** Authentication result |
| Logout from the system | **GET** `/logout` | **Request:** Authenticated session<br>**Response:** Logout result |

## Administrators

| Description | Method + Path | Request / Response |
| --- | --- | --- |
| Get all administrators | **GET** `/administrator` | **Request:** Authenticated administrator<br>**Response:** `JSON array (administrators)` |
| Get administrator by ID | **GET** `/administrator/{id}` | **Request:** `id: int`<br>**Response:** `JSON object (administrator)` |
| Create administrator | **POST** `/administrator` | **Request:** Administrator data<br>**Response:** `JSON object / result` |
| Update administrator | **PUT** `/administrator/{id}` | **Request:** `id: int`, Administrator data<br>**Response:** `JSON object / result` |
| Delete administrator | **DELETE** `/administrator/{id}` | **Request:** `id: int`<br>**Response:** `JSON object / result` |

## Students

| Description | Method + Path | Request / Response |
| --- | --- | --- |
| Get all students | **GET** `/student` | **Request:** Authenticated administrator<br>**Response:** `JSON array (students)` |
| Get student by ID | **GET** `/student/{id}` | **Request:** `id: int`<br>**Response:** `JSON object (student)` |
| Create student | **POST** `/student` | **Request:** Student data<br>**Response:** `JSON object / result` |
| Update student | **PUT** `/student` | **Request:** Student data<br>**Response:** `JSON object / result` |
| Delete student | **DELETE** `/student/{id}` | **Request:** `id: int`<br>**Response:** `JSON object / result` |

## Courses

| Description | Method + Path | Request / Response |
| --- | --- | --- |
| Get all courses | **GET** `/course` | **Request:** Authenticated administrator<br>**Response:** `JSON array (courses)` |
| Get course by ID | **GET** `/course/{id}` | **Request:** `id: int`<br>**Response:** `JSON object (course)` |
| Create course | **POST** `/course` | **Request:** Course data<br>**Response:** `JSON object / result` |
| Update course | **PUT** `/course/{id}` | **Request:** `id: int`, Course data<br>**Response:** `JSON object / result` |
| Delete course | **DELETE** `/course/{id}` | **Request:** `id: int`<br>**Response:** `JSON object / result` |
