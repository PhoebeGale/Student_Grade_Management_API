<?php
namespace contract;

interface IBaseRepository {
    public function getAll();
    public function findById($id);
    public function create($data);
    public function update($id, $data);
    public function delete($id);
}