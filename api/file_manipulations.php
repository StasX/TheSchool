<?php

function moveUploadedFile($directory, $uploadedFile) {
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $basename = bin2hex(openssl_random_pseudo_bytes(8));
    $filename = sprintf('%s.%0.8s', $basename, $extension);
    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
    return $filename;
}

function deleteImage($image) {
    $image = str_replace('../', DIRECTORY_SEPARATOR, $image);
    $image = str_replace('/', DIRECTORY_SEPARATOR, $image);
    unlink(dirname(__DIR__) . $image);
}
