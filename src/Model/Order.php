<?php

class Order {
    protected $conn;

    public function __construct(){
        global $conn;
        $this->conn = $conn;
    } 

    public function getOrders(): array {
        $sql = "SELECT * FROM orders";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        return $orders;
    }

    public function getOrder(int $id): ?array {
        $sql = "SELECT * FROM orders WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }

    public function createOrder(int $customerId, array $products): bool {
        $this->conn->begin_transaction();
        try {
            $sql = "INSERT INTO orders (customer_id, order_date) VALUES (?, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $customerId);
            if (!$stmt->execute()) {
                throw new Exception("Failed to create order");
            }

            $orderId = $this->conn->insert_id;
            foreach ($products as $product) {
                $sql = "INSERT INTO order_products (order_id, product_id, quantity) VALUES (?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param('iii', $orderId, $product['id'], $product['quantity']);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to add product to order");
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function updateOrder(int $id, int $customerId, array $products): bool {
        // Implement order update logic if needed
        return true;
    }

    public function deleteOrder(int $id): bool {
        $sql = "DELETE FROM orders WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
