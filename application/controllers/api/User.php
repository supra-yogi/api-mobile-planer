<?php
 
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
 
class User extends REST_Controller {
 
    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->model('Model_User');
        $this->load->model('Model_Batasan');
    }
 
    // GET ALL DATA
    // api/customer or api/customer/1 [GET]
    public function index_get()
    {
        $id = $this->get('id');
        if ($id != null) {
            $data = $this->Model_User->getById($id);
            if ($data == null){
                $data = ARRAY(
                'Error'   => REST_Controller::HTTP_NOT_FOUND,
                'Message' => 'User Id = '.$id.' not found');
                $this->response($data, REST_Controller::HTTP_NOT_FOUND);
            }
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $data = $this->Model_User->getAll();
            $this->response($data, REST_Controller::HTTP_OK);
        }
    }
 
    // INSERT NEW DATA
    // api/customer [POST]
    public function index_post() {
        $password = hash("sha1", $this->POST('password'));
        $data = ARRAY(
            'id'           => 0,
            'username'     => $this->POST('username'),
            'email'        => $this->POST('email'),
            'password'     => $password,
            'created_date' => date("Y-m-d h:i:s"));

        $email = $data['email'];
        $canChangeEmail = false;
        if (!empty($email)) {
          $dataEmail = $this->Model_User->getByEmail($email);
          if (empty($dataEmail)) {
            $canChangeEmail = true;
          } 
        }

        if ($canChangeEmail) {
            $id = $this->Model_User->insert($data);
            $data = ARRAY(
                'id'           => $id,
                'username'     => $this->POST('username'),
                'email'        => $this->POST('email'),
                'password'     => $password,
                'created_date' => date("Y-m-d h:i:s"));

            $batasan = array(
                'id'                  => 0, 
                'userId'              => $id,
                'waktuCepatFrom'      => 4,
                'waktuCepatTo'        => 24,
                'waktuLamaFrom'       => 12,
                'waktuLamaTo'         => 36,
                'biayaRendahFrom'     => 1000000,
                'biayaRendahTo'       => 4000000,
                'biayaSedangFrom'     => 2000000,
                'biayaSedangTo'       => 8000000,
                'biayaTinggiFrom'     => 6000000,
                'biayaTinggiTo'       => 10000000,
                'kebutuhanRendahFrom' => 1,
                'kebutuhanRendahTo'   => 7,
                'kebutuhanTinggiFrom' => 4,
                'kebutuhanTinggiTo'   => 10,
                'created_date'        => date("Y-m-d h:i:s"));

            
            $this->Model_Batasan->insert($batasan);
            $this->response($data, REST_Controller::HTTP_CREATED);
        } else {
            $data = ARRAY(
                'Error' => REST_Controller::HTTP_NOT_FOUND,
                'Message' => "email ".$data['email']." has been taken");
            $this->response($data, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    // UPDATE DATA
    // api/customer/id [PUT]
    public function index_put() {
        $id = $this->PUT('id');

        // Validate the id.
        if ($id <= 0)
        {
            $data = ARRAY(
                'Error'   => REST_Controller::HTTP_BAD_REQUEST,
                'Message' => 'Id must > 0 | Id =' . $id);
            $this->response($data, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        $data = $this->Model_User->getById($id);
        if ($data == null){
            $data = ARRAY(
                'Error'   => REST_Controller::HTTP_NOT_FOUND,
                'Message' => 'User Id = '.$id.' not found');
            $this->response($data, REST_Controller::HTTP_NOT_FOUND);
        }

        $update = array();
        $isChangePassword = $this->PUT('isChangePassword') === 'true' ? true : false;
        if ($isChangePassword) {
            $password = hash("sha1", $this->PUT('password'));
            $update = ARRAY(
                'id'           => $id,
                'username'     => $this->PUT('username'),
                'email'        => $this->PUT('email'),
                'password'     => $password,
                'updated_date' => date("Y-m-d h:i:s"));    
        } else {
            $update = ARRAY(
                'id'           => $id,
                'username'     => $this->PUT('username'),
                'email'        => $this->PUT('email'),
                'updated_date' => date("Y-m-d h:i:s"));
        }
        

        $email = $this->PUT("email");
        $canChangeEmail = false;
        if (!empty($email)) {
          $dataEmail = $this->Model_User->getByEmail($email);
          if (!empty($dataEmail)) {
            if ($dataEmail[0]->id == $id) {
              $canChangeEmail = true;
            }
          } else {
            $canChangeEmail = true;
          }
        }

        if ($canChangeEmail) {
            $this->Model_User->insert($update);
            $this->response($update, REST_Controller::HTTP_OK);
        } else {
            $data = ARRAY(
                'Error' => REST_Controller::HTTP_NOT_FOUND,
                'Message' => "email ".$update['email']." has been taken");
            $this->response($data, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function index_delete() {
        $id = $this->GET('id');

        // Validate the id.
        if ($id <= 0)
        {
            $data = ARRAY(
                'Error'   => REST_Controller::HTTP_BAD_REQUEST,
                'Message' => 'Id must > 0');
            $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
        }

        $data = $this->Model_User->getById($id);
        if ($data == null){
            $data = ARRAY(
                'Error'   => REST_Controller::HTTP_NOT_FOUND,
                'Message' => 'User Id = '.$id.' not found');
            $this->response($data, REST_Controller::HTTP_NOT_FOUND);
        }

        $this->Model_User->delete($id);

        $data = ARRAY(
            'Message' => 'Deleted');
        $this->response($data, REST_Controller::HTTP_NO_CONTENT);
    }

    public function login_post() {
        $email    = $this->POST('email');
        $password = $this->post('password');
        $password = hash("sha1", $password);

        $data = $this->Model_User->login($email, $password);
        if ($data != null) {
            if ($data[0]->suspend == 1) {
                $data = ARRAY(
                    'Error'   => REST_Controller::HTTP_BAD_REQUEST,
                    'Message' => 'User has been suspended');
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            }
            $this->response($data, REST_Controller::HTTP_OK);
        }

        $data = ARRAY(
            'Error'   => REST_Controller::HTTP_NOT_FOUND,
            'Message' => 'Email or password incorect');
        $this->response($data, REST_Controller::HTTP_NOT_FOUND);
    }

    public function getByUsername_post() {
        $username = $this->post('username');

        $data = $this->Model_User->getByUsername($username);
        if ($data == null) {
            $data = ARRAY(
            'Error'   => REST_Controller::HTTP_NOT_FOUND,
            'Message' => 'User '.$username.' doesnt exist');
            $this->response($data, REST_Controller::HTTP_NOT_FOUND);
        }

        $this->response($data, REST_Controller::HTTP_OK);
    }

    public function getByEmail_post() {
        $email = $this->post('email');

        $data = $this->Model_User->getByEmail($email);
        if ($data == null) {
            $data = ARRAY(
            'Error'   => REST_Controller::HTTP_NOT_FOUND,
            'Message' => 'User '.$email.' doesnt exist');
            $this->response(REST_Controller::HTTP_NOT_FOUND, REST_Controller::HTTP_NOT_FOUND);
        }

        $this->response($data, REST_Controller::HTTP_OK);
    }

    public function checkPassword_post() {
        $id = $this->post('id');
        $password = hash("sha1", $this->post('password'));

        // Validate the id.
        if ($id <= 0)
        {
            $data = ARRAY(
                'Error'   => REST_Controller::HTTP_BAD_REQUEST,
                'Message' => 'Id must > 0 | Id =' . $id);
            $this->response($data, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        $data = $this->Model_User->getById($id);
        if ($data == null){
            $data = ARRAY(
                'Error'   => REST_Controller::HTTP_NOT_FOUND,
                'Message' => 'User Id = '.$id.' not found');
            $this->response($data, REST_Controller::HTTP_NOT_FOUND);
        }

        $return = array('isEqual' => false);
        if ($data[0]->password == $password) {
            $return = array('isEqual' => true);
        }

        $this->response($return, REST_Controller::HTTP_OK);
    }

    public function suspend_post() {
        $id = $this->post('id');

        // Validate the id.
        if ($id <= 0)
        {
            $data = ARRAY(
                'Error'   => REST_Controller::HTTP_BAD_REQUEST,
                'Message' => 'Id must > 0');
            $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
        }

        $data = $this->Model_User->getById($id);
        if ($data == null){
            $data = ARRAY(
                'Error'   => REST_Controller::HTTP_NOT_FOUND,
                'Message' => 'Usere Id = '.$id.' not found');
            $this->response($data, REST_Controller::HTTP_NOT_FOUND);
        }

        $suspend = ARRAY('suspend' => 1);
        $data = $this->Model_User->suspend($id, $suspend);

        $this->response($data, REST_Controller::HTTP_OK);
    }

     public function unSuspend_post() {
        $id = $this->post('id');

        // Validate the id.
        if ($id <= 0)
        {
            $data = ARRAY(
                'Error'   => REST_Controller::HTTP_BAD_REQUEST,
                'Message' => 'Id must > 0');
            $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
        }

        $data = $this->Model_User->getById($id);
        if ($data == null){
            $data = ARRAY(
                'Error'   => REST_Controller::HTTP_NOT_FOUND,
                'Message' => 'Usere Id = '.$id.' not found');
            $this->response($data, REST_Controller::HTTP_NOT_FOUND);
        }

        $suspend = ARRAY('suspend' => 0);
        $data = $this->Model_User->suspend($id, $suspend);

        $this->response($data, REST_Controller::HTTP_OK);
    }

    public function resetPassword_post() {
        $email = $this->post('email');

        $data = $this->Model_User->getByEmail($email);
        if ($data == null){
            $data = ARRAY(
                'Error'   => REST_Controller::HTTP_NOT_FOUND,
                'Message' => 'Email = '.$email.' not found');
            $this->response($data, REST_Controller::HTTP_NOT_FOUND);
        }

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
            $data = ARRAY(
                'Response' => REST_Controller::HTTP_OK,
                'Message'  => 'Request reset password has been sent');
            $this->response($data, REST_Controller::HTTP_OK);
        }
        else
        {
          $data = ARRAY(
                'Error'   => REST_Controller::HTTP_BAD_REQUEST,
                'Message' => 'Error sending email');
            $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
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