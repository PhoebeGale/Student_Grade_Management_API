<?php

namespace model;

class Student {
    public $id;
    public $name;
    public $midterm;
    public $final;
    public $finalGrade;
    public $status;

    public function __construct($id, $name, $midterm, $final, $finalGrade = null, $status = null) {
        $this->id = $id;
        $this->name = $name;
        $this->midterm = $midterm;
        $this->final = $final;
        $this->finalGrade = $finalGrade;
        $this->status = $status;
    }
}