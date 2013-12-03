<?php

class Board extends CI_Controller {
     
    function __construct() {
        // Call the Controller constructor
        parent::__construct();
        session_start();
    }

    public function _remap($method, $params = array()) {
        // enforce access control to protected functions

        if (!isset($_SESSION['user']))
            redirect('account/loginForm', 'refresh'); //Then we redirect to the index page again
         
        return call_user_func_array(array($this, $method), $params);
    }
    
    function getGameBoard() {
        $user = $_SESSION['user'];
         
        $this->load->model('user_model');
        $this->load->model('invite_model');
        $this->load->model('match_model');
        
        $user = $this->user_model->get($user->login);
        
        if ($user->user_status_id == User::PLAYING) {
            $match = $this->match_model->get($user->match_id);
            if ($match->user1_id == $user->id) {
                $otherUser = $this->user_model->getFromId($match->user2_id);
                $data["player_no"] = Board_model::P1;
            } else {
                $otherUser = $this->user_model->getFromId($match->user1_id);
                $data["player_no"] = Board_model::P2;
            }
            $data["chip_color"] = ($data["player_no"] == 0 ? "red" : "black");
            $data["match_status"] = $match->match_status_id;
            $data["board"] = unserialize(base64_decode($match->board_state));
        } else {
            // Show an empty dummy board.
            $data["match_status"] = Match::ACTIVE;
            $data["board"] = new Board_model(Match::BOARD_WIDTH);
        }
        
        $this->load->view("match/_board_view", $data);
    }


    function index() {
        $data["main"] = "match/board";
        $data["title"] = "Playing Connect4!";
        $data["script"] = "match/_js";
        $data["data"] = $data;
        
        $user = $_SESSION['user'];
         
        $this->load->model('user_model');
        $this->load->model('invite_model');
        $this->load->model('match_model');
         
        $user = $this->user_model->get($user->login);

        $invite = $this->invite_model->get($user->invite_id);
        
        //TODO: Make this page redirect to matchmaking page if user is not playing.
         
        if ($user->user_status_id == User::WAITING) {
            $invite = $this->invite_model->get($user->invite_id);
            $otherUser = $this->user_model->getFromId($invite->user2_id);
            $data["match_status"] = Match::ACTIVE;
            $data["chip_color"] = "red";
        }
        else if ($user->user_status_id == User::PLAYING) {
            $match = $this->match_model->get($user->match_id);
            if ($match->user1_id == $user->id) {
                $otherUser = $this->user_model->getFromId($match->user2_id);
                $data["player_no"] = Board_model::P1;
            } else {
                $otherUser = $this->user_model->getFromId($match->user1_id);
                $data["player_no"] = Board_model::P2;
            }
            $data["chip_color"] = ($data["player_no"] == 0 ? "red" : "black");
            $data["match_status"] = $match->match_status_id;
            $data["board"] = unserialize(base64_decode($match->board_state));
        }
         
        $data['user']=$user;
        $data['otherUser']=$otherUser;
        
         
        switch($user->user_status_id) {
            case User::PLAYING:
                $data['status'] = 'playing';
                break;
            case User::WAITING:
                $data['status'] = 'waiting';
                break;
        }

        $this->load->view("template", $data);
    }

    function postMsg() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('msg', 'Message', 'required');
         
        if ($this->form_validation->run() == TRUE) {
            $this->load->model('user_model');
            $this->load->model('match_model');

            $user = $_SESSION['user'];
             
            $user = $this->user_model->getExclusive($user->login);
            if ($user->user_status_id != User::PLAYING) {
                $errormsg="Not in PLAYING state";
                goto error;
            }

            $match = $this->match_model->get($user->match_id);

            $msg = $this->input->post('msg');

            if ($match->user1_id == $user->id)  {
                $msg = $match->u1_msg == ''? $msg :  $match->u1_msg . "\n" . $msg;
                $this->match_model->updateMsgU1($match->id, $msg);
            }
            else {
                $msg = $match->u2_msg == ''? $msg :  $match->u2_msg . "\n" . $msg;
                $this->match_model->updateMsgU2($match->id, $msg);
            }
             
            echo json_encode(array('status'=>'success'));
             
            return;
        }

        $errormsg="Missing argument";
         
        error:
        echo json_encode(array('status'=>'failure','message'=>$errormsg));
    }
    
    /**
     * Encode whether there is an update available for the user in the session.
     */
    function getUpdateStatus() {
        $user = $_SESSION['user'];
        
        $this->load->model('user_model');
        $this->load->model('match_model');
         
        $user = $this->user_model->get($user->login);
        
        if ($user->user_status_id != User::PLAYING) {
            return;
        }
    }

    function getMsg() {
        $this->load->model('user_model');
        $this->load->model('match_model');

        $user = $_SESSION['user'];

        $user = $this->user_model->get($user->login);
        if ($user->user_status_id != User::PLAYING) {
            $errormsg="Not in PLAYING state";
            goto error;
        }
        // start transactional mode
        $this->db->trans_begin();

        $match = $this->match_model->getExclusive($user->match_id);

        if ($match->user1_id == $user->id) {
            $msg = $match->u2_msg;
            $this->match_model->updateMsgU2($match->id,"");
        }
        else {
            $msg = $match->u1_msg;
            $this->match_model->updateMsgU1($match->id,"");
        }

        if ($this->db->trans_status() === FALSE) {
            $errormsg = "Transaction error";
            goto transactionerror;
        }
         
        // if all went well commit changes
        $this->db->trans_commit();
         
        echo json_encode(array('status'=>'success','message'=>$msg));
        return;

        transactionerror:
        $this->db->trans_rollback();

        error:
        echo json_encode(array('status'=>'failure','message'=>$errormsg));
    }
    
    function dropDisk($column) {
        $this->load->model('user_model');
        $this->load->model('match_model');
        
        $user = $_SESSION['user'];
        
        $user = $this->user_model->get($user->login);
        if ($user->user_status_id != User::PLAYING) {
            $errormsg="Not in PLAYING state";
            goto error;
        }
        
        // start transactional mode
        $this->db->trans_begin();
        
        $match = $this->match_model->getExclusive($user->match_id);
        
        if (is_null($match)) {
            $errormsg = "Match does not exist.";
            goto error;
        }
        
        if ($match->user1_id == $user->id) {
            if ($match->drop_disk(Board_model::P1, $column)) {
                $msg = "P1 dropped disk into column $column.";
            }
        }
        else {
            if ($match->drop_disk(Board_model::P2, $column)) {
                $msg = "P2 dropped disk into column $column.";
            }
        }
        
//         if ($match->check_victory_state($column)) {
//             // If the chip at the top of this column connects four.
//             $msg .= $user->fullName() . " has won the game!";
            
//             $win_state = ($match->user1_id == $user->id ? Match::U1WON : Match::U2WON);
//             $this->match_model->updateStatus($match->id, $win_state);
//         }
        
        $this->match_model->updateBoard($match->id, $match->board_state);
        $this->match_model->updateMsgU1($match->id, $msg);
        $this->match_model->updateMsgU2($match->id, $msg);
        
        if ($this->db->trans_status() === FALSE) {
            $errormsg = "Transaction error";
            goto transactionerror;
        }
         
        $this->db->trans_commit();
         
        echo json_encode(array('status'=>'success','message'=>$msg));
        return;
        
        transactionerror:
        $this->db->trans_rollback();
        
        error:
        echo json_encode(array('status'=>'failure','message'=>$errormsg));
    }
    
    function leaveGame() {
        redirect("arcade/declineInvitation");
    }

}

