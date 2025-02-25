<?php
require_once '../includes/functions.php';

try {
    $roles = getAllRoles();
    echo '<pre>';
    print_r($roles);
    echo '</pre>';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

