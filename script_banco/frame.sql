-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tempo de Geração: Set 19, 2012 as 11:44 AM
-- Versão do Servidor: 5.5.8
-- Versão do PHP: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Banco de Dados: `frame`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `emprestimos`
--

CREATE TABLE IF NOT EXISTS `emprestimos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `livro_id` int(11) NOT NULL,
  `dtemprestimo` date NOT NULL,
  `dtdevolucao` date NOT NULL,
  `devolvidodia` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Extraindo dados da tabela `emprestimos`
--

INSERT INTO `emprestimos` (`id`, `usuario_id`, `livro_id`, `dtemprestimo`, `dtdevolucao`, `devolvidodia`) VALUES
(11, 5, 6, '2012-05-23', '2012-05-30', '2012-05-24'),
(12, 3, 6, '2012-05-30', '2012-06-06', '2012-05-30'),
(13, 5, 6, '2012-05-30', '2012-06-06', '2012-05-30');

-- --------------------------------------------------------

--
-- Estrutura da tabela `livros`
--

CREATE TABLE IF NOT EXISTS `livros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(70) NOT NULL,
  `subtitulo` varchar(70) NOT NULL,
  `autor` varchar(200) NOT NULL,
  `editora` varchar(50) NOT NULL,
  `isbn` varchar(30) NOT NULL,
  `resumo` text NOT NULL,
  `datacad` date NOT NULL,
  `tot_exemplares` int(11) NOT NULL,
  `exemplar_dispo` int(11) NOT NULL,
  `arquivo_demo` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Extraindo dados da tabela `livros`
--

INSERT INTO `livros` (`id`, `titulo`, `subtitulo`, `autor`, `editora`, `isbn`, `resumo`, `datacad`, `tot_exemplares`, `exemplar_dispo`, `arquivo_demo`) VALUES
(2, 'Rails 3 BÃ¡sico', 'Guia para a construÃ§Ã£o de aplicaÃ§Ãµes web com Ruby on Rails', 'Amilton Silva,\r\nTereza Dias', 'Novatec', '978-85-7522-265-2', 'Um excelente livro sobre ruby mostrando os fundamentos desta linguagem e sua aplicaÃ§Ã£o na construÃ§Ã£o de sites WEB.\r\nRuby on Rails 3: uma nova ferramenta para desenvolvimento WEB.', '2012-02-29', 7, 7, ''),
(6, 'PROGRAMAÃ‡ÃƒO PHP', 'PHP & MYSQL', 'Gina Paula,\r\nMaria Sena.', 'INOVAÃ‡ÃƒO', '1234567890', 'Este livro Ã© uma introduÃ§Ã£o de PHP!!! aaaaaaaaaaaaaaaaaaaaaaaaa\r\naaaaaaaaaaaaaaaaaaaaaaaaaa\r\naaaaaaaaaaaaaaaaaaaaaaaaaa', '2013-04-27', 10, 10, ''),
(7, 'teste', 'eeeeeeeeeee', 'Mariana Ribeiro.', '34123', '132413', '12314324123424213123421432\r\ndsfadsdsfd\r\ndsafasdfdsaf\r\nadsfasd', '1979-03-21', 13, 13, '');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datacad` date NOT NULL,
  `nome` varchar(70) NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `sexo` varchar(1) NOT NULL,
  `endereco` varchar(70) NOT NULL,
  `bairro` varchar(70) NOT NULL,
  `complemento` varchar(70) NOT NULL,
  `cidade` varchar(70) NOT NULL,
  `estado` varchar(2) NOT NULL,
  `CEP` varchar(9) NOT NULL,
  `telcomercial` varchar(13) NOT NULL,
  `telresidencial` varchar(13) NOT NULL,
  `celular` varchar(13) NOT NULL,
  `obs` text NOT NULL,
  `pessoa` varchar(1) NOT NULL,
  `bloqueado` varchar(1) NOT NULL,
  `email` varchar(70) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `datacad`, `nome`, `cpf`, `sexo`, `endereco`, `bairro`, `complemento`, `cidade`, `estado`, `CEP`, `telcomercial`, `telresidencial`, `celular`, `obs`, `pessoa`, `bloqueado`, `email`) VALUES
(3, '2012-04-26', 'Gabi Oliveira', '', 'F', 'Rua HilÃ¡rio Garcia, 23', 'Centro', '', 'Ouro Preto', 'AC', '12300-000', '(12)1234-4321', '(12)0000-9999', '(12)1111-2345', '', '', 'N', 'gabi@yahoo.com.br'),
(5, '2012-05-04', 'Edimar', '', 'M', 'Rua bahia, 25', 'Centro', '', 'JacareÃ­', 'AC', '12300-000', '(12)1234-1234', '(12)1234-1111', '(12)1234-2222', '', '', 'N', 'ed@nomefantasia.com');
