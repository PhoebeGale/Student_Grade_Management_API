<?php

namespace repositories;

use config\Database;
use contract\IBaseRepository;
use PDO;
use services\GradeCalculation;

class StudentRepository implements IBaseRepository {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM student");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM student WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }    

    public function create($data) {
        if (!isset($data['name'], $data['midterm'], $data['final'])) {
            throw new \InvalidArgumentException("Missing required fields: 'name', 'midterm', 'final'.");
        }

        $finalGrade = GradeCalculation::calculateFinalGrade($data['midterm'], $data['final']);
        $status = GradeCalculation::determineStatus($finalGrade);

        $stmt = $this->conn->prepare("
            INSERT INTO student (name, midterm, final, finalGrade, status) 
            VALUES (:name, :midterm, :final, :finalGrade, :status)

        ");
        
        if ($stmt->execute([
            'name'       => $data['name'],
            'midterm'    => $data['midterm'],
            'final'      => $data['final'],
            'finalGrade' => $finalGrade,
            'status'     => $status
        ])) {
            return $this->conn->lastInsertId(); 
        }

        return false;
    }

    public function update($id, $data) {
        if (!isset($data['midterm'], $data['final'])) {
            throw new \InvalidArgumentException("Missing required fields: 'midterm', 'final'.");
        }
    
        if (!$this->findById($id)) {
            return ["error" => "Student with ID $id not found."];
        }
    
        $midterm = (int) $data['midterm'];
        $final = (int) $data['final'];
    
        $finalGrade = GradeCalculation::calculateFinalGrade($midterm, $final);
        $status = GradeCalculation::determineStatus($finalGrade);
    
        $stmt = $this->conn->prepare("
            UPDATE student 
            SET name = :name, midterm = :midterm, final = :final, finalGrade = :finalGrade, status = :status 
            WHERE id = :id

        ");

        $result = $stmt->execute([
            'name'    => $data['name'], 
            'midterm' => $data['midterm'],  
            'final'   => $data['final'],
            'finalGrade' => $finalGrade,
            'status'  => $status,
            'id'      => $id
        ]);

        if (!$result) {
            var_dump($stmt->errorInfo()); 
            return ["error" => "SQL execution failed."];
        }
    
        if ($stmt->rowCount() === 0) {
            return ["error" => "No changes made. Record may already be up-to-date."];
        }
    
        return ["success" => true, "message" => "Student updated successfully."];
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM student WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    
        if ($stmt->rowCount() > 0) {
            return ["message" => "Student deleted successfully"];
        } else {
            return ["error" => "Failed to delete student"];
        }
    }
} 