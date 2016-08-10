<?php
/*
<IfModule mod_rewrite.c>
    RewriteEngine On

	RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-s
	RewriteRule ^(.*)$ api.php?x=$1 [QSA,NC,L]

	RewriteCond %{REQUEST_FILENAME} -d
	RewriteRule ^(.*)$ api.php [QSA,NC,L]

	RewriteCond %{REQUEST_FILENAME} -s
	RewriteRule ^(.*)$ api.php [QSA,NC,L]	
</IfModule>
*/
	//http://www.ncut.edu.tw/ncut/phone/services/testgetphones
  session_start();

  ini_set('error_reporting', E_ALL | E_STRICT);
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);

			// 權限管理待加入  

//angularcode_customers   替換成 crud_customers

require_once("Rest.inc.php");
	



function logfile($logfileName,$msgstr)    {
	$fp = fopen($logfileName, "a+") ;  //  debug 的方法 改成物件化 用來除錯 
	// $cr  換行 mac linux window 
	// $fd  路徑分格字元
	// 本機與遠端伺服器
	//$content = stripslashes($content) ; 
	fwrite($fp, $msgstr) ; 
	fclose($fp) ; 
}

	class API extends REST {
	
		public $data = "";
		const DB_SERVER = "localhost:33060";
		const DB_USER = "root";
		const DB_PASSWORD = "root";
		const DB = "ncut";

		private $db = NULL;
		private $mysqli = NULL;


		public function __construct(){
			parent::__construct();				// Init parent contructor
						// Initiate Database connection
			//$this->dbConnect();		// 不是毎個請求 都要先 連結 DB 吧 
		}
		
		/*
		 *  Connect to Database
		*/
		private function dbConnect(){
			$this->mysqli = new mysqli(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD, self::DB);
			$this->mysqli->query('SET NAMES utf8');
		}
		
		/*
		 * Dynmically call the method based on the query string
		   主要處理 /service/api.php 解析 指令 URL 
		 */
		public function processApi(){
	    	logfile("restfill.log" , $_REQUEST['x'] ."\n" ) ; // 
			$func = strtolower(trim(str_replace("/","",$_REQUEST['x'])));
			if((int)method_exists($this,$func) > 0) {

				 $this->dbConnect();		// 不是毎個請求 都要先 連結 DB 吧 
                
                $this->$func(); 
				////偵測是否 session 有效 若 否則 傳回應登入 權限才能讀取 
				// 檢視session 管理權限 是否許可 調用子函式 ::
				 // 間接呼叫 所有的 子函式 若使用者 由 rest full 呼叫中 
				//改參數時即可調用到伺服器造成資料 資安風險 !! 應加以加密處理才行  xExdXagtew
				// $error = array('status' => "access deny", "msg" => "登入 權限才能讀取 ");
			    // $this->response($this->json($error), 400);
			}else{

				$this->response($func.' Page not found',404); // If the method not exist with in this class "".
			}	
		}
//////////////////////////////////////////////////////////////////////////////////////////////
//$fucn =login     this->login()   				
  		private function login(){ // 處理登入動作 在這加php Session 確保資安 

// // 2016 06 28 allen 
// 			// 
		date_default_timezone_set('Asia/Taipei'); 
   			$today =  strftime('%Y-%m-%d  %H:%M:%S ', strtotime('now') );	
 			
 			$userlist =array("allen"=>"@llen#888" , "admin"=>"ncut#2244");

			
   if($this->get_request_method() != "POST"){
 			 $this->response('',406);
 	}

 			$user    = $this->_request['user'];		
 			$password = $this->_request['pwd']; // query Md5處理防 sql injection

 			 $logmsgss =  $today . "ip:" . $_SERVER['REMOTE_ADDR']. "  user:" . $user . "  sn:" .$password ;

 			logfile( "restfill.log" , $logmsgss ."\n" ) ; // 

 			if(!empty($user) and !empty($password)){ // 不為空白
					
					if( $userlist[$user] == $password ){
						  $result = array('status' => "loged admin", "msg" => $user );
					      $_SESSION['UserName']= $user;
					      session_write_close() ;
						  $this->response($this->json($result), 200);
					}else{
						 $result = array('status' => "Failed", "msg" => "empty userid or Password");
						  $this->response($this->json($result), 400);
					} 
                     

 			}
 			$error = array('status' => "Failed", "msg" => "Invalid userid or Password");
 			$this->response($this->json($error), 400);
  		}

		private function ddlogin(){ // 處理登入動作 在這加php Session 確保資安 
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			// 讀取變數 email, pwd
			$email = $this->_request['email'];		
			$password = $this->_request['pwd']; // query Md5處理防 sql injection
			
			if(!empty($email) and !empty($password)){
				if(filter_var($email, FILTER_VALIDATE_EMAIL)){
					// 自USERs 表單中 查詢	
					$query="SELECT uid, name, email FROM users WHERE email = '$email' AND password = '".md5($password)."' LIMIT 1";

					$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

					if($r->num_rows > 0) {
						$result = $r->fetch_assoc();	
						// 回應訊息 將結果傳回客端
						// If success everythig is good send header as "OK" and user details
						$this->response($this->json($result), 200);
						// 寫入 session login 成功 
					}
					$this->response('', 204);	// If no records "No Content" status
				}
			}
			
			$error = array('status' => "Failed", "msg" => "Invalid Email address or Password");
			$this->response($this->json($error), 400);
		}

	//customers	
		private function customers(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$query="SELECT distinct c.customerNumber, c.customerName, c.email, c.address, c.city, c.state, c.postalCode, c.country FROM crud_customers c order by c.customerNumber desc";
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

			if($r->num_rows > 0){
				$result = array();
				while($row = $r->fetch_assoc()){
					$result[] = $row;
				}
				$this->response($this->json($result), 200); // send user details
			}
			$this->response('',204);	// If no records "No Content" status
		}
//todolists
		
		private function todolists(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			//SELECT distinct 唯一的
			 
			 $query="SELECT c.nid,c.title,c.pid,c.cday,c.dday,c.uday,c.attfilename,c.typecode,c.content,c.imp,c.auth,c.mynote FROM todolist c order by c.nid desc";
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

			if($r->num_rows > 0){
				$result = array();
				while($row = $r->fetch_assoc()){
					$result[] = $row;
				}
				$this->response($this->json($result), 200); // send user details
			}
			$this->response('',204);	// If no records "No Content" status
		}


//ncut phones
/*

-- 編號 pid 
-- 名稱 name
-- 單位名稱 deptname
-- 分機 phone
//-- email email
-- 職稱 title 
-- 備註 mynote 

// R 查詢  all   
   obj.getNCUT_Phones = function(){
          return $http.get(serviceBase + "getphones" );
    }
// R 查詢 by ID
   obj.getNCUT_Phone = function(phoenID){
        return $http.get(serviceBase + "getphone?id=" + phoenID );
    }
// C 新增    
    obj.insertNCUT_Phone = function (customer) {
    return $http.post(serviceBase + 'insertNCUT_Phone', phones).then(function (results) {
        return results;
    });
  };

// U 編輯更新 
  obj.updateNCUT_Phone = function (id,customer) {
      return $http.post(serviceBase + 'updateNCUT_Phone', {id:id, dataset:phones}).then( function (status) {
          return status.data;
      });
  };
// D 刪除
  obj.deleteNCUT_Phone = function (id) {
      return $http.delete(serviceBase + 'deleteNCUT_Phone?id=' + id).then( function (status) {
          return status.data;
      });
*/		


// 分機表 
		
private function phones(){	
	/*
		 
取得 路徑 + 檔名 (要取得 /var/www/project/test.php)
echo __FILE__;
取得 檔名 (要取得 test.php)
echo basename(__FILE__);
取得 不含附檔名的檔名 (要取得 test)
echo basename(__FILE__, ‘.php’);
取得 到此目錄前的完整 PATH, 不含檔名 (要取得 /var/www/project)
echo dirname(__FILE__);
取得 到上層目錄前的完整 PATH (要取得 /var/www)
echo dirname(dirname(__FILE__));

	http://localhost/php/seed/services/phones/

		*/

	$jsonfilename =dirname( dirname(__FILE__) ). "\\data\\phone.json" ;
	//echo $jsonfilename ;
	$result =  file_get_contents($jsonfilename );
	//echo $result ;
	$this->response( $result , 200); 


}

private function testgetphones(){	
			 //http://www.ncut.edu.tw/php/seed/services/testgetphones
			//http://www.ncut.edu.tw/ncut/phone/services/testgetphones
			echo "<meta charset=\"utf-8\">testgetphones !!" ;
			$query = "SELECT * FROM `phones` "  ;
			echo $query ."<br>" ;
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
			if($r->num_rows > 0) {
					$result = array();
				while($row = $r->fetch_assoc()){
					$result[] = $row;
				}
				//$this->response($this->json($result), 200);
				print_r($result) ;
					//$this->response( $this->json($result) , 200); // send user details
			}
			
			 
		}

		private function getphones(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			
			
			$query="SELECT  *  FROM  phones "  ;
 				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
    	  if($r->num_rows > 0){
				$result = array();
				while($row = $r->fetch_assoc()){
					$result[] = $row;
				}
				$this->response($this->json($result), 200); // send user details
			}
			



			$this->response('',204);	// If no records "No Content" status
		}

		private function getphone(){	

			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}

			$id = (int)$this->_request['id'];
			if($id > 0){	
			    //logfile("restfill.log" , "getphone ? id=".$id."\n" ) ; // 	
				$query="SELECT  *  FROM phones    where pid=$id";
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				if($r->num_rows > 0) {
					$result = $r->fetch_assoc();	
					$this->response($this->json($result), 200); // send user details
					//echo $result ;
				}
			}
			$this->response('',204);	// If no records "No Content" status

		}

	
	


		private function insertNCUT_Phone(){
			logfile("restfill.log" , "insertNCUT_Phone   \n" ) ; // 	
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}

			$phones = json_decode(file_get_contents("php://input"),true);
		//	$column_names = array('customerName', 'email', 'city', 'address', 'country');
			$column_names = array( 'name', 'deptname','phone', 'title', 'mynote');
			$keys = array_keys($phones);
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){ // Check the customer received. If blank insert blank into the array.
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $phones[$desired_key];
				}
				$columns = $columns.$desired_key.',';
				$values = $values."'".$$desired_key."',";
			}
			$query = "INSERT INTO phones (".trim($columns,',').") VALUES(".trim($values,',').")";
			logfile("restfill.log" , "$query   \n" ) ; // 	
			if(!empty($phones)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "phones Created Successfully.", "data" => $phones);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	//"No Content" status
		}

/*

-- 編號 pid 
-- 名稱 name
-- 單位名稱 deptname
-- 分機 phone
//-- email email
-- 職稱 title 
-- 備註 mynote 
*/
		private function updateNCUT_Phone(){
			//serviceBase + 'updateNCUT_Phone', {id:id, dataset:phones})
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			//接收傳回值
			$phones = json_decode(file_get_contents("php://input"),true);

			$id = (int)$phones['id'];
			// $column_names = array('customerName', 'email', 'city', 'address', 'country');
			// 	$column_names = array( 'name', 'deptname','phone', 'title', 'mynote');
	//		 $keys = array_keys($phones);
		//	 $keys = array_keys($phones['pid']);
			$values = '';
			// 合成欄位名稱  及 數值
			// foreach($column_names as $desired_key){ // Check the customer received. If key does not exist, insert blank into the array.
			//    if(!in_array($desired_key, $keys)) {
			//    		$$desired_key = '';
			// 	}else{
			// 		$$desired_key = $phones['phones'][$desired_key];
			// 	}
			// 	$columns = $columns.$desired_key."='".$$desired_key."',";
			// }
			// todo 資安 clear sql injection
			 $p_name = $phones['dataset']['name'];
			 $p_phone = $phones['dataset']['phone'];
			 $p_title = $phones['dataset']['title'];
			 $p_deptname = $phones['dataset']['deptname'];
			 $p_mynote = $phones['dataset']['mynote'];

			$columns = "`name` = '$p_name' , `phone` = '$p_phone', `title` = '$p_title', `deptname` = '$p_deptname', `mynote` = '$p_mynote' ";
//UPDATE `ncut`.`phones` SET `name` = '1000' WHERE `phones`.`pid` = 138;
/*
<< <
影響了 1 列。
UPDATE `ncut`.`phones` SET `name` = '121212', `deptname` = 'test22', `title` = 'test22', `mynote` = 'test22' WHERE `phones`.`pid` = 139; 

*/

			$query = "UPDATE `ncut`.`phones` SET ". $columns ." WHERE `phones`.`pid` = $id  ";
			
			if(!empty($phones)){
logfile("restfill.log" , "insertNCUT_Phone  UPDATE $r  \n".$query ."\n" ) ; // 	
				$r = $this->mysqli->query($query) or die($this->mysqli->error. __LINE__  .$query );
				
				$success = array('status' => "Success", "msg" => "phones ".$id." Updated Successfully.", "data" => $phones);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// "No Content" status
		}

		
		private function deleteNCUT_Phone(){
			if($this->get_request_method() != "DELETE"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){				
				$query="DELETE FROM phones WHERE pid = $id";
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Successfully deleted one record.");
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// If no records "No Content" status
		}







// 增 刪 改 查all 查one

		private function customer(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){	
				$query="SELECT distinct c.customerNumber, c.customerName, c.email, c.address, c.city, c.state, c.postalCode, c.country FROM crud_customers c where c.customerNumber=$id";
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				if($r->num_rows > 0) {
					$result = $r->fetch_assoc();	
					$this->response($this->json($result), 200); // send user details
				}
			}
			$this->response('',204);	// If no records "No Content" status
		}
		

		private function insertCustomer(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}

			$customer = json_decode(file_get_contents("php://input"),true);
			$column_names = array('customerName', 'email', 'city', 'address', 'country');
			$keys = array_keys($customer);
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){ // Check the customer received. If blank insert blank into the array.
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $customer[$desired_key];
				}
				$columns = $columns.$desired_key.',';
				$values = $values."'".$$desired_key."',";
			}
			$query = "INSERT INTO crud_customers(".trim($columns,',').") VALUES(".trim($values,',').")";
			if(!empty($customer)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Customer Created Successfully.", "data" => $customer);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	//"No Content" status
		}


		private function updateCustomer(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$customer = json_decode(file_get_contents("php://input"),true);
			$id = (int)$customer['id'];
			$column_names = array('customerName', 'email', 'city', 'address', 'country');
			$keys = array_keys($customer['customer']);
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){ // Check the customer received. If key does not exist, insert blank into the array.
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $customer['customer'][$desired_key];
				}
				$columns = $columns.$desired_key."='".$$desired_key."',";
			}
			$query = "UPDATE crud_customers SET ".trim($columns,',')." WHERE customerNumber=$id";
			if(!empty($customer)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Customer ".$id." Updated Successfully.", "data" => $customer);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// "No Content" status
		}

		
		private function deleteCustomer(){
			if($this->get_request_method() != "DELETE"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){				
				$query="DELETE FROM crud_customers WHERE customerNumber = $id";
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Successfully deleted one record.");
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// If no records "No Content" status
		}

		
		/*
		 *	Encode array into JSON
		*/
		private function json($data){
			if(is_array($data)){
				return json_encode($data);
			}

		}

/**
  my API test function 
  http://localhost:8000/services/api.php?x=test
  for mysqli connect debug 
*/ 
		public	function test(){ 	 

		  echo "Api test " ;  

		     } 



	}


	
	// Initiiate Library

	$api = new API; // 建立db 
	$api->processApi();  // 讀取 REST FULL API 送來的指令 
?>