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
$GLOBALS['TL_LANG']['FFL']['notelistvariants']           = array('Merkliste', 'Katalogmerkliste und Varianten.');

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_form_field']['catalog']          = array('Katalog', 'Bitte wählen Sie den Katalog.');
$GLOBALS['TL_LANG']['tl_form_field']['catalog_visible']  = array('Sichtbare Felder', 'Bitte wählen Sie die sichtbaren Katalogfelder (Variantenoptionen sind immer sichtbar unabhängig ob diese hier gewählt sind oder nicht).');

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_form_field']['catalog_legend'] = 'Katalog';

?>