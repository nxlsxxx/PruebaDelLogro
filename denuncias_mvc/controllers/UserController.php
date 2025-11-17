<?php

require_once 'models/User.php';
require_once 'config/database.php';

class UserController {
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    public function showLogin() {
        // If user is already logged in, redirect to home
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit();
        }
        include 'views/login_form.php';
    }

    public function showRegister() {
        // If user is already logged in, redirect to home
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit();
        }
        include 'views/register_form.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->user->dni = $_POST['dni'] ?? '';
            $this->user->contrasena = $_POST['contrasena'] ?? '';

            if ($this->user->login()) {
                // Login successful, set session variables
                $_SESSION['user_id'] = $this->user->id;
                $_SESSION['user_name'] = $this->user->nombre . ' ' . $this->user->apellido_paterno;
                $_SESSION['user_rol'] = $this->user->rol_id; // This now holds the role name (e.g., 'ciudadano')
                $_SESSION['user_phone'] = $this->user->telefono_ciudadano;

                header('Location: index.php');
                exit();
            } else {
                // Login failed
                $error = "DNI o contraseña incorrectos.";
                include 'views/login_form.php';
            }
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->user->nombre = $_POST['nombre'] ?? '';
            $this->user->apellido_paterno = $_POST['apellido_paterno'] ?? '';
            $this->user->apellido_materno = $_POST['apellido_materno'] ?? '';
            $this->user->dni = $_POST['dni'] ?? '';
            $this->user->correo = $_POST['correo'] ?? '';
            $this->user->contrasena = $_POST['contrasena'] ?? '';
            $this->user->telefono_ciudadano = $_POST['telefono_ciudadano'] ?? ''; // Assuming this field is now in the registration form

            if (empty($this->user->nombre) || empty($this->user->apellido_paterno) || empty($this->user->apellido_materno) || empty($this->user->dni) || empty($this->user->correo) || empty($this->user->contrasena) || empty($this->user->telefono_ciudadano)) {
                $error = "Todos los campos son obligatorios.";
                include 'views/register_form.php';
                return;
            }

            if ($this->user->register()) {
                // Registration successful, log in the user automatically
                $_SESSION['user_id'] = $this->user->id;
                $_SESSION['user_name'] = $this->user->nombre . ' ' . $this->user->apellido_paterno;
                $_SESSION['user_rol'] = 'ciudadano'; // New registered users are always 'ciudadano'
                $_SESSION['user_phone'] = $this->user->telefono_ciudadano;

                header('Location: index.php');
                exit();
            } else {
                $error = "Error al registrar. El DNI o correo ya existen, o hubo un problema.";
                include 'views/register_form.php';
            }
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: index.php?controller=User&action=showLogin');
        exit();
    }

    public function showCreate() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'ingeniero') {
            header('Location: index.php');
            exit();
        }
        $roles = $this->user->getAllRoles(); // Assuming a method to get all roles
        include 'views/user_create_form.php';
    }

    public function create() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'ingeniero') {
            header('Location: index.php');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Collect and sanitize input data
            $this->user->nombre = $_POST['nombre'] ?? '';
            $this->user->apellido_paterno = $_POST['apellido_paterno'] ?? '';
            $this->user->apellido_materno = $_POST['apellido_materno'] ?? '';
            $this->user->dni = $_POST['dni'] ?? '';
            $this->user->correo = $_POST['correo'] ?? '';
            $this->user->contrasena = $_POST['contrasena'] ?? '';
            $this->user->telefono_ciudadano = $_POST['telefono_ciudadano'] ?? '';
            $this->user->rol_id = $_POST['rol_id'] ?? 0;

            // Basic validation
            if (empty($this->user->nombre) || empty($this->user->apellido_paterno) || empty($this->user->dni) || empty($this->user->correo) || empty($this->user->contrasena) || empty($this->user->rol_id)) {
                $error = "Todos los campos obligatorios deben ser llenados.";
                $roles = $this->user->getAllRoles();
                include 'views/user_create_form.php';
                return;
            }

            if ($this->user->createEngineerUser()) { // Assuming a method to create a user with a specified role
                header('Location: index.php?controller=User&action=showCreate&message=success');
                exit();
            } else {
                $error = "Error al crear el usuario. El DNI o correo ya existen, o hubo un problema.";
                $roles = $this->user->getAllRoles();
                include 'views/user_create_form.php';
                return;
            }
        }
    }
}

?>
