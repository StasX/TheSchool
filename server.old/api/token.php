<?php

function createToken($admin) {
    $iv = "e792db641824daef";
    $key = "8f78dba0332f15f0e373260c8c00fb5d43a549f148e6bdcd63509e2f2779e00a";
    $token = base64_encode(openssl_encrypt($admin->password . ',' . $admin->email . ',' . time(), "AES-256-CBC", $key, 0, $iv));
    return $token;
}

function parseToken($token) {
    $iv = "e792db641824daef";
    $key = "8f78dba0332f15f0e373260c8c00fb5d43a549f148e6bdcd63509e2f2779e00a";

    $data = base64_decode($token);
    $decryptedData = openssl_decrypt($data, "AES-256-CBC", $key, 0, $iv);
    $time = substr($decryptedData, strrPos($decryptedData, ",") + 1);

    if ((time() - (7 * 60)) - $time <= 0) {
        $emailAndPassword = substr($decryptedData, 0, (strlen($decryptedData) - (strlen($time) + 1)));
        $email = substr($emailAndPassword, strrPos($emailAndPassword, ",") + 1);
        $password = substr($emailAndPassword, 0, (strlen($emailAndPassword) - (strlen($email) + 1)));
        require_once 'Modules/User.php';
        $user = new User();
        $user->setEmail($email);
        $user->setPass($password);
    } else {
        $user = NULL;
    }
    return $user;
}
