<?php

namespace App\Services;

class TermsService extends BaseService
{

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM terms");
    }

    public function save($term)
    {
        $this->db->insert("terms", $term);
        return $this->db->lastInsertId();
    }
    
    public function match($text){
        return $this->db->fetchAll("SELECT * FROM terms WHERE UPPER(text) LIKE UPPER(:text)", array(':text' => "%$text%"));
    }


}
