<?php

class Model_Admin extends CI_Model {

    public function __construct()
    {
        parent::__construct();          
    }
    
    public function insert($entity) {
        if ($entity['id'] == 0) {
            $query = $this->db->insert('admin', $entity);
            return $this->db->insert_id();;
        } else {
            return $this->update($entity['id'], $entity);            
        }
    }

    public function update($id, $entity) {
        $this->db->trans_start();
        $this->db->where('id', $id);
        $this->db->update('admin', $entity);
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
        $this->db->delete('admin');
    }

    public function getAll() {
        $query = $this->db->query("SELECT * FROM admin");
        return $query->result();
    }

    public function getByLimit($limit) {
        $query = $this->db->query("SELECT * FROM admin 
                                    ORDER BY id DESC LIMIT $limit");
        return $query->result();
    }

    public function getById($id) {
        $id    = $this->db->escape_str($id);
        $query = $this->db->query("SELECT * FROM admin WHERE id = $id");
        return $query->result();
    }
    
    public function getByEmail($email) {
        $email = $this->db->escape_str($email);
        $query = $this->db->query("SELECT * FROM admin WHERE email = '$email'");
        return $query->result();
    }

    public function getByUsername($username) {
        $username = $this->db->escape_str($username);
        $query = $this->db->query("SELECT * FROM admin WHERE username = '$username'");
        return $query->result();
    }

    public function search($key) {
        $key   = $this->db->escape_str($key);
        $query = $this->db->query("SELECT 8FROM admin 
                                    WHERE username Like'%$key%'
                                    OR email LIKE'%$key%'");
        return $query->result();
    }

    public function changePassword($email, $entity) {
        $this->db->trans_start();
        $this->db->where('email', $email);
        $this->db->update('admin', $entity);
        $this->db->trans_complete();

        if ($this->db->affected_rows() == 1) {
            return $email;
        } else {
            // any trans error?
            if ($this->db->trans_status() === FALSE) {
                return trim('');
            }
            return $email;
        }
    }
}