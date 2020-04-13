<?php
/**
 *
 * User: satish4820
 * Date: 3/1/2018
 * Time: 11:33 PM
 */
error_reporting(0);
require(APPPATH . '/libraries/REST_Controller.php');

class SmartSearch extends REST_Controller
{
    function index() {
        if (($this->flag) == "1") {

            $data = $this->request_paramiters;
            
            $sLimit = "";
            $start = 0;
            $length = 10;
            $draw = 1;
            
            
            if (isset($data['start']) && $data['length'] != '-1') {
                $start = $data['start'];
                $length = $data['length'];
            }else {
                $start = 0;
                $length = 25;
            }
            
            $filterby = $data['filter_by'];
            $sWhere = $data['search']['value'];
            $dataList = $this->Users_model->get_global_search("", $sWhere, $start, $length, "id", "DESC",$filterby);
            $dataListCount = $this->Users_model->get_global_search("", $sWhere, "", "", "id", "DESC",$filterby);
            
            
            
            if(!empty($dataList)){
                $totalMem = 0;
                foreach($dataList as $d) {
                    $totalMem = $totalMem + count($d['members']);
                }
                $response = array('success' => true, 'total_records' => count($dataListCount),"members" => $dataList);
                echo json_encode($response);
                exit;
            }else{
                $error = array('success' => false, 'message' => $this->config->item('data_not_found'));
                echo json_encode($error);
                exit;
            }
            
            
        } 
    }
}