<?php
/**
 *
 * User: satish4820
 * Date: 3/1/2018
 * Time: 11:33 PM
 */
error_reporting(0);
require(APPPATH . '/libraries/REST_Controller.php');

class SearchByCity extends REST_Controller
{
    function index()
    {
        if (($this->flag) == "1") {

            $data = $this->request_paramiters;
            
            $sLimit = "";
            $start = 0;
            $length = 10;
            $draw = 1;
            $filterby = $data['filter_by'];
            $alpha = $data['alpha'];
            
            if (isset($data['start']) && $data['length'] != '-1') {
                $start = $data['start'];
                $length = $data['length'];
            }else {
                $start = 0;
                $length = 25;
            }
            
           
            $sWhere = $data['search']['value'];
            $dataList = $this->Users_model->get_datatables_for_api("", $sWhere, $start, $length, "id", "DESC",$filterby,$alpha);
            $dataListCount = $this->Users_model->get_datatables_for_api("", $sWhere, "", "", "id", "DESC",$filterby,$alpha);
            // $dataListCount = $this->Users_model->get_datatables("", $sWhere, "", "", "id", "DESC",$filterby,$alpha);
            
            $totalMem = 0;
            foreach($dataList as $d) {
                $totalMem = $totalMem + $d['members_count'];
            }
            
            $response = array('success' => true, "totalHead" => intval(count($dataListCount)), 'totalMem' => $totalMem, "members" => $dataList);
            echo json_encode($response);
            exit;
        } 
    }
}