-- --------------------------------------------------------

-- 
-- Table structure for table `reports`
-- 

CREATE TABLE `reports` ( `id` int(11) NOT NULL auto_increment, `name` varchar(128) NOT NULL default '', `desc` tinytext NOT NULL, `creator` varchar(128) NOT NULL default '', `type` enum('student','judge') NOT NULL default 'student', PRIMARY KEY  (`id`)) TYPE=MyISAM;

-- 
-- Dumping data for table `reports`
-- 

INSERT INTO `reports` VALUES (1, 'Student+Project -- Sorted by Last Name', 'Student Name, Project Number and Title, Category, Division short form sorted by Last Name', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (2, 'Student+Project -- Sorted by Project Number', 'Student Name, Project Number and Title, Category sorted by Project Number', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (3, 'Student+Project -- Grouped by Category', 'Student Name, Project Number and Title sorted by Last Name, grouped by Category', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (4, 'Student+Project -- School Names sorted by Last Name', 'Student Name, Project Num, School Name sorted by Last Name', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (5, 'Student+Project -- Grouped by School sorted by Last Name', 'Student Name, Project Number and Name sorted by Last Name, grouped by School Name', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (6, 'Teacher -- Name and School Info sorted by Teacher Name', 'Teacher, School Info sorted by Teacher Name', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (8, 'Teacher -- Names and Contact for each Student by School', 'Student Name, Teacher Name, Teacher Email, School Phone and Fax grouped by School Name with Addresses', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (9, 'Check-in Lists', 'List of students and partners, project number and name, division, registration fees, tshirt size, sorted by project number, grouped by age category', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (10, 'Student+Project -- Student (and Partner) grouped by School', 'Student Pairs, Project Name/Num Grouped by School', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (11, 'Student+Project -- Grouped by School sorted by Project Number', 'Individual Students, Project Name/Num Grouped by School', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (12, 'Student -- T-Shirt List by School', 'Individual Students, Project Num, TShirt, Grouped by School', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (13, 'Media -- Program Guide', 'Project Number, Both student names, and Project Title, grouped by School', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (14, 'Projects -- Titles and Grades from each School', 'Project Name/Num, Grade Grouped by School', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (15, 'Media -- Award Winners List', 'Project Number, Student Name and Contact info, by each Award', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (16, 'Projects -- Logistical Display Requirements', 'Project Number, Students, Electricity, Table, and special needs', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (17, 'Emergency Contact Information', 'Emergency Contact Names, Relationship, and Phone Numbers for each student.', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (18, 'Student -- Grouped by Grade and Gender (YSF Stats)', 'A list of students grouped by Grade and Gender.  A quick way to total up the info for the YSF regional stats page.', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (19, 'Student+Project -- Grouped by School, 1 per page', 'Both students names grouped by school, each school list begins on a new page.', 'The Grant Brothers', 'student');

-- --------------------------------------------------------

-- 
-- Table structure for table `reports_items`
-- 

CREATE TABLE `reports_items` ( `id` int(11) NOT NULL auto_increment, `reports_id` int(11) NOT NULL default '0', `field` varchar(64) NOT NULL default '', `type` enum('col','sort','group','distinct','option') NOT NULL default 'col', `value` varchar(64) NOT NULL default '', `order` int(11) NOT NULL default '0', PRIMARY KEY  (`id`)) TYPE=MyISAM ;

-- 
-- Dumping data for table `reports_items`
-- 

INSERT INTO `reports_items` VALUES ('', 1, 'grade', 'col', '', 5);
INSERT INTO `reports_items` VALUES ('', 1, 'div', 'col', '', 4);
INSERT INTO `reports_items` VALUES ('', 1, 'last_name', 'sort', '', 0);
INSERT INTO `reports_items` VALUES ('', 2, 'category', 'col', '', 3);
INSERT INTO `reports_items` VALUES ('', 2, 'pn', 'col', '', 0);
INSERT INTO `reports_items` VALUES ('', 2, 'pn', 'sort', '', 0);
INSERT INTO `reports_items` VALUES ('', 3, 'div', 'col', '', 3);
INSERT INTO `reports_items` VALUES ('', 4, 'allow_multiline', 'option', 'no', 2);
INSERT INTO `reports_items` VALUES ('', 3, 'last_name', 'sort', '', 0);
INSERT INTO `reports_items` VALUES ('', 3, 'category', 'group', '', 0);
INSERT INTO `reports_items` VALUES ('', 4, 'grade', 'col', '', 3);
INSERT INTO `reports_items` VALUES ('', 4, 'name', 'col', '', 1);
INSERT INTO `reports_items` VALUES ('', 4, 'pn', 'col', '', 0);
INSERT INTO `reports_items` VALUES ('', 4, 'last_name', 'sort', '', 0);
INSERT INTO `reports_items` VALUES ('', 5, 'category', 'col', '', 3);
INSERT INTO `reports_items` VALUES ('', 5, 'div', 'col', '', 4);
INSERT INTO `reports_items` VALUES ('', 5, 'last_name', 'sort', '', 0);
INSERT INTO `reports_items` VALUES ('', 5, 'school', 'group', '', 0);
INSERT INTO `reports_items` VALUES ('', 6, 'school_phone', 'col', '', 2);
INSERT INTO `reports_items` VALUES ('', 6, 'school', 'col', '', 1);
INSERT INTO `reports_items` VALUES ('', 6, 'teacher', 'col', '', 0);
INSERT INTO `reports_items` VALUES ('', 6, 'teacher', 'sort', '', 0);
INSERT INTO `reports_items` VALUES ('', 6, 'teacher', 'distinct', '', 0);
INSERT INTO `reports_items` VALUES ('', 11, 'allow_multiline', 'option', 'no', 2);
INSERT INTO `reports_items` VALUES ('', 11, 'group_new_page', 'option', 'no', 1);
INSERT INTO `reports_items` VALUES ('', 11, 'type', 'option', 'pdf', 0);
INSERT INTO `reports_items` VALUES ('', 8, 'school_fax', 'col', '', 5);
INSERT INTO `reports_items` VALUES ('', 8, 'school_phone', 'col', '', 4);
INSERT INTO `reports_items` VALUES ('', 8, 'teacheremail', 'col', '', 3);
INSERT INTO `reports_items` VALUES ('', 8, 'pn', 'sort', '', 0);
INSERT INTO `reports_items` VALUES ('', 9, 'div', 'col', '', 6);
INSERT INTO `reports_items` VALUES ('', 9, 'tshirt', 'col', '', 5);
INSERT INTO `reports_items` VALUES ('', 9, 'name', 'col', '', 3);
INSERT INTO `reports_items` VALUES ('', 9, 'pn', 'sort', '', 0);
INSERT INTO `reports_items` VALUES ('', 9, 'category', 'group', '', 0);
INSERT INTO `reports_items` VALUES ('', 9, 'type', 'option', 'pdf', 0);
INSERT INTO `reports_items` VALUES ('', 10, 'partner', 'col', '', 2);
INSERT INTO `reports_items` VALUES ('', 10, 'name', 'col', '', 1);
INSERT INTO `reports_items` VALUES ('', 10, 'pn', 'col', '', 0);
INSERT INTO `reports_items` VALUES ('', 10, 'pn', 'sort', '', 0);
INSERT INTO `reports_items` VALUES ('', 10, 'school', 'group', '', 0);
INSERT INTO `reports_items` VALUES ('', 10, 'pn', 'distinct', '', 0);
INSERT INTO `reports_items` VALUES ('', 2, 'title', 'col', '', 2);
INSERT INTO `reports_items` VALUES ('', 11, 'div', 'col', '', 4);
INSERT INTO `reports_items` VALUES ('', 11, 'category', 'col', '', 3);
INSERT INTO `reports_items` VALUES ('', 11, 'pn', 'sort', '', 0);
INSERT INTO `reports_items` VALUES ('', 11, 'school', 'group', '', 0);
INSERT INTO `reports_items` VALUES ('', 12, 'name', 'col', '', 1);
INSERT INTO `reports_items` VALUES ('', 12, 'pn', 'col', '', 0);
INSERT INTO `reports_items` VALUES ('', 12, 'pn', 'sort', '', 0);
INSERT INTO `reports_items` VALUES ('', 12, 'school', 'group', '', 0);
INSERT INTO `reports_items` VALUES ('', 13, 'bothnames', 'col', '', 1);
INSERT INTO `reports_items` VALUES ('', 13, 'pn', 'col', '', 0);
INSERT INTO `reports_items` VALUES ('', 13, 'pn', 'sort', '', 0);
INSERT INTO `reports_items` VALUES ('', 13, 'school', 'group', '', 0);
INSERT INTO `reports_items` VALUES ('', 13, 'pn', 'distinct', '', 0);
INSERT INTO `reports_items` VALUES ('', 14, 'pn', 'col', '', 0);
INSERT INTO `reports_items` VALUES ('', 14, 'pn', 'sort', '', 0);
INSERT INTO `reports_items` VALUES ('', 14, 'school', 'group', '', 0);
INSERT INTO `reports_items` VALUES ('', 14, 'pn', 'distinct', '', 0);
INSERT INTO `reports_items` VALUES ('', 15, 'postal', 'col', '', 5);
INSERT INTO `reports_items` VALUES ('', 15, 'province', 'col', '', 4);
INSERT INTO `reports_items` VALUES ('', 15, 'city', 'col', '', 3);
INSERT INTO `reports_items` VALUES ('', 15, 'address', 'col', '', 2);
INSERT INTO `reports_items` VALUES ('', 15, 'name', 'col', '', 1);
INSERT INTO `reports_items` VALUES ('', 15, 'pn', 'sort', '', 0);
INSERT INTO `reports_items` VALUES ('', 15, 'awards', 'group', '', 0);
INSERT INTO `reports_items` VALUES ('', 1, 'allow_multiline', 'option', 'yes', 2);
INSERT INTO `reports_items` VALUES ('', 1, 'group_new_page', 'option', 'no', 1);
INSERT INTO `reports_items` VALUES ('', 1, 'type', 'option', 'pdf', 0);
INSERT INTO `reports_items` VALUES ('', 1, 'category', 'col', '', 3);
INSERT INTO `reports_items` VALUES ('', 1, 'title', 'col', '', 2);
INSERT INTO `reports_items` VALUES ('', 3, 'name', 'col', '', 1);
INSERT INTO `reports_items` VALUES ('', 3, 'allow_multiline', 'option', 'no', 2);
INSERT INTO `reports_items` VALUES ('', 3, 'group_new_page', 'option', 'no', 1);
INSERT INTO `reports_items` VALUES ('', 9, 'partner', 'col', '', 4);
INSERT INTO `reports_items` VALUES ('', 9, 'title', 'col', '', 2);
INSERT INTO `reports_items` VALUES ('', 9, 'allow_multiline', 'option', 'no', 2);
INSERT INTO `reports_items` VALUES ('', 9, 'pn', 'col', '', 1);
INSERT INTO `reports_items` VALUES ('', 9, 'group_new_page', 'option', 'yes', 1);
INSERT INTO `reports_items` VALUES ('', 5, 'grade', 'col', '', 5);
INSERT INTO `reports_items` VALUES ('', 5, 'group_new_page', 'option', 'no', 1);
INSERT INTO `reports_items` VALUES ('', 5, 'allow_multiline', 'option', 'no', 2);
INSERT INTO `reports_items` VALUES ('', 3, 'title', 'col', '', 2);
INSERT INTO `reports_items` VALUES ('', 4, 'school', 'col', '', 2);
INSERT INTO `reports_items` VALUES ('', 8, 'teacher', 'col', '', 2);
INSERT INTO `reports_items` VALUES ('', 8, 'schooladdr', 'group', '', 1);
INSERT INTO `reports_items` VALUES ('', 8, 'type', 'option', 'pdf', 0);
INSERT INTO `reports_items` VALUES ('', 11, 'grade', 'col', '', 5);
INSERT INTO `reports_items` VALUES ('', 2, 'name', 'col', '', 1);
INSERT INTO `reports_items` VALUES ('', 2, 'group_new_page', 'option', 'no', 1);
INSERT INTO `reports_items` VALUES ('', 2, 'type', 'option', 'pdf', 0);
INSERT INTO `reports_items` VALUES ('', 12, 'tshirt', 'col', '', 2);
INSERT INTO `reports_items` VALUES ('', 12, 'group_new_page', 'option', 'no', 1);
INSERT INTO `reports_items` VALUES ('', 8, 'group_new_page', 'option', 'no', 1);
INSERT INTO `reports_items` VALUES ('', 12, 'allow_multiline', 'option', 'no', 2);
INSERT INTO `reports_items` VALUES ('', 12, 'type', 'option', 'pdf', 0);
INSERT INTO `reports_items` VALUES ('', 8, 'name', 'col', '', 1);
INSERT INTO `reports_items` VALUES ('', 8, 'pn', 'col', '', 0);
INSERT INTO `reports_items` VALUES ('', 8, 'school', 'group', '', 0);
INSERT INTO `reports_items` VALUES ('', 15, 'pn', 'col', '', 0);
INSERT INTO `reports_items` VALUES ('', 15, 'allow_multiline', 'option', 'no', 2);
INSERT INTO `reports_items` VALUES ('', 15, 'group_new_page', 'option', 'no', 1);
INSERT INTO `reports_items` VALUES ('', 15, 'type', 'option', 'pdf', 0);
INSERT INTO `reports_items` VALUES ('', 13, 'title', 'col', '', 2);
INSERT INTO `reports_items` VALUES ('', 13, 'allow_multiline', 'option', 'no', 2);
INSERT INTO `reports_items` VALUES ('', 13, 'group_new_page', 'option', 'no', 1);
INSERT INTO `reports_items` VALUES ('', 13, 'type', 'option', 'pdf', 0);
INSERT INTO `reports_items` VALUES ('', 14, 'title', 'col', '', 1);
INSERT INTO `reports_items` VALUES ('', 14, 'allow_multiline', 'option', 'no', 2);
INSERT INTO `reports_items` VALUES ('', 14, 'group_new_page', 'option', 'no', 1);
INSERT INTO `reports_items` VALUES ('', 14, 'type', 'option', 'pdf', 0);
INSERT INTO `reports_items` VALUES ('', 16, 'req_special', 'col', '', 4);
INSERT INTO `reports_items` VALUES ('', 16, 'req_table', 'col', '', 3);
INSERT INTO `reports_items` VALUES ('', 16, 'req_elec', 'col', '', 2);
INSERT INTO `reports_items` VALUES ('', 16, 'title', 'col', '', 1);
INSERT INTO `reports_items` VALUES ('', 16, 'category', 'group', '', 0);
INSERT INTO `reports_items` VALUES ('', 16, 'pn', 'sort', '', 0);
INSERT INTO `reports_items` VALUES ('', 16, 'pn', 'distinct', '', 0);
INSERT INTO `reports_items` VALUES ('', 16, 'group_new_page', 'option', 'no', 1);
INSERT INTO `reports_items` VALUES ('', 16, 'type', 'option', 'pdf', 0);
INSERT INTO `reports_items` VALUES ('', 16, 'pn', 'col', '', 0);
INSERT INTO `reports_items` VALUES ('', 16, 'allow_multiline', 'option', 'yes', 2);
INSERT INTO `reports_items` VALUES ('', 17, 'emerg_phone', 'col', '', 4);
INSERT INTO `reports_items` VALUES ('', 17, 'emerg_relation', 'col', '', 3);
INSERT INTO `reports_items` VALUES ('', 17, 'emerg_name', 'col', '', 2);
INSERT INTO `reports_items` VALUES ('', 17, 'last_name', 'sort', '', 0);
INSERT INTO `reports_items` VALUES ('', 8, 'allow_multiline', 'option', 'no', 2);
INSERT INTO `reports_items` VALUES ('', 14, 'grade', 'col', '', 2);
INSERT INTO `reports_items` VALUES ('', 17, 'group_new_page', 'option', 'no', 1);
INSERT INTO `reports_items` VALUES ('', 17, 'type', 'option', 'pdf', 0);
INSERT INTO `reports_items` VALUES ('', 6, 'type', 'option', 'pdf', 0);
INSERT INTO `reports_items` VALUES ('', 6, 'school_fax', 'col', '', 3);
INSERT INTO `reports_items` VALUES ('', 17, 'allow_multiline', 'option', 'yes', 2);
INSERT INTO `reports_items` VALUES ('', 17, 'name', 'col', '', 1);
INSERT INTO `reports_items` VALUES ('', 17, 'pn', 'col', '', 0);
INSERT INTO `reports_items` VALUES ('', 6, 'group_new_page', 'option', 'no', 1);
INSERT INTO `reports_items` VALUES ('', 6, 'allow_multiline', 'option', 'no', 2);
INSERT INTO `reports_items` VALUES ('', 9, 'paid', 'col', '', 0);
INSERT INTO `reports_items` VALUES ('', 1, 'name', 'col', '', 1);
INSERT INTO `reports_items` VALUES ('', 2, 'allow_multiline', 'option', 'no', 2);
INSERT INTO `reports_items` VALUES ('', 3, 'type', 'option', 'pdf', 0);
INSERT INTO `reports_items` VALUES ('', 3, 'pn', 'col', '', 0);
INSERT INTO `reports_items` VALUES ('', 4, 'group_new_page', 'option', 'no', 1);
INSERT INTO `reports_items` VALUES ('', 4, 'type', 'option', 'pdf', 0);
INSERT INTO `reports_items` VALUES ('', 10, 'title', 'col', '', 3);
INSERT INTO `reports_items` VALUES ('', 10, 'type', 'option', 'pdf', 0);
INSERT INTO `reports_items` VALUES ('', 10, 'group_new_page', 'option', 'no', 1);
INSERT INTO `reports_items` VALUES ('', 10, 'allow_multiline', 'option', 'no', 2);
INSERT INTO `reports_items` VALUES ('', 5, 'title', 'col', '', 2);
INSERT INTO `reports_items` VALUES ('', 5, 'name', 'col', '', 1);
INSERT INTO `reports_items` VALUES ('', 5, 'type', 'option', 'pdf', 0);
INSERT INTO `reports_items` VALUES ('', 5, 'pn', 'col', '', 0);
INSERT INTO `reports_items` VALUES ('', 11, 'title', 'col', '', 2);
INSERT INTO `reports_items` VALUES ('', 11, 'name', 'col', '', 1);
INSERT INTO `reports_items` VALUES ('', 11, 'pn', 'col', '', 0);
INSERT INTO `reports_items` VALUES ('', 18, 'pn', 'col', '', 0);
INSERT INTO `reports_items` VALUES ('', 18, 'name', 'col', '', 1);
INSERT INTO `reports_items` VALUES ('', 18, 'school', 'col', '', 2);
INSERT INTO `reports_items` VALUES ('', 18, 'grade', 'group', '', 0);
INSERT INTO `reports_items` VALUES ('', 18, 'gender', 'group', '', 1);
INSERT INTO `reports_items` VALUES ('', 18, 'pn', 'sort', '', 0);
INSERT INTO `reports_items` VALUES ('', 18, 'type', 'option', 'pdf', 0);
INSERT INTO `reports_items` VALUES ('', 18, 'group_new_page', 'option', 'no', 1);
INSERT INTO `reports_items` VALUES ('', 18, 'allow_multiline', 'option', 'no', 2);
INSERT INTO `reports_items` VALUES ('', 3, 'grade', 'col', '', 4);
INSERT INTO `reports_items` VALUES ('', 1, 'pn', 'col', '', 0);
INSERT INTO `reports_items` VALUES ('', 2, 'div', 'col', '', 4);
INSERT INTO `reports_items` VALUES ('', 2, 'grade', 'col', '', 5);
INSERT INTO `reports_items` VALUES ('', 19, 'pn', 'col', '', 0);
INSERT INTO `reports_items` VALUES ('', 19, 'title', 'col', '', 1);
INSERT INTO `reports_items` VALUES ('', 19, 'bothnames', 'col', '', 2);
INSERT INTO `reports_items` VALUES ('', 19, 'school', 'group', '', 0);
INSERT INTO `reports_items` VALUES ('', 19, 'pn', 'sort', '', 0);
INSERT INTO `reports_items` VALUES ('', 19, 'type', 'option', 'pdf', 0);
INSERT INTO `reports_items` VALUES ('', 19, 'group_new_page', 'option', 'yes', 1);
INSERT INTO `reports_items` VALUES ('', 19, 'allow_multiline', 'option', 'no', 2);

