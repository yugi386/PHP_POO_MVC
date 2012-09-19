<?php/*|===========================================================================||	MODELO DE DADOS PARA A TABELA           						        ||===========================================================================||																			||	VERS�O:								1.0.0								||	 																		|	|===========================================================================|*/	Class livrosModel extends Model {		public $_tabela = "livros";	//	ATRIBUTO PARA SETAR QUAL A TABELA QUE EST� EM USO			# ---------------------------------------------------------------------------------------------------------------------------------	# METODO 001: [ PUBLIC FUNCTION LISTA REGISTROS ]	# ESTE METODO � RETORNA TODOS OS REGISTROS DO BANCO DE DADOS	# ---------------------------------------------------------------------------------------------------------------------------------				public function listalivros($condicao = null, $qtd = null, $offset = null, $ordem = 'id ASC') {			return $this->read($condicao, $qtd, $offset, $ordem);		}			# ---------------------------------------------------------------------------------------------------------------------------------	# METODO 002: [ PUBLIC FUNCTION MOSTRA REGISTRO ]	# ESTE METODO RETORNA UM �NICO REGISTRO	# ---------------------------------------------------------------------------------------------------------------------------------						public function livrosShow($where) {			return $this->read($where, null, null, 'id ASC');		}			# ---------------------------------------------------------------------------------------------------------------------------------		# METODO DE VALIDACAO DE DADOS: parametro formulairo	# ---------------------------------------------------------------------------------------------------------------------------------			public function ValidaDados($form,$campos) {			global $start;						// criando as sessoes para recuperar os dados do usuario:			for ($ct=0;$ct<count($campos);$ct++){			// EVITANDO SQL_INJECTION:				if (isset($_POST[$campos[$ct]]) ) {					$_POST[$campos[$ct]] = $start->NoSqlInject($_POST[$campos[$ct]]);				}				$_SESSION[$campos[$ct]] = (isset($_POST[$campos[$ct]]) ? $_POST[$campos[$ct]] : "");			}				$_SESSION['posic_erro'] = 0;	//	Ponteiro para gerenciar erros na validacao						// Validando titulo tamanho minimo			$this->VerErro( $this->ValidarStringMinimoMaximo('titulo', $_POST['titulo'], 5, 70, null, null) );						// Validando sub-titulo tamanho minimo			$this->VerErro(  $this->ValidarStringMinimoMaximo('sub-titulo', $_POST['subtitulo'], 5, 70, null, null) ) ;						// Validando autor tamanho minimo			$this->VerErro(   $this->ValidarStringMinimoMaximo('autor', $_POST['autor'], 4, 70, null, null) );			// Validando editora tamanho minimo			$this->VerErro(   $this->ValidarStringMinimoMaximo('editora', $_POST['editora'], 4, 50, null, null) );			// Validando editora tamanho minimo			$this->VerErro(  $this->ValidarStringMinimoMaximo('resumo', $_POST['resumo'], 50, 400, null, null) );						// validando Data:			$this->VerErro(  $this->ValidarData('data do cadastro',$_POST['datacad'], null, null, null) ) ;			// validando total de exemplares			$this->VerErro(  $this->ValidarNumeroMinimoMaximo('total de exemplares', $_POST['tot_exemplares'], 1, 100, null, null) );			// validando total de exemplares disponiveis			$this->VerErro(  $this->ValidarNumeroMinimoMaximo('exemplares disponiveis', $_POST['exemplar_dispo'], 0, 100, null, null));						// validando exemplares disponiveis			$this->VerErro(  $this->ValidarVazio('exemplares disponiveis', $_POST['exemplar_dispo'], null) );						// CUIDANDO DO UPLOAD:			/*			$redireciona = new RedirectorLib();				$caminho = $redireciona->GetCurrentPackage() . "/" . $redireciona->GetCurrentController();						if ( !isset($_SESSION["ERRO0"]) ) {		//	S� faz o upload se n�o existem erros nos outros campos!!!				$up = new UploadLib;				if (strlen($_FILES["arquivo_demo"]['name']) != 0) {					$erro = $up->Upload("arquivo_demo",$caminho,$_FILES["arquivo_demo"]['name']);					$this->VerErro($erro);	//	Gerenciando Erros no Upload...				}			}					*/			// RETORNO DE ERROS:			if ( isset($_SESSION["ERRO0"]) ) {	//	se existe ao menos um erro...				return array('1');	//	retorna o vetor de erros...					} else {				return array();		//	retorna o vetor de erros...								}			}						} // fim de classe?>