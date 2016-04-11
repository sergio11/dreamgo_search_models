<?php

namespace App\Services;

class TermsService extends BaseService
{

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM terms");
    }

    function save($term)
    {
        $this->db->insert("terms", $term);
        return $this->db->lastInsertId();
    }


}
