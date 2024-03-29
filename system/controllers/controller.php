<?php 
/*
|===========================================================================|
|	CLASSE DOS CONTROLADORES												|
|===========================================================================|
|																			|
|	VERS�O:								1.0.0								|
|	DATA DA CRIA��O:		 			28/03/2012 							|
|	DATA DA �LTIMA ATUALIZA��O: 		01/04/2012							|
|	 																		|	
|===========================================================================|

OBS: TODOS OS CONTROLADORES S�O HERDADOS DESTA CLASSE DE SISTEMA!!!
*/

	Class Controller extends System{
	
	# ---------------------------------------------------------------------------------------------------------------------------------
	# METODO 001: [ PROTECTED FUNCTION VIEW ]
	# ESTE METODO PROTEGIDO � RESPONS�VEL PELA CHAMADA DAS VIEWS (TELAS) DO SISTEMA ATRAV�S DO LAYOUT DEFINIDO PELO USU�RIO
	# PARAMETROS:
	# 	$nome => REFERE-SE AO NOME DA VIEW
	# 	$vars => � UM ARRAY QUE RECEBE TODAS AS VARI�VEIS QUE SER�O PASSADAS PARA A VIEW.
	# ---------------------------------------------------------------------------------------------------------------------------------
	
		protected function view($nome, $vars = null){
			if (is_array($vars) && count($vars) > 0 ) 		// TRANSFORMA O VETOR DE PARAMETROS EM VARIAVEIS PARA SEREM USADOS NA VIEW!!!
				extract($vars, EXTR_PREFIX_ALL, "view");
			
			return require_once(FW_LAYOUTS . 'index.phtml');	// CHAMA O LAYOUT DA APLICA��O QUE CHAMAR� A VIEW!!! 	
			exit();
		}
		
	# ---------------------------------------------------------------------------------------------------------------------------------
	# METODO 002: [ PRIVATE FUNCTION INIT ]
	# ESTE METODO � EXECUTADO ANTES DE QUALQUER METODO NOS CONTROLADORES DO USUARIO
	# ---------------------------------------------------------------------------------------------------------------------------------
	
		public function init() {
		}
		
	# ---------------------------------------------------------------------------------------------------------------------------------
	# METODO 003: [ PRIVATE FUNCTION FINALIZE ]
	# ESTE METODO � EXECUTADO DEPOIS DE QUALQUER METODO NOS CONTROLADORES DO USUARIO
	# ---------------------------------------------------------------------------------------------------------------------------------
	
		public function finalize() {
		}	
		
	# ---------------------------------------------------------------------------------------------------------------------------------
	# METODO 004: [ FUNCTION REDIRECIONAPATH ]
	# ESTE METODO � RESPONS�VEL POR INDICAR UM CAMINHO DE REDIRECIONAMENTO DO PACOTE E CONTROLADOR CORRENTE
	# ---------------------------------------------------------------------------------------------------------------------------------
	protected function RedirecionaPath() {					//	REDIRECIONA PARA O PATH DO CADASTRO
			$redireciona = new RedirectorLib();		
			return $redireciona->GetCurrentPackage() . "/" . $redireciona->GetCurrentController()	. "/";		//	RETORNA PACOTE E CONTROLADOR
	}	
		
	# ---------------------------------------------------------------------------------------------------------------------------------
	# METODO 005: [ FUNCTION REDIRECIONAR_PAGINA_INICIAL ]
	# ESTE METODO � RESPONS�VEL POR REDIRECIONAR O CADASTRO PARA A INDEX DO CONTROLADOR
	# ---------------------------------------------------------------------------------------------------------------------------------
	protected function redirecionar_pagina_inicial() {	//	REDIRECIONA PARA A INDEX DO CADASTRO
			$redir = new RedirectorLib();
			$redir->goToPackageControllerAction($redir->GetCurrentPackage(),$redir->GetCurrentController(),'index');
	}		
	
	# ---------------------------------------------------------------------------------------------------------------------------------
	# METODO 006: [ PUBLIC FUNCTION VERIFICAREGISTRO ]
	# ESTA ACAO PERMITE VERIFICAR SE UM DETERMINADO REGISTRO EXISTE (PARA CONSULTA, ALTERACAO E EXCLUS�O)
	# PARAMETRO: ID DO REGISTRO
	#			OPERACAO : CONSULTA DO BANCO DE DADOS	
	# ---------------------------------------------------------------------------------------------------------------------------------	
	
	protected function verificaRegistro($id,$operacao) {
		if (!is_numeric($id) ) {					//	variavel id n�o � numerica
			$this->redirecionar_pagina_inicial();	
			exit;
		}	

		$datas['show'] = $operacao;
		
		if (empty($datas['show'])) {		//	tratamento de erro: cliente n�o existe!!!
			$this->redirecionar_pagina_inicial();	
			exit;
		} else {
			return $datas['show'];	
		}			
	
	}
	
	# ---------------------------------------------------------------------------------------------------------------------------------
	# METODO 007: [ PROTECTED RETORNA_FORMULARIO ]
	# ESTE METODO � RESPONS�VEL POR RESGATAR OS DADOS DO FORMULARIO E INCLU�-LOS EM UM VETOR
	# ---------------------------------------------------------------------------------------------------------------------------------
	protected function retorna_formulario($tabela,$rotulo = null, $excluir = null) {	
		/*
			EXEMPLO DE INSTANCIAMENTO MANUAL DOS DADOS:
			return Array(
					"nome" 		=> $_POST['nome'] ,
					"cpf" 		=> $_POST['cpf'] , 
					"endereco" 	=> $_POST['endereco'] , 
					"bairro" 	=> $_POST['bairro'] , 
					"cidade" 	=> $_POST['cidade'] , 
					"estado" 	=> $_POST['estado'] , 
					"telefone" 	=> $_POST['telefone'] , 
					"celular" 	=> $_POST['celular'] , 
					"email" 	=> $_POST['email'], 
					"cep" 		=> $_POST['cep']
				  );
		*/
		
		/* 	VARIACAO GENERICA PARA INCLUSAO E ALTERACAO DE DADOS VINDOS DA TABELA!!!
			ATEN��O: APENAS OS CAMPOS DO FORMULARIO QUE EXISTEM NA TABELA SER�O AUTOMATICAMNETE GRAVADOS!!!

		1. o PRIMEIRO PARAMETRO � A TABELA EM USO.
		
		2. 	O parametro array [$rotulo] representado um rotulo para ser usado para fazer a correspondencia entre os nomes dos campos na tabela e 
			os nomes dos campos no formulario. Se o vetor � nulo, ent�o assume-se que os campos da tabela do formulario s�o iguais. 
			
				EX DE USO: O INDICE DO ARRAY � O CAMPO DO BANCO DE DADOS E O CONTEUDO DA VARIAVEL � UM CAMPO DO FORMULARIO
			
							$rotulo = array( 
								"nome" => "nome2",
								"cpf" => "cpf2"
							);		
							
		3. O parametro array $excluir permite excluir determinado campos de serem gravados no banco de dados, ou seja ser�o excluidos 	
		   da gravacao mas continuam na variavel $_POST do formulario.	
		   Por default o campo ID das tabelas s�o excluidos da gravacao visto terem sempre auto-incremento.
		   EXEMPLO DE USO:
		   
					$excluir = array("cpf","data");
		*/
		
		global $start;	//	para acessar metodo contra sql injection na classe system
		
		$bd = new InfoBDlib();													//	BIBLIOTECA COM FUNCOES QUE RETORNAM INFORMACOES SOBRE O BANCO, TABELAS E CAMPOS.
		$campos = $bd->retorna_info_CP(FW_BANCO,$tabela);						//	RETORNA INFORMCOES SOBRE OS CAMPOS
		$vet = array();
		$limit = count($campos);
		
		// CRIANDO O VETOR DE RETORNO COM OS DADOS DO FORMULARIO:
		for ($ct=0; $ct<$limit; $ct++) {
			$campo = $campos[$ct][0];

			if ($rotulo == null) {
				$tmp = (isset($_POST[$campo]) ? $start->NoSqlInject($_POST[$campo]) : ''); 
				$vet[$campo] = $this->textTransform($tmp,$campos[$ct][1]); 
			} else {
				$troca = (isset( $rotulo[$campo] ) ? $rotulo[$campo] : "NAO1473" ); 	//	SE EXISTIR O ROTULO ELE PREVALECE, CASO CONTRARIO VALE O CAMPO DA TABELA					
				if ($troca != "NAO1473") {
					$tmp = (isset($_POST[$troca]) ? $start->NoSqlInject($_POST[$troca]) : ''); 
					$vet[$campo] = $this->textTransform($tmp,$campos[$ct][1]); 
				} else {
					$tmp = (isset($_POST[$campo]) ? $start->NoSqlInject($_POST[$campo]) : ''); 
					$vet[$campo] = $this->textTransform($tmp,$campos[$ct][1]); 
				}	
			}	
		}
		
		if ($excluir != null) {
			for ($i = 0; $i<$limit;$i++) {				// EXCLUINDO CAMPOS QUE N�O SER�O GRAVADOS:
				$campo = $campos[$i][0];
				if ( in_array($campo,$excluir) ) {
					unset($vet[$campo]);	
				}
			}
		}	
		unset($vet['id']);							//	REMOVE O ID QUE NUNCA � INCLUIDO (SOMENTE AUTO-INCREMENTO) OU ALTERADO
		
		return $vet;
	}
	
	# ---------------------------------------------------------------------------------------------------------------------------------
	# METODO 008: [ PROTECTED VERIFICAPESQUISA ]
	# ESTE METODO � RESPONS�VEL POR VERIFICAR E RETORNAR OS DADOS DO FORMULARIO DE PESQUISA DE REGISTROS
	# ---------------------------------------------------------------------------------------------------------------------------------
	protected function verificaPesquisa() {	
			global $start;
			
			$pesq = $start->NoSqlInject($_POST['fw_pesquisar']);					//	para evitar SQL INJECTION
			$campo = $start->NoSqlInject($_POST['fw_campo']);
			
			if ($_POST['fw_relacional'] == '1') {
				$op = "LIKE '%" . $pesq ."%'";
			} else if ($_POST['fw_relacional'] == '2') {	
				$op = "NOT LIKE '%" . $pesq ."%'";
			} else if ($_POST['fw_relacional'] == '3') {
				$op = " = '" . $pesq . "'";
			} else if ($_POST['fw_relacional'] == '4') {
				$op = " <> '" . $pesq . "'";				
			} else if ($_POST['fw_relacional'] == '5') {
				$op = " > '" . $pesq . "'";
			} else if ($_POST['fw_relacional'] == '6') {
				$op = " < '" . $pesq . "'";								
			} else {
				$op = "LIKE '%" . $pesq ."%'";			
			}
			
			return array($campo,$op);	
	}
	
	# ---------------------------------------------------------------------------------------------------------------------------------
	# METODO 009: [ FORMATA CAMPOS PARA DATA ]
	# ESTE METODO RECEBE UMA ENTRA DA DO FORMULARIO E DE ACORDO COM O SEU TIPO FAZ A ALTERA��O PARA GRAVAR NO BANCO 
	# NO FORMATO DATA dd/mm/aaaa E CONVERTE PARA aaaa-mm-dd PARA GRAVACAO NO BANCO MYSQL ENTRE OUTROS
	# O SEGUNDO PARAMETRO SE REFERE AO TIPO DO CAMPO
	# ---------------------------------------------------------------------------------------------------------------------------------
	
	protected function textTransform($text,$tipo) {
		if (strtoupper($tipo) === "DATE")  { // SE O CAMPO � UMA DATA CONVERTE DO FORMATO DIA/MES/ANO PARA ANO/MES/DIA	
			// $text = str_replace("/","-",$text);
			$text = substr($text,6,4) . '-' . substr($text,3,2) . '-' . substr($text,0,2);
		}
		
		if (strtoupper($tipo) === "DATETIME" or strtoupper($tipo) === "TIMESTAMP")  { // SE O CAMPO � UMA datetime ou timestamp
			$text = substr($text,6,4) . '-' . substr($text,3,2) . '-' . substr($text,0,2) . substr($text,10,9);
		}
		
		return $text;
	}
	
	# ---------------------------------------------------------------------------------------------------------------------------------
	# METODO 010: [ PEGA DATA E DATE DO BANCO E CONVERTE PARA FORMATO DE ENTRADA DE DADOS ]
	# NO FORMATO DATA aaaa-mm-dd E CONVERTE PARA dd/mm/aa quando da leitura dos campos na consulta, alteracao e exclusao.
	# PARAMETRO 1 : NOME DAVARIAVEL
	# PARAMETRO 2 : CONTEUDO DA VARIAVEL: 
	# PARAMETRO 3 : VETOR COM OS DADOS DOS TIPOS DE VARIAVEIS DA TABELA DE DADOS.
	# ---------------------------------------------------------------------------------------------------------------------------------
	
	public function textTransform2($identVar, $text, $campos) {
		$verif = 0;
		for ($ct=0; $ct<count($campos);$ct++) {
			if ( trim($identVar) === trim($campos[$ct][0]) ) {
				if ( trim(strtoupper($campos[$ct][1])) === "DATE") {
					$verif = 1;
				}
				if ( trim(strtoupper($campos[$ct][1])) === "DATETIME") {
					$verif = 2;
				}
				if ( trim(strtoupper($campos[$ct][1])) === "TIMESTAMP") {
					$verif = 2;
				}
			}
		}
	
		if ($verif == 1)  { // SE O CAMPO � UMA DATA CONVERTE DO FORMATO DIA/MES/ANO PARA ANO/MES/DIA	
			// 2012-04-26
			$text = substr($text,8,2) . '/' . substr($text,5,2) . '/' . substr($text,0,4);
		}
		
		if ($verif == 2)  { // SE O CAMPO � UMA datetime ou timestamp
			// 2012-04-26 14:50:30
			$text = substr($text,8,2) . '/' . substr($text,5,2) . '/' . substr($text,0,4) . substr($text,10,9);
		}
		
		return $text;
	}
	
	// ------------------------------------------------------------------------------------------------------------------
	// METODO 011 - CONVETE DATA ANO-MES-DIA PARA DIA-MES-ANO
	// ------------------------------------------------------------------------------------------------------------------
	public function converteData($data) {
			$data = substr($data,8,2) . '/' . substr($data,5,2) . '/' . substr($data,0,4);
			return $data;
	}
	
	// ------------------------------------------------------------------------------------------------------------------
	// METODO 012 - ELIMINA SESSAO DE ERROS E DE DADOS RECUPERADOS DO FORMULARIO
	// ------------------------------------------------------------------------------------------------------------------
	public function LimpaFormulario($tabela) {
		for ($ct=0;$ct<1000;$ct++){		//	apagando sessoes de erros...
			if (isset($_SESSION["ERRO".$ct])){
				unset($_SESSION["ERRO".$ct]);
			}
		}
		
		include(FW_PATH_SERV . FW_RAIZ . FW_VIEWS . $tabela . "/blocks/cabCampo.php");	//	CABECALHO E ROTULOS DOS CAMPOS DA TABELA		
		$campos = array_values($FW_campos);
		for ($ct=0;$ct<count($campos);$ct++){		//	apagando valor dos formularios
			if (isset($_SESSION[$campos[$ct]])){
				unset($_SESSION[$campos[$ct]]);
			}
		}		
	}

	// =========================================================================	
	// DEVOLVE UM CAMPOS SELECT PREENCHIDO COM DADOS DO BANCO
	// Parametro 01 = nome e id do campo select
	// Parametro 02 Dados do Banco
	// Parametro 03 Campos que ser�o Apresentados.
	// =========================================================================	
	
	public function DevolveSelect($NomeSelect, $dados, $campos) {
		$CampoSelect = '<select name ="' . $NomeSelect .'" id="' . $NomeSelect . '" size="1">';
		
		for ($ct=0;$ct<count($dados);$ct++) {
			$CampoSelect .= '<option value="'.$dados[$ct]["id"] .'">'; 
			for($lt=0;$lt<count($campos);$lt++){
				$CampoSelect .= $dados[$ct][$campos[$lt]];
				if (isset($campos[$lt+1])) {
					$CampoSelect .= " - ";
				}
			}
			$CampoSelect .= ' ';
		}	
		$CampoSelect .= '</select>';
		return $CampoSelect;
	}
	
	}	// FINAL DA CLASSE ------------------------------------------------------------------------------------------------------------------		
?>