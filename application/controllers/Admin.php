<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() 
    {
      parent::__construct();
      $this->load->model("Model_User");
      $this->load->helper('form');
      $this->load->helper('url');
    }

    public function index() {
      $c_email  = '';
      $c_pass   = '';
      $remember = false;
      if (isset($_COOKIE['email']) and isset($_COOKIE['password'])) {
        $c_email  = $_COOKIE['email'];
        $c_pass   = $_COOKIE['password'];
        $remember = true;
      }

      $data['c_email']  = $c_email;
      $data['c_pass']   = $c_pass;
      $data['remember'] = $remember;
      $this->load->view("login", $data);
    }

    public function dashboard() {
      if(empty($this->session->email)) {
        redirect(base_url().'admin/', 'refresh');
      } else {
        $this->load->view("dashboard");
      }
    }

    public function myprofile() {
      if(empty($this->session->email)) {
        redirect(base_url().'admin/', 'refresh');
      } else {
        $data['data'] = $this->Model_User->getByEmail($this->session->email);
        $this->load->view("myprofile", $data);
      }
    }

    public function users() {
      if(empty($this->session->email)) {
        redirect(base_url().'admin/', 'refresh');
      } else {
        $this->load->view("user");
      }
    }

    public function plannings() {
      if(empty($this->session->email)) {
        redirect(base_url().'admin', 'refresh');
      } else {
        $this->load->view("planning");
      }
    }

    public function getById() {
      $data = $this->Model_User->getById($this->input->post('id'));
      echo json_encode($data);
    }

    public function save() {
      $canChangeUsername = false;
      $username = $this->input->post("username");
      if (!empty($username)) {
        $dataUsername = $this->Model_User->getByUsername($username);
        if (!empty($dataUsername)) {
          if ($dataUsername[0]->id == $this->input->post("id")) {
            $canChangeUsername = true;
          }
        } else {
          $canChangeUsername = true;
        }
      } else {
        $canChangeUsername = true;
      }

      $canChangeEmail = false;
      $email = $this->input->post("email");
      if (!empty($email)) {
        $dataEmail = $this->Model_User->getByEmail($email);
        if (!empty($dataEmail)) {
          if ($dataEmail[0]->id == $this->input->post("id")) {
            $canChangeEmail = true;
          }
        } else {
          $canChangeEmail = true;
        }
      } else {
        $canChangeEmail = true;
      }
      $password = hash("sha1", $this->input->post("password"));

      $message = "";
      $isSubmited = false;

      if (!$canChangeUsername) {
        $message = "Username ". $username ." has been taken";
      } else if (!$canChangeEmail) {
        $message = "Email ". $username ." has been taken";
      } else {
        // AS EMPTY
        $id = trim('');
        // NEW
        if ($this->input->post("id") == 0) {
          $entity = array(
              'id'           => $this->input->post("id"),
              'username'     => $this->input->post("username"), 
              'password'     => $password,
              'email'        => $email, 
              'user_level'   => 'admin', 
              'created_date' => date("Y-m-d h:i:s"));
          $id = $this->Model_User->insert($entity);
        } else { // UPDATE
          $changePassword = $this->input->post("changepassword");
          $entity = array();
          if ($changePassword == 'true') {
            $entity = array(
              'id'           => $this->input->post("id"),
              'username'     => $this->input->post("username"),
              'password'     => $password,
              'email'        => $email,
              'updated_date' => date("Y-m-d h:i:s"));
          } else {
             $entity = array(
              'id'           => $this->input->post("id"),
              'username'     => $this->input->post("username"),
              'email'        => $email,
              'updated_date' => date("Y-m-d h:i:s"));
          }

          setcookie('email', $email, time() + (86400 * 30)); // 1 day
          setcookie('password', $this->input->post("password"), time() + (86400 * 30));

          // set sessions
          $newdata = array(
              'id'       => $this->input->post("id"),
              'email'    => $this->input->post("email"),
              'username' => $this->input->post("username")
          );

          $this->session->set_userdata($newdata);

          $id = $this->Model_User->insert($entity);
        }

        if (empty($id)) {
          $message = "Something wrong, please try again!";
        } else {
          $message = "Successfully submited";
          $isSubmited = true;
        }
      }

      $status = array("message" => $message, "isSubmited" => $isSubmited);
      echo json_encode($status);
    }

    public function delete($id) {
      $this->Model_User->delete($id);
    }

    public function getByEmail() {
      $data = $this->Model_User->getByEmail($this->input->post("email"));
      echo json_encode($data); 
    }

     public function login() {
        $Email    = $this->input->post("email");
        $Password = hash("sha1", $this->input->post("password"));
        $exist    = $this->Model_User->getByEmail($Email);

        $message = "";
        $isLogin = false;
        if ($exist) {
            $message = "Email or password is incorect";
            if ($exist[0]->password == $Password && $exist[0]->user_level == 'admin') {
                $message = "Login success";
                $isLogin = true;

                //set cookies
                $remember = $this->input->post("remember");
                if (!is_null($remember)) {
                  setcookie('email', $Email, time() + (86400 * 30)); // 1 day
                  setcookie('password', $this->input->post("password"), time() + (86400 * 30));
                } else {
                  unset($_COOKIE['email']);
                  unset($_COOKIE['password']);
                  setcookie('email', null, -1);
                  setcookie('password', null, -1);
                }

                // set sessions
                $newdata = array(
                    'id'       => $exist[0]->id,
                    'email'    => $exist[0]->email,
                    'username' => $exist[0]->username
                );

                $this->session->set_userdata($newdata);
            } 
        } else { 
            $message = "Email doesnt exist, please register";
        }

        $status = array("message" => $message, "isLogin" => $isLogin);
        echo json_encode ($status); 
    }

    public function checkPassword() {
        $Password = hash("sha1", $this->input->post("password"));
        $Email    = $this->input->post("email");

        $data     = $this->Model_User->getByEmail($Email);
        $isExist = false;
        if($data[0]->password == $Password && $data[0]->user_level == 'admin') {
            $isExist = true;
        } else {
            $isExist = false;
        }

        $status = array('isExist' => $isExist);
        echo json_encode($status);
    }

    public function forgetPassword() {
      $this->load->view("forgetpassword");
    }

    public function logout() {
        $this->session->unset_userdata('id');
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('username');
        redirect(URL_BASE.'admin/', 'refresh');
    }

    public function resetPassword() {
        $email = $this->input->post("email");

        $data = $this->Model_User->getByEmail($email);
        if ($data == null){
          $status = array("message" => 'Email '. $email .' is not exist', "isSubmited" => false);
          echo json_encode ($status); 
        } else {
          $password = $this->generateRandomString(6);
          $passwordHash = hash("sha1", $password);
          $update = array('password' => $passwordHash);
          $this->Model_User->changePassword($email, $update);

          $htmlContent = '<p>Berikut ini adalah password baru anda</p>';
          $htmlContent .= '<h4>New Password : '.$password.'</h4>';

          $smtp_user = 'hermawanyogi42@gmail.com';
          $smtp_pass = 'yoris.123';
          $config = Array(
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => $smtp_user,
            'smtp_pass' => $smtp_pass,
            'mailtype'  => 'html',
            'charset'   => 'iso-8859-1',
            'wordwrap'  => TRUE
          );

          $this->load->library('email', $config);
          $this->email->set_newline("\r\n");
          $this->email->from($smtp_user);
          $this->email->to($email);
          $this->email->subject('Reset Password');
          $this->email->message($htmlContent);
          if($this->email->send())
          {
            $status = array("message" => 'Request reset password has been sent', "isSubmited" => true);
            echo json_encode ($status); 
          }
          else
          {
            $status = array("message" => 'Error sending email', "isSubmited" => false);
            echo json_encode ($status); 
          }
        }        
    }

    function generateRandomString($length = 6) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}