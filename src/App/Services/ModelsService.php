<?php

namespace App\Services;

class ModelsService extends BaseService
{

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM models");
    }

    function save($model)
    {
        $this->db->insert("models", $model);
        return $this->db->lastInsertId();
    }

    function update($id, $note)
    {
        return $this->db->update('notes', $note, ['id' => $id]);
    }

    function delete($id)
    {
        return $this->db->delete("notes", array("id" => $id));
    }

}
