<?php	/*		BLOCK: CABCAMPO - CADASTRO 		APRESENTA DADOS DE UMA TABELA DO BANDO DE DADOS DE FORMA A POSSIBILITAR CONSULTA, ALTERA��O E EXCLUS�O		PARAMETROS:		1 - FW_campos   = ARRAY COM OS CABECALHOS E NOMES DAS COLUNAS 	*/	// CODIGO PARA EVITAR EXECUCAO DE BLOCOS SEM PASSAR PELA PAGINA INICIAL	if (!defined('FW_RAIZ'))		die("ERRO DE REDIRECIONAMENTO");	include_once($_SERVER["DOCUMENT_ROOT"]  . FW_RAIZ . "system/checkBlock.php");// -----------------------------------------------------------------------------------------------------------------------	$FW_campos = array(	"ID" => "id",					"DATA DO CADASTRO" => "datacad"	,
					"TITULO" => "titulo",
					"SUB-TITULO" => "subtitulo",
					"AUTOR(ES)" => "autor",
					"EDITORA" => "editora",
					"ISBN" => "isbn",					"TOTAL DE EXEMPLARES" => "tot_exemplares",					"EXEMPLARES DISPONIVEIS" => "exemplar_dispo",
					"RESUMO" => "resumo" 					// "ARQUIVO" => "arquivo_demo"
);		 //	CABE�ALHOS E CAMPOS DA TABELA DO BANCO DE DADOS					?>