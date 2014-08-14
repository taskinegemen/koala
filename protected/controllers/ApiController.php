<?php

class ApiController extends Controller
{
	public $response=null; 
	public $errors=null; 

	public function response($response_avoition=null){

		$response['result']=$response_avoition ? $response_avoition : $this->response;
		if ($this->errors) $response['errors']=$this->errors;

		$response_string=json_encode($response);


		header('Content-type: plain/text');
		header("Content-length: " . strlen($response_string) ); //tells file size
		echo $response_string;
	}
 
	public function error($domain='Api',$explanation='Error', $arguments=null,$debug_vars=null ){
		$error=new error($domain,$explanation, $arguments,$debug_vars);
		$this->errors[]=$error; 
		return $error;
	}



	public function actionService(){
		$auth=Yii::app()->request->getPost('auth',0);
		$http_service_ticket=Yii::app()->request->getPost('http_service_ticket',0);
		$kerberized=new KerberizedServer($auth,$http_service_ticket);	
		$myarray=$kerberized->ticketValidation();

		//error_log("ticket validation:".serialize($myarray));	
		$kerberized->authenticate();			
	}

	private function authenticate()
	{
		$auth=Yii::app()->request->getPost('auth',0);
		$http_service_ticket=Yii::app()->request->getPost('http_service_ticket',0);
		$type=Yii::app()->request->getPost('type','android');
		// error_log("auth:".$auth);
		// error_log("http_service_ticket:".$http_service_ticket);
		$kerberized=new KerberizedServer($auth,$http_service_ticket,KerbelaEncryptionFactory::create($type));
		

		 $myarray=$kerberized->ticketValidation();
		 //error_log("array: ".$myarray);
		 //error_log("user_id:".$kerberized->getUserId());
		//$kerberized->authenticate();
		if ($kerberized->getUserId()) {
			return $kerberized->getUserId();
		}
		else
			return 0;
	} 

	public function actionAuthenticate()
	{
		$auth=Yii::app()->request->getPost('auth',0);
		$http_service_ticket=Yii::app()->request->getPost('http_service_ticket',0);
		$type=Yii::app()->request->getPost('type','android');
		// error_log("auth:".$auth);
		// error_log("http_service_ticket:".$http_service_ticket);
		$kerberized=new KerberizedServer($auth,$http_service_ticket,KerbelaEncryptionFactory::create($type));
		

		 $myarray=$kerberized->ticketValidation();
		// error_log("user_id:".$kerberized->getUserId());
		 //error_log("array: ".$myarray);
		 //error_log("user_id:".$kerberized->getUserId());
		$kerberized->authenticate();
	}

	private function checkUser($user_id)
	{
		// $user=User::model()->findByPk($user_id);
		$user=User::model()->find('email=:email',array('email'=>$user_id));
		if (!$user) {
			$newUser=new User;
			$criteria=new CDbCriteria;
			$criteria->select='max(id) AS maxColumn';
			 $max = Yii::app()->db->createCommand("SELECT * FROM user WHERE user.created IN (SELECT max(created) FROM user)")->queryScalar();
			
			//$row=User::model()->find($criteria);
			//$row = $newUser->model()->find($criteria);	
			$userId = $max+1;//$row['maxColumn']+1;
			$newUser->id=$userId;
			$newUser->email=$user_id;
			$newUser->created = date('Y-n-d g:i:s',time());
			if (!$newUser->save()) {
				return false;
			}
		}
		return true;
	}


	public function actionDocumentation()
	{
		

		$this->render('documentation');
	}

	public function actionIndex()
	{
		if (!$this->authenticate()) {
			return null;
		}
		$this->render('index');
	}

	public function actionCheckUserBook()
	{
		$response=null;
		$ID=$this->authenticate();
		if (!$ID) {
			$this->error("AC-CUB","Unauthenticated Request",func_get_args(),CHttpRequest::getIsPostRequest());
			$this->response($response);
		 	return null;
		}
		if (!CHttpRequest::getIsPostRequest()) {
			$this->error("AC-CUB","Wrong Request",func_get_args(),CHttpRequest::getIsPostRequest());
			$this->response($response);
			return null;			
		}

		$book_id=CHttpRequest::getPost('book_id',0);
		$ID=CHttpRequest::getPost('user_id',0);

		if (!$this->checkUser($ID)) {
			$this->error("AC-CUB","UserNotFound",func_get_args(),CHttpRequest::getIsPostRequest());
			$this->response($response);
			return null;
		}
		$user=User::model()->find('email=:email',array('email'=>$ID));
		$userBook=UserBooks::model()->find('user_id=:user_id AND book_id=:book_id',array('user_id'=>$user->id,'book_id'=>$book_id));
		if ($userBook) {
			$this->response($userBook->book_id);
		}
		else
		{	
			$this->error("AC-CUB","RecordNotFound",func_get_args(),CHttpRequest::getIsPostRequest());
			$this->response($response);
		}
	}

	public function addUserNote($user_id, $book_id, $page_id, $note){
		if (!$user_id || !$book_id || !$page_id || !$note) {
			$this->errors("AC-AUNote","Empty filed",func_get_args());
			return null;
		}
		
		$model= new UserNotes;
		$model->user_id=$user_id;
		$model->book_id=$book_id;
		$model->page_id=$page_id;
		$model->note=$note;
		$model->created = date('Y-n-d g:i:s',time());
		
		if (!$model->save()) {
			$this->errors("AC-AUNote","Operation failed",func_get_args());
			return null;
		}
		
		return $model->note_id;
	}

	public function actionAddUserNote()
	{
		$ID=$this->authenticate();
		if (!$ID) {
			return null;
		}
		$response=null;
		if (!CHttpRequest::getIsPostRequest()) {
			$this->error("AC-AUNote","Wrong Request",func_get_args(),CHttpRequest::getIsPostRequest());
			$this->response($response);
			return null;			
		}

		$book_id=CHttpRequest::getPost('book_id',0);
		$page_id=CHttpRequest::getPost('page_id',0);
		$note=CHttpRequest::getPost('note',0);

		if (!$this->checkUser($ID)) {
			return null;
		}
		$user=User::model()->find('email=:email',array('email'=>$ID));
		$user_id=$user->id;
	

		$response->note_id = $this->addUserNote($user_id, $book_id, $page_id, $note);

		$this->response($response);

	}

	public function actionAddUserNotes()
	{ 
		$ID=$this->authenticate();
		if (!$ID) {
			return null;
		}
		$response=null;

		if (!CHttpRequest::getIsPostRequest()) {
			$this->error("AC-AUNote","Wrong Request",func_get_args(),CHttpRequest::getIsPostRequest());
			$this->response($response);
			return null;			
		}
		
		$data=json_decode(CHttpRequest::getPost('notes',0),true);

		if (!$this->checkUser($ID)) {
			return null;
		}
		$user=User::model()->find('email=:email',array('email'=>$ID));
		$user_id=$user->id;

		foreach ($data as $key => $note) {
			$response[$key]=$this->addUserNote($user_id, $note['book_id'], $note['page_id'], $note['note']);
		}
		foreach ($response as $key => &$note) {
			$note=$note;
		}
		$this->response($response);

	}

	public function actionGetBookNotes()
	{
		$ID=$this->authenticate();
		if (!$ID) {
			return null;
		}
		$response=array();

		if (!CHttpRequest::getIsPostRequest()) {
			$this->error("AC-GBNotes","Wrong Request",func_get_args(),CHttpRequest::getIsPostRequest());
			$this->response($response);
			return null;			
		}

		$book_id=CHttpRequest::getPost('book_id',0);
		
		if (!$this->checkUser($ID)) {
			return null;
		}
		$user=User::model()->find('email=:email',array('email'=>$ID));
		$user_id=$user->id;

		$notes=UserNotes::model()->findAll('user_id=:user_id AND book_id=:book_id',array('book_id'=>$book_id,'user_id'=>$user_id));

		if (!$notes) {
			$this->error("AC-GBNotes","No notes found",array('user_id'=>$user_id,'book_id'=>$book_id));
			$this->response($response);
			return null;				
		}

		foreach ($notes as $key => &$note) {
			$note=$note->attributes;
		}

		$this->response($notes);

	}

	public function actionGetUserBooks()
	{
		
		$ID=$this->authenticate();
		if (!$ID && $ID!='NULL') {
			return null;
		}
		
		$response=array();
		if (!CHttpRequest::getIsPostRequest()) {
			$this->error("AC-GUBooks","Wrong Request",func_get_args(),CHttpRequest::getIsPostRequest());
			$this->response($response);
			return null;
		}
		
		if (!$this->checkUser($ID)) {
			return null;
		}
		$user=User::model()->find('email=:email',array('email'=>$ID));
		$user_id=$user->id;

		$userBooks=UserBooks::model()->findAll('user_id=:user_id',array('user_id'=>$user_id));
		if($userBooks){
			foreach ($userBooks as $key => &$book) {
				$book=$book->attributes;
			}
			$this->response($userBooks);
		}
		else
		{$this->response(false);}
	}

	public function actionAddUserBook()
	{
		// $ID=$this->authenticate();
		// if (!$ID) {
		// 	return null;
		// }
		if (CHttpRequest::getIsPostRequest()) {
			$method='aes128';
        	$pass='qaxcftyjmolsp';
        	$iv='8759226422345672';

			$user_email=openssl_decrypt(CHttpRequest::getPost('user',0), $method, $pass,true,$iv);
			$type=openssl_decrypt(CHttpRequest::getPost('type',0), $method, $pass,true,$iv);
			
			$user=User::model()->find('email=:email',array('email'=>$user_email));

			$user_id=$user->id;
			
			if ($user_id) {
				$model= new UserBooks;
				$model->user_id = $user_id;
				$model->book_id = $type;
				$model->created = date('Y-n-d g:i:s',time());
				$model->save();
			}
		}
	}


	public function actionDeneme()
	{
		$url = 'http://koala.lindneo.com/api/documentation';

		$a="2";
		//$b="556633";
		// $c="1234";
		// $d="asd";
		$params = array(
						'user_id'=>$a,
						//'book_id'=>$b,
						// 'notes'=>json_encode(array(
						// 			array('book_id'=>'12345','page_id'=>'8','note'=>'asd'),
						// 			array('book_id'=>'12345','page_id'=>'8','note'=>'12312asd'),
						// 			array('book_id'=>'556633','page_id'=>'8','note'=>'12312asd'),
						// 			))
						// 'page_id'=>$c,
						// 'note'=>$d
						);
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec( $ch );
		$this->response($response);
	}


	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}
