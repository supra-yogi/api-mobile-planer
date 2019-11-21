<?php
 
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
 
class Planning extends REST_Controller {
 
    function __construct($config = 'rest') {
        parent::__construct($config);
        $this->load->model('Model_Planning');
        $this->load->model('Model_User');
        $this->load->model('Model_Batasan');
        $this->load->model('Model_DetailPlanning');
    }
 
    // GET ALL DATA
    // api/customer or api/customer/1 [GET]
    public function index_get()
    {
        $id = $this->get('id');
        if ($id != null) {
            $data = $this->Model_Planning->getDetailById($id);
            if ($data == null) {
                $data = ARRAY(
                'Error'   => REST_Controller::HTTP_NOT_FOUND,
                'Message' => 'Planning Id = '.$id.' not found');
                $this->response($data, REST_Controller::HTTP_NOT_FOUND);
            }
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $data = $this->Model_Planning->getAll();
            $this->response($data, REST_Controller::HTTP_OK);
        }
    }
 
    // INSERT NEW DATA
    // api/customer [POST]
    public function index_post() {
        $currentCost = $this->POST('currentCost');
        $period = $this->POST('timePeriod');
        $inflasi = $this->POST('inflationRate') / 100;
        $interestRate = $this->POST('interestRate') / 100;
        $alreadyInvest = $this->POST('alreadyInvest');
        $biayaAdmin = $this->POST('biayaAdmin');
        $pajakBunga = $this->POST('pajakBunga') / 100;

        // Rumus Modal Akhir
        $periodInYear = $period / 12;
        $futureCost = ($currentCost * pow((1 + $inflasi), $periodInYear));

        //aturan pemerintah yang mengharuskan kena pajak 20% jika melebihi 7.500.000
        $kenaPajak = 7500000;
        $biayaKenaPajakBunga = 0;
        $totalBunga = 0;
        $totalBiayaAdmin = $biayaAdmin * $period;

        // bunga per bulan
        $rate = ($interestRate / 12);
        // prevent value
        $pv = $alreadyInvest;
        // Total yang harus dibayar (future value)
        $fv = $futureCost + $totalBiayaAdmin;
        // periode dalam bulan
        $nper = $period;

        // Rumus Anuitas
        //Payment
        $monthlyInvest = $this->pmt($rate, $nper, $pv, $fv, 1);
        // Untuk menentukan future value dengan menjumlahkan biaya kena pajak bunga
        for ($i=0; $i < $nper; $i++) { 
            $setoranAwal = $i == 0 ? $pv : $tabunganAkhir;
            $setoranBulanan = $monthlyInvest;
            $bunga = ($setoranAwal + $setoranBulanan) * $rate;
            $totalBunga += $bunga;
            $tabunganAkhir = $setoranAwal + $setoranBulanan + $bunga;

            $biayaPajakBunga = 0;
            if ($tabunganAkhir > $kenaPajak) {
                $biayaPajakBunga = $pajakBunga * $bunga;
            }
            $biayaKenaPajakBunga += $biayaPajakBunga;
        }

        // Rumus Lumpsum (Sama seperti rumus modal awal yang dibalik, menggunakan persamaan linear)
        $lumpsum = ($fv / pow((1 + $rate), $period)) - $alreadyInvest;

        $data = ARRAY(
            'id'              => 0,
            'userId'          => $this->POST('userId'),
            'goalName'        => $this->POST('goalName'),
            'jangkaWaktu'     => $this->POST('timePeriod'),
            'currentCost'     => $currentCost,
            'futureCost'      => $futureCost,
            'alreadyInvest'   => $alreadyInvest,
            'lumpsum'         => $lumpsum,
            'monthlyInvest'   => $monthlyInvest,
            'requiredRate'    => $this->POST('requiredRate'),
            'biayaAdmin'      => $this->POST('biayaAdmin'),
            'totalBiayaAdmin' => $totalBiayaAdmin,
            'pajakBunga'      => $this->POST('pajakBunga'),
            'totalPajakBunga' => $biayaKenaPajakBunga,
            'totalBunga'      => $totalBunga,
            'inflationRate'   => $this->POST('inflationRate'),
            'interestRate'    => $this->POST('interestRate'),
            'created_date'    => date("Y-m-d h:i:s"));

        $planningId = $this->Model_Planning->insert($data);
        $this->rincianTabungan($planningId);
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

        $data = $this->Model_Planning->getById($id);
        if ($data == null){
            $data = ARRAY(
                'Error'   => REST_Controller::HTTP_NOT_FOUND,
                'Message' => 'Planning Id = '.$id.' not found');
            $this->response($data, REST_Controller::HTTP_NOT_FOUND);
        }


        $currentCost = $this->PUT('currentCost');
        $period = $this->PUT('timePeriod');
        $inflasi = $this->PUT('inflationRate') / 100;
        $interestRate = $this->PUT('interestRate') / 100;
        $alreadyInvest = $this->PUT('alreadyInvest');
        $biayaAdmin = $this->PUT('biayaAdmin');
        $pajakBunga = $this->PUT('pajakBunga') / 100;

        // Rumus Modal Akhir
        $periodInYear = $period / 12;
        $futureCost = ($currentCost * pow((1 + $inflasi), $periodInYear));

        //aturan pemerintah yang mengharuskan kena pajak 20% jika melebihi 7.500.000
        $kenaPajak = 7500000;
        $biayaKenaPajakBunga = 0;
        $totalBunga = 0;
        $totalBiayaAdmin = $biayaAdmin * $period;

        // bunga per bulan
        $rate = ($interestRate / 12);
        // prevent value
        $pv = $alreadyInvest;
        // Total yang harus dibayar (future value)
        $fv = $futureCost + $totalBiayaAdmin;
        // periode dalam bulan
        $nper = $period;

        // Rumus Anuitas
        //Payment
        $monthlyInvest = $this->pmt($rate, $nper, $pv, $fv, 1);
        // Untuk menentukan future value dengan menjumlahkan biaya kena pajak bunga
        for ($i=0; $i < $nper; $i++) { 
            $setoranAwal = $i == 0 ? $pv : $tabunganAkhir;
            $setoranBulanan = $monthlyInvest;
            $bunga = ($setoranAwal + $setoranBulanan) * $rate;
            $totalBunga += $bunga;
            $tabunganAkhir = $setoranAwal + $setoranBulanan + $bunga;

            $biayaPajakBunga = 0;
            if ($tabunganAkhir > $kenaPajak) {
                $biayaPajakBunga = $pajakBunga * $bunga;
            }
            $biayaKenaPajakBunga += $biayaPajakBunga;
        }

        // Rumus Lumpsum (Sama seperti rumus modal awal yang dibalik, menggunakan persamaan linear)
        $lumpsum = ($fv / pow((1 + $rate), $period)) - $alreadyInvest;

        $update = ARRAY(
            'id'              => $id,
            'goalName'        => $this->PUT('goalName'),
            'jangkaWaktu'     => $this->PUT('timePeriod'),
            'currentCost'     => $currentCost,
            'futureCost'      => $futureCost,
            'alreadyInvest'   => $alreadyInvest,
            'lumpsum'         => $lumpsum,
            'monthlyInvest'   => $monthlyInvest,
            'requiredRate'    => $this->PUT('requiredRate'),
            'biayaAdmin'      => $this->PUT('biayaAdmin'),
            'totalBiayaAdmin' => $totalBiayaAdmin,
            'pajakBunga'      => $this->PUT('pajakBunga'),
            'totalPajakBunga' => $biayaKenaPajakBunga,
            'totalBunga'      => $totalBunga,
            'inflationRate'   => $this->PUT('inflationRate'),
            'interestRate'    => $this->PUT('interestRate'),
            'updated_date'    => date("Y-m-d h:i:s"));

        $this->Model_Planning->insert($update);
        $this->Model_DetailPlanning->delete($id);
        $this->rincianTabungan($id);
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

        $data = $this->Model_Planning->getById($id);
        if ($data == null){
            $data = ARRAY(
                'Error'   => REST_Controller::HTTP_NOT_FOUND,
                'Message' => 'Planning Id = '.$id.' not found');
            $this->response($data, REST_Controller::HTTP_NOT_FOUND);
        }

        $this->Model_DetailPlanning->delete($id);
        $this->Model_Planning->delete($id);

        $data = ARRAY(
            'Message' => 'Deleted');
        $this->response($data, REST_Controller::HTTP_NO_CONTENT);
    }

    public function getPlanningByUserId_post() {
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

        $data = $this->Model_Planning->getPlanningByUserId($id);
        $this->response($data, REST_Controller::HTTP_OK);
    }

    public function getPriorityPlanningByUserId_post() {
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

        $planning = $this->Model_Planning->getPlanningByUserId($id);
        //batasan
        $batasan  = $this->Model_Batasan->getBatasanByUserId($id);
        //waktu
        $waktuCepatFrom      = $batasan[0]->waktuCepatFrom;
        $waktuCepatTo        = $batasan[0]->waktuCepatTo;
        $waktuLamaFrom       = $batasan[0]->waktuLamaFrom;
        $waktuLamaTo         = $batasan[0]->waktuLamaTo;
        //biaya
        $biayaRendahFrom     = $batasan[0]->biayaRendahFrom;
        $biayaRendahTo       = $batasan[0]->biayaRendahTo;
        $biayaSedangFrom     = $batasan[0]->biayaSedangFrom;
        $biayaSedangTo       = $batasan[0]->biayaSedangTo;
        $biayaSedangMid      = ($biayaSedangFrom + $biayaSedangTo) / 2;
        $biayaTinggiFrom     = $batasan[0]->biayaTinggiFrom;
        $biayaTinggiTo       = $batasan[0]->biayaTinggiTo;
        //kebutuhan
        $kebutuhanRendahFrom = $batasan[0]->kebutuhanRendahFrom;
        $kebutuhanRendahTo   = $batasan[0]->kebutuhanRendahTo;
        $kebutuhanTinggiFrom = $batasan[0]->kebutuhanTinggiFrom;
        $kebutuhanTinggiTo   = $batasan[0]->kebutuhanTinggiTo;

        for ($i=0; $i < count($planning); $i++) { 
            $jangkaWaktu = $planning[$i]->jangkaWaktu;
            $cost        = $planning[$i]->futureCost;
            $kebutuhan   = $planning[$i]->requiredRate;

            //FUZZIFIKASI
            $mWaktuCepat = $jangkaWaktu >= $waktuCepatTo ? 0 : 
                            ($jangkaWaktu <= $waktuCepatFrom ? 1 : 
                            ($waktuCepatTo-$jangkaWaktu)/($waktuCepatTo-$waktuCepatFrom));
            $mWaktuLama  = $jangkaWaktu <= $waktuLamaFrom ? 0 : 
                            ($jangkaWaktu >= $waktuLamaTo ? 1 : 
                            ($jangkaWaktu-$waktuLamaFrom)/($waktuLamaTo-$waktuLamaFrom));

            $mBiayaRendah = $cost >= $biayaRendahTo ? 0 :
                            ($cost <= $biayaRendahFrom ? 1 : 
                            ($biayaRendahTo-$cost)/($biayaRendahTo-$biayaRendahFrom));
            $mBiayaSedang = ($cost <= $biayaSedangFrom || $cost >= $biayaSedangTo) ? 0 :
                            ($cost == $biayaSedangMid ? 1 : 
                            (($cost > $biayaSedangFrom && $cost < $biayaSedangMid) ? (($cost-$biayaSedangFrom) / ($biayaSedangMid - $biayaSedangFrom)) : 
                            ($biayaSedangTo-$cost)/($biayaSedangTo-$biayaSedangMid)));
            $mBiayaTinggi = $cost <= $biayaTinggiFrom ? 0 :
                            ($cost >= $biayaTinggiTo ? 1 : 
                            ($cost-$biayaTinggiFrom)/($biayaTinggiTo-$biayaTinggiFrom));

            $mKebutuhanRendah = $kebutuhan >= $kebutuhanRendahTo ? 0 : 
                            ($kebutuhan <= $kebutuhanRendahFrom ? 1 : ($kebutuhanRendahTo-$kebutuhan)/($kebutuhanRendahTo-$kebutuhanRendahFrom));
            $mKebutuhanTinggi = $kebutuhan <= $kebutuhanTinggiFrom ? 0 : 
                            ($kebutuhan >= $kebutuhanTinggiTo ? 1 : ($kebutuhan-$kebutuhanTinggiFrom)/($kebutuhanTinggiTo-$kebutuhanTinggiFrom));

            //FUNGSI KEANGGOTAAN / PREDIKAT
            $a1  = min($mKebutuhanRendah, $mBiayaRendah, $mWaktuCepat);
            $a2  = min($mKebutuhanRendah, $mBiayaRendah, $mWaktuLama);
            $a3  = min($mKebutuhanRendah, $mBiayaSedang, $mWaktuCepat);
            $a4  = min($mKebutuhanRendah, $mBiayaSedang, $mWaktuLama);
            $a5  = min($mKebutuhanRendah, $mBiayaTinggi, $mWaktuCepat);
            $a6  = min($mKebutuhanRendah, $mBiayaTinggi, $mWaktuLama);
            $a7  = min($mKebutuhanTinggi, $mBiayaRendah, $mWaktuCepat);
            $a8  = min($mKebutuhanTinggi, $mBiayaRendah, $mWaktuLama);
            $a9  = min($mKebutuhanTinggi, $mBiayaSedang, $mWaktuCepat);
            $a10 = min($mKebutuhanTinggi, $mBiayaSedang, $mWaktuLama);
            $a11 = min($mKebutuhanTinggi, $mBiayaTinggi, $mWaktuCepat);
            $a12 = min($mKebutuhanTinggi, $mBiayaTinggi, $mWaktuLama);

            //INFERENSI
            //Himpunan Output Rekomendasi Rendah
            $z1  = abs(($a1*60)-60);
            $z2  = abs(($a2*60)-60);
            $z3  = abs(($a3*60)-60);
            $z4  = abs(($a4*60)-60);
            $z5  = abs(($a5*60)-60);
            $z6  = abs(($a6*60)-60);
            $z10 = abs(($a10*60)-60);
            $z11 = abs(($a11*60)-60);
            $z12 = abs(($a12*60)-60);

            //Himpunan Output Rekomendasi Tinggi
            $z7  = (60*$a7)+40;
            $z8  = (60*$a8)+40;
            $z9  = (60*$a9)+40;

            //DEFUZZIFIKASI
            $Z = (($a1*$z1)+($a2*$z2)+($a3*$z3)+($a4*$z4)+($a5*$z5)+($a6*$z6)+($a7*$z7)+($a8*$z8)+($a9*$z9)+($a10*$z10)+($a11*$z11)+($a12*$z12))
                / ($a1+$a2+$a3+$a4+$a5+$a6+$a7+$a8+$a9+$a10+$a11+$a12); 

            // echo $Z . ' ';
            $priority = array('priority' => $Z);

            $insertedId = $this->Model_Planning->update($planning[$i]->id, $priority);
        }

        $data = $this->Model_Planning->getPriorityByUserId($id);
        $this->response($data, REST_Controller::HTTP_OK);
    }

    public function getRincianTabungan_post() {
        $id = $this->post('id');

        // Validate the id.
        if ($id <= 0)
        {
            $data = ARRAY(
                'Error'   => REST_Controller::HTTP_BAD_REQUEST,
                'Message' => 'Id must > 0');
            $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
        }

        $data = $this->Model_Planning->getDetailById($id);
        if ($data == null){
            $data = ARRAY(
            'Error'   => REST_Controller::HTTP_NOT_FOUND,
            'Message' => 'Planning Id = '.$id.' not found');
            $this->response($data, REST_Controller::HTTP_NOT_FOUND);
        }

        
        $result = $this->Model_DetailPlanning->getDetailByPlanningId($id);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function rincianTabungan($planningId) {
        $data          = $this->Model_Planning->getDetailById($planningId);
        $currentCost   = $data[0]->currentCost;
        $period        = $data[0]->jangkaWaktu;
        $inflasi       = $data[0]->inflationRate / 100;
        $interestRate  = $data[0]->interestRate / 100;
        $alreadyInvest = $data[0]->alreadyInvest;
        $biayaAdmin    = $data[0]->biayaAdmin;
        $pajakBunga    = $data[0]->pajakBunga / 100;

        // Rumus Modal Akhir
        $periodInYear = $period / 12;
        $futureCost   = ($currentCost * pow((1 + $inflasi), $periodInYear));

        //aturan pemerintah yang mengharuskan kena pajak 20% jika melebihi 7.500.000
        $kenaPajak           = 7500000;
        $biayaKenaPajakBunga = 0;
        $totalBunga          = 0;
        $totalBiayaAdmin     = $biayaAdmin * $period;

        // bunga per bulan
        $rate = ($interestRate / 12);
        // prevent value
        $pv   = $alreadyInvest;
        // Total yang harus dibayar (future value)
        $fv   = $futureCost + $totalBiayaAdmin;
        // periode dalam bulan
        $nper = $period;

        // Rumus Anuitas
        //Payment
        $monthlyInvest = $this->pmt($rate, $nper, $pv, $fv, 1);
        // Untuk menentukan future value dengan menjumlahkan biaya kena pajak bunga
        for ($i=0; $i < $nper; $i++) { 
            $setoranAwal    = $i == 0 ? $pv : $tabunganAkhir;
            $setoranBulanan = $monthlyInvest;
            $bunga          = ($setoranAwal + $setoranBulanan) * $rate;
            $totalBunga     += $bunga;
            $tabunganAkhir  = $setoranAwal + $setoranBulanan + $bunga;

            $biayaPajakBunga = 0;
            if ($tabunganAkhir > $kenaPajak) {
                $biayaPajakBunga = $pajakBunga * $bunga;
            }
            $biayaKenaPajakBunga += $biayaPajakBunga;

            $result = array(
                        'planningId'     => $planningId,
                        'bulan'          => $i+1, 
                        'tabunganAwal'   => round($setoranAwal, 2),
                        'setoranBulanan' => round($setoranBulanan, 2),
                        'bunga'          => round($bunga, 2),
                        'pajak'          => round($biayaPajakBunga, 2),
                        'tabunganAkhir'  => round($tabunganAkhir, 2));
            $this->Model_DetailPlanning->insert($result);
        }
    }

    public function getAll_get() {
        $data = $this->Model_Planning->getAllInAdmin();
        $this->response($data, REST_Controller::HTTP_OK);
    }

    public function pmt($rate, $nper, $pv, $fv, $type) {
        return ($rate * ($fv * -1 + $pv * pow(1 + $rate, $nper))) / ((1 + $rate * $type) * (1 - pow(1 + $rate, $nper)));
    }
}