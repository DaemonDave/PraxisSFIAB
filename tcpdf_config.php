<?php
//============================================================+
// File name   : tcpdf_config.php
// Begin       : 2004-06-11
// Last Update : 2009-09-30
//
// Description : Configuration file for TCPDF.
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com s.r.l.
//               Via Della Pace, 11
//               09044 Quartucciu (CA)
//               ITALY
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Configuration file for TCPDF.
 * @author Nicola Asuni
 * @copyright 2004-2008 Nicola Asuni - Tecnick.com S.r.l (www.tecnick.com) Via Della Pace, 11 - 09044 - Quartucciu (CA) - ITALY - www.tecnick.com - info@tecnick.com
 * @package com.tecnick.tcpdf
 * @version 4.0.014
 * @link http://tcpdf.sourceforge.net
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @since 2004-10-27
 */

// If you define the constant K_TCPDF_EXTERNAL_CONFIG, the following settings will be ignored.

if (!defined('K_TCPDF_EXTERNAL_CONFIG')) {
	
	define('K_TCPDF_EXTERNAL_CONFIG',true);

	$k_path_main = $_SERVER['DOCUMENT_ROOT'].$config['SFIABDIRECTORY'].'/';
	$k_path_url = $config['SFIABDIRECTORY'].'/';

	define ('K_PATH_MAIN', $k_path_main);
	define ('K_PATH_URL', $k_path_url);
	
	/**
	 * path for PDF fonts
	 * use K_PATH_MAIN.'fonts/old/' for old non-UTF8 fonts
	 */
	define ('K_PATH_FONTS', K_PATH_MAIN.'tcpdf/fonts/');
	
	/**
	 * cache directory for temporary files (full path)
	 */
	define ('K_PATH_CACHE', K_PATH_MAIN.'tcpdf/cache/');
	
	/**
	 * cache directory for temporary files (url path)
	 */
	define ('K_PATH_URL_CACHE', K_PATH_URL.'cache/');
	
	/**
	 *images directory
	: */
	$sfiab_dir = $_SERVER['DOCUMENT_ROOT'].$config['SFIABDIRECTORY'];
	define ('K_PATH_IMAGES', "$sfiab_dir/data/");
	
	/**
	 * blank image
	 */
	define ('K_BLANK_IMAGE', K_PATH_IMAGES.'_blank.png');
	
	/**
	 * page format
	 */
	define ('PDF_PAGE_FORMAT', 'LETTER');
	
	/**
	 * page orientation (P=portrait, L=landscape)
	 */
	define ('PDF_PAGE_ORIENTATION', 'P');
	
	/**
	 * document creator
	 */
	define ('PDF_CREATOR', 'TCPDF');
	
	/**
	 * document author
	 */
	define ('PDF_AUTHOR', 'SFIAB');
	
	/**
	 * header title
	 */
	define ('PDF_HEADER_TITLE', $config['fairname']);
	
	/**
	 * header description string
	 */
	define ('PDF_HEADER_STRING', "");
	
	/**
	 * image logo
	 */
	define ('PDF_HEADER_LOGO', 'logo.png');
	
	/**
	 * header logo image width [mm]
	 */
	define ('PDF_HEADER_LOGO_WIDTH', 16);
	
	/**
	 *  document unit of measure [pt=point, mm=millimeter, cm=centimeter, in=inch]
	 */
	define ('PDF_UNIT', 'mm');
	
	/**
	 * header margin
	 */
	define ('PDF_MARGIN_HEADER', 5);
	
	/**
	 * footer margin
	 */
	define ('PDF_MARGIN_FOOTER', 10);
	
	/**
	 * top margin (includes header height)
	 */
	define ('PDF_MARGIN_TOP', 25);
	
	/**
	 * bottom margin
	 */
	define ('PDF_MARGIN_BOTTOM', 25);
	
	/**
	 * left margin
	 */
	define ('PDF_MARGIN_LEFT', 15);
	
	/**
	 * right margin
	 */
	define ('PDF_MARGIN_RIGHT', 15);
	
	/**
	 * default main font name
	 */
	define ('PDF_FONT_NAME_MAIN', 'helvetica');
	
	/**
	 * default main font size
	 */
	define ('PDF_FONT_SIZE_MAIN', 10);
	
	/**
	 * default data font name
	 */
	define ('PDF_FONT_NAME_DATA', 'helvetica');
	
	/**
	 * default data font size
	 */
	define ('PDF_FONT_SIZE_DATA', 8);
	
	/**
	 * default monospaced font name
	 */
	define ('PDF_FONT_MONOSPACED', 'courier');
	
	/**
	 * ratio used to adjust the conversion of pixels to user units
	 */
	define ('PDF_IMAGE_SCALE_RATIO', 1);
	
	/**
	 * magnification factor for titles
	 */
	define('HEAD_MAGNIFICATION', 1.1);
	
	/**
	 * height of cell repect font height
	 */
	define('K_CELL_HEIGHT_RATIO', 1.25);
	
	/**
	 * title magnification respect main font size
	 */
	define('K_TITLE_MAGNIFICATION', 1.3);
	
	/**
	 * reduction factor for small font
	 */
	define('K_SMALL_RATIO', 2/3);
}

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
