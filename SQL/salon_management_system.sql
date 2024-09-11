-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 06, 2024 at 06:50 AM
-- Server version: 5.7.31
-- PHP Version: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `salon_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `currency`
--

DROP TABLE IF EXISTS `currency`;
CREATE TABLE IF NOT EXISTS `currency` (
  `ss_no` int(11) NOT NULL AUTO_INCREMENT,
  `ss_currency` varchar(3) NOT NULL,
  PRIMARY KEY (`ss_no`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=koi8r;

--
-- Dumping data for table `currency`
--

INSERT INTO `currency` (`ss_no`, `ss_currency`) VALUES
(1, 'RS');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
CREATE TABLE IF NOT EXISTS `customer` (
  `c_no` int(11) NOT NULL AUTO_INCREMENT,
  `c_name` varchar(30) NOT NULL,
  `c_mobile` varchar(11) NOT NULL,
  `c_birthday` date NOT NULL,
  `c_note` text NOT NULL,
  `c_cat` varchar(15) NOT NULL,
  `c_reg_date` date DEFAULT NULL,
  `c_is_del` varchar(15) DEFAULT NULL,
  `c_del_by` varchar(255) DEFAULT NULL,
  `c_del_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`c_no`)
) ENGINE=MyISAM AUTO_INCREMENT=76 DEFAULT CHARSET=koi8r;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`c_no`, `c_name`, `c_mobile`, `c_birthday`, `c_note`, `c_cat`, `c_reg_date`, `c_is_del`, `c_del_by`, `c_del_time`) VALUES
(4, 'sam', '03000000', '2024-07-01', 'for droping', 'F', NULL, '1', NULL, NULL),
(13, 'samra1', '030012548', '2024-07-04', 'for droping', 'N', '2024-07-03', NULL, NULL, NULL),
(1, 'samra', '03001212123', '2024-07-03', 'for droping', 'S', '2024-07-03', NULL, NULL, NULL),
(16, 'bhabi', '03001212123', '2024-07-04', 'for droping jjjjjjjjjjjjjj', 'B', '2024-07-03', NULL, NULL, NULL),
(29, 'salary ', '03000', '2024-07-05', 'for droping', 'N', '2024-07-04', '1', NULL, NULL),
(18, 'jjjjjj', '0300000', '2023-01-11', 'testing ', 'F', '2024-07-03', NULL, NULL, NULL),
(2, 'wajiha', '03001212123', '2024-07-01', 'this is last', 'S', '2024-07-03', '1', NULL, NULL),
(20, 'iqra', '02222', '2024-07-04', 'llllllllllllll', 'B', '2024-07-03', '1', NULL, NULL),
(21, 'f', '03001212123', '2024-07-03', 'for droping', 'N', '2024-07-03', '1', NULL, NULL),
(22, 'samr', '03002123', '2024-07-04', 'for droping', 'S', '2024-07-03', '1', NULL, NULL),
(17, 'huda', '031212123', '2024-07-03', 'for droping', 'N', '2024-07-03', NULL, NULL, NULL),
(3, 'Malika', '03001212123', '2024-07-03', 'for droping', 'F', '2024-07-04', NULL, NULL, NULL),
(31, 'samra', '03001212123', '2024-07-23', 'vvvvvvvvvvvv', 'S', '2024-07-23', NULL, NULL, NULL),
(32, 'samra', '03001212123', '2024-07-23', 'vvvvvvvvvvvv', 'S', '2024-07-23', NULL, NULL, NULL),
(33, 'samra', '03001212111', '2024-07-19', '', 'R', NULL, '1', NULL, NULL),
(56, 'Nimra', '0325458756', '2024-07-24', '', 'S', NULL, NULL, NULL, NULL),
(57, 'nosheen', '03004387750', '2024-01-03', 'this is good ', 'S', NULL, NULL, NULL, NULL),
(58, 'yasira', '03004387714', '2024-07-24', 'this is good ', 'S', NULL, NULL, NULL, NULL),
(59, 'hareem', '0328736500', '2023-11-29', 'this is good ', 'S', '2024-07-25', '1', NULL, NULL),
(60, 'hoorain', '03055555555', '2024-07-13', 'this is good ', 'R', '2024-07-25', NULL, NULL, NULL),
(61, 'rubab', '03455545545', '2024-04-10', 'dfdsfgdf g fgsdf ', 'R', '2024-07-26', NULL, NULL, NULL),
(62, 'Amiiii', '03121221212', '2024-07-02', 'this is practice', 'N', '2024-07-26', NULL, NULL, NULL),
(63, 'habiba', '03055555458', '2024-07-05', 'i am a first customer', 'R', '2024-07-26', '1', NULL, NULL),
(64, 'Sawara', '03000458796', '2024-04-03', 'dfdffggg', 'S', '2024-07-31', '1', NULL, NULL),
(65, 'Muniza king', '03123456789', '2024-02-29', 'i am a new customer ', 'N', '2024-08-01', NULL, NULL, NULL),
(66, 'fatima', '03225697455', '2024-07-02', 'this is king ', 'B', '2024-08-01', NULL, NULL, NULL),
(67, 'shahida', '03000000000', '2019-01-26', 'this is practice', 'N', '2024-08-26', NULL, NULL, NULL),
(68, 'kiran', '03000000458', '2024-02-05', 'this is practice only', 'F', '2024-08-26', NULL, NULL, NULL),
(69, 'maida', '03567895225', '2002-06-04', 'this is only practice', 'F', '2024-08-28', NULL, NULL, NULL),
(70, 'mareeb', '03000004444', '2002-05-08', 'this is a practice', 'S', '2024-08-28', NULL, NULL, NULL),
(71, 'naseem', '03000347999', '2024-07-30', 'this is panga', 'R', '2024-08-28', NULL, NULL, NULL),
(72, 'sabia', '03000009876', '2024-07-30', 'this is only', 'N', '2024-08-28', NULL, NULL, NULL),
(73, 'eman', '03000765444', '2024-03-04', 'this', 'S', '2024-08-28', NULL, NULL, NULL),
(74, 'mafia', '23456778999', '2009-02-02', 'thyybgfff', 'B', '2024-08-28', NULL, NULL, NULL),
(75, 'shamza', '23456778955', '2024-08-07', 'ggggg', 'R', '2024-08-29', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `expense`
--

DROP TABLE IF EXISTS `expense`;
CREATE TABLE IF NOT EXISTS `expense` (
  `e_no` int(11) NOT NULL AUTO_INCREMENT,
  `e_exp_date` date NOT NULL,
  `e_ex_no` int(11) NOT NULL,
  `e_description` varchar(255) NOT NULL,
  `e_price` int(11) NOT NULL,
  `e_qty` int(11) DEFAULT NULL,
  `e_memo` text NOT NULL,
  `e_amount` int(11) NOT NULL,
  PRIMARY KEY (`e_no`)
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=koi8r;

--
-- Dumping data for table `expense`
--

INSERT INTO `expense` (`e_no`, `e_exp_date`, `e_ex_no`, `e_description`, `e_price`, `e_qty`, `e_memo`, `e_amount`) VALUES
(2, '2024-07-11', 3, 'staff lunch', 5000, 3, 'we take lunch all staff ', 15000),
(46, '2024-07-30', 3, 'aj ka lunch shan krwai gi', 750, 4, 'only pizza dominots ka bs', 3000),
(53, '2024-08-01', 26, 'gass bill', 650, 1, 'i have payed bill', 650),
(59, '2024-08-26', 3, 'lunch', 1500, 2, 'jjjj', 3000),
(44, '2024-07-03', 3, 'kuuu', 1200, 2, 'ffffffffff', 2400),
(45, '2024-07-04', 3, 'aj ka lunch samra krwai gi', 500, 3, 'bs samra ap ny her aik person 500 ka kahna kahwan hy pass nhi lyny ok ap wasy hi ameer hoo ', 1500),
(52, '2024-07-08', 3, 'gas bill', 1500, 4, 'kkkkkk', 6000),
(47, '2024-07-01', 3, 'll', 5000, 4, 'kkkkk', 1200),
(55, '2024-08-15', 4, 'Gass Bill pay', 5000, 1, 'kfdjlfjdl', 5000),
(56, '2024-08-21', 3, 'billl', 15000, 1, 'ddfdffd', 15000),
(57, '2024-08-02', 3, 'bill', 1500, 2, 'this is payed', 3000),
(58, '2024-08-02', 3, 'bill', 1500, 2, 'this is payed', 3000),
(60, '2024-09-02', 3, 'hhh', 1200, 1, 'ffffffffff', 1200),
(61, '2024-09-01', 3, 'staff lunch', 25000, 1, 'all staff lunch', 25000),
(62, '2024-09-03', 3, 'Staff lunch', 1200, 2, 'only practice', 2400),
(63, '2024-09-03', 4, 'Staff lunch', 1200, 2, 'ssss', 2400),
(69, '2024-09-06', 28, 'i payed gass bill', 12000, 2, 'this is payd', 24000),
(65, '2024-09-03', 3, 'staff lunch', 4500, 8, 'jjjjjjjj', 36000),
(67, '2024-09-04', 4, 'staff lunch', 4500, 5, 'kkkkkkkkk', 22500),
(68, '2024-09-05', 26, 'Staff lunch', 4570, 1, 'jjjjjjjjjjjj', 4570);

-- --------------------------------------------------------

--
-- Table structure for table `expense_cat`
--

DROP TABLE IF EXISTS `expense_cat`;
CREATE TABLE IF NOT EXISTS `expense_cat` (
  `ex_no` int(11) NOT NULL AUTO_INCREMENT,
  `ex_cat_name` varchar(255) NOT NULL,
  PRIMARY KEY (`ex_no`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=koi8r;

--
-- Dumping data for table `expense_cat`
--

INSERT INTO `expense_cat` (`ex_no`, `ex_cat_name`) VALUES
(28, 'Gas'),
(4, 'Others'),
(26, 'bill ele'),
(31, 'Eletricity');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE IF NOT EXISTS `feedback` (
  `f_no` int(11) NOT NULL AUTO_INCREMENT,
  `f_c_no` int(11) NOT NULL,
  `f_o_no` int(11) NOT NULL,
  `f_rate1` varchar(5) DEFAULT NULL,
  `f_review1` text,
  `f_rate2` varchar(5) DEFAULT NULL,
  `f_review2` text,
  `f_rate3` varchar(5) DEFAULT NULL,
  `f_review3` text,
  `f_rate4` varchar(5) DEFAULT NULL,
  `f_review4` text,
  `f_rate5` varchar(5) DEFAULT NULL,
  `f_review5` text,
  `f_rate6` varchar(5) DEFAULT NULL,
  `f_review6` text,
  `f_rate7` varchar(5) DEFAULT NULL,
  `f_review7` text,
  `f_rate8` varchar(5) DEFAULT NULL,
  `f_review8` text,
  `f_rate9` varchar(5) DEFAULT NULL,
  `f_review9` text,
  `f_rate10` varchar(5) DEFAULT NULL,
  `f_review10` text,
  `f_date` date DEFAULT NULL,
  PRIMARY KEY (`f_no`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=koi8r;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`f_no`, `f_c_no`, `f_o_no`, `f_rate1`, `f_review1`, `f_rate2`, `f_review2`, `f_rate3`, `f_review3`, `f_rate4`, `f_review4`, `f_rate5`, `f_review5`, `f_rate6`, `f_review6`, `f_rate7`, `f_review7`, `f_rate8`, `f_review8`, `f_rate9`, `f_review9`, `f_rate10`, `f_review10`, `f_date`) VALUES
(1, 1, 2, '4', 'this item is a good performance ', '4', 'this item is a good performance ', '2', 'this item is a good performance ', '1', 'this item is a good performance ', '5', 'this item is a good performance ', '3', 'this item is a good performance ', '5', 'this item is a good performance ', '4', 'this item is a good performance ', '3', 'this item is a good performance ', '4', 'this item is a good performance ', '2024-07-10'),
(2, 2, 2, '4', 'this item is a good performance ', '4', 'this item is a good performance ', '2', 'this item is a good performance ', '4', 'this item is a good performance ', '5', 'this item is a good performance ', '3', 'this item is a good performance ', '5', 'this item is a good performance ', '4', 'this item is a good performance ', '3', 'this item is a good performance ', '4', 'this item is a good performance ', '2024-07-10'),
(7, 15, 15, '4', 'dffffffffffffffffffffffffffffffffffffffffff', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-07-25'),
(18, 59, 64, '5', 'this is good services', '3', 'this is good services', '4', 'this is good services', '4', 'this is good services', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '2024-08-01'),
(9, 17, 17, '4', 'dffffffffffffffffffffffffffffffffffffffffff', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-07-07'),
(16, 61, 49, '5', 'this is a good dd', '5', 'this is a good dd', '5', 'this is a good dd', '5', 'this is a good dd', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '2024-07-27'),
(15, 59, 40, '3', '', '3', 'ssssffff', '5', '', '3', '', '3', '', '3', '', '3', '', '3', '', '3', '', '3', '', '2024-07-26'),
(17, 62, 50, '4', 'this is a good dd', '3', 'this is a good dd', '2', 'this is a good dd', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '2024-07-28'),
(19, 65, 65, '2', 'this is a practice only', '2', 'this is a practice only', '3', 'this is a practice only', '2', 'this is a practice only', '2', 'this is a practice only', '2', 'this is a practice only', '2', 'this is a practice only', '0', '', '0', '', '0', '', '2024-08-08'),
(20, 65, 66, '5', 'this is good', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '2024-08-09'),
(21, 67, 78, '3', 'this is only practice only', '5', 'this', '2', 'ya yasira kryn gi ', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '2024-08-26'),
(22, 67, 84, '4', '', '4', '', '4', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '2024-08-26'),
(23, 67, 106, '4', '', '4', '', '4', '', '2', '', '3', '', '0', '', '0', '', '0', '', '0', '', '0', '', '2024-09-01'),
(25, 57, 107, '4', 'ggggggg', '4', 'ggggggg', '4', 'ggggggg', '4', 'ggggggg', '4', 'ggggggg', '0', '', '0', '', '0', '', '0', '', '0', '', '2024-09-02'),
(28, 75, 109, '5', 'ggggggg', '5', 'ggggggggg', '4', 'ghhhhhhhh', '3', 'gggggggggg', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '2024-09-03'),
(29, 75, 109, '5', '', '4', '', '0', '', '3', '', '3', '', '3', '', '0', '', '3', '', '0', '', '0', '', '2024-09-01'),
(27, 59, 111, '5', 'gggggggx', '5', 'ddddddddddd', '5', 'ddddddddd', '2', 'dddddddd', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '2024-09-06'),
(30, 58, 113, '4', 'ddddddddddddd', '4', 'gffffffffffffffffff', '5', 'ffffffffffffffffffff', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '2024-09-07'),
(31, 58, 114, '5', 'ddddddddddddd', '5', 'gffffffffffffffffff', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '0', '', '2024-09-08');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
CREATE TABLE IF NOT EXISTS `login` (
  `l_no` int(11) NOT NULL AUTO_INCREMENT,
  `l_username` varchar(20) NOT NULL,
  `l_password` varchar(255) NOT NULL,
  PRIMARY KEY (`l_no`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`l_no`, `l_username`, `l_password`) VALUES
(11, 'Danish', '$2y$10$tNoddZ51BEn8/h43YrEOQuOIHjKldIKF5985Q6UNN1P6ySDSgBQB2'),
(12, 'sohail', '$2y$10$sisQiAxOyKfoEQcq7DTJaOIarTmAlXw2OH3rt6k2yJ2POhDXXX3fa'),
(13, 'hok', '$2y$10$F.0V1IL9Z4GSr97/GhLRPuYQiKgirc/D94fr.Keuvd9hdM4y98lpW');

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
CREATE TABLE IF NOT EXISTS `member` (
  `m_no` int(11) NOT NULL AUTO_INCREMENT,
  `m_fullname` varchar(30) NOT NULL,
  `m_name` varchar(20) NOT NULL,
  `m_password` varchar(255) NOT NULL,
  `m_role` int(11) DEFAULT NULL,
  `m_mail` varchar(30) NOT NULL,
  PRIMARY KEY (`m_no`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`m_no`, `m_fullname`, `m_name`, `m_password`, `m_role`, `m_mail`) VALUES
(19, 'yasira', 'yasira', 'WU5QT1RVc0xIeU4wbTBJb0crVG1tUT09OjpZijlcUofdIopE4KICEZvV', 0, 'aliawanbrosis11@gmail.com'),
(25, 'sohail', 'sohail', 'Kzh5YXd3SnV3aGhwWnBPR1laUEhjZz09Ojq8KLr/9g848Z7S0eLFcuQi', 0, 'aliawanbrosis@gmail.comm'),
(22, 'yasria', 'Danish ', 'RG96ZXY5UHE2dTB6WWpEUVhFaDNhZz09Ojp/8clsS1zezyXNJCg/KHiz', 0, 'aliawanbrosis@gmail.com'),
(26, 'hokmafttc', 'hok', 'RHV4L0ZJSStmK3VvR0Fhc2hvWWFJUT09OjqDsLkicCQdDE4kCofgB716', 1, 'hokma123@gmail.com'),
(27, 'king', 'king', 'eDB5K2ZlUDdCOEdlcm9UdnppdmdKUT09Ojr7ylAPHbEFDL7f0M0wcOQP', 0, 'sohailpahat@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order` (
  `o_no` int(11) NOT NULL AUTO_INCREMENT,
  `o_c_no` int(11) NOT NULL,
  `o_date` date NOT NULL,
  `o_memo` text NOT NULL,
  `o_amount` decimal(10,0) DEFAULT NULL,
  `o_service_count` int(11) DEFAULT NULL,
  `o_pymt_method` varchar(15) DEFAULT NULL,
  `o_pro_no` int(11) DEFAULT NULL,
  PRIMARY KEY (`o_no`)
) ENGINE=MyISAM AUTO_INCREMENT=115 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`o_no`, `o_c_no`, `o_date`, `o_memo`, `o_amount`, `o_service_count`, `o_pymt_method`, `o_pro_no`) VALUES
(107, 57, '2024-09-02', 'mkkkkkkkkkkkkkk', '8200', NULL, 'cash', 84),
(108, 75, '2024-09-01', 'ggggggggggggggggggg', '16390', NULL, 'cash', 84),
(104, 60, '2024-08-30', 'hhhhhhhhhhhhhhhhh', '4850', NULL, 'card', 84),
(105, 67, '2024-08-31', 'jjjjjjjjjjjjjjjjj', '8190', NULL, 'card', 84),
(101, 17, '2024-08-22', 'fffffffffffffff', '2900', NULL, 'cash', 0),
(109, 75, '2024-09-03', 'only practice', '5850', NULL, 'cash', 0),
(112, 32, '2024-09-07', 'ddddd', '7000', NULL, 'cash', 0),
(111, 59, '2024-09-06', 'ssssssss', '3200', NULL, 'cash', 0),
(113, 58, '2024-09-07', 'dddd', '4000', NULL, 'card', 0),
(114, 58, '2024-09-08', 'ddd', '3500', NULL, 'cash', 0);

-- --------------------------------------------------------

--
-- Table structure for table `orderservice`
--

DROP TABLE IF EXISTS `orderservice`;
CREATE TABLE IF NOT EXISTS `orderservice` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `s_o_no` int(11) NOT NULL,
  `s_order_no` int(11) NOT NULL,
  `s_cat` varchar(1) DEFAULT NULL,
  `s_si_no` int(11) DEFAULT NULL,
  `s_qty` int(11) DEFAULT NULL,
  `s_price` int(11) DEFAULT NULL,
  PRIMARY KEY (`s_no`)
) ENGINE=MyISAM AUTO_INCREMENT=344 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `orderservice`
--

INSERT INTO `orderservice` (`s_no`, `s_o_no`, `s_order_no`, `s_cat`, `s_si_no`, `s_qty`, `s_price`) VALUES
(299, 104, 60, '0', 28, 1, 1350),
(287, 101, 17, '0', 34, 1, 800),
(288, 101, 17, '0', 37, 1, 300),
(289, 101, 17, '0', 40, 1, 1000),
(290, 101, 17, '0', 43, 1, 800),
(298, 104, 60, '0', 36, 1, 1500),
(320, 108, 75, '0', 54, 1, 7500),
(319, 108, 75, '0', 28, 1, 1350),
(297, 104, 60, '0', 45, 1, 2000),
(318, 108, 75, '0', 36, 1, 1500),
(300, 105, 67, '0', 45, 1, 2000),
(301, 105, 67, '0', 36, 1, 1500),
(302, 105, 67, '0', 38, 1, 800),
(303, 105, 67, '0', 28, 1, 1350),
(304, 105, 67, '0', 46, 1, 2000),
(305, 105, 67, '0', 55, 1, 540),
(317, 108, 75, '0', 34, 1, 1000),
(316, 108, 75, '0', 45, 1, 2000),
(311, 107, 57, '0', 45, 1, 2000),
(312, 107, 57, '0', 36, 1, 1500),
(313, 107, 57, '0', 39, 1, 2000),
(314, 107, 57, '0', 66, 1, 1500),
(315, 107, 57, '0', 67, 1, 1200),
(321, 108, 75, '0', 55, 1, 540),
(322, 108, 75, '0', 64, 1, 1000),
(323, 108, 75, '0', 66, 1, 1500),
(324, 109, 75, '0', 45, 1, 2000),
(325, 109, 75, '0', 36, 1, 1500),
(326, 109, 75, '0', 28, 1, 1350),
(327, 109, 75, '0', 64, 1, 1000),
(337, 112, 32, '0', 39, 1, 2000),
(336, 112, 32, '0', 36, 1, 1500),
(335, 112, 32, '0', 45, 1, 2000),
(331, 111, 59, '0', 34, 1, 1000),
(332, 111, 59, '0', 37, 1, 300),
(333, 111, 59, '0', 29, 1, 900),
(334, 111, 59, '0', 64, 1, 1000),
(338, 112, 32, '0', 66, 1, 1500),
(339, 113, 58, '0', 36, 1, 1500),
(340, 113, 58, '0', 39, 1, 2000),
(341, 113, 58, '0', 63, 1, 500),
(342, 114, 58, '0', 45, 1, 2000),
(343, 114, 58, '0', 36, 1, 1500);

-- --------------------------------------------------------

--
-- Table structure for table `promotion`
--

DROP TABLE IF EXISTS `promotion`;
CREATE TABLE IF NOT EXISTS `promotion` (
  `p_no` int(11) NOT NULL AUTO_INCREMENT,
  `p_name` varchar(30) NOT NULL,
  `p_s_date` date NOT NULL,
  `p_e_date` date NOT NULL,
  `p_s_items` int(11) DEFAULT NULL,
  `p_rate_price` varchar(5) NOT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`p_no`)
) ENGINE=MyISAM AUTO_INCREMENT=87 DEFAULT CHARSET=koi8u;

--
-- Dumping data for table `promotion`
--

INSERT INTO `promotion` (`p_no`, `p_name`, `p_s_date`, `p_e_date`, `p_s_items`, `p_rate_price`, `status`) VALUES
(81, 'new offer 02', '2024-08-25', '2024-08-27', 7, '20', 0),
(80, 'Eid offer', '2024-08-26', '2024-08-28', 5, '15', 0),
(83, 'New offer only check', '2024-08-28', '2024-08-31', 10, '20', 0),
(84, 'only check ', '2024-08-30', '2024-09-03', 4, '10', 0),
(85, 'only check ', '2024-09-01', '2024-09-03', 7, '15', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pro_service`
--

DROP TABLE IF EXISTS `pro_service`;
CREATE TABLE IF NOT EXISTS `pro_service` (
  `pro_s_no` int(11) NOT NULL AUTO_INCREMENT,
  `pro_p_no` int(11) NOT NULL,
  `pro_s_cat` varchar(1) NOT NULL,
  `pro_si_no` int(11) NOT NULL,
  `pro_s_price` int(11) NOT NULL,
  PRIMARY KEY (`pro_s_no`)
) ENGINE=MyISAM AUTO_INCREMENT=259 DEFAULT CHARSET=koi8u;

--
-- Dumping data for table `pro_service`
--

INSERT INTO `pro_service` (`pro_s_no`, `pro_p_no`, `pro_s_cat`, `pro_si_no`, `pro_s_price`) VALUES
(258, 81, 'P', 30, 1500),
(257, 81, 'P', 29, 1000),
(256, 81, 'N', 37, 300),
(199, 80, 'M', 55, 459),
(198, 80, 'P', 31, 170),
(197, 80, 'P', 30, 1275),
(196, 80, 'P', 29, 850),
(195, 80, 'P', 28, 1275),
(255, 81, 'N', 36, 1500),
(254, 81, 'N', 35, 2000),
(253, 81, 'N', 34, 1000),
(252, 81, 'N', 45, 2000),
(217, 83, 'N', 45, 1600),
(218, 83, 'N', 34, 800),
(219, 83, 'N', 35, 1600),
(220, 83, 'P', 28, 1200),
(221, 83, 'P', 29, 800),
(222, 83, 'P', 30, 1200),
(223, 83, 'E', 54, 6000),
(224, 83, 'E', 50, 10400),
(225, 83, 'E', 52, 4000),
(226, 83, 'M', 55, 432),
(227, 84, 'P', 28, 1350),
(228, 84, 'P', 29, 900),
(229, 84, 'P', 30, 1350),
(230, 84, 'P', 31, 180),
(244, 85, 'S', 67, 1200),
(243, 85, 'T', 64, 1000),
(242, 85, 'M', 55, 540),
(241, 85, 'E', 54, 7500),
(240, 85, 'P', 29, 1000),
(239, 85, 'N', 34, 1000),
(238, 85, 'N', 45, 2000);

-- --------------------------------------------------------

--
-- Table structure for table `service_item`
--

DROP TABLE IF EXISTS `service_item`;
CREATE TABLE IF NOT EXISTS `service_item` (
  `si_no` int(11) NOT NULL AUTO_INCREMENT,
  `si_cat` varchar(255) DEFAULT NULL,
  `si_service_name` varchar(50) NOT NULL,
  `si_price` int(11) NOT NULL,
  `si_promotion_price` int(11) DEFAULT NULL,
  `si_image1` varchar(255) DEFAULT NULL,
  `si_image2` varchar(255) DEFAULT NULL,
  `si_image3` varchar(255) DEFAULT NULL,
  `si_ext1` varchar(255) DEFAULT NULL,
  `si_ext2` varchar(255) DEFAULT NULL,
  `si_ext3` varchar(255) DEFAULT NULL,
  `si_description` text NOT NULL,
  `si_show` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`si_no`)
) ENGINE=MyISAM AUTO_INCREMENT=68 DEFAULT CHARSET=koi8r;

--
-- Dumping data for table `service_item`
--

INSERT INTO `service_item` (`si_no`, `si_cat`, `si_service_name`, `si_price`, `si_promotion_price`, `si_image1`, `si_image2`, `si_image3`, `si_ext1`, `si_ext2`, `si_ext3`, `si_description`, `si_show`) VALUES
(54, 'E', 'Lifting (50min)', 7500, NULL, 'uploads/e_pic02.jpg', 'uploads/e_pic03.jpg', NULL, NULL, NULL, NULL, 'this is a good performance', 0),
(50, 'E', '3D Volume', 13000, NULL, 'uploads/m_pic02.jpg', 'uploads/n_pic01.jpg', 'uploads/n_pic03.jpg', NULL, NULL, NULL, 'this is a good performace', 0),
(28, 'P', 'Basic care', 1500, NULL, 'uploads/n_pic03.jpg', NULL, NULL, NULL, NULL, NULL, 'this is so good performance item', 0),
(55, 'M', 'Foot & calf', 540, NULL, 'uploads/m_pic02.jpg', 'uploads/m_pic01.jpg', NULL, NULL, NULL, NULL, '', 0),
(45, 'N', 'Keratin care', 2000, 2000, 'uploads/p_pic03.jpg', 'uploads/n_pic02.jpg', 'uploads/n_pic04.jpg', NULL, NULL, NULL, 'this is services is so beautful and no side effect and alwais using so never fully', 0),
(29, 'P', 'Bleach/Scrub/Massage', 1000, NULL, 'uploads/p_pic01.jpg', NULL, NULL, NULL, NULL, NULL, 'this is so good performance item\r\n', 0),
(30, 'P', 'Spa(soak,scrup,massage)', 1500, NULL, 'uploads/p_pic02.jpg', NULL, NULL, NULL, NULL, NULL, 'this is so good performance item', 0),
(31, 'P', 'Callus remove', 200, NULL, 'uploads/p_pic03.jpg', NULL, NULL, NULL, NULL, NULL, 'this is so good performance item', 0),
(52, 'E', 'Hybrid', 5000, NULL, 'uploads/n_pic01.jpg', 'uploads/n_pic01.jpg', 'uploads/p_pic04.jpg', NULL, NULL, NULL, 'this is a good ', 0),
(33, 'P', 'Fungus Care', 1000, NULL, 'uploads/e_pic01.jpg', NULL, NULL, NULL, NULL, NULL, 'this is so good performance item', 0),
(34, 'N', 'Halal polish', 1000, 1000, 'uploads/e_pic02.jpg', NULL, NULL, NULL, NULL, NULL, 'this is so good performance item', 0),
(35, 'N', 'Gel Polish', 2000, 2000, 'uploads/e_pic03.jpg', NULL, NULL, NULL, NULL, NULL, 'this is so good performance item', 0),
(36, 'N', 'Volum base /Top', 1500, 1500, 'uploads/m_pic01.jpg', NULL, NULL, NULL, NULL, NULL, 'this is so good performance item', 0),
(37, 'N', 'Add Color', 300, 300, 'uploads/m_pic02.jpg', NULL, NULL, NULL, NULL, NULL, 'this is so good performance item\r\n', 0),
(38, 'N', 'Trend Art (1ea)', 800, NULL, 'uploads/m_pic04.jpg', NULL, NULL, NULL, NULL, NULL, 'this is so good performance item', 0),
(39, 'N', 'French / Gradation', 2000, NULL, 'uploads/t_pic01.jpg', NULL, NULL, NULL, NULL, NULL, 'this is so good performance item', 0),
(40, 'N', 'Stone Art (1ea)', 1000, NULL, 'uploads/t_pic02.jpg', NULL, NULL, NULL, NULL, NULL, 'this is so good performance item', 0),
(41, 'N', 'Acrylic/Gel (color)', 800, NULL, 'uploads/t_pic03.jpg', NULL, NULL, NULL, NULL, NULL, 'this is so good performance item', 0),
(42, 'N', 'Refill (with color)', 3500, NULL, 'uploads/t_pic04.jpg', NULL, NULL, NULL, NULL, NULL, 'this is so good performance item', 0),
(43, 'N', 'Repair(each)', 800, NULL, 'uploads/s_pic01.jpg', NULL, NULL, NULL, NULL, NULL, 'this is so good performance item\r\n', 0),
(44, 'N', 'Remove', 1500, NULL, 'uploads/s_pic02.jpeg', NULL, NULL, NULL, NULL, NULL, 'this is so good performance item', 0),
(46, 'E', 'Classic Basic 100% Fully ', 2000, NULL, 'uploads/n_pic04.jpg', 'uploads/p_pic01.jpg', 'uploads/p_pic04.jpg', NULL, NULL, NULL, 'jdsfjsdl jsfkjsd fkl thek klfjljdkl ', 0),
(47, 'E', 'Hybrid  lash', 10000, 0, 'uploads/t_pic01.jpg', NULL, NULL, NULL, NULL, NULL, 'ffdfdff ff f  ff', 0),
(53, 'E', 'volume', 6000, 0, 'uploads/p_pic04.jpg', 'uploads/m_pic01.jpg', 'uploads/n_pic01.jpg', NULL, NULL, NULL, '', 0),
(56, 'P', 'foot nail color', 1500, NULL, 'uploads/e_pic02.jpg', 'uploads/n_pic01.jpg', 'uploads/n_pic04.jpg', NULL, NULL, NULL, 'this is good ', 0),
(51, 'E', 'Classic', 4000, 0, 'uploads/e_pic02.jpg', 'uploads/n_pic04.jpg', 'uploads/s_pic02.jpeg', NULL, NULL, NULL, 'this item is so beautiful ', 0),
(57, 'N', 'shine nail ', 1500, 0, 'uploads/fff.png', NULL, NULL, NULL, NULL, NULL, 'ddddddd', 0),
(58, 'M', 'Relaxing Oil Massage (30min)', 3000, 0, 'uploads/fff.png', 'uploads/employee-organization-or-company-worker-team-or-teamwork-success-together-staff-partnership-or-community-concept-success-businessman-businesswoman-colleague-high-five-for-winning-celebration-vector (1).jpg', NULL, NULL, NULL, NULL, 'this is only practice', 0),
(59, 'M', 'Aroma Deep tissue Massage(50min)', 3000, 0, 'uploads/e_pic02.jpg', 'uploads/p_pic04.jpg', NULL, NULL, NULL, NULL, 'this is only practice', 0),
(60, 'M', 'Extra time (10min)', 500, 0, 'uploads/e_pic01.jpg', 'uploads/m_pic02.jpg', NULL, NULL, NULL, NULL, 'this is only practice', 0),
(61, 'M', 'Relaxing Oil Massage, Back side (70min)', 4500, 0, 'uploads/m_pic02.jpg', NULL, NULL, NULL, NULL, NULL, 'this is only practice', 0),
(62, 'M', 'Aroma Deep Tissue Massage Back side (90min)', 3000, 0, 'uploads/m_pic01.jpg', NULL, NULL, NULL, NULL, NULL, 'this is only practice', 0),
(63, 'M', 'Extra time back side(10min)', 500, 0, 'uploads/m_pic01.jpg', NULL, NULL, NULL, NULL, NULL, 'this is only practice', 0),
(64, 'T', 'Relaxing Oil Massage', 1000, 0, 'uploads/m_pic02.jpg', NULL, NULL, NULL, NULL, NULL, 'this is only sale item', 0),
(65, 'T', 'Aroma Deep Tissue Massage', 4500, 0, 'uploads/t_pic02.jpg', NULL, NULL, NULL, NULL, NULL, 'this is only sale item', 0),
(66, 'S', 'Relaxing Oil ', 1500, 0, 'uploads/s_pic02.jpeg', NULL, NULL, NULL, NULL, NULL, 'this is only sale item', 0),
(67, 'S', 'Aroma Deep Tissue', 1200, 0, 'uploads/p_pic04.jpg', NULL, NULL, NULL, NULL, NULL, 'this is only sale item', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
