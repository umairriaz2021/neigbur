<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("usermodel");
        $this->load->model('Admin_model', 'Admin');
        $this->load->model('Order_model','Order');
        $this->load->model('Rating_model','Rating');
    }

    public function index()
    {
        redirect("home");
    }

    public function dashboard()
    {

        if ($this->session->userdata('loggedUser')) {
            $data['admin'] = $this->session->userdata('loggedUser');
            $this->load->view('front/admin/pages/dashboard', $data);
        } else {
            redirect('user/login');
        }

    }

    public function editProfile($user_id)
    {
        if ($this->session->userdata('loggedUser')) {

            $data['countries'] = $this->Admin->getAllCountries();
            $data['user'] 	=	$this->Admin->getUserById($user_id);

            $this->load->view('front/editprofile',$data);

        } else {
            redirect('user/login');
        }
    }


    public function  updateProfile() {

        if ($this->session->userdata('loggedUser')) {

            $image_name     =   '';

            if($_FILES['user_image']['name']) {

                $image_name     =   $this->imageUpload('user_image');

                if(!$image_name) {

                    $this->session->set_flashdata('error_message',"<p class='alert alert-danger'>Invalid Image!</p>");
                    redirect('user/editProfile/'.$this->input->post('user_id'));
                }

                $image_name     =   $image_name;

            }

            $udata 	=	array(
                'uname'	    =>	$this->input->post('uname'),
                'email'     =>	$this->input->post('email'),
                'city'	    =>	$this->input->post('city'),
                'country'	=>	$this->input->post('country'),
                'reg_type'	=>	$this->input->post('reg_type'),
                'user_image'=>  $image_name
            );
            if(isset($_POST['password']) && !empty($_POST['password'])){
                $udata['password'] = md5($_POST['password']);
            }
            $result = $this->Admin->updateUser($this->input->post('user_id'),$udata);

            $this->session->set_flashdata("error_message","<p class='alert alert-success'>Updated successfully!</p>");
            redirect('user/editProfile/'.$this->input->post('user_id'));

        } else {
            redirect('user/login');
        }

    }


    public function ajaxlogin()
    {
        if ($this->input->is_ajax_request()) {
            /* echo "<pre>"; print_r($this->input->post()); */
            $pass = md5($this->input->post('password'));
            $uname = $this->input->post('username');
            $userexist = $this->usermodel->dologin($uname, $pass);

            if ($userexist) {
                if ($userexist->status == 1) {
                    $where = array("email" => $this->input->post('username'), "password" => $pass);
                    $user = $this->usermodel->getuser("users", $where);
                    $update_login_status = $this->usermodel->updateLoginStatus('1', $user->id);
                    $this->session->set_userdata('loggedUser', $user);
                    echo json_encode(array('status' => 'ok', 'uid' => $user->id, 'reg_type' => $user->reg_type));
                } else {
                    echo json_encode(array('status' => 'error', 'msg' => 'Error: Please activate your account first.'));
                }
            } else {
                echo json_encode(array('status' => 'error', 'msg' => 'Error: Invalid Username/Password.'));
            }
        }
    }

    public function login()
    {

        if ($this->session->userdata('loggedUser')) {
            redirect('home');
        } else {

            if ($this->input->post()) {
                $pass = md5($this->input->post('password'));
                $uname = $this->input->post('username');
                $userexist = $this->usermodel->dologin($uname, $pass);
                if ($userexist) {

                    if ($userexist->status == 1) {
                        $where = array("email" => $this->input->post('username'), "password" => $pass);
                        $user = $this->usermodel->getuser("users", $where);
                        $update_login_status = $this->usermodel->updateLoginStatus('1', $user->id);
                        $this->session->set_userdata('loggedUser', $user);
                        if ($user->reg_type == 'buyer') {

                            if($this->uri->segment(3) && $this->uri->segment(4)) {

                                $order_id   =   base64_decode($this->uri->segment(3));
                                redirect('user/rating/'.$order_id);
                            }
                            redirect('user/orderrequests');
                        } else {
                            redirect('crypto');
                        }
                    } else {
                        $this->session->set_flashdata("error_message",
                            "<p class='alert alert-danger'>Error: Please activate your account first.</p>");
                        redirect('user/login');
                    }
                } else {
                    $this->session->set_flashdata("error_message",
                        "<p class='alert alert-danger'>Error: Invalid Username/Password.</p>");
                    redirect('user/login');
                }
            } else {
                $this->load->view('front/login');
            }
        }
    }

    public function register()
    {
        if ($this->session->userdata('loggedUser')) {
            redirect('home');
        } else {
            if ($this->input->post()) {

//                if($_FILES['user_image']['name']) {
//
//                    $image_name     =   $this->imageUpload('user_image');
//
//                    if(!$image_name) {
//
//                        $this->session->set_flashdata('error_message',"<p class='alert alert-danger'>Invalid Image!</p>");
//                        redirect('user/register');
//                    }
//
//                   $_POST['user_image'] =   $image_name;
//                }

                $pdata = $_POST;
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen($characters);
                $token = '';
                for ($i = 0; $i < 7; $i++) {
                    $token .= $characters[rand(0, $charactersLength - 1)];
                }
                $pdata['email_varification'] = md5($token);

                $insert_id = $this->usermodel->adduser($pdata);

                if ($insert_id > 0) {
                    $this->load->library('email');
                    $content = '<p><a href="' . base_url() . '"><img class="img-fluid" src="' . base_url() . 'assets/front/img/liberi-logo-150.jpg"></a></p>';
                    $content .= '<p>Thank you, ' . ucfirst($this->input->post('uname')) . ' for registering for an account on Liberi.Exchange.<br>Please follow the below link to complete your registration process.</p>';
                    $content .= '<br>';
                    $content .= "<a href='" . base_url() . "user/emailVarification/" . $insert_id . "/" . md5($token) . "'>" . base_url() . "login/emailverification/" . $insert_id . "/" . md5($token) . "</a>";
                    $this->email->from('info@liberi.exchange', 'LiberiExchange');
                    $this->email->to($this->input->post('email'));
                    $this->email->set_mailtype('html');
                    $this->email->set_newline('\r\n');
                    $this->email->subject('Email Verification');
                    $this->email->message($content);
                    $this->email->send();

                    $this->session->set_flashdata("error_message",
                        "<p class='alert alert-success'>Please check your email for verification!</p>");
                } else {
                    $this->session->set_flashdata("error_message",
                        "<p class='alert alert-error'>Please try later!</p>");
                    redirect('user/login');
                }
            }
            $data['countries'] = $this->usermodel->getAllCountries();
            $data['title'] = "Sign Up";
            $this->load->view('front/register', $data);
        }
    }

    public function checkUniqueEmail()
    {
        $result = $this->usermodel->checkUniqueEmail($this->input->post('email'));
        if ($result) {
            echo json_encode(1);
            die;
        } else {
            echo json_encode(0);
            die;
        }
    }

    public function checkUniqueName()
    {
        $result = $this->usermodel->checkUniqueName($this->input->post('uname'));
        if ($result) {
            echo json_encode(1);
            die;
        } else {
            echo json_encode(0);
            die;
        }
    }

    public function emailVarification()
    {
        if ($this->uri->segment(4)) {
            $auth_token = $this->usermodel->auth_token($this->uri->segment(3), $this->uri->segment(4));

            /* echo "<pre>"; print_r($auth_token); die; */

            if ($auth_token) {
                if ($auth_token->reg_type != 'buyer') {
                    $data['fee'] = 25;
                    $data['success_url'] = $auth_token->id . '/success';
                    $data['cancle_url'] = $auth_token->id . '/cancle';
                    $this->load->view('front/payment', $data);
                } else {
                    $active_user = $this->usermodel->active_user($this->uri->segment(3));
                    $this->session->set_flashdata("error_message",
                        "<p class='alert alert-success'>Email Verification done successfully! Please Login To Continue.</p>");
                    redirect('user/login');
                }
            } else {
                $this->session->set_flashdata("error_message",
                    "<p class='alert alert-danger'>Error: Invalid User.</p>");
                redirect('user/login');
            }
        } else {
            $this->session->set_flashdata("error_message", "<p class='alert alert-danger'>Error: Invalid User.</p>");
            redirect('login');
        }

    }

    public function payment()
    {
        if ($this->uri->segment(4) == 'success') {
            $futureDate = date('Y-m-d', strtotime('+1 year'));
            $this->db->insert('members_billing',
                array('uid' => $this->uri->segment(3), 'start' => date('Y-m-d'), 'end' => $futureDate));
            $active_user = $this->usermodel->active_user($this->uri->segment(3));
            $this->session->set_flashdata("error_message",
                "<p class='alert alert-success'>Payment has been done successfully! Please Login To Continue.</p>");
            redirect('user/login');
        } else {
            $this->session->set_flashdata("error_message",
                "<p class='alert alert-danger'>Error: Payment canceled. Please try later.</p>");
            redirect('user/login');
        }
    }

    public function order()
    {
        if ($this->session->userdata('loggedUser')) {

            if ($this->input->post()) {

                $data = array(

                    'crypto'    =>  $this->input->post('crypto'),
                    'fiat'    =>  $this->input->post('fiat'),
                    'seller_id'    =>  $this->input->post('seller_id'),
                    'crypto_value'    =>  $this->input->post('crypto_value'),
                    'fiat_value'    =>  $this->input->post('fiat_value'),
                    'buyer_id'    =>  $this->input->post('buyer_id'),
                    'type'    =>  'buy'
                );

                $this->db->insert('orders', $data);
                $user_id = $this->db->insert_id();

                if ($user_id) {
                    $where = ['id' => $this->input->post('seller_id')];
                    $sellerUser = $this->usermodel->getuser("users", $where);

                    if($sellerUser->login_status == '0') {

                        $this->load->library('email');
                        $content = '<p><a href="' . base_url() . '"><img class="img-fluid" src="' . base_url() . 'assets/front/img/liberi-logo-150.jpg"></a></p>';
                        $content .= '<p>Hello ' . ucfirst($sellerUser->uname) . ', </p>';
                        $content .= '<p>'. ucfirst($_SESSION['loggedUser']->uname) . ' wants to trade or chat with you.</p>';
                        $content .= '<p> Please <a href="https://liberi.exchange/user/login"><b>Login and Start Chatting</a></b>.</p>';
                        $content .= '<br>';
                        $this->email->from('chat@liberi.exchange', 'LiberiExchange');
                        $this->email->to($sellerUser->email);
                        $this->email->set_mailtype('html');
                        $this->email->set_newline('\r\n');
                        $this->email->subject('Somebody want to trade or chat with you.');
                        $this->email->message($content);
                        $this->email->send();
                    }

                    redirect('user/chat/' . $user_id);
                }

            } else {
                redirect('home');
            }
        } else {

            redirect('user/login');
        }
    }

    public function sellOrder()
    {
        if ($this->session->userdata('loggedUser')) {

            if ($this->input->post()) {

                $data = array(

                    'crypto'    =>  $this->input->post('crypto'),
                    'fiat'    =>  $this->input->post('fiat'),
                    'seller_id'    =>  $this->input->post('seller_id'),
                    'crypto_value'    =>  $this->input->post('crypto_value'),
                    'fiat_value'    =>  $this->input->post('fiat_value'),
                    'buyer_id'    =>  $this->input->post('buyer_id'),
                    'type'    =>  'sell'
                );

                $this->db->insert('orders', $data);
                $user_id = $this->db->insert_id();

                if ($user_id) {

                    $where = ['id' => $this->input->post('buyer_id')];
                    $sellerUser = $this->usermodel->getuser("users", $where);

                    if($sellerUser->login_status == '0') {

                        $this->load->library('email');
                        $content = '<p><a href="' . base_url() . '"><img class="img-fluid" src="' . base_url() . 'assets/front/img/liberi-logo-150.jpg"></a></p>';
                        $content .= '<p>Hello ' . ucfirst($sellerUser->uname) . ', </p>';
                        $content .= '<p>'. ucfirst($_SESSION['loggedUser']->uname) . ' wants to trade or chat with you.</p>';
                        $content .= '<p> Please <a href="https://liberi.exchange/user/login"><b>Login and Start Chatting</a></b>.</p>';
                        $content .= '<br>';
                        $this->email->from('chat@liberi.exchange', 'LiberiExchange');
                        $this->email->to($sellerUser->email);
                        $this->email->set_mailtype('html');
                        $this->email->set_newline('\r\n');
                        $this->email->subject('Somebody want to trade or chat with you.');
                        $this->email->message($content);
                        $this->email->send();

                    }

                    redirect('user/sellChat/' . $user_id);
                }

            } else {
                redirect('home');
            }
        } else {

            redirect('user/login');
        }
    }

    public function orderrequests()
    {
        if ($this->session->userdata('loggedUser')) {
            $user = $this->session->userdata('loggedUser');
            $update_order_status = $this->Order->updateOrderStatus($user->id);
            $data['messages'] = $this->db->select('*')->from('orders')->where('buyer_id', $user->id)->order_by('created_at', 'DESC')->get()->result();
            $data['user_id']  = $user->id;

            $this->load->view('front/orderrequests', $data);
        } else {
            redirect('user/login');
        }
    }

    public function sellRequests()
    {
        if ($this->session->userdata('loggedUser')) {
            $user = $this->session->userdata('loggedUser');
            $update_order_status = $this->Order->updateOrderStatus($user->id);
            $data['messages'] = $this->db->select('*')->from('orders')->where('seller_id', $user->id)->order_by('created_at', 'DESC')->get()->result();
            $data['user_id']  = $user->id;

            $this->load->view('front/sellrequests', $data);
        } else {
            redirect('user/login');
        }
    }

    public function chat($id)
    {
        if($this->session->userdata('loggedUser')) {
            $user = $this->session->userdata('loggedUser');
            $data['id'] = $id;
            $order  =   $this->Order->orderById($id);
            $data['seller_id'] = $order->seller_id;


            if(!$order){

                redirect('home');
            }

            if($order->seller_id == $user->id || $order->buyer_id == $user->id) {

//                $sellerUser =   $this->usermodel->getUser('users', ['id' => $order->seller_id]);
//                $this->load->library('email');
//                $content = '<p><a href="' . base_url() . '"><img class="img-fluid" src="' . base_url() . 'assets/front/img/liberi-logo-150.jpg"></a></p>';
//                $content .= '<p>Hello ' . ucfirst($sellerUser->uname) . ', ' . ucfirst($_SESSION['loggedUser']->uname) . ' wants to trade or chat with you. Please <a href="https://liberi.exchange/user/login"><b>Login and Start Chatting</a></b>.</p>';
//                $content .= '<br>';
//                $this->email->from('chat@liberi.exchange', 'LiberiExchange');
//                $this->email->to($sellerUser->email);
//                $this->email->set_mailtype('html');
//                $this->email->set_newline('\r\n');
//                $this->email->subject('Somebody wants to trade or chat with you.');
//                $this->email->message($content);
//                $this->email->send();

                $data['messages'] = $this->db->query('SELECT messages.*, users.user_image FROM messages inner join users on users.id = messages.user_id WHERE order_id="' . $id . '"')->result();
                $update     =   $this->Order->updateMsgReadStatus($id, $user->id);
               // $updat_order_status = $this->Order->updateOrderStatus($id, $user->id);
                $this->load->view('front/orderchat', $data);
            } else {

                redirect('home');
            }

        } else {

            redirect('user/login');
        }
    }

    public function sellChat($id)
    {

        if($this->session->userdata('loggedUser')) {
            $user = $this->session->userdata('loggedUser');
            $data['id'] = $id;
            $order  =   $this->Order->orderById($id);

            $data['buyer_id'] = $order->buyer_id;

            if(!$order){

                redirect('home');
            }

            if($order->seller_id == $user->id || $order->buyer_id == $user->id) {
                $buyerUser = $this->usermodel->getUser('users', ['id' => $order->buyer_id]);

//                $this->load->library('email');
//                $content = '<p><a href="' . base_url() . '"><img class="img-fluid" src="' . base_url() . 'assets/front/img/liberi-logo-150.jpg"></a></p>';
//                $content .= '<p>Hello ' . ucfirst($buyerUser->uname) . ', ' . ucfirst($_SESSION['loggedUser']->uname) . ' wants to trade or chat with you. Please <a href="https://liberi.exchange/user/login"><b>Login and Start Chatting</a></b>.</p>';
//                $content .= '<br>';
//                $this->email->from('chat@liberi.exchange', 'LiberiExchange');
//                $this->email->to($buyerUser->email);
//                $this->email->set_mailtype('html');
//                $this->email->set_newline('\r\n');
//                $this->email->subject('Somebody wants to trade or chat with you.');
//                $this->email->message($content);
//                $this->email->send();

               $data['messages'] = $this->db->query('SELECT messages.*, users.user_image FROM messages inner join users on users.id = messages.user_id WHERE order_id="' . $id . '"')->result();

                $update     =   $this->Order->updateMsgReadStatus($id, $user->id);
                //$updat_order_status = $this->Order->updateOrderStatus($id, $user->id);
                $this->load->view('front/sellorderchat', $data);
            } else {

                redirect('home');
            }
        } else {
            redirect('user/login');
        }
    }

    public function chat1($orderid, $last_id)
    {
        $chat = $this->db->query('SELECT messages.*, users.user_image FROM messages inner join users on users.id = messages.user_id WHERE order_id="' . $orderid . '" AND messages.id > "' . $last_id . '"')->result();
        echo json_encode($chat);
    }

    public function checkmessage($last_id)
    {
        $chat = $this->db->query('SELECT * FROM messages WHERE is_client="1" AND id > "' . $last_id . '"')->result();
        echo json_encode(array('count' => count($chat)));
    }

    public function saveChat()
    {
        if ($this->input->post()) {

            $user = $this->session->userdata('loggedUser');
            $count_message = $_POST['count_message'];

            if($count_message <= 0) {

                $sellerUser =   $this->usermodel->getUser('users', array('id' => $_POST['to_user']));

                if($sellerUser->login_status == '0'){

                    $this->load->library('email');
                    $content = '<p><a href="' . base_url() . '"><img class="img-fluid" src="' . base_url() . 'assets/front/img/liberi-logo-150.jpg"></a></p>';
                    $content .= '<p>Hello ' . ucfirst($sellerUser->uname) . ', </p>';
                    $content .= '<p>'. ucfirst($user->uname) . ' wants to trade or chat with you.</p>';
                    $content .= '<p> Please <a href="https://liberi.exchange/user/login"><b>Login and Start Chatting</a></b>.</p>';
                    $content .= '<br>';
                    $this->email->from('chat@liberi.exchange', 'LiberiExchange');
                    $this->email->to($sellerUser->email);
                    $this->email->set_mailtype('html');
                    $this->email->set_newline('\r\n');
                    $this->email->subject('Somebody wants to trade or chat with you.');
                    $this->email->message($content);
                    $this->email->send();
                }
            }

            $data = array(

                'order_id' => $_POST['order_id'],
                'message' => $_POST['msg'],
                'status' => 'unread',
                'date' => date("Y-m-d"),
                'time' => date("H:i:s"),
                'mdate' => date("Y-m-d H:i:s"),
                'is_client' => $_POST['is_client'],
                'is_admin' => $_POST['is_admin'],
                'user_id'   => $user->id,
                'from_user' =>  $user->id,
                'to_user'   =>  $_POST['to_user']
            );

            $this->db->insert('messages', $data);
            $insert_id = $this->db->insert_id();
            $data['id'] = $_POST['order_id'];

            if (!$this->input->is_ajax_request()) {

                redirect('user/chat/' . $_POST['order_id']);

            } else {

                $chat = $this->db->query('SELECT messages.*, users.user_image FROM messages inner join users on users.id = messages.user_id WHERE messages.id="' . $insert_id . '"')->row();
                echo json_encode($chat);
            }

        }

    }

    public function rating($order_id = 0, $rating_param='') {

        if(!$this->session->userdata('loggedUser')) {

           if(isset($rating_param) && $rating_param != '') {

               redirect('user/login/'.base64_encode($order_id).'/'.base64_encode($rating_param));
           }

            redirect('user/login');
        }

        $user = $this->session->userdata('loggedUser');
        $order = $this->Order->orderById($order_id);

        if(!$order) {

            redirect('home');
        }

        if($order->buyer_id != $user->id) {

            redirect('home');
        }

        $order_rating = $this->Rating->getOrderRatingByUser($user->id, $order_id);

        if($order_rating) {

            $this->session->set_flashdata('success',"<p class='alert alert-success'>You have already given the rating.!</p>");
            $data['rating_user']    =   'done';
        }

        $data['order_id']    =   $order_id;
        $this->load->view('front/rating', $data);
    }

    public function saveRating($order_id) {

        if(!$this->session->userdata('loggedUser')) {

            redirect('user/login');
        }

        $user = $this->session->userdata('loggedUser');

        if($this->input->post()) {

            $order = $this->Order->orderById($order_id);

            $count = 0;

            if($this->input->post('first_radio') == 'yes') {

                $count++;
            }

            if($this->input->post('second_radio') == 'yes') {

                $count++;
            }

            if($this->input->post('third_radio') == 'yes') {

                $count++;
            }

            if($this->input->post('fourth_radio') == 'yes') {

                $count++;
            }

            if($this->input->post('fifth_radio') == 'yes') {

                $count++;
            }

            $data = array(

                'user_id'   =>  $order->buyer_id,
                'to_rating_user'  =>  $order->seller_id,
                'order_id'  =>  $order->id,
                'rating'    =>  $count,
                'first_answer'  =>  $this->input->post('first_radio'),
                'second_answer'  =>  $this->input->post('second_radio'),
                'third_answer'  =>  $this->input->post('third_radio'),
                'fourth_answer'  =>  $this->input->post('fourth_radio'),
                'fifth_answer'  =>  $this->input->post('fifth_radio'),
                'feedback'  =>  $this->input->post('feedback')
            );

            $insert_id  =   $this->Rating->insert_rating($data);

            if($insert_id) {

                $this->session->set_flashdata('success',"<p class='alert alert-success'>Rating successfully done.!</p>");
                redirect('user/rating/'.$order_id);
            }

        }
    }

    public function logout()
    {
        if($this->session->userdata('loggedUser')) {

            $user = $this->session->userdata('loggedUser');
            $update_login_status = $this->usermodel->updateLoginStatus('0', $user->id);
        }
        $this->session->unset_userdata('loggedUser');
        redirect("home");
    }

    public function imageUpload($tag_name = '')
    {
        $this->load->library('upload');


        $files      =   $_FILES;
        $file_ext   =   pathinfo($files[$tag_name]['name'], PATHINFO_EXTENSION);
        $fileName   =   rand().time().'.'.$file_ext;

        $_FILES[$tag_name]['name']        	   =   strtolower($fileName);
        $_FILES[$tag_name]['type']        	   =   $files[$tag_name]['type'];
        $_FILES[$tag_name]['tmp_name']   	   =   $files[$tag_name]['tmp_name'];
        $_FILES[$tag_name]['error']      	   =   $files[$tag_name]['error'];
        $_FILES[$tag_name]['size']        	   =   $files[$tag_name]['size'];

        $this->upload->initialize($this->set_upload_options());

        if($this->upload->do_upload($tag_name))
        {
            $image_name 		=	str_replace(' ','_',strtolower($fileName));
            return $image_name;
        }  else {

            return false;
        }
    }

    private function set_upload_options()
    {
        //upload an image options
        $config = array();
        $config['upload_path']          = './uploads/users';
        $config['allowed_types']        = 'png|PNG|jpg|JPG|gif|GIF';
        $config['max_size']             = 20000;
        $config['max_width']            = 20000;
        $config['max_height']           = 20000;

        return $config;
    }

    public function delOrder($orderId = 0)
    {

        if($this->session->userdata('loggedUser')) {

            if($this->Order->delOrder($orderId)) {

                $this->session->set_flashdata('success',"<p class='alert alert-success'>Successfully Deleted!</p>");
                redirect('user/orderrequests');
            }
        } else {

            redirect('user/login');
        }
    }
    public function selldelOrder($orderId = 0)
    {

        if($this->session->userdata('loggedUser')) {

            if($this->Order->delOrder($orderId)) {

                $this->session->set_flashdata('success',"<p class='alert alert-success'>Successfully Deleted!</p>");
                redirect('user/sellrequests');
            }
        } else {

            redirect('user/login');
        }
    }

    public function getUnreadRequest() {

        if($this->session->userdata('loggedUser')) {

            $user = $this->session->userdata('loggedUser');

            $unread_msg = $this->Order->getUnreadRequest($user->id);
            $unread_requests = $this->Order->getUnreadOrder($user->id);

            echo json_encode(array('unread_msg' => $unread_msg->unread_requests, 'unread_request' => $unread_requests->unread_order));die;
        }

      /*  else {

            redirect('user/login');
        }*/
    }
}
