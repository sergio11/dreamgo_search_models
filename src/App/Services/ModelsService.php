<?php

namespace App\Services;

class ModelsService extends BaseService
{

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM models");
    }

    public function get($start,$count){
        $models = $this->db->fetchAll("SELECT * FROM models LIMIT $start, $count");
        for($i = 0, $len = count($models); $i < $len; $i++){
            $models[$i]['tags'] = $this->db->fetchAll("SELECT id, text FROM terms T JOIN models_tagged MT ON(T.id = MT.idterm) WHERE idmodel = :model ", array(':model' => $models[$i]['id']));
        }
        return $models;
    }
    
    public function getModelById($id){
        $statement = $this->db->executeQuery('SELECT name FROM models WHERE id = :model', array(':model' => $id));
        return $statement->fetch();
    }

    public function count(){
        return $this->db->executeQuery("SELECT * FROM models")->rowCount();
    }

    public function save($model)
    {
        $this->db->insert("models", $model);
        return $this->db->lastInsertId();
    }

    public function saveModelTag($modelTag){
        $this->db->insert("models_tagged", $modelTag);
        return $this->db->lastInsertId();
    }

    public function getModelTags($model){
        return $this->db->fetchAll("SELECT T.id as id, T.text as text FROM terms T JOIN models_tagged MT ON (T.id = MT.idterm) WHERE MT.idmodel = $model ");
    }
    
    public function delete($id){
       return $this->db->delete('models',array('id' => $id ));
    }

}
