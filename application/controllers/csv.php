<?php

class Csv extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('csv_model'); //meload csv_model
        $this->load->library('csvimport'); //meload library csvimport.php
    }

    function index() {
        $data['addressbook'] = $this->csv_model->get_addressbook(); //menampilkan data addressbook 
        $this->load->view('csvindex', $data); //meload view dan menampilkan data di browser
    }

    function importcsv() {
        $data['addressbook'] = $this->csv_model->get_addressbook();
        $data['error'] = '';    //initialize image upload error array to empty

        //convigure upload 
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = '*';
        $config['max_size'] = '1000';

        $this->load->library('upload', $config);


        // jika upload gagal, muncul error
        if (!$this->upload->do_upload()) {
            $data['error'] = $this->upload->display_errors();

            $this->load->view('csvindex', $data);
        } else {

            //prosses upload csv berhasil serta memproses insert data ke database
            $file_data = $this->upload->data();
            $file_path =  './uploads/'.$file_data['file_name'];
            
            if ($this->csvimport->get_array($file_path)) {
                $csv_array = $this->csvimport->get_array($file_path);
                foreach ($csv_array as $row) {
                    $insert_data = array(
                        'firstname'=>$row['firstname'],
                        'lastname'=>$row['lastname'],
                        'phone'=>$row['phone'],
                        'email'=>$row['email'],
                    );
                    $this->csv_model->insert_csv($insert_data);
                }
                $this->session->set_flashdata('success', 'Csv Data Imported Succesfully');
                redirect(base_url().'csv');
                //echo "<pre>"; print_r($insert_data);
            } else 
                $data['error'] = "Error occured";
                $this->load->view('csvindex', $data);
            }  
        } 
}