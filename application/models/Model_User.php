<?php

class Model_User extends CI_Model {    

    public function getAll() {
        $query = $this->db->query("SELECT *, DATE_FORMAT(created_date,'%d-%m-%Y') as 'created_date' FROM users
                                    WHERE user_level <> 'admin'");
        return $query->result();
    }

    public function getByUsername($username) {
        $query = $this->db->query("SELECT * FROM users WHERE username = '$username'");
        return $query->result();
    }

    public function getByEmail($email) {
        $query = $this->db->query("SELECT * FROM users WHERE email = '$email'");
        return $query->result();
    }

    public function getById($id) {
        $id    = $this->db->escape_str($id);
        $query = $this->db->query("SELECT * FROM users WHERE id = $id");
        return $query->result();
    }

    public function insert($entity) {
        if ($entity['id'] == 0) {
            $query = $this->db->insert('users', $entity);
            return $this->db->insert_id();
        } else {
            return $this->update($entity['id'], $entity);            
        }
    }

    public function update($id, $entity) {
        $this->db->trans_start();
        $this->db->where('id', $id);
        $this->db->update('users', $entity);
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

    public function changePassword($email, $entity) {
        $this->db->trans_start();
        $this->db->where('email', $email);
        $this->db->update('users', $entity);
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
    
    public function delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('users');
    }

    public function login($email, $password) {
        $email = $this->db->escape_str($email);
        $password = $this->db->escape_str($password);

        $query = $this->db->query("SELECT * FROM users 
            WHERE email = '$email' AND password = '$password'");

        return $query->result();
    }

    public function suspend($id, $entity) {
        $this->db->trans_start();
        $this->db->where('id', $id);
        $this->db->update('users', $entity);
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
}