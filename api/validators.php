<?php

function validateUserData($userName, $password) {
    return (preg_match("/^.*([a-z0-9])([@]).*([a-z0-9])(\.).*([a-z])(\.)?(.*([a-z]))?$/", $userName) && strlen($password) >= 8);
}

function validateStudentData($name, $phone, $email, $id) {
    return (preg_match("/^([A-Z]).*([a-z])([ ])([A-Z]).*([a-z])$/", $name) &&
            preg_match("/^.*([0-9])(\-)?.*([0-9])$/", $phone) &&
            preg_match("/^.*([a-z0-9])([@]).*([a-z0-9])(\.).*([a-z])(\.)?(.*([a-z]))?$/", $email) &&
            preg_match("/^.*([0-9])$/", $id));
}

function validateCourseData($name, $description, $id) {
    return (preg_match("/^.*([A-Za-z0-9 ])$/", $name) && preg_match("/^.*([A-Za-z0-9 ])$/", $description) && preg_match("/^.*([0-9])$/", $id));
}

function validateAdministratorData($name, $phone, $email, $role, $password) {
    return (preg_match("/^([A-Z]).*([a-z])([ ])([A-Z]).*([a-z])$/", $name) &&
            preg_match("/^.*([0-9])(\-)?.*([0-9])$/", $phone) &&
            preg_match("/^.*([a-z0-9])([@]).*([a-z0-9])(\.).*([a-z])(\.)?(.*([a-z]))?$/", $email) &&
            ($role = "owner" || $role === "manager" || $role === "sales") && (strlen($password) >= 8));
}
