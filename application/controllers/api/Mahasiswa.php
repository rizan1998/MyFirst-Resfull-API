<?php

use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//To Solve File REST_Controller not found
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Mahasiswa extends REST_Controller
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('Mahasiswa_model', 'mahasiswa');
        $this->methods['index_get']['limit'] = 1000; // yang ini hanya 1000x per jam di hitung per key

        //catatan jadi data nya sudah lolos auth lolos key dan limitnya juga lolos
    }

    public function index_get()
    {
        //cek di request get ada id atau tidak
        $id = $this->get('id');
        if ($id === null) {
            $mahasiswa = $this->mahasiswa->getMahasiswa();
        } else {
            $mahasiswa = $this->mahasiswa->getMahasiswa($id);
        }

        //$mahasiswa = $this->mahasiswa->getMahasiswa();
        //var_dump($mahasiswa);

        //kalau manual harus menggunakan encode untuk convert data base ke json
        //di template ini hanya perlu menggunakan syntax dibawah
        if ($mahasiswa) {
            // Set the response and exit
            $this->response([
                'status' => TRUE, //nama status
                'data' => $mahasiswa
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        } else {
            $this->response([
                'status' => FALSE, //nama status
                'message' => 'id not found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function index_delete()
    {
        //catatan pada postman jika get akan pada di params tapi jika delete akan berada di body
        //pokonya sealin get ada di body lalu untuk delete nyalakan x-www nya

        $id = $this->delete('id');
        if ($id === null) { //cek jika id null atau yg mau di deletenya tidak ada
            $this->response([
                'status' => FALSE, //nama status
                'message' => 'provide an id !'
            ], REST_Controller::HTTP_BAD_REQUEST); // NOT_FOUND (404) being the HTTP response code
        } else {
            if ($this->mahasiswa->deleteMahasiswa($id) > 0) { //0 adalah menangkap return dari mode
                //dimana antara 1 dan 0 jika ada data yg terhapus maka jalankan jika tidak ada maka false
                //atau id not found
                // ok
                $this->response([
                    'status' => TRUE, //nama status
                    'id' => $id,
                    'message' => 'deleted'
                ], REST_Controller::HTTP_NO_CONTENT);
            } else {
                $this->response([
                    'status' => FALSE, //nama status
                    'message' => 'id not found!'
                ], REST_Controller::HTTP_BAD_REQUEST); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }

    public function index_post()
    {
        //pada input data dengan post juga dilakukan pada bagian body sama
        // seperti delete

        $data = [
            'nrp' => $this->post('nrp'),
            'nama' => $this->post('nama'),
            'email' => $this->post('email'),
            'jurusan' => $this->post('jurusan')
        ];
        if ($this->mahasiswa->createMahasiswa($data) > 0) {

            $this->response([
                'status' => TRUE, //nama status
                'message' => 'new mahasiwa has been created'
            ], REST_Controller::HTTP_CREATED);
        } else {
            $this->response([
                'status' => TRUE, //nama status
                'message' => 'failed to create new data!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function index_put()
    {
        $id = $this->put('id');
        $data = [
            'nrp' => $this->put('nrp'),
            'nama' => $this->put('nama'),
            'email' => $this->put('email'),
            'jurusan' => $this->put('jurusan')
        ];

        if ($this->mahasiswa->updateMahasiswa($data, $id) > 0) {

            $this->response([
                'status' => TRUE, //nama status
                'message' => 'new mahasiwa has been updated'
            ], REST_Controller::HTTP_NO_CONTENT);
        } else {
            $this->response([
                'status' => FALSE, //nama status
                'message' => 'failed to updated new data!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}
