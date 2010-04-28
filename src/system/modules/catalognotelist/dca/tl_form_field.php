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
 * Table tl_form_field
 */

$GLOBALS['TL_DCA']['tl_form_field']['palettes']['notelistvariants'] = '{type_legend},type,name,label;{text_legend},text;{catalog_legend},catalog,catalog_visible';
$GLOBALS['TL_DCA']['tl_form_field']['fields']['catalog'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_form_field']['catalog'],
	'exclude'                 => true,
	'inputType'               => 'radio',
	'foreignKey'              => 'tl_catalog_types.name',
	'eval'                    => array('mandatory'=> true, 'submitOnChange'=> true)
);

$GLOBALS['TL_DCA']['tl_form_field']['fields']['catalog_visible'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_form_field']['catalog_visible'],
	'exclude'                 => true,
	'inputType'               => 'checkboxWizard',
	'options_callback'        => array('tl_form_field_catalognotelist', 'getCatalogFields'),
	'eval'                    => array('multiple'=> true, 'mandatory'=> true)
);

class tl_form_field_catalognotelist extends Backend
{
	public function getCatalogs()
	{
		$objLists=$this->Database->prepare('SELECT * FROM tl_module WHERE type=?')->execute('cataloglist');
	}

	/**
	 * Get all catalog fields and return them as array
	 * @return array
	 */
	public function getCatalogFields(DataContainer $dc)
	{
		$arrTypes=$GLOBALS['BE_MOD']['content']['catalog']['typesCatalogFields'];
		$fields = array();
		$objFields = $this->Database->prepare("SELECT c.* FROM tl_catalog_fields c, tl_form_field m WHERE c.pid=m.catalog AND m.id=? AND c.type IN ('" . implode("','", $arrTypes) . "') ORDER BY c.sorting ASC")
							->execute($this->Input->get('id'));
		while ($objFields->next())
		{
			$value = strlen($objFields->name) ? $objFields->name.' ' : '';
			$value .= '['.$objFields->colName.':'.$objFields->type.']';
			$fields[$objFields->colName] = $value;
		}
		return $fields;
	}
}

?>