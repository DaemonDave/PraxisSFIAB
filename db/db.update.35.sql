-- Clean up the multiple default questions.. And insert the 5 proper questions.
DELETE FROM `questions` WHERE year='-1';


INSERT INTO `questions` (`id`, `year`, `section`, `db_heading`, `question`, `type`, `required`, `ord`) VALUES ('', -1, 'judgereg', 'Years School', 'Years of judging experience at a School level:', 'int', 'yes', 1);
INSERT INTO `questions` (`id`, `year`, `section`, `db_heading`, `question`, `type`, `required`, `ord`) VALUES ('', -1, 'judgereg', 'Years Regional', 'Years of judging experience at a Regional level:', 'int', 'yes', 2);
INSERT INTO `questions` (`id`, `year`, `section`, `db_heading`, `question`, `type`, `required`, `ord`) VALUES ('', -1, 'judgereg', 'Years National', 'Years of judging experience at a National (CWSF) level:', 'int', 'yes', 3);
INSERT INTO `questions` (`id`, `year`, `section`, `db_heading`, `question`, `type`, `required`, `ord`) VALUES ('', -1, 'judgereg', 'Attending Lunch', 'Will you be attending the Judge''s Lunch?', 'yesno', 'yes', 4);
INSERT INTO `questions` (`id`, `year`, `section`, `db_heading`, `question`, `type`, `required`, `ord`) VALUES ('', -1, 'judgereg', 'Willing Chair', 'Are you willing to be the lead for your judging team?', 'yesno', 'yes', 5);

