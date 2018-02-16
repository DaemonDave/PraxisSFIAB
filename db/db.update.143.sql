UPDATE `fundraising_goals` SET `goal` = 'sfgeneral', `name` = 'Science Fair - General Funds', `description` = 'General funds donated to the science fair may be allocated as the science fair organizers see fit.' WHERE `goal`='general' AND fiscalyear='-1';
UPDATE `fundraising_goals` SET `goal` = 'sfawards', `name` = 'Science Fair - Awards', `description` = 'Award Sponsorships are provided to allow an sponsor/donor to give a specific award.' WHERE `goal`='awards' AND fiscalyear='-1';