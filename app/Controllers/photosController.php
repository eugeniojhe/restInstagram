<?php
	namespace app\Controllers; 
	use lib\Core\Controller;
	use app\Models\Users; 
	use app\Models\Photos; 
	use app\Models\Photo_Comments; 
	use app\Models\Photo_likes; 

	class photosController extends Controller {
		private $ioUser; 
		private $ioPhotos; 
        public function __construct()
        {
        	$this->ioUser = new Users(); 
        	$this->ioPhotos = new Photos(); 
        }


        public function random()
        {
        	$response = array(
		    'error'  => '',
		    'logged' => false);
		    $method = $this->getMethod();
			$data = $this->getRequestData();
			if ($method == "GET"){
				if (!empty($data['jwt'])){
					if ($this->ioUser->validateJwt($data['jwt'])){
						$response['logged'] = true;
						 $itemsPerPage = intval((!empty($data['itemspage'])?$data['itemspage']:10));
						$except = array(); 
						if (!empty($data['except'])){
							$except[] = $data['except']; 
						}
					    $response['aleatory_photos'] = $this->ioPhotos->randomPhotos($itemsPerPage,$except);	     
					}else {
						$response['error'] = "Please enter a valid jwt ".__METHOD__; 
					}

				}else{
					$response['error'] = "Please jwt is required ".__METHOD__;; 
				}

			}else{
				$response['error'] = "Please inter a valid method. It must be GET method ".__METHOD__;; 
			}
			$this->jsonReturn($response);
        }

        		 // 
		public function view($photoId)
		{
			$response = array(
		    'error'  => '',
		    'logged' => false); 
			$method = $this->getMethod();
			$data = $this->getRequestData();
			if (!empty($data['jwt'])){
				if ($this->ioUser->validateJwt($data['jwt'])){
					$response['logged'] = true;
					switch($method){
						case "GET":
			   				$response['photo_info'] = $this->ioPhotos->get($photoId);
			   				break;
						 
						case "DELETE":
						    $response['msg'] = $this->ioPhotos->delete($photoId,$this->ioUser->getId()); 
			    			break;
			 			default:
			 				$response['error'] = "Invalid Method {$method} for this app ".__METHOD__;;  
			    		break;
			    	}	    	

				}else{
					$response['error'] = "jwt is not valid for this user ".__METHOD__;; 
				}
			}else{
				$response['error'] = "Access Denied - Please enter JWT hash".__METHOD__;
			}				
			$this->jsonReturn($response); 
		}

		public function  feed()
		{
			$response = array(
		    'error'  => '',
		    'logged' => false);
		    $method = $this->getMethod();
			$data = $this->getRequestData();
			if ($method = "GET"){
				if (!empty($data['jwt'])){
					if ($this->ioUser->validateJwt($data['jwt'])){
						$response['logged'] = true;
						   $offset = intval((!empty($data['offset'])?$data['offset']:0));
					    $itemsPerPage = intval((!empty($data['itemspage'])?$data['itemspage']:10));
					    $response['users_feed'] = $this->ioUser->feed($offset,$itemsPerPage);	     
					}else {
						$response['error'] = "Please enter a valid jwt ".__METHOD__; 
					}

				}else{
					$response['error'] = "Please jwt is required ".__METHOD__; 
				}

			}else{
				$response['error'] = "Please inter a valid method. It must be GET methdo ".__METHOD__; 
			}
			$this->jsonReturn($response); 
		}

		public function  photos($usr_id)
		{
			$ioPhotos = new Photos();
			$response = array(
		    'error'  => '',
		    'logged' => false);
		    $method = $this->getMethod();
			$data = $this->getRequestData();
			if ($method = "GET"){
				if (!empty($data['jwt'])){
					if ($this->ioUser->validateJwt($data['jwt'])){
						$response['logged'] = true;
						   $offset = intval((!empty($data['offset'])?$data['offset']:0));
					    $itemsPerPage = intval((!empty($data['itemspage'])?$data['itemspage']:10));
					    $response['isMe'] = false; 
					    if($usr_id == $this->ioUser->getId()){
							$response['isMe'] = true;
						} 
					    $response['user_photos'] = $ioPhotos->getPhotos($usr_id,$offset,$itemsPerPage);	     
					}else {
						$response['error'] = "Please enter a valid jwt ".__METHOD__; 
					}

				}else{
					$response['error'] = "Please jwt is required ".__METHOD__; 
				}

			}else{
				$response['error'] = "Please inter a valid method. It must be GET methdo ".__METHOD__; 
			}
			$this->jsonReturn($response); 
		}

		public function comment($photoId)
		{
			$response = array(
		    'error'  => '',
		    'logged' => false); 
		    $ioPhotoComments = new Photo_Comments(); 
			$method = $this->getMethod();
			$data = $this->getRequestData();
			if (!empty($data['jwt'])){
				if ($this->ioUser->validateJwt($data['jwt'])){
					$response['logged'] = true;
					if (isset($data['comment']) && !empty($data['comment'])){
						switch($method){
							case "POST":
				   				$response['error'] =  $ioPhotoComments->store($photoId,$this->ioUser->getId(),$data['comment']);
				   				break; 			 
							default:
				 				$response['error'] = "Invalid Method {$method} for this app - Please use POST method ".__METHOD__;  
				    		break;
			    		}

					}else{
						$response['error'] = "You must add a comment ".__METHOD__; 
					}
				
				}else{
					$response['error'] = "jwt is not valid for this user"; 
				}
			}else{
				$response['error'] = "Access Denied - Please enter JWT hash".__METHOD__;
			}				
			$this->jsonReturn($response); 
		}

		public function delcomment($commentId)
		{
			$response = array(
		    'error'  => '',
		    'logged' => false); 
		    $ioPhotoComments = new Photo_Comments(); 
			$method = $this->getMethod();
			$data = $this->getRequestData();
			if (!empty($data['jwt'])){
				if ($this->ioUser->validateJwt($data['jwt'])){
					$response['logged'] = true;
						switch($method){
							case "DELETE":
				   				$response['error'] =  $ioPhotoComments->delete($commentId,$this->ioUser->getId(),$data['comment']);	
				   				break; 			 
							default:
				 				$response['error'] = "Invalid Method {$method} for this action - Please use DELETE method".__METHOD__;  
				    		break;
			    		}
					
				}else{
					$response['error'] = "jwt is not valid for this user ".__METHOD__; 
				}
			}else{
				$response['error'] = "Access Denied - Please enter JWT hash".__METHOD__;
			}				
			$this->jsonReturn($response); 
		}

		public function like($photoId)
		{
			$response = array(
		    'error'  => '',
		    'logged' => false); 
		    $ioPhotoLikes = new Photo_Likes(); 
			$method = $this->getMethod();
			$data = $this->getRequestData();
			if (!empty($data['jwt'])){
				if ($this->ioUser->validateJwt($data['jwt'])){
					$response['logged'] = true;
						switch($method){
							case "POST":
				   				$response['error'] =  $ioPhotoLikes->store($photoId,$this->ioUser->getId());
				   				break; 
				   			case "DELETE":
				   				$response['error'] =  $ioPhotoLikes->delete($photoId,$this->ioUser->getId());
				   				break; 				 
							default:
				 				$response['error'] = "Invalid Method {$method} for this app - Please use POST method ".__METHOD__;  
				    		break;
			    		}
				
				}else{
					$response['error'] = "jwt is not valid for this user ".__METHOD__; 
				}
			}else{
				$response['error'] = "Access Denied - Please enter JWT hash".__METHOD__;
			}				
			$this->jsonReturn($response); 
		}

		public function loadFile()
		{
			$method = $this->getMethod();
			$data = $this->getRequestData();
			print_r($_SERVER); 
			print_r($_REQUEST);
			print_r($_FILES);   
			$this->jsonReturn($data); 
		}


	}
