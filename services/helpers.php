<?php 

function isEmptyFields($data) {
    $errors = [];
    foreach($data as $key => $value) {
        if (empty($value)) {
            $errors[] = $key;
        }
    }

    return $errors;
}