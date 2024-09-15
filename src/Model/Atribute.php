<?php

class ProductAttribute {
    protected $conn;

    public function __construct(){
        global $conn;
        $this->conn = $conn;
    } 

    public function getAttributes(): array {
        $sql = "SELECT * FROM attributes";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $attributes = [];
        while ($row = $result->fetch_assoc()) {
            $attributes[] = $row;
        }
        return $attributes;
    }

    public function getAttribute(int $id): ?array {
        $sql = "SELECT * FROM attributes WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    public function createAttribute(string $name, string $value): bool {
        $sql = "INSERT INTO attributes (name, value) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ss', $name, $value);
        return $stmt->execute();
    }

    public function updateAttribute(int $id, string $name, string $value): bool {
        $sql = "UPDATE attributes SET name = ?, value = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssi', $name, $value, $id);
        return $stmt->execute();
    }

    public function deleteAttribute(int $id): bool {
        $sql = "DELETE FROM attributes WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
