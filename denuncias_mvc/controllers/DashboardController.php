<?php

require_once 'models/Denuncia.php';
require_once 'models/User.php';
require_once 'config/database.php';

class DashboardController {
    private $conn;
    private $denuncia;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->denuncia = new Denuncia($this->conn);
        $this->user = new User(); // Assuming User model has methods to get user details if needed
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=User&action=showLogin');
            exit();
        }

        $userRole = $_SESSION['user_rol'] ?? 'guest';
        $userId = $_SESSION['user_id'] ?? null;

        switch ($userRole) {
            case 'ciudadano':
                // Logic for citizen dashboard
                // Fetch denuncias created by this citizen
                $citizenDenuncias = $this->denuncia->buscarTodos(1, 10, null, $userId);
                $totalDenuncias = $this->denuncia->contarTodosConBusqueda(null, $userId);
                $totalPages = ceil($totalDenuncias / 10); // Assuming 10 denuncias per page

                include 'views/dashboard_citizen.php';
                break;
            case 'ingeniero':
                // Logic for engineer dashboard
                $pendingDenuncias = $this->denuncia->buscarTodos(1, 10, 'Pendiente'); // Fetch pending denuncias
                $newUsers = $this->user->getRecentUsers(5); // Fetch 5 most recent users
                include 'views/dashboard_engineer.php';
                break;
            default:
                // Default dashboard or redirect
                include 'views/dashboard_default.php';
                break;
        }
    }

    public function getEngineerDashboardDataAjax() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'ingeniero') {
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }

        $pendingDenuncias = $this->denuncia->buscarTodos(1, 10, null, null, 'Pendiente');
        $newUsers = $this->user->getRecentUsers(5);

        echo json_encode([
            'pendingDenuncias' => $pendingDenuncias,
            'newUsers' => $newUsers
        ]);
        exit();
    }
}
