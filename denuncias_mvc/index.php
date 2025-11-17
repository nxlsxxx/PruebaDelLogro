<?php
session_start();

require_once 'config/database.php';
require_once 'models/User.php';
require_once 'controllers/UserController.php';
require_once 'controllers/DenunciaController.php';
require_once 'controllers/DashboardController.php';
require_once 'controllers/AboutController.php';

// Determine the controller and action
$controllerName = $_GET['controller'] ?? 'Denuncia';
$actionName = $_GET['action'] ?? 'index';

// If not logged in, force to User controller for login/register
if (!isset($_SESSION['user_id'])) {
    $allowedPublicActions = [
        'showLogin',
        'login',
        'showRegister',
        'register',
    ];
    if ($controllerName !== 'User' || !in_array($actionName, $allowedPublicActions)) {
        // Redirect to login if trying to access unauthorized pages
        header('Location: index.php?controller=User&action=showLogin');
        exit();
    }
}

$controllerClass = $controllerName . 'Controller';

// Ensure the controller file exists before requiring it
$controllerFile = 'controllers/' . $controllerClass . '.php';
if (!file_exists($controllerFile)) {
    // Handle 404 or redirect to a default page
    die("Controller file not found: " . $controllerFile);
}

$controllerObj = new $controllerClass();

// Check if the action exists in the controller
if (!method_exists($controllerObj, $actionName)) {
    die("Action not found: " . $actionName);
}

ob_start(); // Start output buffering
$controllerObj->$actionName();
$content = ob_get_clean(); // Get buffered content and clean buffer

// For login and register forms, we might not want to include the full layout with sidebar/navbar
// This will be handled in layout.php to show/hide sections based on session
include "views/layout.php";

?>
