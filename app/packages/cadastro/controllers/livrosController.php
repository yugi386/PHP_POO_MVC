<?php
/*
|===========================================================================|
|	CONTROLADOR DO CADASTRO 												|
|===========================================================================|
|																			|
|	VERSÃO:								1.0.0								|
|	 																		|	
|===========================================================================|
*/
Class livros extends Controller {
	
	private $livros;			//	ATRIBUTO PARA INSTANCIAR O MODELO DE DADOS
	private $datas;				//  VETOR COM PARAMETRO PARA AS VIEWS
	private $emprestimos;
	
	# ---------------------------------------------------------------------------------------------------------------------------------
	# METODO 001: [ PUBLIC FUNCTION INIT ]
	# ESTE METODO É RESPONSÁVEL POR INSTANCIAR O MODELO DE DADOS
	# ---------------------------------------------------------------------------------------------------------------------------------
		public function init() {					
			$this->livros = new livrosModel();	
			$this->emprestimos = new emprestimosModel();	//	para relacionamento
			$this->datas['redir'] = $this->redirecionaPath();		//	RETORNA PACOTE E CONTROLADOR			
		}	
	
	# ---------------------------------------------------------------------------------------------------------------------------------
	# METODO 002: [ PUBLIC FUNCTION INDEX_ACTION ]
	# ESTA ACAO CHAMA UM METODO DA CLASSE MODEL PARA LISTAR TODOS OS REGISTROS
	# ---------------------------------------------------------------------------------------------------------------------------------
	public function index_action() {	//	LISTAGEM DE TODOS OS REGISTROS OU DE ACORDO COM O FILTRO DE PESQUISA
		global $start;

		$this->LimpaFormulario("livros");	//	limpa o formulario e sessao de erros...		
		$numreg = 20;	//	numero de registros por página

		// Lendo o parametro de paginacao	
		$pag = $this->getParam('pag');
		if ($pag == null) {
			$pag = 1;
		}
		
		if ( isset($_POST['fw_pesquisar']) and !empty($_POST['fw_pesquisar']) ) {

			 $vetor = $this->verificaPesquisa();	//	Verifica o tipo de pesquisa
			 $campo = $vetor[0];
			 $op = $vetor[1];

			$this->datas['lista_registros'] = $this->livros->listalivros(" `{$campo}` " . $op);	
			$totreg = count($this->datas['lista_registros']);	//	Total de registros com filtro

			$this->datas['lista_registros'] = $this->livros->listalivros(" `{$campo}` " . $op,$numreg,($pag-1)*$numreg);	//	Pesquisa com condição
		} else {
			$this->datas['lista_registros'] = $this->livros->listalivros();
			$totreg = count($this->datas['lista_registros']); // total de registros 
		
			$this->datas['lista_registros'] = $this->livros->listalivros(null,$numreg,($pag-1)*$numreg);						//	Passa o resultado para o vetor $datas
		}
		
		$this->datas['numreg'] = $numreg;	//	número de registros por página
		$this->datas['pag'] = $pag;			//	número da página corrente
		$this->datas['totreg'] = $totreg;	//	total de páginas com registros.
		
		$this->view('index', $this->datas);						//	Chama a View...
	}

	# ---------------------------------------------------------------------------------------------------------------------------------
	# METODO 003: [ PUBLIC FUNCTION CONSULTAR ]
	# ESTA ACAO CHAMA UM METODO DA CLASSE MODEL PARA LISTAR UM ÚNICO REGISTRO.
	# ---------------------------------------------------------------------------------------------------------------------------------
	public function consultar() {			//	CONSULTANDO UM REGISTRO!!!
		$id = $this->getParam('id');		//	Pega parametro na classe System
		
		$this->datas['show_registro'] = $this->verificaRegistro($id, $this->livros->livrosShow("id=".$id));	//	VERIFICA SE O REGISTRO EXISTE!!!
		$this->datas['tipo'] = '1';								// TIPO 1 = CONSULTA
		
		$this->view('consultar_excluir', $this->datas);
	}
	
	# ---------------------------------------------------------------------------------------------------------------------------------
	# METODO 004: [ PUBLIC FUNCTION EXCLUIR ]
	# ESTA ACAO PERMITE EXCLUIR UM REGISTRO
	# ---------------------------------------------------------------------------------------------------------------------------------
	public function excluir() {						//	EXCLUINDO REGISTRO!!!
		$id = $this->getParam('id');				//	Pega parametro na classe System
		$confirma = $this->getParam('confirma');	//	Pega parametro na classe System
		
		$this->datas['show_registro'] = $this->verificaRegistro($id, $this->livros->livrosShow("id=".$id));	//	VERIFICA SE O REGISTRO EXISTE!!!
		$this->datas['tipo'] = '2';					// TIPO 2 = EXCLUSÃO
		
		// nao deixa excluir livro se existem registros referenciados:
		if (count($this->emprestimos->emprestimosShow("livro_id = ".$id)) != 0) {
			$this->view('aviso_excluir', $this->datas);
			return;
		}
		
		if ($confirma == "sim") {
			$this->livros->delete('id ='.$id);
			$this->redirecionar_pagina_inicial();	
		} else {
			$this->view('consultar_excluir', $this->datas);
		}	
	}

	# ---------------------------------------------------------------------------------------------------------------------------------
	# METODO 005: [ PUBLIC FUNCTION INCLUIR ]
	# ESTA ACAO PERMITE INCLUIR UM REGISTRO
	# ---------------------------------------------------------------------------------------------------------------------------------
	public function incluir() {	
		global $start;	//	INCLUINDO UM REGISTRO!!!
		$confirma = $this->getParam('confirma');	//	Pega parametro na classe System
		
		$this->datas['tipo'] = '1';					// Inclusao

		if ($confirma == "sim") {
			$form = array();
			$form = $this->retorna_formulario($this->livros->_tabela);	//	Lendo dados do formulario
			
			include_once(FW_PATH_SERV . FW_RAIZ. FW_VIEWS . $start->_controller ."/blocks/cabCampo.php");	//	INCLUI O ARQUIVO COM CABEÇALHOS E CAMPOS
			$campos = array_values($FW_campos);
			$this->datas['erros'] =  $this->livros->ValidaDados($form,$campos);	//	validacao de dados...	
			
			if (count($this->datas['erros']) == 0) {		//	 se existem erros volta para o formulario
				$this->LimpaFormulario("livros");	//	limpa o formulario e sessao de erros...					
				
				$this->livros->insert($form);
				$this->redirecionar_pagina_inicial();	
			}else{
				$redireciona = new RedirectorLib();		
			    $redireciona->goToControllerAction($redireciona->GetCurrentController(), $redireciona->GetCurrentAction());		//	RETORNA PACOTE E CONTROLADOR			
			}		
		} else {
			$this->view('incluir_alterar', $this->datas);
		}	
	}

	# ---------------------------------------------------------------------------------------------------------------------------------
	# METODO 006: [ PUBLIC FUNCTION ALTERAR ]
	# ESTA ACAO PERMITE ALTERAR UM REGISTRO
	# ---------------------------------------------------------------------------------------------------------------------------------
	public function alterar() {			//	ALTERANDO REGISTRO!!!
		global $start;
		$id = $this->getParam('id');				//	Pega parametro na classe System
		$confirma = $this->getParam('confirma');	//	Pega parametro na classe System
		
		$this->datas['show_registro'] = $this->verificaRegistro($id, $this->livros->livrosShow("id=".$id));	//	VERIFICA SE O REGISTRO EXISTE!!!
		$this->datas['tipo'] = '2';						// Alteração		
		
		if ($confirma == "sim") {
			$form = $this->retorna_formulario($this->livros->_tabela);	//	Lendo dados do formulario
			
			include_once(FW_PATH_SERV . FW_RAIZ. FW_VIEWS . $start->_controller ."/blocks/cabCampo.php");	//	INCLUI O ARQUIVO COM CABEÇALHOS E CAMPOS
			$campos = array_values($FW_campos);
			$this->datas['erros'] =  $this->livros->ValidaDados($form,$campos);	//	validacao de dados...	
			
			if (count($this->datas['erros']) == 0) {		//	 se existem erros volta para o formulario
				$this->LimpaFormulario("livros");		//	limpa o formulario e sessao de erros...		
				$this->livros->update( $form, "id=".$id) ;
				$this->redirecionar_pagina_inicial();	
			}else{
				$redireciona = new RedirectorLib();		
				header("Location: ". FW_RAIZ . $redireciona->GetCurrentPackage() . "/" . $redireciona->GetCurrentController() . "/" . 
						$redireciona->GetCurrentAction() . "/id/" . $id );
			}
		} else {
			$this->view('incluir_alterar', $this->datas);
		}	
	}

} // FIM DA CLASSE -----------------------------------------------------------------------------------------------------------------------	
?>