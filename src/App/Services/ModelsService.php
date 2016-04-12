<?php

namespace App\Services;

class ModelsService extends BaseService
{

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM models");
    }

    public function get($start,$count){
        return $this->db->fetchAll("SELECT * FROM models LIMIT $start, $count");
    }

    public function count(){
        return $this->db->executeQuery("SELECT * FROM models")->rowCount();
    }

    function save($model)
    {
        $this->db->insert("models", $model);
        return $this->db->lastInsertId();
    }

    function saveModelTag($modelTag){
        $this->db->insert("models_tagged", $modelTag);
        return $this->db->lastInsertId();
    }

    function getModelTags($model){
        return $this->db->fetchAll("SELECT T.id as id, T.text as text FROM terms T JOIN models_tagged MT ON (T.id = MT.idterm) WHERE MT.idmodel = $model ");
    }

}
