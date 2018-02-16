CREATE TABLE `isefforms` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`name` varchar(128) NOT NULL,
	`required` enum('N','Y') NOT NULL,
	`description` text NOT NULL,
	`url` varchar(128) default NULL,
	`formper` ENUM('student','project') NOT NULL DEFAULT project,
	`year` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM ;

CREATE TABLE `isefforms_registrations_link` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`registrations_id` int(10) unsigned NOT NULL,
	`isefforms_id` int(10) unsigned NOT NULL,
	`filename` varchar(128) NOT NULL
	PRIMARY KEY (`id`)
) ENGINE=MyISAM;

INSERT INTO `isefforms` (`name`, `required`, `description`, `url`, `year`) VALUES ('(1) Checklist for Adult Sponsor', 'Y', 'This completed form is required for ALL projects and must be completed before experimentation.', 'http://www.societyforscience.org/isef/document/chklst09.pdf', 2009);
INSERT INTO `isefforms` (`name`, `required`, `description`, `url`, `year`) VALUES ('(1A) Student Checklist', 'Y', 'This form is required for ALL projects.', 'http://www.societyforscience.org/isef/document/respln09.pdf', 2009);
INSERT INTO `isefforms` (`name`, `required`, `description`, `url`, `year`) VALUES ('(1A2) Research Plan', 'Y', 'REQUIRED for ALL Projects Before Experimentation.  A complete research hplan must accompany Checklist for Student (1A).', 'http://www.societyforscience.org/isef/document/respln09.pdf' , 2009);
INSERT INTO `isefforms` (`name`, `required`, `description`, `url`, `formper`, `year`) VALUES ('(1B) Approval Form', 'Y', 'A completed form is required for each student, including all team members.', 'http://www.societyforscience.org/isef/document/1bappr09.pdf', 'student', 2009);
INSERT INTO `isefforms` (`name`, `required`, `description`, `url`, `year`) VALUES ('(1C) Regulated Research Institutional/Industrial Setting Form', 'N', 'This form must be completed after experimentation by the adult supervising the student research conducted in a regulated research institution, industrial setting or an work site other than home, school or field.', 'http://www.societyforscience.org/isef/document/1cinst09.pdf', 2009);
INSERT INTO `isefforms` (`name`, `required`, `description`, `url`, `year`) VALUES ('(2) Qualified Scientist Form', 'N', 'May be required for researc involving human subjects, vertebrate animals, potentially hazardous biological agents, and DEA-controlled substances.  Must be completed and signed before the start of student experimentation.', 'http://www.societyforscience.org/isef/document/qualsc09.pdf', 2009);
INSERT INTO `isefforms` (`name`, `required`, `description`, `url`, `year`) VALUES ('(3) Risk Assessment Form', 'N', 'Required for projects using hazardous chemicals, activities or devices.  Must be completed before experimentation.', 'http://www.societyforscience.org/isef/document/rskass09.pdf', 2009);
INSERT INTO `isefforms` (`name`, `required`, `description`, `url`, `year`) VALUES ('(4) Human Subjects Form', 'N', 'Required for all research involving human subjects.  IRB approval required before experimentation.', 'http://www.societyforscience.org/isef/document/hmsubj09.pdf', 2009);
INSERT INTO `isefforms` (`name`, `required`, `description`, `url`, `year`) VALUES ('(5A) Vertebrate Animal Form', 'N', 'Required for all research involving vertebrate animals that is conducted in a Non-Regulated Research Site.  (SRC approval required before experimentation.)', 'http://www.societyforscience.org/isef/document/vertan09.pdf', 2009);
INSERT INTO `isefforms` (`name`, `required`, `description`, `url`, `year`) VALUES ('(5B) Vertebrate Animal Form', 'N', 'Required for all research involving vertebrate animals that is conducated at a Regulated Research Institution.  (IACUC approval required before experimentation.)', 'http://www.societyforscience.org/isef/document/vertan09.pdf', 2009);
INSERT INTO `isefforms` (`name`, `required`, `description`, `url`, `year`) VALUES ('(6A) Potentially Hazardous Biological Agents Risk Assessment Form', 'N', 'Required for research involving microorganisms, rDNA, fresh/frozen tissue, blood and body fluids.  SRC/IACUC/IBC approval required before experimentation.', 'http://www.societyforscience.org/isef/document/biohaz09.pdf', 2009);
INSERT INTO `isefforms` (`name`, `required`, `description`, `url`, `year`) VALUES ('(6B) Human and Vertebrate Animal Tissue Form', 'N', 'Required for projects using fresh/frozen tissue, primary cell cultures, blood, blood products and body fluids.  If the research involves living organisms, please ensure that the proper human or animal forms are completed.  All projets using any tissue listed above, must also complete Form 6A', 'http://www.societyforscience.org/isef/document/tissue09.pdf', 2009);
INSERT INTO `isefforms` (`name`, `required`, `description`, `url`, `year`) VALUES ('(7A) Continuation Projects Form', 'N', 'Required for projects that are a continuation in the same field of study as a previous project.  This form must be accompanied by the previous year\'s abstract, Form (1A) and Research Plan', 'http://www.societyforscience.org/isef/document/contin09.pdf', 2009);
