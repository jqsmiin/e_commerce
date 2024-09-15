<?php

class Product {
    protected $conn;


    public function __construct(){
        global $conn;
        $this->conn = $conn;
    } 

    public function getProducts(){
        $sql = "SELECT * FROM products";
        $result = $this->conn->query($sql);
        $products = [];
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $products[] = $row;
            }
        }
        return $products;
    }

    public function getProduct($id){
        $sql = "SELECT * FROM products WHERE id = $id";
        $result = $this->conn->query($sql);
        $product = [];
        if($result->num_rows > 0){
            $product = $result->fetch_assoc();
        }
        return $product;
    }

    public function createProduct($name, $price, $category_id){
        $sql = "INSERT INTO products (name, price, category_id) VALUES ('$name', $price, $category_id)";
        if($this->conn->query($sql) === TRUE){
            return true;
        } else {
            return false;
        }
    }

    public function updateProduct($id, $name, $price, $category_id){
        $sql = "UPDATE products SET name = '$name', price = $price, category_id = $category_id WHERE id = $id";
        if($this->conn->query($sql) === TRUE){
            return true;
        } else {
            return false;
        }
    }

    public function deleteProduct($id){
        $sql = "DELETE FROM products WHERE id = $id";
        if($this->conn->query($sql) === TRUE){
            return true;
        } else {
            return false;
        }
    }

}