<?php

class Model_DetailPlanning extends CI_Model {    

    public function getAll() {
        $query = $this->db->query("SELECT * FROM detail_plannings");
        return $query->result();
    }

    public function getById($id) {
        $id    = $this->db->escape_str($id);
        $query = $this->db->query("SELECT * FROM detail_plannings WHERE id = $id");
        return $query->result();
    }

    public function getDetailByPlanningId($id) {
        $query = $this->db->query("SELECT * FROM detail_plannings WHERE planningId = $id ORDER BY bulan ASC");
        return $query->result();
    }

    public function insert($entity) {
        $query = $this->db->insert('detail_plannings', $entity);
        return $this->db->insert_id();
    }
    
    public function delete($id) {
        $this->db->where('planningId', $id);
        $this->db->delete('detail_plannings');
    }
}