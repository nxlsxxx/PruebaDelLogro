<?php
require_once "models/Denuncia.php";

class DenunciaController {

    public function index(){
        require_once "config/database.php";
        $db = new Database();
        $conn = $db->getConnection();

        $denuncia = new Denuncia($conn);
        
        // Data loading and pagination are now handled by AJAX in public/ajax.js
        // and the buscarAjax() method in this controller.
        // The index method now only loads the view.

        require "views/denuncia_list.php";
    }

    public function buscarAjax() {
        require_once "config/database.php";
        $db = new Database();
        $conn = $db->getConnection();

        $denuncia = new Denuncia($conn);

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
        $status = isset($_GET['status']) ? $_GET['status'] : null; // Add status parameter
        $records_per_page = 10;
        $offset = ($page - 1) * $records_per_page;

        $userId = null;
        $userRole = $_SESSION['user_rol'] ?? '';

        // Debugging: Log all relevant data
        error_log("DEBUG buscarAjax: " .
                  "Page: " . $page .
                  ", Search Query: '" . $searchQuery . "'" .
                  ", Status: '" . ($status ?? 'NOT SET') . "'" .
                  ", Session User Role: '" . ($userRole ?? 'NOT SET') . "'" .
                  ", Session User ID: " . ($_SESSION['user_id'] ?? 'NOT SET'));

        if ($userRole === 'ciudadano' && isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            error_log("DEBUG buscarAjax: Filtering by User ID: " . $userId);
        }

        $denuncias_array = $denuncia->buscarTodos($page, $records_per_page, $searchQuery, $userId, $status); // Use buscarTodos with status
        $total_records = $denuncia->contarTodosConBusqueda($searchQuery, $userId, $status); // Use contarTodosConBusqueda with status
        $total_pages = ceil($total_records / $records_per_page);

        // No need for while loop as buscarTodos now returns fetchAll(PDO::FETCH_ASSOC)

        error_log("DEBUG buscarAjax: Found " . count($denuncias_array) . " denuncias out of " . $total_records . " total records.");
        error_log("DEBUG buscarAjax: Sending JSON: " . json_encode([
            "denuncias" => $denuncias_array,
            "currentPage" => $page,
            "totalPages" => $total_pages
        ]));

        echo json_encode([
            "denuncias" => $denuncias_array,
            "currentPage" => $page,
            "totalPages" => $total_pages
        ]);
        exit();
    }

    public function crear(){

    }

    public function guardar(){
        error_log("DEBUG: guardar method invoked."); // Added debug log
        require_once "config/database.php";
        $db = new Database();
        $conn = $db->getConnection();

        $denuncia = new Denuncia($conn);

        $data = [
            "titulo" => $_POST["titulo"],
            "descripcion" => $_POST["descripcion"],
            "ubicacion" => $_POST["ubicacion"],
            "estado" => $_POST["estado"] ?? 'Pendiente', // Default status
            "ciudadano" => $_SESSION['user_name'] ?? ($_POST["ciudadano"] ?? null),
            "telefono" => $_SESSION['user_phone'] ?? ($_POST["telefono_ciudadano"] ?? null), // Assuming user_phone might be in session
            "user_id" => $_SESSION['user_id'] ?? null
        ];

        if ($denuncia->crear($data)) {
            echo json_encode(["success" => true, "message" => "Denuncia creada exitosamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al crear la denuncia."]);
        }
        exit();
    }

    public function actualizar(){
        error_log("DEBUG: actualizar method invoked."); // Added debug log
        error_log("DEBUG: POST Data: " . print_r($_POST, true)); // Log POST data

        require_once "config/database.php";
        $db = new Database();
        $conn = $db->getConnection();

        $denuncia = new Denuncia($conn);

        $userRole = $_SESSION['user_rol'] ?? '';
        $loggedInUserId = $_SESSION['user_id'] ?? null;

        $data = [
            "id" => $_POST["id"] ?? null,
            "titulo" => $_POST["titulo"] ?? null,
            "descripcion" => $_POST["descripcion"] ?? null,
            "ubicacion" => $_POST["ubicacion"] ?? null,
            "estado" => $_POST["estado"] ?? null,
            "ciudadano" => $_POST["ciudadano"] ?? null,
            "telefono_ciudadano" => $_POST["telefono_ciudadano"] ?? null // Corrected key to match DB
        ];

        // Remove null values from data to prevent overwriting with nulls
        $data = array_filter($data, function($value) { return $value !== null; });

        // Ensure 'id' is present after filtering
        if (!isset($data['id'])) {
            echo json_encode(["success" => false, "message" => "ID de denuncia no proporcionado para actualizar."]);
            exit();
        }

        // Get the existing denuncia to check ownership
        $existingDenuncia = $denuncia->obtenerPorId($data['id']);

        if (empty($existingDenuncia)) {
            echo json_encode(["success" => false, "message" => "Denuncia no encontrada."]);
            exit();
        }

        error_log("DEBUG: Existing Denuncia: " . print_r($existingDenuncia, true)); // Log existing denuncia
        error_log("DEBUG: Data to Update: " . print_r($data, true)); // Log data being sent to model

        // Engineers can update any field, including status
        if ($userRole === 'ingeniero') {
            if ($denuncia->actualizar($data)) {
                echo json_encode(["success" => true, "message" => "Denuncia actualizada exitosamente por Ingeniero."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al actualizar la denuncia por Ingeniero."]);
            }
        }
        // Citizens can only update their own denuncias (excluding status if already set)
        else if ($userRole === 'ciudadano' && $existingDenuncia['user_id'] == $loggedInUserId) {
            // Prevent citizens from changing status if it's already set to something other than 'Pendiente'
            if (isset($data['estado']) && $existingDenuncia['estado'] !== 'Pendiente' && $data['estado'] !== $existingDenuncia['estado']) {
                echo json_encode(["success" => false, "message" => "No puedes cambiar el estado de una denuncia que ya está en proceso o resuelta."]);
                exit();
            }
            
            // Remove status from data if citizen is not allowed to change it
            if (isset($data['estado']) && $data['estado'] !== $existingDenuncia['estado'] && $existingDenuncia['estado'] !== 'Pendiente') {
                unset($data['estado']);
            }

            // Ensure citizen can't change user_id or ciudadano (if auto-filled from session)
            unset($data['user_id']);
            // Optionally, if ciudadano is always taken from session for citizens, prevent update from form
            if (isset($_SESSION['user_name'])) {
                unset($data['ciudadano']);
            }

            if ($denuncia->actualizar($data)) {
                echo json_encode(["success" => true, "message" => "Denuncia actualizada exitosamente por Ciudadano."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al actualizar la denuncia por Ciudadano."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "No tienes permiso para actualizar esta denuncia."]);
        }
        exit();
    }

    public function obtenerPorId() {
        require_once "config/database.php";
        $db = new Database();
        $conn = $db->getConnection();

        $denuncia = new Denuncia($conn);
        $id = $_GET['id'] ?? null;

        if ($id) {
            $data = $denuncia->obtenerPorId($id);
            echo json_encode($data);
        } else {
            echo json_encode(["error" => "ID no proporcionado"]);
        }
        exit();
    }

    public function destroy(){
        require_once "config/database.php";
        $db = new Database();
        $conn = $db->getConnection();

        $denuncia = new Denuncia($conn);
        $denunciaId = $_POST["id"] ?? null;

        if (!$denunciaId) {
            echo json_encode(["success" => false, "message" => "ID de denuncia no proporcionado."]);
            exit();
        }

        $userRole = $_SESSION['user_rol'] ?? '';
        $loggedInUserId = $_SESSION['user_id'] ?? null;

        // Get the existing denuncia to check ownership
        $existingDenuncia = $denuncia->obtenerPorId($denunciaId);

        if (!$existingDenuncia) {
            echo json_encode(["success" => false, "message" => "Denuncia no encontrada."]);
            exit();
        }

        // Engineers can delete any denuncia
        if ($userRole === 'ingeniero') {
            if ($denuncia->eliminar($denunciaId)) {
                echo json_encode(["success" => true, "message" => "Denuncia eliminada exitosamente por Ingeniero."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al eliminar la denuncia por Ingeniero."]);
            }
        }
        // Citizens can only delete their own denuncias
        else if ($userRole === 'ciudadano' && $existingDenuncia['user_id'] == $loggedInUserId) {
            if ($denuncia->eliminar($denunciaId)) {
                echo json_encode(["success" => true, "message" => "Denuncia eliminada exitosamente por Ciudadano."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al eliminar la denuncia por Ciudadano."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "No tienes permiso para eliminar esta denuncia."]);
        }
        exit();
    }
}
