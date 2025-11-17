<?php

require_once 'config/database.php';

class User {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $nombre;
    public $apellido_paterno;
    public $apellido_materno;
    public $dni;
    public $correo;
    public $contrasena;
    public $rol_id;
    public $fecha_registro;
    public $telefono_ciudadano;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Register a new user
    public function register() {
        // Check if DNI or correo already exists
        if ($this->dniExists() || $this->emailExists()) {
            return false; // DNI or email already registered
        }

        $query = "INSERT INTO " . $this->table_name . " SET
                    nombre = :nombre,
                    apellido_paterno = :apellido_paterno,
                    apellido_materno = :apellido_materno,
                    dni = :dni,
                    correo = :correo,
                    contrasena = :contrasena,
                    rol_id = :rol_id,
                    telefono_ciudadano = :telefono_ciudadano";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellido_paterno = htmlspecialchars(strip_tags($this->apellido_paterno));
        $this->apellido_materno = htmlspecialchars(strip_tags($this->apellido_materno));
        $this->dni = htmlspecialchars(strip_tags($this->dni));
        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $this->contrasena = htmlspecialchars(strip_tags($this->contrasena));
        // rol_id will be set to 'ciudadano' by default, which is 1 based on our SQL insert
        $this->rol_id = 1; // Default to 'ciudadano' role
        $this->telefono_ciudadano = htmlspecialchars(strip_tags($this->telefono_ciudadano));

        // Bind values
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellido_paterno', $this->apellido_paterno);
        $stmt->bindParam(':apellido_materno', $this->apellido_materno);
        $stmt->bindParam(':dni', $this->dni);
        $stmt->bindParam(':correo', $this->correo);
        $password_hash = password_hash($this->contrasena, PASSWORD_BCRYPT);
        $stmt->bindParam(':contrasena', $password_hash);
        $stmt->bindParam(':rol_id', $this->rol_id);
        $stmt->bindParam(':telefono_ciudadano', $this->telefono_ciudadano);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Check if DNI exists
    public function dniExists() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE dni = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->dni);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }

    // Check if email exists
    public function emailExists() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE correo = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->correo);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }

    // Login user
    public function login() {
        $query = "SELECT
                    u.id, u.nombre, u.apellido_paterno, u.apellido_materno, u.dni, u.correo, u.contrasena, u.telefono_ciudadano, r.nombre_rol as rol
                  FROM
                    " . $this->table_name . " u
                  LEFT JOIN
                    roles r ON u.rol_id = r.id
                  WHERE
                    u.dni = ?
                  LIMIT
                    0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->dni);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->id = $row['id'];
            $this->nombre = $row['nombre'];
            $this->apellido_paterno = $row['apellido_paterno'];
            $this->apellido_materno = $row['apellido_materno'];
            $this->correo = $row['correo'];
            $this->rol_id = $row['rol']; // 'rol_id' now holds the role name
            $this->telefono_ciudadano = $row['telefono_ciudadano'];

            // Verify password
            if (password_verify($this->contrasena, $row['contrasena'])) {
                return true;
            }
        }

        return false;
    }

    // Get user details by ID
    public function getById($id) {
        $query = "SELECT
                    u.id, u.nombre, u.apellido_paterno, u.apellido_materno, u.dni, u.correo, u.telefono_ciudadano, r.nombre_rol as rol
                  FROM
                    " . $this->table_name . " u
                  LEFT JOIN
                    roles r ON u.rol_id = r.id
                  WHERE
                    u.id = ?
                  LIMIT
                    0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllRoles() {
        $query = "SELECT id, nombre_rol FROM roles ORDER BY nombre_rol";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createEngineerUser() {
        // Check if DNI or correo already exists
        if ($this->dniExists() || $this->emailExists()) {
            return false; // DNI or email already registered
        }

        $query = "INSERT INTO " . $this->table_name . " SET
                    nombre = :nombre,
                    apellido_paterno = :apellido_paterno,
                    apellido_materno = :apellido_materno,
                    dni = :dni,
                    correo = :correo,
                    contrasena = :contrasena,
                    rol_id = :rol_id,
                    telefono_ciudadano = :telefono_ciudadano";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellido_paterno = htmlspecialchars(strip_tags($this->apellido_paterno));
        $this->apellido_materno = htmlspecialchars(strip_tags($this->apellido_materno));
        $this->dni = htmlspecialchars(strip_tags($this->dni));
        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $this->contrasena = htmlspecialchars(strip_tags($this->contrasena));
        $this->telefono_ciudadano = htmlspecialchars(strip_tags($this->telefono_ciudadano));
        // rol_id is already set by the controller based on form input

        // Bind values
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellido_paterno', $this->apellido_paterno);
        $stmt->bindParam(':apellido_materno', $this->apellido_materno);
        $stmt->bindParam(':dni', $this->dni);
        $stmt->bindParam(':correo', $this->correo);
        $password_hash = password_hash($this->contrasena, PASSWORD_BCRYPT);
        $stmt->bindParam(':contrasena', $password_hash);
        $stmt->bindParam(':rol_id', $this->rol_id);
        $stmt->bindParam(':telefono_ciudadano', $this->telefono_ciudadano);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function getRecentUsers($limit = 5) {
        $query = "SELECT u.id, u.nombre, u.apellido_paterno, u.fecha_registro, r.nombre_rol as rol
                  FROM " . $this->table_name . " u
                  LEFT JOIN roles r ON u.rol_id = r.id
                  ORDER BY fecha_registro DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>
