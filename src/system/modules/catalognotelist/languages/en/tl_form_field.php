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
 * Form fields
 */
$GLOBALS['TL_LANG']['FFL']['notelistvariants']           = array('Catalog notelist', 'Catalog notelist and variants.');

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_form_field']['catalog']          = array('Catalog', 'Please select the catalog.');
$GLOBALS['TL_LANG']['tl_form_field']['catalog_visible']  = array('Visible fields', 'Please select all fields that shall be visible (Variant options are always visible, no matter if specified here or not).');

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_form_field']['catalog_legend'] = 'Catalog';

?>