<?php
/**
 *
 * User: satish4820
 * Date: 3/1/2018
 * Time: 11:33 PM
 */
error_reporting(0);
require(APPPATH . '/libraries/REST_Controller.php');

class GetCities extends REST_Controller
{
    function index()
    {
        if (($this->flag) == "1") {

            $data = $this->request_paramiters;
            $date = '';
            if (isset($data['date'])) {
                $date = $data['date'];
            }
            if (isset($data['state_id']) && $data['state_id'] > 0) {

                $cities = $this->model_name->getCities($data['state_id'],$date);
                $deletedids = $this->model_name->getDeleted('cities');
                $lastUpdated = $this->model_name->getLastUpdated('cities');
                if ($deletedids) {
                    $deletedids = explode(',', $deletedids['id']);
                }

                if ($date!='' && isset($cities)) {
                    $succes = array('success' => true, 'message' => $this->config->item('cities_retried'), 'data' => $cities,'deleted' => $deletedids,'last_updated' => $lastUpdated);
                    echo json_encode($succes);
                    exit;
                }elseif(!empty($cities)) {
                    $succes = array('success' => true, 'message' => $this->config->item('cities_retried'), 'data' => $cities,'deleted' => $deletedids,'last_updated' => $lastUpdated);
                    echo json_encode($succes);
                    exit;
                }else{
                    $error = array('success' => false, 'message' => $this->config->item('cities_not_found'));
                    echo json_encode($error);
                    exit;
                }
            }
        } 
    }
}