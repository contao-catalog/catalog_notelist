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
 * @package    CatalogNotelist
 * @license    LGPL 
 * @filesource
 */


/**
 * Table tl_catalog_fields 
 */


// register to catalog module that we provide the notelistvariants as field type.
$GLOBALS['TL_DCA']['tl_catalog_fields']['fields']['type']['options'][] = 'notelistvariants';

// sub palette
$GLOBALS['TL_DCA']['tl_catalog_fields']['palettes']['notelistvariants'] = '{title_legend},name,description,colName,type,notelistvariants,notelistselamount;{display_legend},insertBreak,width50';

$GLOBALS['TL_DCA']['tl_catalog_fields']['fields']['notelistvariants'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_catalog_fields']['notelistvariants'],
	'exclude'       => true,
	'inputType'               => 'checkboxWizard',
	'options_callback'        => array('tl_catalog_fields', 'getOptionSelectors'),
	'eval'      => array
	(
		'multiple' => true
	)
);
$GLOBALS['TL_DCA']['tl_catalog_fields']['fields']['notelistselamount'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_catalog_fields']['notelistselamount'],
	'exclude'       => true,
	'inputType'               => 'checkbox',
);

?>