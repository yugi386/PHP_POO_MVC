<?php
	Class authLib {
		protected $sessionLib, $redirectorLib, $tablename, $userColumn,
				  $passColumn, $user, $pass, $loginController = 'index', $loginAction = 'index',
				  $logoutController = 'index',  $logoutAction = 'index';
		
		public function __construct() {
			$this->sessionLib = new SessionLib();
			$this->redirectorLib = new RedirectorLib();
			return $this;
		}
		
		public function setTableName ($val) {
			$this->tableName = $val;
			return $this;
		}
		
		public function setUserColumn ($val) {
			$this->userColumn = $val;
			return $this;
		}
		
		public function setPassColumn ($val) {
			$this->passColumn = $val;
			return $this;
		}
		
		public function setUser ($val) {
			$this->user = $val;
			return $this;
		}
		
		public function setPass($val) {
			$this->pass = $val;
			return $this;
		}
		
		public function setLoginControllerAction ($controller, $action) {
			$this->loginController = $controller;
			$this->loginAction = $action;
			return $this;
		}
		
		public function setLogoutControllerAction ($controller, $action) {
			$this->logoutController = $controller;
			$this->logoutAction = $action;
			return $this;
		}
		
		public function login() {
			$db = new Model();
			$db->_tabela = $this->tableName;
			$where = $this->userColumn."='".$this->user."' AND ".$this->passColumn."='".$this->pass."'";
			$sql = $db->read($where,'1');
			
			if (count($sql) > 0 ) :
				$this->sessionLib->createSession("userAuth",true)
									->createSession("userData",$sql[0]);
			else:
				die("USUÁRIO NÃO EXISTE!!!");
			endif;	
			
			$this->redirectorLib->goToControllerAction($this->loginController, $this->loginAction);
			return $this;
		}
		
		public function logout() {
			$this->sessionLib->deleteSession("userAuth")
								->deleteSession("userData");
			$this->redirectorLib->goToControllerAction($this->logoutController, $this->logoutAction);								
			return $this;
		}
		
		public function checkLogin($action) {
			switch ($action) {
				case "boolean":
					if (!$this->sessionLib->checkSession("userAuth") )
						return false;
					else
						return true;
					break;
				case "redirect":
					if (!$this->sessionLib->checkSession("userAuth") )
						if (trim($this->redirectorLib->getCurrentController()) != trim($this->loginController) || trim($this->redirectorLib->getCurrentAction()) != trim($this->loginAction) )
							$this->redirectorLib->goToControllerAction($this->loginController, $this->loginAction);
				
					break;
				case "stop":
					if (!$this->sessionLib->checkSession("userAuth") )
						exit;
					break;
			}
		}

		public function userData ($key) {
			$s = $this->sessionLib->selectSession("userData");
			return $s[$key];
		}
		
	}

?>