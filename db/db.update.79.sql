ALTER TABLE `users` ADD `year` INT NOT NULL AFTER `email` ;
ALTER TABLE `schools` ADD `designate` VARCHAR( 32 ) NOT NULL AFTER `postalcode` ;

INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year`) VALUES (
'volunteer_registration_type', 'open', 'Volunteer Registration', 'enum', 'open=Open|singlepassword=Single Password|invite=Invite', '150', 'The type of Volunteer Registration to use', '-1');

INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year`) VALUES (
 'volunteer_registration_singlepassword', '', 'Volunteer Registration', 'text', '', '160', 'The single password to use if using Single Password Volunteer Registration (the option above this one). Ignored if not using Single Password volunteer registration.', '-1');

INSERT INTO `emails` ( `id` , `val` , `name` , `description` , `from` , `subject` , `body` , `type` )
VALUES (
'', 'volunteer_new_invite', 'Volunteers - New Volunteeer Invitation', 'This is sent to a new volunteer when they are invited using the invite volunteers administration section, only available when the Volunteer Registration Type is set to Invite', '', 'Volunteer Registration for [FAIRNAME]', 'You have been invited to be a volunteer for the [FAIRNAME].  An account has been created for you to login with and complete your information.  You can login to the volunteer registration site with:\n\nEmail Address: [EMAIL]\nPassword: [PASSWORD]\n
You can change your password once you login.', 'system'
);

INSERT INTO `emails` ( `id` , `val` , `name` , `description` , `from` , `subject` , `body` , `type` )
VALUES (
'', 'volunteer_add_invite', 'Volunteers - New Volunteeer Invitation', 'This is sent to existing users when they are invited using the invite volunteers administration section, only available when the Volunteer Registration Type is set to Invite', '', 'Volunteer Registration for [FAIRNAME]', 'The role of volunteer for the [FAIRNAME] has been added to your account by a committee member.  When you login again, there will be a [Switch Roles] link in the upper right hand area of the page.  Clicking on [Switch Roles] will let you switch between being a Volunteer and your other roles without needing to logout.\n', 'system');

