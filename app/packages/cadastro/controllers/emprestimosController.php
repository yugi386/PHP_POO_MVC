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
Class emprestimos extends Controller {
	
	public $emprestimos;			//	ATRIBUTO PARA INSTANCIAR O MODELO DE DADOS
	public $livros;					//	ATRIBUTO PARA INSTANCIAR O MODELO DE DADOS
	public $usuarios;				//	ATRIBUTO PARA INSTANCIAR O MODELO DE DADOS	
	private $datas;					//  VETOR COM PARAMETRO PARA AS VIEWS
	
	# ---------------------------------------------------------------------------------------------------------------------------------
	# METODO 001: [ PUBLIC FUNCTION INIT ]
	# ESTE METODO É RESPONSÁVEL POR INSTANCIAR O MODELO DE DADOS
	# ---------------------------------------------------------------------------------------------------------------------------------
		public function init() {					
			$this->emprestimos = new emprestimosModel();	
			$this->usuarios = new usuariosModel();	//	necessario para relacionamento
			$this->livros = new livrosModel();		//	necessario para relacionamento
			$this->datas['redir'] = $this->redirecionaPath();		//	RETORNA PACOTE E CONTROLADOR			
		}	
	
	# ---------------------------------------------------------------------------------------------------------------------------------
	# METODO 002: [ PUBLIC FUNCTION INDEX_ACTION ]
	# ESTA ACAO CHAMA UM METODO DA CLASSE MODEL PARA LISTAR TODOS OS REGISTROS
	# ---------------------------------------------------------------------------------------------------------------------------------
	public function index_action() {	//	LISTAGEM DE TODOS OS REGISTROS OU DE ACORDO COM O FILTRO DE PESQUISA
		global $start;

		$this->LimpaFormulario("emprestimos");	//	limpa o formulario e sessao de erros...		
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

			$this->datas['lista_registros'] = $this->emprestimos->listaemprestimos(" `{$campo}` " . $op);	
			$totreg = count($this->datas['lista_registros']);	//	Total de registros com filtro

			$this->datas['lista_registros'] = $this->emprestimos->listaemprestimos(" `{$campo}` " . $op,$numreg,($pag-1)*$numreg);	//	Pesquisa com condição
		} else {
			$this->datas['lista_registros'] = $this->emprestimos->listaemprestimos();
			$totreg = count($this->datas['lista_registros']); // total de registros 
		
			$this->datas['lista_registros'] = $this->emprestimos->listaemprestimos(null,$numreg,($pag-1)*$numreg);						//	Passa o resultado para o vetor $datas
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
		
		$this->datas['show_registro'] = $this->verificaRegistro($id, $this->emprestimos->emprestimosShow("id=".$id));	//	VERIFICA SE O REGISTRO EXISTE!!!
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
		
		$this->datas['show_registro'] = $this->verificaRegistro($id, $this->emprestimos->emprestimosShow("id=".$id));	//	VERIFICA SE O REGISTRO EXISTE!!!
		$this->datas['tipo'] = '2';					// TIPO 2 = EXCLUSÃO

		// nao deixa excluir livro nao devolvido
		if ($this->datas['show_registro'][0]['devolvidodia'] == "0000-00-00") {
			$this->view('aviso_excluir', $this->datas);
			return;
		}
		
		if ($confirma == "sim") {
			$this->emprestimos->delete('id ='.$id);
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
			$form = $this->retorna_formulario($this->emprestimos->_tabela);	//	Lendo dados do formulario
			
			include_once(FW_PATH_SERV . FW_RAIZ. FW_VIEWS . $start->_controller ."/blocks/cabCampo.php");	//	INCLUI O ARQUIVO COM CABEÇALHOS E CAMPOS
			$campos = array_values($FW_campos);
			$this->datas['erros'] =  $this->emprestimos->ValidaDados($form,$campos,'1');	//	validacao de dados...	
			
			if (count($this->datas['erros']) == 0) {		//	 se existem erros volta para o formulario
				$this->LimpaFormulario("emprestimos");	//	limpa o formulario e sessao de erros...					
				
				$this->emprestimos->insert($form);	//	insere dados
				
				// alterando a quantidade de exemplares disponiveis:
				// $livro_id = $start->NoSqlInject($_POST['livro_id']);
				
				$ficha = $this->livros->listalivros("id = " .$form['livro_id'] );	//	verificando a quantidade de livros disponiveis
				$dados = array(	'exemplar_dispo' => $ficha[0]['exemplar_dispo'] - 1	);

				// alterando a tabela livro - ajustar quantidade disponivel
				$this->livros->update( $dados,"id = ".$form['livro_id']);	//	ALTERANDO LIVROS
				
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
	public function devolver() {			//	ALTERANDO REGISTRO!!!
		global $start;
		$id = $this->getParam('id');				//	Pega parametro na classe System
		$confirma = $this->getParam('confirma');	//	Pega parametro na classe System
		
		$this->datas['show_registro'] = $this->verificaRegistro($id, $this->emprestimos->emprestimosShow("id=".$id));	//	VERIFICA SE O REGISTRO EXISTE!!!
		$this->datas['tipo'] = '2';						// Alteração		
		
		if ($confirma == "sim") {
			$form = $this->retorna_formulario($this->emprestimos->_tabela);	//	Lendo dados do formulario
			
			include_once(FW_PATH_SERV . FW_RAIZ. FW_VIEWS . $start->_controller ."/blocks/cabCampo.php");	//	INCLUI O ARQUIVO COM CABEÇALHOS E CAMPOS
			$campos = array_values($FW_campos);
			$this->datas['erros'] =  $this->emprestimos->ValidaDados($form,$campos,'2');	//	validacao de dados...	
			
			if (count($this->datas['erros']) == 0) {		//	 se existem erros volta para o formulario
				// alterando a quantidade de exemplares disponiveis:
				$livro_id = $this->datas['show_registro'][0]['livro_id'];
				$ficha = $this->livros->listalivros("id=".$livro_id);	//	verificando a quantidade de livros disponiveis
				$dados = array(	'exemplar_dispo' => $ficha[0]['exemplar_dispo'] + 1	);
				$this->livros->update($dados ,"id = ".$livro_id );	//	alterando quantidade de livros
				
				$this->LimpaFormulario("emprestimos");		//	limpa o formulario e sessao de erros...		
				$this->emprestimos->update( $form, "id=".$id) ;
				$this->redirecionar_pagina_inicial();	
			}else{
				$redireciona = new RedirectorLib();		
				header("Location: ". FW_RAIZ . $redireciona->GetCurrentPackage() . "/" . $redireciona->GetCurrentController() . "/" . 
						$redireciona->GetCurrentAction() . "/id/" . $id );
			}
		} else {
			if ($this->datas['show_registro'][0]['devolvidodia'] == "0000-00-00") {
				$this->view('incluir_alterar', $this->datas);
			} else {
				$this->view('aviso', $this->datas);
			}	
		}	
	}

} // FIM DA CLASSE -----------------------------------------------------------------------------------------------------------------------	
?>