<?php
/**
 *
 * User: satish4820
 * Date: 3/1/2018
 * Time: 11:33 PM
 */
error_reporting(0);
require(APPPATH . '/libraries/REST_Controller.php');

class GetStatistics extends REST_Controller
{
    function index()
    {
        if (($this->flag) == "1") {

            $data = $this->request_paramiters;

            if (isset($data['city_id'])) {
                
                $city_id = $data['city_id'];
                
                if($city_id > 0) {
                  $concat = " AND city_id=".$city_id;
                }else{
                    $concat = "";
                }
                
                $TotalVillages = $this->db->query("SELECT DISTINCT(city_id) FROM users WHERE 1=1".$concat);
                $TotalFamily = $this->db->query("SELECT id FROM users WHERE 1=1".$concat." AND head_id=0");
                
                if($city_id > 0) {
                    $TotalMembers = $this->db->query("SELECT * FROM users WHERE city_id = '".$city_id."' UNION SELECT * FROM users WHERE head_id IN (SELECT id FROM users WHERE city_id = '".$city_id."')");
                }else {
                    $TotalMembers = $this->db->query("SELECT * FROM users");
                }
                
                
                
                if($city_id > 0) {
                    $TotalMale = $this->db->query("SELECT id FROM users WHERE 1=1 AND gender='Male' AND id IN (SELECT id FROM users WHERE city_id = '".$city_id."' UNION SELECT id FROM users WHERE head_id IN (SELECT id FROM users WHERE city_id = '".$city_id."'))");
                     
                }else {
                    $TotalMale = $this->db->query("SELECT id FROM users WHERE 1=1".$concat." AND gender='Male'");
                }
                
                
                
                if($city_id > 0) {
                    $TotalFemale = $this->db->query("SELECT id FROM users WHERE 1=1 AND gender='Female' AND id IN (SELECT id FROM users WHERE city_id = '".$city_id."' UNION SELECT id FROM users WHERE head_id IN (SELECT id FROM users WHERE city_id = '".$city_id."'))");
                     
                }else {
                    $TotalFemale = $this->db->query("SELECT id FROM users WHERE 1=1".$concat." AND gender='Female'");
                }
                
                
                
               if($city_id > 0) {
                     $TotalUnmarriedMale = $this->db->query("SELECT id FROM users WHERE 1=1 AND gender='Male' AND marital_status='Single' AND id IN (SELECT id FROM users WHERE city_id = '".$city_id."' UNION SELECT id FROM users WHERE head_id IN (SELECT id FROM users WHERE city_id = '".$city_id."'))");
                     
                }else {
                    $TotalUnmarriedMale = $this->db->query("SELECT id FROM users WHERE 1=1".$concat." AND gender='Male' AND marital_status='Single'");
                }
                
                
                
                if($city_id > 0) {
                    $TotalUnmarriedFemale = $this->db->query("SELECT id FROM users WHERE 1=1  AND gender='Female' AND marital_status='Single' AND id IN (SELECT id FROM users WHERE city_id = '".$city_id."' UNION SELECT id FROM users WHERE head_id IN (SELECT id FROM users WHERE city_id = '".$city_id."'))");
                }else {
                    $TotalUnmarriedFemale = $this->db->query("SELECT id FROM users WHERE 1=1".$concat." AND gender='Female' AND marital_status='Single'");
                }
                
                
                
                
                
                $response = array(
                    'TotalVillages' => $TotalVillages->num_rows(),
                    'TotalFamily' => $TotalFamily->num_rows(),
                    'TotalMembers' => $TotalMembers->num_rows(),
                    'TotalMale' => $TotalMale->num_rows(),
                    'TotalFemale' => $TotalFemale->num_rows(),
                    'TotalUnmarriedMale' => $TotalUnmarriedMale->num_rows(),
                    'TotalUnmarriedFemale' => $TotalUnmarriedFemale->num_rows(),
                );
                
                if($city_id > 0) {
                    unset($response['TotalVillages']);   
                }
                
                $succes = array('success' => true, 'data' => $response);
                echo json_encode($succes);
                exit;
            }
        } 
    }
}