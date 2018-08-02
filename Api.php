<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

include_once (dirname(__FILE__) . "/Report_others.php");
include_once (dirname(__FILE__) . "/Dashboard.php");
include_once (dirname(__FILE__) . "/Report_others_f_k.php");
include_once (dirname(__FILE__) . "/Report_others_m1_m7.php");
include_once (dirname(__FILE__) . "/Report_others_m8_m13.php");
include_once (dirname(__FILE__) . "/Report.php");
include_once (dirname(__FILE__) . "/APP_Report.php");

class Api {
     
	public $Report_others, $Report_others_f_k, $Report_others_m1_m7, $Report_others_m8_m13, $Report, $Dashboard, $APP_Report;
	
	public function __construct() {
		$this->Report_others = new Report_others('Yes');
		$this->Report_others_f_k = new Report_others_f_k('No', clone $this->Report_others);
		$this->Report_others_m1_m7 = new Report_others_m1_m7('No', clone $this->Report_others);
		$this->Report_others_m8_m13 = new Report_others_m8_m13('No', clone $this->Report_others);
		$this->Report = new Report('No', clone $this->Report_others);
		$this->Dashboard = new Dashboard('No', clone $this->Report_others);
		$this->APP_Report = new APP_Report('No', clone $this->Report_others);
		
	 }
	/*==================================================
	* Login api code
	*
	====================================================*/
	public function headers(){
        header_remove();
	header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // JSONs are by default dynamic data
        header('Content-type: application/json');
    }
    
	public function login(){
		$this->headers();
		$method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'POST';
		
		switch($method){
			case 'GET':
				$user 	  = isset($_GET['user']) ? $_GET['user'] : '';
				$password = isset($_GET['password']) ? $_GET['password'] : '';
				echo $this->loginData($user,$password);
			break;
			
			case 'POST':
				$data = json_decode(file_get_contents('php://input'), true);
				$user = '';
				$password = '';
				if(array_key_exists('user', $data)){
					$user 	  = $data['user'];
				}
				if(array_key_exists('password', $data)){
					$password 	  = $data['password'];
				}
				
				echo $this->loginData($user,$password);
			break;
			
			/*case 'PUT':
				$data = json_decode(file_get_contents('php://input'), true);
				$user = '';
				$password = '';
				if(array_key_exists('user', $data)){
					$user 	  = $data['user'];
				}
				if(array_key_exists('password', $data)){
					$password 	  = $data['password'];
				}	
				$this->loginData($user,$password);	
			break;
			
			case 'DELETE':
				$data = json_decode(file_get_contents('php://input'), true);
				$user = '';
				$password = '';
				if(array_key_exists('user', $data)){
					$user 	  = $data['user'];
				}
				if(array_key_exists('password', $data)){
					$password 	  = $data['password'];
				}
				$this->loginData($user,$password);	
			break;
			*/
			
			default:
				$data = json_decode(file_get_contents('php://input'), true);
				$user = '';
				$password = '';
				if(array_key_exists('user', $data)){
					$user 	  = $data['user'];
				}
				if(array_key_exists('password', $data)){
					$password 	  = $data['password'];
				}
				
				echo $this->loginData($user,$password);
			break;
			
		}
		
		
	}
	
	private function encode_str($string){
		$hex='';
		for ($i=0; $i < strlen($string); $i++){
			$hex .= dechex(ord($string[$i]));
		}
		return $hex;
	}
	
	private function decode_str($hex){
		$string='';
		for ($i=0; $i < strlen($hex)-1; $i+=2){
			$string .= chr(hexdec($hex[$i].$hex[$i+1]));
		}
		return $string;
	}
	
	private function explode_last($data){
		$text = explode('___', $data);
		return $text[sizeof($text)-1];
	}
	
	private function check_token($token){
		$token_id = $this->explode_last($this->decode_str($token));
		$text = explode('___', $this->decode_str($token));
		$token_type = $text[0];
		$userquery = $this->Report_others->db->query("SELECT `userID` FROM `us_users` WHERE `userID` = '".$token_id."' AND `loginName` = '".$token_type."' AND 1 = 1");
		if($userquery->num_rows() == 1){
			return true;
		}else{
			return false;
		}		
		
	}
	
	private function loginData($user,$password){
		$this->headers();
		if(strlen(trim($user)) > 0){
			if(strlen(trim($password)) > 0){
				
				$userquery = $this->Report_others->db->query("SELECT `userID`, `userRoleID`, `loginName`, `email`, `firstName`, `LastName`, `agencyID`, `projectID` FROM `us_users` WHERE (`loginName` = '".$user."' OR `email` = '".trim($user)."') AND `password` = '".md5(trim($password))."'");
				$result = array();
				if($userquery->num_rows() == 1){
					$data = $userquery->row();
					$result['result'] = array(
											'token_id' => $this->encode_str($data->loginName.'___'.$data->userID),
											//'role_id'  => $this->encode_str($data->userID.'___'.$data->userRoleID),
											'role_id'  => $data->userRoleID,
											'user_name' => $data->loginName,
											'user_email' => $data->email,
											'first_name' => $data->firstName,
											'last_name' => $data->LastName,
											'agency_id' => $this->encode_str($data->userID.'___'.$data->agencyID),
											//'agency_id' => $data->agencyID,
											'project_id' => $this->encode_str($data->userID.'___'.$data->projectID),
											//'project_id' => $data->projectID,
										);
					echo json_encode($result);
				}else{
					echo '{"result": "Sorry! user or password is invalid"}';
				}
			}else{
				echo '{"result": "Please enter valid password"}';
			}
		}else{
			echo '{"result": "Please enter user name/email"}';
		}
	}
	
	/*==================================================
	* Report List api code
	*
	====================================================*/
	
	public function report_list(){
		$this->headers();
		$method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'POST';
		
		switch($method){
			case 'GET':
				$type 	  = isset($_GET['role_id']) ? $_GET['role_id'] : '0';
				$token 	  = isset($_GET['token_id']) ? $_GET['token_id'] : '0';
				$this->reportListData($type,$token);
			break;
			
			case 'POST':
				$data = json_decode(file_get_contents('php://input'), true);
				$type = '0';
				$token = '0';
				if(array_key_exists('role_id', $data)){
					$type 	  = $data['role_id'];
				}
				if(array_key_exists('token_id', $data)){
					$token 	  = $data['token_id'];
				}
				
				$this->reportListData($type,$token);
			break;
			
			default:
				$data = json_decode(file_get_contents('php://input'), true);
				$type = '';
				$token = '';
				if(array_key_exists('role_id', $data)){
					$type 	  = $data['role_id'];
				}
				if(array_key_exists('token_id', $data)){
					$token 	  = $data['token_id'];
				}
				$this->reportListData($type,$token);
			break;
			
		}
	}
	
	private function reportListData($type1,$token){
		$this->headers();
		$checkToken = $this->check_token($token);
		//$type = $this->explode_last($this->decode_str($type1));
		$type = $type1;
		
		$reportList = $this->list_of_report();
		$result = array();
		$data = array();
		$sub = array();
		if($type > 0 AND $checkToken == true){
		
			foreach($reportList AS $key=>$list){
				if($type == 1 OR $type == 2){
					// Super report
					$reportType = array('ajax' => 'Portfolio Report', 'srs' => 'Sectors Report', 'prs' => 'Projects Report', 'ars' => 'Agencies Report');		
					$default = 'ajax'; 
				}else if($type == 3){
					// Ageency report	
					$reportType = array('ars' => 'Agencies Report');			
					$default = 'ars';
				}else if($type == 4){
					// Super report
					$reportType = array('prs' => 'Projects Report', 'ars' => 'Agencies Report');			
					$default = 'prs';
				}else{
					$reportType = array();
					$default = '';
				}
				
				//$data[$key] = array('name' => $list['name'], 'step' => $reportType);
				$data[$key] = array('name' => $list['name'], 'default' => $default, 'filter' => $this->report_filter($key));
				//$data[$key] = array('name' => $list['name'], 'default' => $default);
				
			}
			
			$otherReport = $this->list_of_others_report();
			foreach($otherReport AS $keyO=>$listO){
				$default = 'ajax'; 
				if($keyO == 'pp' OR $keyO == 'report_r'){
					if($type == 3 AND $keyO == 'pp'){
						$data[$keyO] = array('name' => $listO['name'], 'default' => $default, 'filter' => $this->report_filter($keyO));
					}else if($type != 3 AND $keyO == 'report_r'){
						$data[$keyO] = array('name' => $listO['name'], 'default' => $default, 'filter' => $this->report_filter($keyO));
					}
				}else{
					$data[$keyO] = array('name' => $listO['name'], 'default' => $default, 'filter' => $this->report_filter($keyO));
				}
			}
			
			
			$result['result'] = $data;
			echo json_encode($result);
		}else{
			$result['result'] = 'Sorry invalid token id';
			echo json_encode($result);
		}
	}
	
	private function list_of_report(){
		return array(
					/*'report_a' => array('name' => 'Report (A) : Basic Contract Information from Procurement Plan - By Procurement and Review Type', 'url' => 'report_others/report_title_a', 'type' => 'step'),
					'report_b'  => array('name' => 'Report (B): Basic Contract Information from Procurement Plan - By Procurement Method', 'url' => 'report_others/report_title_b', 'type' => 'step'),
					*/
					'report_c'  => array('name' => 'Report (C): Contract Implementation Status', 'url' => 'c_report', 'type' => 'step'),
					/*'report_d'  => array('name' => 'Report (D): Competitive Bidding Information ', 'url' => 'report_others/report_title_d', 'type' => 'step'),
					'report_e'  => array('name' => 'Report (E): Procurement Processing Time ', 'url' => 'report_others/report_title_e', 'type' => 'step'),
					'report_f'  => array('name' => 'Report (F1): Procurement Efficiency Information', 'url' => 'report_others_f_k/report_title_f', 'type' => 'step'),
					'report_g'  => array('name' => 'Report (G): Procurement Compliance Information', 'url' => 'report_others_f_k/report_title_g', 'type' => 'step'),
					'report_h'  => array('name' => 'Report (H): Contract Management Information ', 'url' => 'report_others_f_k/report_title_h', 'type' => 'step'),
					'report_i'  => array('name' => 'Report (I): Inverse Quality Information', 'url' => 'report_others_f_k/report_title_i', 'type' => 'step'),
					'report_j'  => array('name' => 'Report (J): Post Review Contracts Information ', 'url' => 'report_others_f_k/report_title_j', 'type' => 'step'),
					'report_k'  => array('name' => 'Report (K): Activity Delay and Causes of Delay ', 'url' => 'report_others_f_k/report_title_k', 'type' => 'step'),
					*/);
	}
	
	private function list_of_others_report(){
		return array(
					/*
					'report_n'  => array('name' => 'Report (N): List of Contracts of Incomplete Data', 'url' => 'report_others_m8_m13/report_title_n', 'type' => 'none'),
					*/
					'report_o'  => array('name' => 'Report (O): Stage-wise Average Procurement Processing Time from Receipt of DBD till Contract Signing ', 'url' => 'report_o', 'type' => 'none'),
					/*'report_p'  => array('name' => 'Report (P): Procurement Activity Monitoring Sheet', 'url' => 'report_others_m8_m13/report_title_p', 'type' => 'none'),
					'report_q'  => array('name' => 'Report (Q): Details of Incomplete Contracts', 'url' => 'report_others_m8_m13/report_title_q', 'type' => 'none'),
					'report_r'  => array('name' => 'Report (R): Contractor\'s Performance Status ', 'url' => 'report_others_m8_m13/report_title_r', 'type' => 'none'),
					*/
					'dashborad' => array('name' => 'Report : Statistics of Key Procurement Performance Parameter', 'url' => 'dashborad', 'type' => 'none'),
					'graph_b'  	=> array('name' => 'Graphical Report - Average Procurement Processing Time', 'url' => 'graph_b_report', 'type' => 'none'),
					'pams'  	=> array('name' => 'Procurement Activity Monitoring Sheet', 'url' => 'procurement_activity_monitoring_sheet', 'type' => 'none'),
					'pp'  	   => array('name' => 'Procurement Plan', 'url' => 'annual_procurement_plan', 'type' => 'none'),
					);
	}
	
	public function report_filter($report = ''){
		$sectorList = $this->Report_others->general_model->getOptionsList('ps_sector', 'sectorName', 'sectorID');
		$type = $this->Report_others->general_model->getOptionsList('ps_procurementtype', 'ptName', 'procurementTypeID');
        $method = $this->Report_others->general_model->getOptionsList('ps_procurementmethod', 'pmName', 'procurementMethodID');
        $precedure = $this->Report_others->general_model->getOptionsList('ps_biddingprocedure', 'bpName', 'bpName');
			
		//print_r($sectorList);
		if(strlen(trim($report)) > 0){
			if($report == 'report_a' OR $report == 'report_b' OR $report == 'report_q'){
				return array('data' => 
									array('select' => array(
														'display' => 'Currency', 'name' => 'convertCost', 'id' => 'convertCost', 'type' => 'select', 'option' => array(
																																										'dolar' => 'Million US$',
																																										'bdt' => 'Million BDT'
																																									)
														),
										 'input_1' => array(
														'display' => 'From Date', 'name' => 'from_date', 'id' => 'from_date', 'type' => 'date', 'value' => 'current date'
														), 
										 'input_2' => array(
														'display' => 'To Date', 'name' => 'to_date', 'id' => 'to_date', 'type' => 'date', 'value' => 'current date'
														),
									),
									
							);
			}else if($report == 'report_c' OR $report == 'report_h'){
				return array('data' => 
									array('select' => array(
														'display' => 'Currency', 'name' => 'convertCost', 'id' => 'convertCost', 'type' => 'select', 'option' => array(
																																										'dolar' => 'Million US$',
																																										'bdt' => 'Million BDT'
																																									)
														),
										  'input_1' => array(
														'display' => 'From Date', 'name' => 'from_date', 'id' => 'from_date', 'type' => 'date', 'value' => 'current date'
														), 
										  'input_2' => array(
														'display' => 'To Date', 'name' => 'to_date', 'id' => 'to_date', 'type' => 'date', 'value' => 'current date'
														),
										  'input_3' => array(
														'display' => 'From [Million USD]', 'name' => 'from_amount', 'id' => 'from_amount', 'type' => 'number', 'value' => '0'
														),
										  'input_4' => array(
														'display' => 'To [Million USD]', 'name' => 'to_amount', 'id' => 'to_amount', 'type' => 'number', 'value' => '0'
														),
									),
									
							);
			}else if($report == 'report_d' OR $report == 'report_g' OR $report == 'report_i' OR $report == 'report_j' OR $report == 'report_p' OR $report == 'report_n'){
				return array('data' => 
									array('input_1' => array(
														'display' => 'From Date', 'name' => 'from_date', 'id' => 'from_date', 'type' => 'date', 'value' => 'current date'
														), 
										  'input_2' => array(
														'display' => 'To Date', 'name' => 'to_date', 'id' => 'to_date', 'type' => 'date', 'value' => 'current date'
														),
										
									),
									
							);
			}else if($report == 'report_e' OR $report == 'report_f' OR $report == 'report_k'){
				return array('data' => 
									array('input_1' => array(
														'display' => 'From Date', 'name' => 'from_date', 'id' => 'from_date', 'type' => 'date', 'value' => 'current date'
														), 
										  'input_2' => array(
														'display' => 'To Date', 'name' => 'to_date', 'id' => 'to_date', 'type' => 'date', 'value' => 'current date'
														),
										  'input_3' => array(
														'display' => 'From [Million USD]', 'name' => 'from_amount', 'id' => 'from_amount', 'type' => 'number', 'value' => '0'
														),
										  'input_4' => array(
														'display' => 'To [Million USD]', 'name' => 'to_amount', 'id' => 'to_amount', 'type' => 'number', 'value' => '0'
														),
									),
									
							);
			}else if($report == 'report_r'){
				return array('data' => 
									array('select' => array(
														'display' => 'Sectors', 'name' => 'sectorID', 'id' => 'sectorID', 'type' => 'select', 'option' => $sectorList
														),
										  'input_1' => array(
														'display' => 'From Year', 'name' => 'years', 'id' => 'years', 'type' => 'number', 'value' => '0'
														), 
										  'input_2' => array(
														'display' => 'To Date', 'name' => 'to_date', 'id' => 'to_date', 'type' => 'date', 'value' => 'current date'
														),
										  
									),
									
							);
			}else if($report == 'report_o'){
				return array('data' => 
									array('input_1' => array(
														'display' => 'From Date', 'name' => 'from_date', 'id' => 'from_date', 'type' => 'date', 'value' => 'current date'
														), 
										  'input_2' => array(
														'display' => 'To Date', 'name' => 'to_date', 'id' => 'to_date', 'type' => 'date', 'value' => 'current date'
														),
										  'select' => array(
														'display' => 'Bidding Procedure', 'name' => 'BCI_14_BIDDING_PROC', 'id' => 'BCI_14_BIDDING_PROC', 'type' => 'select', 'option' => $precedure
														),
										  
									),
									
							);
			}else if($report == 'pams'){
				return array('data' => 
									array('select_1' => array(
														'display' => 'Procurement Type', 'name' => 'procurementTypeID', 'id' => 'procurementTypeID', 'type' => 'select', 'option' => $type
														),
										  'select_2' => array(
														'display' => 'Procurement Method', 'name' => 'procurementMethodID', 'id' => 'procurementMethodID', 'type' => 'select', 'option' => $method
														),
										  'select_3' => array(
														'display' => 'Bidding Procedure', 'name' => 'BCI_14_BIDDING_PROC', 'id' => 'BCI_14_BIDDING_PROC', 'type' => 'select', 'option' => $precedure
														),
									),
									
							);
			}else if($report == 'pp'){
				return array('data' => 
									array('select_1' => array(
														'display' => 'Procurement Type', 'name' => 'procurementTypeID', 'id' => 'procurementTypeID', 'type' => 'select', 'option' => $type
														),
										 'input_1' => array(
														'display' => 'From Date', 'name' => 'from_date', 'id' => 'from_date', 'type' => 'date', 'value' => 'current date'
														), 
										  'input_2' => array(
														'display' => 'To Date', 'name' => 'to_date', 'id' => 'to_date', 'type' => 'date', 'value' => 'current date'
														),
										  
									),
									
							);
			}else if($report == 'graph_b'){
				return array('data' => 
									array('select_2' => array(
														'display' => 'Procurement Method', 'name' => 'procurementMethodID', 'id' => 'procurementMethodID', 'type' => 'select', 'option' => $method
														),
										  'input_3' => array(
														'display' => 'From [Million USD]', 'name' => 'from_amount', 'id' => 'from_amount', 'type' => 'number', 'value' => '0'
														),
										  'input_4' => array(
														'display' => 'To [Million USD]', 'name' => 'to_amount', 'id' => 'to_amount', 'type' => 'number', 'value' => '0'
														),
									),
									
							);
			}else{
				return array('data' => array());
			}
		}
	}

	public function report_view(){
		$this->headers();
		$method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'POST';
		
		switch($method){
			case 'GET':
				$report 	  = isset($_GET['report']) ? $_GET['report'] : '';
				$step 	  = isset($_GET['step']) ? $_GET['step'] : '';
				$token 	  = isset($_GET['token_id']) ? $_GET['token_id'] : '0';
				echo $this->report_result_Data($report,$step,$token);
			break;
			
			case 'POST':
				$data = json_decode(file_get_contents('php://input'), true);
				$type = '0';
				$token = '0';
				if(array_key_exists('report', $data)){
					$report 	  = $data['report'];
				}
				if(array_key_exists('step', $data)){
					$step 	  = $data['step'];
				}
				if(array_key_exists('token_id', $data)){
					$token 	  = $data['token_id'];
				}
				echo $this->report_result_Data($report,$step,$token);
			break;
			
			default:
				$data = json_decode(file_get_contents('php://input'), true);
				$type = '0';
				$token = '0';
				if(array_key_exists('report', $data)){
					$report 	  = $data['report'];
				}
				if(array_key_exists('step', $data)){
					$step 	  = $data['step'];
				}
				if(array_key_exists('token_id', $data)){
					$token 	  = $data['token_id'];
				}
				echo $this->report_result_Data($report,$step,$token);
			break;
			
		}
	}
	
	private function report_result_Data($report,$step,$token){
		$this->headers();
		$checkToken = $this->check_token($token);
		
		$reportListPro = $this->list_of_report();
		$reportList = $this->list_of_others_report();
		$result = array();
		$data = array();
		$sub = array();
		if($checkToken == true){
			
				if(array_key_exists($report, $reportList) ){
					$reportArray = $reportList[$report];					
					if(array_key_exists('url', $reportArray) ){
						$mthod = $reportArray['url'];
						$result['result'] = $this->$mthod($step);
                        echo json_encode($result); 
					}
				}else if(array_key_exists($report, $reportListPro) ){
					$reportArray = $reportListPro[$report];					
					if(array_key_exists('url', $reportArray) ){
						$mthod = $reportArray['url'];
						$result['result'] = $this->$mthod($step);
                        echo json_encode($result); 
					}
				}else{
					$result['result'] = 'Sorry report invalid..';
                    echo json_encode($result); 
				}
		}else{
			$result['result'] = 'Sorry invalid token id';
			echo json_encode($result);
		}
	}
	
	private function dashborad($step){		
		$project 	  = isset($_GET['project']) ? $_GET['project'] : '1';
		$projectID 	  = $this->explode_last($this->decode_str($project));
		
		$agency 	  = isset($_GET['agency']) ? $_GET['agency'] : '45';
		$agencyID 	  = $this->explode_last($this->decode_str($agency));
		
		$token 	  = isset($_GET['token_id']) ? $_GET['token_id'] : '0';
		$token_id = $this->explode_last($this->decode_str($token));
		$text = explode('___', $this->decode_str($token));
		$token_type = $text[0];
		$userquery = $this->Report_others->db->query("SELECT `userRoleID`, `agencyID`, `projectID` FROM `us_users` WHERE `userID` = '".$token_id."' AND 1 = 1");
		$data = $userquery->row();
		$roleID = $data->userRoleID;
		
		$checkToken = $this->check_token($token);
		
		if($roleID == 3){
			$agencyID = $data->agencyID;
			$projectID = $data->projectID;
			$reportType = 'Agency';
		}else if($roleID == 4){
			$reportType = 'Supervising';
			$agencyID = $data->agencyID;			
		}else{
			$reportType = 'Protfolio';					
		}
		
		if($agencyID == 0){
			$agencyID = 45;
		}
		if($projectID == 0){
			$projectID = 1;
		}
		if($checkToken == true){
			if($roleID == 3){
				$result = $this->Dashboard->summary_report('api', $projectID, $agencyID);
			}else{
				$type1 	  = isset($_GET['type']) ? $_GET['type'] : 'cal';
				$result = $this->Dashboard->portfolio_summary_report($type1, 'api', $projectID, $agencyID, $roleID);
			}
			return $result;
		}else{
			$result = 'Sorry invalid token id';
			return $result;
		}
	}
	
	private function annual_procurement_plan($step){
		
		$project 	  = isset($_GET['project']) ? $_GET['project'] : '1';
		$projectID 	  = $this->explode_last($this->decode_str($project));
		
		$agency 	  = isset($_GET['agency']) ? $_GET['agency'] : '45';
		$agencyID 	  = $this->explode_last($this->decode_str($agency));
		
		
		$token 	  = isset($_GET['token_id']) ? $_GET['token_id'] : '0';
		$token_id = $this->explode_last($this->decode_str($token));
		$text = explode('___', $this->decode_str($token));
		$token_type = $text[0];
		$userquery = $this->Report_others->db->query("SELECT `userRoleID`, `agencyID`, `projectID` FROM `us_users` WHERE `userID` = '".$token_id."' AND 1 = 1");
		$data = $userquery->row();
		$roleID = $data->userRoleID;
		
		$checkToken = $this->check_token($token);
		
		if($roleID == 3){
			$agencyID = $data->agencyID;
			$projectID = $data->projectID;
			$reportType = 'Agency';
		}else if($roleID == 4){
			$reportType = 'Supervising';
			$agencyID = $data->agencyID;
		}else{
			$reportType = 'Protfolio';
		}
		
		if($agencyID == 0){
			$agencyID = 45;
		}
		if($projectID == 0){
			$projectID = 1;
		}
		
		$from_date  = isset($_GET['from_date']) ? $_GET['from_date']: date("d/m/Y"); 
		$to_date    = isset($_GET['to_date']) ? $_GET['to_date']: date("d/m/Y"); 
		
		$from_date2    = explode('/', $from_date);
        $from_date     = hexdec($from_date2[2].'-'.$from_date2[1].'-'.$from_date2[0]);
		
		$to_date2    = explode('/', $to_date);
        $to_date     = hexdec($to_date2[2].'-'.$to_date2[1].'-'.$to_date2[0]);
		
		$bidding    = isset($_GET['type']) ? $_GET['type']: '1';  

		if($checkToken == true){
			$result = $this->APP_Report->annual_procurement_plan_ajax($bidding, $from_date, $to_date, 'api', $projectID, $agencyID);
			
			return $result;
		}else{
			$result = 'Sorry invalid token id';
			return $result; 
		}
	}
	
	private function procurement_activity_monitoring_sheet($step){
		
		$packageID 	  = isset($_GET['package']) ? $_GET['package'] : '0';
		$packageIDLot 	  = isset($_GET['lot']) ? $_GET['lot'] : '0';
		
		
		$token 	  = isset($_GET['token_id']) ? $_GET['token_id'] : '0';
		
		$checkToken = $this->check_token($token);
		
		if($checkToken == true){
			$result = $this->Report->pams_report_view($packageID, $packageIDLot, 'api');			
			return $result;
		}else{
			$result = 'Sorry invalid token id';
			return $result;
		}
	}
	

	private function report_o($step = 'ajax') {
		
		$project 	  = isset($_GET['project']) ? $_GET['project'] : '1';
		$projectID 	  = $this->explode_last($this->decode_str($project));
		
		$agency 	  = isset($_GET['agency']) ? $_GET['agency'] : '45';
		$agencyID 	  = $this->explode_last($this->decode_str($agency));
		
		
		$token 	  = isset($_GET['token_id']) ? $_GET['token_id'] : '0';
		$token_id = $this->explode_last($this->decode_str($token));
		$text = explode('___', $this->decode_str($token));
		$token_type = $text[0];
		$userquery = $this->Report_others->db->query("SELECT `userRoleID`, `agencyID`, `projectID` FROM `us_users` WHERE `userID` = '".$token_id."' AND 1 = 1");
		$data = $userquery->row();
		$roleID = $data->userRoleID;
		
		$checkToken = $this->check_token($token);
		
		if($roleID == 3){
			$agencyID = $data->agencyID;
			$projectID = $data->projectID;
			$reportType = 'Agency';
		}else if($roleID == 4){
			$reportType = 'Supervising';
			$agencyID = $data->agencyID;
		}else{
			$reportType = 'Protfolio';
		}
		
		
		$from_date  = isset($_GET['from_date']) ? $_GET['from_date']: date("d/m/Y"); 
		$to_date    = isset($_GET['to_date']) ? $_GET['to_date']: date("d/m/Y"); 
		
		$from_date2    = explode('/', $from_date);
        $from_date     = hexdec($from_date2[2].'-'.$from_date2[1].'-'.$from_date2[0]);
		
		$to_date2    = explode('/', $to_date);
        $to_date     = hexdec($to_date2[2].'-'.$to_date2[1].'-'.$to_date2[0]);
		
		$bidding    = isset($_GET['bidding']) ? $_GET['bidding']: '1S1E';  

		if($projectID == 0){
			$projectID = 1;
		}
		
		if($agencyID == 0){
			$agencyID = 45;
		}
		if($checkToken == true){
			$result = $this->Report_others_m8_m13->report_title_o_ajax($from_date, $to_date, $bidding, 'api', $projectID, $agencyID, $reportType);
		
			return $result;
		}else{
			$result = 'Sorry invalid token id';
			return $result; 
		}
	}
	
	private function c_report($step = 'ajax'){
		
		$from_date  = isset($_GET['from_date']) ? $_GET['from_date']: date("d/m/Y"); 
		$to_date    = isset($_GET['to_date']) ? $_GET['to_date']: date("d/m/Y"); 
		
		$from_date2  = explode('/', $from_date);
        $to_date2    = explode('/', $to_date);
        if(sizeof($from_date2) > 1){
			$from_date     	= hexdec($from_date2[2].'-'.$from_date2[1].'-'.$from_date2[0]);
		}
		if(sizeof($to_date2) > 1){
			$to_date     	= hexdec($to_date2[2].'-'.$to_date2[1].'-'.$to_date2[0]);
		}	
		
		$currency    = isset($_GET['currency']) ? $_GET['currency']: 'dolar'; 
		$from_usd    = isset($_GET['from_usd']) ? $_GET['from_usd']: '0'; 
		$to_usd    	 = isset($_GET['to_usd']) ? $_GET['to_usd']: '0'; 
		
		$sector_id    	 = isset($_GET['sector']) ? $_GET['sector']: '0'; 
		$project_id    	 = isset($_GET['project']) ? $_GET['project']: '0'; 
		$agency_id    	 = isset($_GET['agency']) ? $_GET['agency']: '0'; 
		$reportType    	 = isset($_GET['report_type']) ? $_GET['report_type']: ''; 
		
		
		$token 	  = isset($_GET['token_id']) ? $_GET['token_id'] : '0';
		$token_id = $this->explode_last($this->decode_str($token));
		$text = explode('___', $this->decode_str($token));
		$token_type = $text[0];
		$userquery = $this->Report_others->db->query("SELECT `userRoleID`, `agencyID`, `projectID` FROM `us_users` WHERE `userID` = '".$token_id."' AND 1 = 1");
		$data = $userquery->row();
		$roleID = $data->userRoleID;
		
		$checkToken = $this->check_token($token);
		
		if($roleID == 3){
			$step1 = 'ars';
			$agency_id = $data->agencyID;
			$project_id = $data->projectID;
			if($reportType == ''){
				$reportType = 'Agency';			
			}
		}else if($roleID == 4){
			$step1 = 'prs';
			$agency_id = $data->agencyID;
			if($reportType == ''){
				$reportType = 'Supervising';	
			}
			
		}else{
			if($reportType == ''){
				$reportType = 'Agency';
			}
			$step1 = 'ajax';
		}
		
		if($sector_id == 0){
			$step = $step1;
			if($step == 'Agency'){
				$sectorFind = $this->Report_others->db->query("SELECT `sectorID` FROM `ps_project` WHERE `projectID` = '".$project_id."' AND 1 = 1");
				$sectorArray = $sectorFind->row();
				//$sector_id = $sectorArray->sectorID;
			}
		}
		if($checkToken == true){		
			if($step == 'ajax'){
				$result 		= $this->Report_others->report_title_c_ajax($from_date, $to_date, $currency, $from_usd, $to_usd, 'api');		
			}else if($step == 'srs'){
				$result 		= $this->Report_others->report_title_c_srs($sector_id, $from_date, $to_date, $currency, $from_usd, $to_usd, 'api');
			}else if($step == 'prs'){
				$result 		= $this->Report_others->report_title_c_prs($sector_id,$project_id,$from_date, $to_date, $currency, $from_usd, $to_usd, $reportType, 'api', $agency_id);
			}else if($step == 'ars'){
				$result 		= $this->Report_others->report_title_c_ars($sector_id,$project_id,$agency_id, $from_date, $to_date, $currency, $from_usd, $to_usd, $reportType, 'api');
			}
			return $result;
		}else{
			$result = 'Sorry invalid token id';
			return $result; 
		}
		
	}
	
	private function graph_b_report($step = 'ajax'){
		
        $method 	  = isset($_GET['method']) ? $_GET['method'] : 'ICB';
		$from_usd 	  = isset($_GET['from_usd']) ? $_GET['from_usd'] : '0';
		$to_usd 	   = isset($_GET['to_usd']) ? $_GET['to_usd'] : '0';
		$project 	  = isset($_GET['project']) ? $_GET['project'] : '0';
		$projectID 	  = $this->explode_last($this->decode_str($project));
		$agency 	  = isset($_GET['agency']) ? $_GET['agency'] : '0';
		$agencyID 	  = $this->explode_last($this->decode_str($agency));
        $sector 	  = isset($_GET['sector']) ? $_GET['sector'] : '0';
		$sectorID 	  = $this->explode_last($this->decode_str($sector));
        
		$reportType    	 = isset($_GET['report_type']) ? $_GET['report_type']: ''; 
		
		
		$token 	  = isset($_GET['token_id']) ? $_GET['token_id'] : '0';
		$token_id = $this->explode_last($this->decode_str($token));
		$text = explode('___', $this->decode_str($token));
		$token_type = $text[0];
		$userquery = $this->Report_others->db->query("SELECT `userRoleID`, `agencyID`, `projectID` FROM `us_users` WHERE `userID` = '".$token_id."' AND 1 = 1");
		$data = $userquery->row();
		$roleID = $data->userRoleID;
		
		$checkToken = $this->check_token($token);
		
		if($roleID == 3){
			$agencyID = $data->agencyID;
			$projectID = $data->projectID;
			if($reportType == ''){
				$reportType = 'Agency';			
			}
		}else if($roleID == 4){
			$agencyID = $data->agencyID;
			if($reportType == ''){
				$reportType = 'Supervising';	
			}
			
		}else{
			if($reportType == ''){
				$reportType = 'Agency';
			}
			
		}		
		
        if($sectorID == 0){
			$sectorID = 6;
		}
		
		if($projectID == 0){
			$projectID = 1;
		}
		
		if($agencyID == 0){
			$agencyID = 45;
		}
        if($checkToken == true){
			$result = $this->Report->graphical_report_b_ajax($method, $from_usd, $to_usd, 'api', $projectID, $agencyID, $sectorID, $reportType); 
			return $result;
		}else{
			$result = 'Sorry invalid token id';
			return $result;
		}
	}
	
	
	public function search_package(){
		$this->headers();
		$project 	  = isset($_GET['project']) ? $_GET['project'] : '1';
		$projectID 	  = $this->explode_last($this->decode_str($project));
		
		$agency 	  = isset($_GET['agency']) ? $_GET['agency'] : '45';
		$agencyID 	  = $this->explode_last($this->decode_str($agency));
		
		$getProcurementTypeID 	  = isset($_GET['type']) ? $_GET['type'] : '0';
		$getProcurementMethodID   = isset($_GET['method']) ? $_GET['method'] : '0';
		$getBiddingProcedureID 	  = isset($_GET['bidding']) ? $_GET['bidding'] : '0';
		
		$token 	  = isset($_GET['token_id']) ? $_GET['token_id'] : '0';
		$token_id = $this->explode_last($this->decode_str($token));
		$text = explode('___', $this->decode_str($token));
		$token_type = $text[0];
		$userquery = $this->Report_others->db->query("SELECT `userRoleID`, `agencyID`, `projectID` FROM `us_users` WHERE `userID` = '".$token_id."' AND 1 = 1");
		$data = $userquery->row();
		$roleID = $data->userRoleID;
		
		$checkToken = $this->check_token($token);
		
		if($roleID == 3){
			$agencyID = $data->agencyID;
			$projectID = $data->projectID;
			$reportType = 'Agency';
		}else if($roleID == 4){
			$reportType = 'Supervising';
			$agencyID = $data->agencyID;			
		}else{
			$reportType = 'Protfolio';					
		}
		
		if($agencyID == 0){
			$agencyID = 45;
		}
		if($projectID == 0){
			$projectID = 1;
		}
		
		$wherePackageLotInfo = '';
            
		$join = '';
		if($getProcurementTypeID > 0){
			$wherePackageLotInfo .= " AND pac.procurementTypeID = '". $getProcurementTypeID ."' ";
			$join .= " INNER JOIN ps_procurementtype AS type ON pac.procurementTypeID = type.procurementTypeID ";
		}
		
		if($getProcurementMethodID > 0){
			$wherePackageLotInfo .= " AND pac.procurementMethodID = '". $getProcurementMethodID."' ";
			$join .= " INNER JOIN ps_procurementmethod AS method ON pac.procurementMethodID = method.procurementMethodID ";
		}
		
		if($getBiddingProcedureID > 0){
			$wherePackageLotInfo .= " AND pac.biddingProcedureID = '". $getBiddingProcedureID."' ";
			$join .= " INNER JOIN ps_biddingprocedure AS bidding ON pac.biddingProcedureID = bidding.biddingProcedureID ";
		}
		
		$where = $this->Report_others->db->query("SELECT pi_29, packageID FROM pamt_package AS pac $join WHERE pac.projectID = $projectID AND pac.agencyID = $agencyID $wherePackageLotInfo ");
		$resultl = $where->result_array();
		$dataList = array();
		foreach($resultl AS $value){
			$dataList[] = array(''.$value['packageID'].'', ''.$value['pi_29'].'');
		}
		if($checkToken == true){
			$result['result'] = $dataList;
		}else{
			$result['result'] = 'Sorry invalid token id';
		}
        echo json_encode($result); 
	}

	public function search_package_lot(){
		$this->headers();
		$packageID 	  = isset($_GET['package']) ? $_GET['package'] : '0';
		$token 	  = isset($_GET['token_id']) ? $_GET['token_id'] : '0';
		$checkToken = $this->check_token($token);
		
		if($checkToken == true){
			$where = $this->Report_others->db->query("SELECT pi_32, packagelotID FROM pamt_packagelot AS pac WHERE pac.packageID = $packageID");
			$resultl = $where->result_array();
			$dataList = array();
			foreach($resultl AS $value){
				$dataList[] = array(''.$value['packagelotID'].'', ''.$value['pi_32'].'');
			}		
			$result['result'] = $dataList;
		}else{
			$result['result'] = 'Sorry invalid token id';
		}
		echo json_encode($result);  
	}

	
}	
