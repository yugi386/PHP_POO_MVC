<?php /*	
==========================================================================	
|	GERADOR DE FORMULARIO PARA O CADASTRO
==========================================================================	
*/
// CODIGO PARA EVITAR EXECUCAO DE BLOCOS SEM PASSAR PELA PAGINA INICIAL
	if (!defined('FW_RAIZ'))
		die("ERRO DE REDIRECIONAMENTO");
	include_once($_SERVER["DOCUMENT_ROOT"]  . FW_RAIZ . "system/checkBlock.php");
	
//	==================================================================================================================================================

		$FW_tabela = "usuarios";											//	NOME DA TABELA DO BANCO DE DADOS
		$FW_tipoform = "post";												//	METODO DE ENVIO POST OU GET

		// VETOR COM OS NOMES DAS COLUNAS DA TABELA E OS RESPECTIVOS CAMPOS
		global $start;
		include_once(FW_PATH_SERV . FW_RAIZ. FW_VIEWS . $start->_controller ."/blocks/cabCampo.php");	//	INCLUI O ARQUIVO COM CABEÇALHOS E CAMPOS

		// PARA EXCLUIR ALGUM CAMPO DA EDIÇÃO DO FORMULÁRIO
		$excluir = array();
		$excluir = array("id");	//	por padrao deve ser excluído
		$FW_campos = $start->ExcluirCampo($FW_campos, $excluir );
		
		// PARA INCLUIR ALGUM CAMPO USE:
		$incluir = array (	"OBS" => "obs"
					);
		
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
							$FW_formato = array(	
								"sexo"				=> '<select name="sexo" id="sexo">
														<option value="M" selected>Masculino
														<option value="F" >Feminino
														</select>',
								"pessoa"			=> '<input type="radio" id="pessoa" name="pessoa" value="F" checked> Fisica <br> 
														<input type="radio" id="pessoa" name="pessoa" value="J"> Juridica',
								"bloqueado"			=>	'<input type="checkbox" id="bloqueado" name="bloqueado" value="$Vbloqueado"> Usuario bloqueado?',
								"sexo2"				=> '<select name="sexo2" id="sexo2">
														<option value="M" selected>Masculino
														<option value="F">Feminino
														</select>',
								"obs" => '<textarea rows="8" cols="50" id="obs" name="obs" 
								onblur=Caracteres(this,400) onkeydown=Caracteres(this,400) onkeyup=Caracteres(this,400)>$Vobs</textarea>'.
								'<br><font class="classe">Limite: 500 caracteres',
								"estado" => '<select name="estado" id = "estado">
											<option value="AC" selected>Acre
											<option value="AL">Alagoas
											<option value="AP">Amapa
											<option value="AM">Amazonas
											<option value="BA">Bahia
											<option value="CE">Ceara
											<option value="DF">Distrito Federal
											<option value="ES">Espirito Santo
											<option value="GO">Goias
											<option value="MA">Maranhao
											<option value="MS">Mato Grosso do Sul
											<option value="MG">Minas Gerais
											<option value="PA">Para
											<option value="PB">Paraiba
											<option value="PR">Parana
											<option value="PE">Pernanbuco
											<option value="PI">Piaui
											<option value="RJ">Rio de Janeiro
											<option value="RN">Rio Grande do Norte
											<option value="RS">Rio Grande do Sul
											<option value="RO">Rondonia
											<option value="RR">Roraima
											<option value="SC">Santa Catarina
											<option value="SP">Sao Paulo
											<option value="SE">Sergipe
											<option value="TO">Tocantins
											</select>',
							);
		// $FW_formato = array();						
		
//	==================================================================================================================================================		
	include_once(FW_PATH_SERV . FW_SCAF_FORM . "scafForm.php");		//	CHAMA O SCAFFOLDING PARA FAZER O FORMULARIO.
?>