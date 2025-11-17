<?php
class Denuncia {
    private $conn;
    private $table = "denuncias";

    public function __construct($db){
        $this->conn = $db;
    }

    public function obtenerTodos(){
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // New method to get paginated denuncias
    public function obtenerPaginado($records_per_page, $offset){
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC LIMIT :offset, :records_per_page";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // New method to count all denuncias
    public function contarTodos(){
        $query = "SELECT COUNT(*) as total_rows FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total_rows'];
    }

    // New method to get paginated denuncias with search and status filter
    public function buscarTodos($page, $records_per_page, $searchQuery = null, $userId = null, $status = null){
        $offset = ($page - 1) * $records_per_page;

        $query = "SELECT d.*, r.nombre_rol as user_rol FROM " . $this->table . " d LEFT JOIN usuarios u ON d.user_id = u.id LEFT JOIN roles r ON u.rol_id = r.id";
        $conditions = [];
        $params = [];

        if (!empty($searchQuery)) {
            $conditions[] = "(d.titulo LIKE :searchQuery OR d.descripcion LIKE :searchQuery OR d.ubicacion LIKE :searchQuery OR d.ciudadano LIKE :searchQuery OR d.estado LIKE :searchQuery)";
            $params[':searchQuery'] = '%' . $searchQuery . '%';
        }
        
        if ($userId !== null) {
            $conditions[] = "d.user_id = :userId";
            $params[':userId'] = $userId;
        }

        if ($status !== null) {
            $conditions[] = "d.estado = :status";
            $params[':status'] = $status;
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY d.id DESC LIMIT :offset, :records_per_page";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // New method to count all denuncias with search and status filter
    public function contarTodosConBusqueda($searchQuery = null, $userId = null, $status = null){
        $query = "SELECT COUNT(*) as total_rows FROM " . $this->table;
        $conditions = [];
        $params = [];

        if (!empty($searchQuery)) {
            $conditions[] = "(titulo LIKE :searchQuery OR descripcion LIKE :searchQuery OR ubicacion LIKE :searchQuery OR ciudadano LIKE :searchQuery OR estado LIKE :searchQuery)";
            $params[':searchQuery'] = '%' . $searchQuery . '%';
        }
        
        if ($userId !== null) {
            $conditions[] = "user_id = :userId";
            $params[':userId'] = $userId;
        }

        if ($status !== null) {
            $conditions[] = "estado = :status";
            $params[':status'] = $status;
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total_rows'];
    }

    public function crear($data){
        $query = "INSERT INTO denuncias (titulo, descripcion, ubicacion, estado, ciudadano, telefono_ciudadano, user_id) 
                  VALUES (:titulo, :descripcion, :ubicacion, :estado, :ciudadano, :telefono, :user_id)";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute($data);
    }

    public function eliminar($id){
        $query = "DELETE FROM denuncias WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute(["id" => $id]);
    }

    public function obtenerPorId($id){
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(["id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($data){
        $query = "UPDATE " . $this->table . " SET ";
        $setParts = [];
        $params = [];

        foreach ($data as $key => $value) {
            if ($key !== 'id') { // 'id' is used in WHERE clause
                $setParts[] = "{$key} = :{$key}";
                $params[":{$key}"] = $value;
            }
        }

        if (empty($setParts)) {
            return false; // No fields to update
        }

        $query .= implode(", ", $setParts);
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
