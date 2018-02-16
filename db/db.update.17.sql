UPDATE `config` SET `description`='The single password to use for judge registration if judge_registration_type is singlepassword.  Leave blank if not using singlepassword judge registration' WHERE `var`='judge_registration_singlepassword';
UPDATE `emails` SET `description`='This is sent to a new judge when they are invited using the invite judges administration section, only available when judge_registration_type=invite' WHERE `val`='new_judge_invite';
UPDATE `config` SET `description`='Allows for the setup of different divisions for each category (yes|no)' WHERE `var`='filterdivisionbycategory';

