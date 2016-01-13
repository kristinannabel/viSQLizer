SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `visqlizer_storage` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `visqlizer_storage`;

DROP TABLE IF EXISTS `saved_queries`;
CREATE TABLE IF NOT EXISTS `saved_queries` (
`id` int(20) NOT NULL,
  `username` varchar(20) NOT NULL,
  `query` varchar(1000) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=40 ;

INSERT INTO `saved_queries` (`id`, `username`, `query`) VALUES
(1, 'testuser', 'SELECT * FROM user'),
(15, 'testuser', 'SELECT * FROM user WHERE userid < 3 ORDER BY userid DESC');


ALTER TABLE `saved_queries`
 ADD PRIMARY KEY (`id`);


ALTER TABLE `saved_queries`
MODIFY `id` int(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=40;

CREATE DATABASE IF NOT EXISTS `visqlizer` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `visqlizer`;

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
`userid` int(10) unsigned NOT NULL,
  `username` varchar(50) NOT NULL,
  `registered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

INSERT INTO `user` (`userid`, `username`, `registered`) VALUES
(1, 'Markus Brovold', '2014-09-18 08:11:27'),
(2, 'Kristin Annabel Folland', '2014-09-18 08:12:59'),
(3, 'Simen Kjaeraas', '2014-09-18 08:13:06'),
(4, 'Fredrik Johnsen', '2014-09-18 08:13:39');

DROP TABLE IF EXISTS `user_type`;
CREATE TABLE IF NOT EXISTS `user_type` (
  `user_type_id` int(11) NOT NULL,
  `user_type` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `user_type` (`user_type_id`, `user_type`) VALUES
(1, 'coba_admin'),
(2, 'business_manager'),
(3, 'business');


ALTER TABLE `user`
 ADD PRIMARY KEY (`userid`), ADD UNIQUE KEY `userid` (`userid`), ADD KEY `userid_2` (`userid`);

ALTER TABLE `user_type`
 ADD PRIMARY KEY (`user_type_id`);


ALTER TABLE `user`
MODIFY `userid` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;

DROP TABLE IF EXISTS `exam`;
CREATE TABLE `exam` (
  `course_code` varchar(10) NOT NULL,
  `student_no` int(6) NOT NULL,
  `year` int(4) NOT NULL,
  `score` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `course`;
CREATE TABLE `course` (
  `code` varchar(10) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `course` (`code`, `name`) VALUES
('IMT4003', 'Applied Computer Science Project'),
('IMT4032', 'Usability and Human Factors in Interaction Design');



INSERT INTO `exam` (`course_code`, `student_no`, `year`, `score`) VALUES
('IMT4003', 99123, 2014,  2),
('IMT4032', 99123, 2013,  3),
('IMT4032', 100902, 2013, 4),
('IMT4003', 100902, 2014, 5);

DROP TABLE IF EXISTS `student`;
CREATE TABLE `student` (
  `student_no` int(6) NOT NULL,
  `name` varchar(50) NOT NULL,
  `age` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `student` (`student_no`, `name`, `age`) VALUES
(99123, 'Markus Brovold', 22),
(100902, 'Kristin Annabel', 22);


ALTER TABLE `course`
 ADD PRIMARY KEY (`code`);

ALTER TABLE `exam`
 ADD KEY `course_code` (`course_code`), ADD KEY `student_no` (`student_no`);

ALTER TABLE `student`
 ADD PRIMARY KEY (`student_no`);


ALTER TABLE `exam`
ADD CONSTRAINT `exam_ibfk_2` FOREIGN KEY (`student_no`) REFERENCES `student` (`student_no`),
ADD CONSTRAINT `exam_ibfk_1` FOREIGN KEY (`course_code`) REFERENCES `course` (`code`) ON DELETE NO ACTION;