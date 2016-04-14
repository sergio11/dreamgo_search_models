<?php

namespace App\Services;

class ModelsTaggedService extends BaseService
{

    public function saveModelTag($modelTag){
        $this->db->insert("models_tagged", $modelTag);
        return $this->db->lastInsertId();
    }

    public function getModelTags($model){
        return $this->db->fetchAll("SELECT T.id as id, T.text as text FROM terms T JOIN models_tagged MT ON (T.id = MT.idterm) WHERE MT.idmodel = $model ");
    }

    public function deleteModelTag($modelTag){
        return $this->db->delete("models_tagged", $modelTag);
    }

}