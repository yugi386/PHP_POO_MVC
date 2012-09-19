<?php /*	
==========================================================================	
|	GERADOR DE FORMULARIO PARA O CADASTRO
==========================================================================	
*/
// CODIGO PARA EVITAR EXECUCAO DE BLOCOS SEM PASSAR PELA PAGINA INICIAL
	if (!defined('FW_RAIZ'))
		die("ERRO DE REDIRECIONAMENTO");
	include_once($_SERVER["DOCUMENT_ROOT"]  . FW_RAIZ . "system/checkBlock.php");

?>
	
<?PHP
	
//	==================================================================================================================================================

		$FW_tabela = "emprestimos";											//	NOME DA TABELA DO BANCO DE DADOS
		$FW_tipoform = "post";												//	METODO DE ENVIO POST OU GET

		// VETOR COM OS NOMES DAS COLUNAS DA TABELA E OS RESPECTIVOS CAMPOS
		global $start;
		include_once(FW_PATH_SERV . FW_RAIZ. FW_VIEWS . $start->_controller ."/blocks/cabCampo.php");	//	INCLUI O ARQUIVO COM CABEÇALHOS E CAMPOS

		// PARA EXCLUIR ALGUM CAMPO DA EDIÇÃO DO FORMULÁRIO
		$excluir = array();
		$excluir = array("id");	//	por padrao deve ser excluído
		$FW_campos = $start->ExcluirCampo($FW_campos, $excluir );
		
		// PERSONALIZANDO FORMULARIO DE EDIÇÃO PARA INCLUSÃO E EXCLUSÃO:
		if ($view_tipo == '1') {	// inclusao
			$excluir = array("devolvidodia");	//	por padrao deve ser excluído
			$FW_campos = $start->ExcluirCampo($FW_campos, $excluir );
		} elseif ($view_tipo == '2') {
			$excluir = array("devolvidodia");	//	por padrao deve ser excluído
			// $FW_campos = $start->ExcluirCampo($FW_campos, $excluir );
		}	
		
		// PARA INCLUIR ALGUM CAMPO USE:
		$incluir = array (	"OBS" => "obs"	);
		// $FW_campos = $start->IncluirCampo($FW_campos, $incluir );
		
	//	ESPECIFICAÇÃO DOS ATRIBUTOS DE LEITURA DO CAMPO => PADRAO type="text" size="65" maxlength="60"
	// ATENÇÃO: o primeiro parametro a ser personalizado é o tipo (type) - NÃO USE ESPACOS ANTES DE TYPE!!!
/*
		$FW_formato = array(	"text1" 			=> 'type="text" size="10" maxlength="5"',
								"carro" 			=> 'type="checkbox"',
								"sexo"				=> '<input type="radio" id="sexo" name="sexo" value="M"  checked> Masculino <br> 
														<input type="radio" id="sexo" name="sexo" value="F"> Feminino' ,
								"tipocar"			=> '<select name="tipocar" id="tipocar">
														<option value="volvo">Volvo
														<option value="saab" selected>Saab
														<option value="fiat">Fiat
														<option value="audi">Audi
														</select>',					
								"obs"				=> '<textarea rows="10" cols="50">
														</textarea>',
								"senha"				=> '<input type="password" id="pw" name="pw" size="25" maxlength="20">'		
							);
*/

		// OBS: os atributos id e name de cada campo devem ter o mesmo nome do campo da tabela do banco de dados...
		// A variavel $Vresumo serve para preencher o textarea com o conteudo do campo resumo no banco.

		// RELACIONAMENTOS DO FORMULARIO:
		global $start;	//	variavel necessario para acessar o sistema			
		$lista_usuarios = $this->usuarios->listausuarios(null, null, null, 'nome ASC');
		$lista_livros = $this->livros->listalivros(null, null, null, "titulo ASC");
		
		$CampoUsuarioId = $this->DevolveSelect("usuario_id", $lista_usuarios, array("nome","email"));
		$CampoLivroId = $this->DevolveSelect("livro_id", $lista_livros, array("titulo","autor"));
		
		// echo '<textarea rows="10" cols="50"> '. $CampoUsuarioId	.'</textarea>';
		// die();

		// Data daqui a 7 dias.
		$datasoma = date('d/m/Y',mktime(0,0,0,date('m'),date('d')+7,date('Y')));
		
		if ($view_tipo == '1') { // inclusao
			$FW_formato = array(	
				"usuario_id" => $CampoUsuarioId,
				"livro_id" => $CampoLivroId,
				"dtemprestimo" => '<input type="text" id="dtemprestimo" name="dtemprestimo" size="15" maxlength="10" value="'.trim(date('d/m/Y')).'">',
				"dtdevolucao" => '<input type="text" id="dtdevolucao" name="dtdevolucao" size="15" maxlength="10" value="'. $datasoma .'">'			
			);							
		} else {	//	dados para alteracao
			$CampoUsuario = $this->usuarios->usuariosShow("id= ". $view_show_registro[0]['usuario_id']);
			$CampoLivro = $this->livros->livrosShow("id= ". $view_show_registro[0]['livro_id']);
		
			$FW_formato = array(	
				"usuario_id" => '<input type="text" readonly="readonly" id="usuario_id" name="usuario_id" size="10" maxlength="10" value="'.$view_show_registro[0]['usuario_id'].'">'
								. " - " . $CampoUsuario[0]['nome'],
				"livro_id" => '<input type="text" readonly="readonly" id="livro_id" name="livro_id" size="10" maxlength="10" value="'.$view_show_registro[0]['livro_id'].'">'
								. " - " . $CampoLivro[0]['titulo'],
				"dtemprestimo" => '<input type="text" readonly="readonly" id="dtemprestimo" name="dtemprestimo" size="15" maxlength="10" value="'.trim(date('d/m/Y')).'">',
				"dtdevolucao" => '<input type="text" readonly="readonly" id="dtdevolucao" name="dtdevolucao" size="15" maxlength="10" value="'. $datasoma .'">',			
			);							
		
		}
		
		
		
		// $FW_formato = array();						
		
//	==================================================================================================================================================		
	include_once(FW_PATH_SERV . FW_SCAF_FORM . "scafForm.php");		//	CHAMA O SCAFFOLDING PARA FAZER O FORMULARIO.
?>