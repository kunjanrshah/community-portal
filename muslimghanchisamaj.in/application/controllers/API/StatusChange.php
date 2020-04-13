<?php
/**
 *
 * User: satish4820
 * Date: 3/1/2018
 * Time: 11:33 PM
 */
error_reporting(0);
require(APPPATH . '/libraries/REST_Controller.php');

class StatusChange extends REST_Controller
{
    function index() 
    {
        $data = $this->request_paramiters;
        // print_r($data);die;
        // $json = ['one'=>'one11','youtube'=>['https://youtube.com','https://youtube.com']];
        // print_r(json_encode($json));die;
        $this->db->where('id IN ('.$data['idList'].')');
        $this->db->update('users', ['status'=>$data['status']]);

        if ($data['status']) {
            $GetLocalAdmin = $this->db->query("SELECT device_token FROM tbl_devices WHERE user_id IN (".$data['idList'].")");
            $LocalAdminRes = $GetLocalAdmin->result();
            // print_r($LocalAdminRes);die;
            $allToken = [];
            foreach($LocalAdminRes as $row){
                if (strlen($row->device_token)>10) {
                    $allToken[] = $row->device_token;
                }
            }
            if (isset($data['id'])) {
                $userData = $this->db->query("SELECT * FROM users WHERE id =".$data['id']);
                $userData = $userData->row();
                // print_r($userData);die;
                if (!empty($allToken)) {
                    $text_msg = 'Your request for account approval is approved.';
                    $notify = [
                        'user_id'=>$userData->id,
                        'message'=>$text_msg,
                    ];
                    $this->model_name->send_android_notification_registration($notify, $allToken);
                }
            }
        }
        // $users = $this->model_name->statusChange($data);
        // print_r($users);die;
        
        if($data){
            $succes = array('success' => true, 'message' => $this->config->item('status_changed'));
            echo json_encode($succes);
            exit;
        }else{
            $error = array('success' => false, 'message' => $this->config->item('data_not_found'));
            echo json_encode($error);
            exit;
        }
    }
}