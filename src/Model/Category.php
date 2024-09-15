<?php

class Category {
    protected $conn;


    public function __construct(){
        global $conn;
        $this->conn = $conn;
    } 

    public function getCategories(){
    $sql = "SELECT * FROM categories";
    echo(json_encode("test"));
    
    $result = $this->conn->query($sql);
    error_log("Query Result Count: " . $result->num_rows);
    
    $categories = [];
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $categories[] = $row;
            error_log("Fetched row: " . print_r($row, true));
        }
    }
    
        error_log("Total categories fetched: " . count($categories));
        return $categories;
    }

    public function testFunction() {
        // Simulate fetching categories
        $categories = [
            ['id' => 1, 'name' => 'All', 'typename' => 'Category'],
            ['id' => 2, 'name' => 'Clothes', 'typename' => 'Category'],
            ['id' => 3, 'name' => 'Tech', 'typename' => 'Category']
        ];

        return $categories;
    }


    public function getCategory($id){
        $sql = "SELECT * FROM categories WHERE id = $id";
        $result = $this->conn->query($sql);
        $category = [];
        if($result->num_rows > 0){
            $category = $result->fetch_assoc();
        }
        return $category;
    }

    public function createCategory($name){
        $sql = "INSERT INTO categories (name) VALUES ('$name')";
        if($this->conn->query($sql) === TRUE){
            return true;
        } else {
            return false;
        }
    }

    public function updateCategory($id, $name){
        $sql = "UPDATE categories SET name = '$name' WHERE id = $id";
        if($this->conn->query($sql) === TRUE){
            return true;
        } else {
            return false;
        }
    }

    public function deleteCategory($id){
        $sql = "DELETE FROM categories WHERE id = $id";
        if($this->conn->query($sql) === TRUE){
            return true;
        } else {
            return false;
        }
    }

}