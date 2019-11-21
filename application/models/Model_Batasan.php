<?php

class Model_Batasan extends CI_Model {    

    public function getAll() {
        $query = $this->db->query("SELECT * FROM batasans");
        return $query->result();
    }

    public function getById($id) {
        $id    = $this->db->escape_str($id);
        $query = $this->db->query("SELECT * FROM batasans WHERE id = $id");
        return $query->result();
    }

    public function getBatasanByUserId($id) {
        $query = $this->db->query("SELECT * FROM batasans WHERE userId = $id ORDER BY created_date, updated_date");
        return $query->result();
    }

    public function insert($entity) {
        if ($entity['id'] == 0) {
            $query = $this->db->insert('batasans', $entity);
            return $this->db->insert_id();
        } else {
            return $this->update($entity['id'], $entity);            
        }
    }

    public function update($id, $entity) {
        $this->db->trans_start();
        $this->db->where('id', $id);
        $this->db->update('batasans', $entity);
        $this->db->trans_complete();

        if ($this->db->affected_rows() == 1) {
            return $id;
        } else {
            // any trans error?
            if ($this->db->trans_status() === FALSE) {
                return trim('');
            }
            return $id;
        }
    }
    
    public function delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('batasans');
    }
}