<?php

namespace services;

class GradeCalculation {
    public static function calculateFinalGrade($midterm, $final) {
        return ($midterm * 0.4) + ($final * 0.6);
    }

    public static function determineStatus($finalGrade) {
        return $finalGrade >= 75 ? 'Pass' : 'Fail';
    }
}