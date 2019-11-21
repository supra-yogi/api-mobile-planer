<?php
 
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
 
class Batasan extends REST_Controller {
 
    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->model('Model_Batasan');
        $this->load->model('Model_User');
    }
 
    // GET ALL DATA
    // api/customer or api/customer/1 [GET]
    public function index_get()
    {
        $id = $this->get('id');
        if ($id != null) {
            $data = $this->Model_Batasan->getById($id);
            if ($data == null){
                $data = ARRAY(
                'Error'   => REST_Controller::HTTP_NOT_FOUND,
                'Message' => 'Batasan Id = '.$id.' not found');
                $this->response($data, REST_Controller::HTTP_NOT_FOUND);
            }
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $data = $this->Model_Batasan->getAll();
            $this->response($data, REST_Controller::HTTP_OK);
        }
    }
 
    // INSERT NEW DATA
    // api/customer [POST]
    public function index_post() {
        $data = ARRAY(
            'id'                  => 0,
            'userId'              => $this->POST('userId'),
            
            'waktuCepatFrom'      => $this->POST('waktuCepatFrom'),
            'waktuCepatTo'        => $this->POST('waktuCepatTo'),
            'waktuLamaFrom'       => $this->POST('waktuLamaFrom'),
            'waktuLamaTo'         => $this->POST('waktuLamaTo'),
            
            'biayaRendahFrom'     => $this->POST('biayaRendahFrom'),
            'biayaRendahTo'       => $this->POST('biayaRendahTo'),
            'biayaSedangFrom'     => $this->POST('biayaSedangFrom'),
            'biayaSedangTo'       => $this->POST('biayaSedangTo'),
            'biayaTinggiFrom'     => $this->POST('biayaTinggiFrom'),
            'biayaTinggiTo'       => $this->POST('biayaTinggiTo'),
            
            'kebutuhanRendahFrom' => $this->POST('kebutuhanRendahFrom'),
            'kebutuhanRendahTo'   => $this->POST('kebutuhanRendahTo'),
            'kebutuhanTinggiFrom' => $this->POST('kebutuhanTinggiFrom'),
            'kebutuhanTinggiTo'   => $this->POST('kebutuhanTinggiTo'),
            'created_date'        => date("Y-m-d h:i:s"));

        $this->Model_Batasan->insert($data);
        $this->response($data, REST_Controller::HTTP_CREATED);        
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

        $data = $this->Model_Batasan->getById($id);
        if ($data == null){
            $data = ARRAY(
                'Error'   => REST_Controller::HTTP_NOT_FOUND,
                'Message' => 'Batasan Id = '.$id.' not found');
            $this->response($data, REST_Controller::HTTP_NOT_FOUND);
        }

        $update = ARRAY(
            'id'                => $id,
            'waktuCepatFrom'      => $this->POST('waktuCepatFrom'),
            'waktuCepatTo'        => $this->POST('waktuCepatTo'),
            'waktuLamaFrom'       => $this->POST('waktuLamaFrom'),
            'waktuLamaTo'         => $this->POST('waktuLamaTo'),
            
            'biayaRendahFrom'     => $this->POST('biayaRendahFrom'),
            'biayaRendahTo'       => $this->POST('biayaRendahTo'),
            'biayaSedangFrom'     => $this->POST('biayaSedangFrom'),
            'biayaSedangTo'       => $this->POST('biayaSedangTo'),
            'biayaTinggiFrom'     => $this->POST('biayaTinggiFrom'),
            'biayaTinggiTo'       => $this->POST('biayaTinggiTo'),
            
            'kebutuhanRendahFrom' => $this->POST('kebutuhanRendahFrom'),
            'kebutuhanRendahTo'   => $this->POST('kebutuhanRendahTo'),
            'kebutuhanTinggiFrom' => $this->POST('kebutuhanTinggiFrom'),
            'kebutuhanTinggiTo'   => $this->POST('kebutuhanTinggiTo'),
            'updated_date'      => date("Y-m-d h:i:s"));

        $this->Model_Batasan->insert($update);
        $this->response($update, REST_Controller::HTTP_OK);  
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

        $data = $this->Model_Batasan->getById($id);
        if ($data == null){
            $data = ARRAY(
                'Error'   => REST_Controller::HTTP_NOT_FOUND,
                'Message' => 'Batasan Id = '.$id.' not found');
            $this->response($data, REST_Controller::HTTP_NOT_FOUND);
        }

        $this->Model_Batasan->delete($id);

        $data = ARRAY(
            'Message' => 'Deleted');
        $this->response($data, REST_Controller::HTTP_NO_CONTENT);
    }

    public function getBatasanByUserId_post() {
        $id = $this->post('userId');

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

        $data = $this->Model_Batasan->getBatasanByUserId($id);
        $this->response($data, REST_Controller::HTTP_OK);
    }
}