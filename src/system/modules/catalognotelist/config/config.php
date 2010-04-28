<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 *
 * The TYPOlight webCMS is an accessible web content management system that 
 * specializes in accessibility and generates W3C-compliant HTML code. It 
 * provides a wide range of functionality to develop professional websites 
 * including a built-in search engine, form generator, file and user manager, 
 * CSS engine, multi-language support and many more. For more information and 
 * additional TYPOlight applications like the TYPOlight MVC Framework please 
 * visit the project website http://www.typolight.org.
 *
 * This is the enhancement to the data container array for table tl_catalog_fields 
 * to allow the custom field type for CatalogVariants which can add items to the notelist.
 *
 * PHP version 5
 * @copyright  Christian Schiffler 2010
 * @author     Christian Schiffler  <c.schiffler@cyberspectrum.de> 
 * @package    Catalog
 * @license    LGPL 
 * @filesource
 */


/**
 * Back-end module
 */
 
// Register field type editor to catalog module.
$GLOBALS['BE_MOD']['content']['catalog']['fieldTypes']['notelistvariants'] = array
(
	'typeimage'    => 'system/modules/catalognotelist/html/notelistvariants.gif',
	'fieldDef'     => array
	(
		'inputType' => 'textarea',
		'eval'      => array
		(
			'rte'=>'tinyMCE',
		),
	),
	'sqlDefColumn' => "text NOT NULL default ''",
	'parseValue' => array(array('CatalogVariantField', 'parseValue')),
);

$GLOBALS['BE_MOD']['content']['catalog']['typesCatalogFields'][] = 'notelistvariants';

$GLOBALS['TL_FFL']['notelistvariants'] = 'FormCatalogNoteList';

?>