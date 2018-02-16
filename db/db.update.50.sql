ALTER TABLE `students` ADD `webfirst` ENUM( 'no', 'yes' ) DEFAULT 'yes' NOT NULL ,
ADD `weblast` ENUM( 'no', 'yes' ) DEFAULT 'yes' NOT NULL ,
ADD `webphoto` ENUM( 'no', 'yes' ) DEFAULT 'yes' NOT NULL ;
