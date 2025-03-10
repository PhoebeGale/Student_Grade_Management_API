<?php

namespace controller;

use services\GradeCalculation;
use repositories\StudentRepository;
use model\Student;

class StudentController {
    private $gradeCalculation;
    private $studentRepository;

    public function __construct() {
        $this->studentRepository = new StudentRepository();
        $this->gradeCalculation = new GradeCalculation();
    }

    public function getAllStudents() {
        echo json_encode($this->studentRepository->getAll());
    }

    public function getStudentById($id) {
    $student = $this->studentRepository->findById($id);

    if (!$student) {
        echo json_encode(["error" => "Student not found"]);
        return;
    }

    echo json_encode($student);
}

    public function createStudent($data) {
        if (!isset($data['name'], $data['midterm'], $data['final'])) {
            echo json_encode(["error" => "Missing required fields"]);
            return;
        }

        $name = $data['name'];
        $midterm = (float) $data['midterm'];
        $final = (float) $data['final'];

        $finalGrade = $this->gradeCalculation->calculateFinalGrade($midterm, $final);
        $status = $finalGrade >= 75 ? 'Pass' : 'Fail';

        $student = new Student(null, $name, $midterm, $final, $finalGrade, $status);

        $result = $this->studentRepository->create([
            'name'    => $student->name,
            'midterm' => $student->midterm,
            'final'   => $student->final
        ]);        

        if ($result) {
            echo json_encode(["message" => "Student added successfully", "student" => $student]);
        } else {
            echo json_encode(["error" => "Failed to add student"]);
        }
    }

    public function updateStudent($id, $data) {
        if (!$this->studentRepository->update($id, $data)) {
            echo json_encode(["error" => "Failed to update student"]);
            return;
        }
        echo json_encode(["message" => "Student updated successfully"]);
    }

    public function deleteStudent($id) {
        if (!$this->studentRepository->delete($id)) {
            echo json_encode(["error" => "Failed to delete student"]);
            return;
        }
        echo json_encode(["message" => "Student deleted successfully"]);
    }
}