CREATE DATABASE IF NOT EXISTS ncut;
 
USE ncut;
--
-- Table structure for table `customers`
--
--  刪除  DROP TABLE `todolist` 

CREATE TABLE `ncut`.`phones` (
`pid` INT NOT NULL AUTO_INCREMENT ,
`name` VARCHAR( 32 )   DEFAULT NULL, 
`deptname` VARCHAR( 128 )  DEFAULT NULL,
`phone` VARCHAR( 12 )  NOT NULL ,
`title` VARCHAR( 128 )  NOT NULL ,
`mynote` VARCHAR( 256 ) DEFAULT NULL,
PRIMARY KEY ( `pid` )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT = 'phones';

-- {phone:2100 , name:"校長 趙敏勳" , deptname:"校長室", title:"校長" ,mynote:""},
-- 編號 pid 
-- 名稱 name
-- 單位名稱 deptname
-- 分機 phone
-- email email
-- 職稱 title 
-- 備註 mynote 
--
-- Dumping data for table `customers`
--
INSERT INTO `ncut`.`phones` (`pid`, `phone` ,`name`, `deptname`, `title`, `mynote`) VALUES
(NULL,"2100",	"趙校長"  , "校長室" ,"校長",""),
(NULL,"2101",	"賴副校長"  , "校長室" ,"副校長",""),
(NULL,"2110",	"李副校長"  , "校長室" ,"副校長",""),
(NULL,"2111",	"陳主任秘書"  , "秘書室" ,"主任秘書",""),
(NULL,"2112",	"吳專員"  , "秘書室" ,"專員","")


