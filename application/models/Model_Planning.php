<?php

class Model_Planning extends CI_Model {    

    public function getAll() {
        $query = $this->db->query("SELECT * FROM plannings");
        return $query->result();
    }

    public function getAllInAdmin() {
        $query = $this->db->query("SELECT p.*, u.username, u.email, DATE_FORMAT(p.created_date,'%d-%m-%Y') as 'created_date' FROM plannings p
                                    LEFT JOIN users u on u.id = p.userId
                                    Where u.suspend <> 1 AND u.user_level <> 'admin'
                                    ORDER BY p.created_date desc, u.username asc");
        return $query->result();
    }

    public function getById($id) {
        $id    = $this->db->escape_str($id);
        $query = $this->db->query("SELECT * FROM plannings WHERE id = $id");
        return $query->result();
    }

    public function getDetailById($id) {
        $id    = $this->db->escape_str($id);
        $query = $this->db->query("SELECT id, userId, goalName, jangkaWaktu, currentCost, futureCost, biayaAdmin, totalBiayaAdmin, pajakBunga, totalPajakBunga, totalBunga, alreadyInvest, lumpsum, monthlyInvest, requiredRate, inflationRate, interestRate, DATE_FORMAT(created_date,'%d-%m-%Y') as 'created_date'
            FROM plannings WHERE id = $id");
        return $query->result();
    }

    public function insert($entity) {
        if ($entity['id'] == 0) {
            $query = $this->db->insert('plannings', $entity);
            return $this->db->insert_id();
        } else {
            return $this->update($entity['id'], $entity);            
        }
    }

    public function update($id, $entity) {
        $this->db->trans_start();
        $this->db->where('id', $id);
        $this->db->update('plannings', $entity);
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
        $this->db->delete('plannings');
    }

    public function getPlanningByUserId($id) {
        $query = $this->db->query("SELECT * FROM plannings WHERE userId = $id ORDER BY created_date, updated_date");
        return $query->result();
    }

    public function getPriorityByUserId($id) {
        $query = $this->db->query("SELECT (@row_number:=@row_number + 1) AS num, p.* 
                                    FROM (SELECT @row_number:=0) AS r,  plannings p
                                    LEFT JOIN users u on u.id = p.userId
                                    WHERE u.id = $id
                                    ORDER BY p.priority DESC;");
        return $query->result();
    }
}