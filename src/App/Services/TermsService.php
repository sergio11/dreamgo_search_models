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

    public function saveTags($tags){
        $ids = [];
        $allTags = array_column($this->getAll(), 'text', 'id');
        for($i = 0, $len = count($tags); $i < $len; $i++){
             $id = array_search($tags[$i]['text'], $allTags);
             $ids[] = $id ? $id : $this->save($tags[$i]);
        }
        return $ids;
    }


}
