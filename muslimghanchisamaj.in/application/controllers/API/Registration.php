<?php

error_reporting(0);
require(APPPATH . '/libraries/REST_Controller.php');

class Registration extends REST_Controller {

    function format_key($k) {
        $k = str_replace("_"," ",$k);
        $k = ucwords(strtolower($k));
        return $k;
    }

    function index() {
        if (($this->flag) == "1") {

            $data = $this->request_paramiters;

            foreach($data as $k=>$v) {
                if(empty($v)) {

                    $response['success'] = 'error';
                    $response['message'] = $this->format_key($k).' Cannot Be Empty';
                    echo json_encode($response);exit;
                }
            }
            if (isset($data['is_admin']) && $data['is_admin']==1) {
                $data['status'] = 1;
                $is_admin = $data['is_admin'];
            }else{
                $data['status'] = 0;
                $is_admin = 0;
            }
            unset($data['is_admin']);
            $data['created_dt'] = date('Y-m-d H:i:s');
            $data['updated_dt'] = date('Y-m-d H:i:s');
            $data['password'] = sha1($data['plain_password']);
            $data['profile_password'] = $data['plain_password'];

            if (!empty($data)) {
                $checkUserExists = $this->model_name->checkUserExists($data['email_address']);

                if ($checkUserExists === FALSE) {

                    $checkUserMobileExists = $this->model_name->checkUserMobileExists($data['mobile']);

                    if ($checkUserMobileExists == FALSE) {
                        if (!empty($data['profile_pic'])) {
                            $path = realpath('./uploads/users/');
                            //$data['profile_pic'] = $this->uploadBase64Image($data['profile_pic'], $path);
                            $temp = explode(".", $_FILES["profile_pic"]["name"]);
                            $newfilename = round(microtime(true)) . '.' . end($temp);
                            if (!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $path . $newfilename)) {
                                $response['success'] = "error";
                                $response['message'] = 'Could not upload the file!';
                            }
                            $data['profile_pic'] = $path . $newfilename;
                            $response['success'] = 'success';
                            $response['message'] = 'File uploaded successfully!';
                            // echo json_encode($response);exit;
                        }
                        
                        $GetLocalAdmin = $this->db->query("SELECT user_id,device_token FROM tbl_devices WHERE user_id IN (SELECT id FROM users WHERE local_community_id = '".$data['local_community_id']."' AND role='LOCAL_ADMIN')");
                        $LocalAdminRes = $GetLocalAdmin->result();
                        
                        if(empty($LocalAdminRes)){
                         $GetLocalAdmin = $this->db->query("SELECT user_id,device_token FROM tbl_devices WHERE user_id IN (SELECT id FROM users WHERE sub_community_id = '".$data['sub_community_id']."' AND role='SUB_ADMIN')");    
                         $LocalAdminRes = $GetLocalAdmin->result();
                        
                        if(empty($LocalAdminRes)){
                            $GetLocalAdmin = $this->db->query("SELECT user_id,device_token FROM tbl_devices WHERE user_id IN (SELECT id FROM users WHERE  role='SUPERADMIN')");    
                                $LocalAdminRes = $GetLocalAdmin->result();
                            }
                        }
                        // print_r($LocalAdminRes);die;
                        $allToken = [];
                        foreach($LocalAdminRes as $row){
                            if (strlen($row->device_token)>10) {
                                $allToken[] = $row->device_token;
                            }
                        }
                       // $device_token = $LocalAdminRes->device_token;
                        // print_r($allToken);die;
                        $result = $this->model_name->addUser($data);
                        // $result = 1;
                        if ($result) {
                            $text_msg = "Please approve " . $data['first_name'] . ' ' . $data['last_name'] . "'s Request ";
                            /* send notification to android */
                            //$androidToken = $this->model_name->getAdminAccessToken("Android");
                            if (!$is_admin) {
                                if (!empty($allToken)) {
                                    $notify = [
                                        'user_id'=>$result,
                                        'message'=>$text_msg
                                    ];
                                    $this->model_name->send_android_notification_registration($notify, $allToken);
                                }
	                            $headers = getallheaders();
	                            $device_id = $headers['Devicetoken'];
	                            $dataDev['access_token'] = $this->genRandomToken();
	                            $dataDev['device_token'] = $device_id;
	                            $dataDev['user_id'] = $result;
	                            $this->model_name->addDeviceDetails($dataDev);
                            }
                            
                            if (isset($is_admin) && $is_admin==1) {
                                $succes = array('success' => 'success', 'message' => $this->config->item('register_success'), 'user_id' => $result);
                            }else{
                                $succes = array('success' => 'success', 'message' => $this->config->item('register_request_sent'), 'user_id' => $result);
                            }
                        } else {
                            $succes = array('success' => 'success', 'message' => $this->config->item('register_success'), 'user_id' => $result);
                        }
                        
                        echo json_encode($succes);
                        exit;
                    } else {
                        $succes = array('success' => 'error', 'message' => $this->config->item('mobile_already_register'));
                        echo json_encode($succes);
                        exit;
                    }
                } else {
                    $succes = array('success' => 'error', 'message' => 'Email Id already exists');
                    echo json_encode($succes);
                    exit;
                }
            } else {
                $error = array('success' => 'error', 'message' => $this->config->item('required_missing'));
                echo json_encode($error);
                exit;
            }
        }
    }

}

?>
