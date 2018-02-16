<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2005 James Grant <james@lightbox.org>

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public
   License as published by the Free Software Foundation, version 2.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
    General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; see the file COPYING.  If not, write to
   the Free Software Foundation, Inc., 59 Temple Place - Suite 330,
   Boston, MA 02111-1307, USA.
*/
?>
<?
 require("../questions.inc.php");
 require("../common.inc.php");
 require_once("../user.inc.php");
 user_auth_required('committee', 'config');
 send_header("Judge Registration Questions",
 		array('Committee Main' => 'committee_main.php',
			'SFIAB Configuration' => 'config/index.php')
            ,"judge_registration_questions"
			);

 questions_editor('judgereg', $config['FAIRYEAR'], 'question',
 			$_SERVER['PHP_SELF']);

 

 send_footer();
?>
