<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * PHP version 5
 * @copyright   Christian Schiffler 2010
 * @author      Christian Schiffler  <c.schiffler@cyberspectrum.de> 
 * @package		Catalog
 * @license		LGPL 
 * @filesource
 */

/**
 * Class InternalModuleCatalogNotelist
 *
 * Used for internal rendering of an notelist.
 *
 * @copyright  Christian Schiffler 2010
 * @author     Christian Schiffler  <c.schiffler@cyberspectrum.de> 
 * @package	   Controller
 *
 */
class InternalModuleCatalogNotelist extends ModuleCatalog
{
	protected $strTemplate='mod_catalognotify';
	protected $stripTagsForNotelist = false;

	public function __construct(Database_Result $objModule, $strColumn='main', $stripTagsForNotelist=false)
	{
		parent::__construct($objModule, $strColumn);
		$this->stripTagsForNotelist=$stripTagsForNotelist;
	}

	public function prepareFields($arrData)
	{
		$arrCatalog=array();
		foreach ($arrData as $k=>$v)
		{
			$fieldConf = &$GLOBALS['TL_DCA'][$this->strTable]['fields'][$k];
			$blnParentCheckbox = $fieldConf['eval']['catalog']['parentCheckbox'] && !$arrData[$fieldConf['eval']['catalog']['parentCheckbox']];
			if (in_array($k, array('id','pid','sorting','tstamp')) || $fieldConf['inputType'] == 'password' || $blnParentCheckbox)
				continue;
			$strLabel = strlen($label = $fieldConf['label'][0]) ? $label : $k;
			$strType = $fieldConf['eval']['catalog']['type'];
			$arrValues = $this->parseValue($this->type, $k, $v, $blnImageLink, $objCatalog);
			$arrCatalog[$k] = array
			(
				'label' => $strLabel,
				'type'	=> $strType,
				'raw' => $v,
				'value' => ($arrValues['html'] ? $arrValues['html'] : '')
			);
			switch ($strType)
			{
				case 'select':
				case 'tags':
					list($refTable, $valueCol) = explode('.', $fieldConf['eval']['catalog']['foreignKey']);

					if (strlen(trim($v)))
					{
						// set sort order
						$sortCol =	$fieldConf['eval']['catalog']['sortCol'];
						if (!strlen($sortCol)) 
							$sortCol = 'sorting';
						$sortOrder = $this->Database->fieldExists($sortCol, $refTable) ? $sortCol : $refCol;
						// Get referenced fields
						$objRef = $this->Database->prepare("SELECT * FROM ".$refTable." WHERE id IN (".trim($v).") ORDER BY ".$sortOrder)
												->execute();
						if ($objRef->numRows)
						{
							// Get Ref Catalog JumpTo
							$objJump = $this->Database->prepare("SELECT tableName, aliasField, jumpTo FROM tl_catalog_types WHERE tableName=?")
													->limit(1)
													->execute($refTable);
							// Add Ref Catalog Links
							if ($objJump->numRows)
							{
								while ($objRef->next())
								{
									$objRef->parentJumpTo = $objJump->jumpTo;
									$objRef->parentLink = $this->generateLink($objRef, $objJump->aliasField, $objJump->tableName, $this->catalog_link_window);
									$objRef->parentUrl = $this->generateCatalogUrl($objRef, $objJump->aliasField, $objJump->tableName);
								}
							}
							// add to reference array
							$arrCatalog[$k]['ref'] = $objRef->fetchAllAssoc();								
						}
					}
					break;
				case 'file':
				case 'image':
					// add file and image information
					$arrCatalog[$k]['files'] = $arrValues['items'];
					$arrCatalog[$k]['meta'] = $arrValues['values'];

					break;
				case 'notelistvariants':
					if($this->stripTagsForNotelist)
						$arrCatalog[$k]['raw'] = strip_tags($arrCatalog[$k]['raw']);
				default:;
				
			}
		}
		return $arrCatalog;
	}

	public function tableName()
	{
		return $this->strTable;
	}

	/*
	 * NOOP function to make this class non-abstract
	 */
	public function compile(){}
}

/**
 * Class FormCatalogNoteList
 *
 * @copyright  Christian Schiffler 2010
 * @author     Christian Schiffler  <c.schiffler@cyberspectrum.de> 
 * @package	   Controller
 *
 */
class FormCatalogNoteList extends Widget
{
	/**
	 * Submit user input
	 * @var boolean
	 */
	protected $blnSubmitInput = true;

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'form_widget';
	protected $strSubTemplate = 'form_catalognotelist';
	protected $strSubTemplateMail = 'form_catalognotelist_mail';

	public function __get($strKey)
	{
		if($strKey=='value')
			return $this->calculateValue(true);
		else
			return parent::__get($strKey);
	}

	public function calculateValue($forMail=false)
	{
		$this->catalog_visible=deserialize($this->catalog_visible);
		$this->import('Database');
		$objCatalogLister=$this->Database->prepare('SELECT ? AS catalog')->execute($this->catalog);
		$objInlineCatalog = new InternalModuleCatalogNotelist($objCatalogLister, 'main', $forMail);
		$objInlineCatalog->generate();
		$notelist=$this->Session->get('catalog_notelist');
		$items=$notelist[$this->catalog];
		if(!$items)
			$items=array();
		$ids=array();
		$formid = $this->strTable;
		foreach($items as $itemIndex=>$item)
		{
			$ids[]=$item['id'];
			$widgetBaseId=$this->strId.'_'.$item['id'];
			if(is_array($item['variants']))
			{
				foreach($item['variants'] as $variantId=>$variant)
				{
					$widgetBaseId.='_'.$variant['id'];
				}
			}
			// only when an amount has been specified, we allow the user to specify another one.
			if($items[$itemIndex]['amount'])
			{
				// amount edit field per item
				$id=$widgetBaseId.'_amount';
				$arrData=array('label'=>&$GLOBALS['TL_LANG']['notelistvariants']['amount'],'eval'=>array('rgxp' => 'digit', 'mandatory'=>true));
				$amount=(strlen($this->Input->post($id))?$this->Input->post($id):$items[$itemIndex]['amount']);
				$objAmountWidget=new FormTextField($this->prepareForWidget($arrData, $id, $amount, $id, $formid));
				if(strlen($this->Input->post($id)) && $objAmountWidget->validate() && $objAmountWidget->hasErrors()){}
				$items[$itemIndex]['input_amount']='<div class="notelistamount">' . $objAmountWidget->parse(array('tableless' => true)) . '</div>';
				// update amount button per item
				$id=$widgetBaseId.'_updateamount';
				$arrData=array('label'=>&$GLOBALS['TL_LANG']['notelistvariants']['updateamount'],'eval'=>array('rgxp' => 'digit', 'mandatory'=>true));
				$arrData=array();
				$objUpdateAmountWidget=new FormSubmit($this->prepareForWidget($arrData, $id, '', $id, $formid));
				$objUpdateAmountWidget->slabel=$GLOBALS['TL_LANG']['notelistvariants']['updateLabel'];
				$items[$itemIndex]['input_update']='<div class="notelistupdateamount">' . $objUpdateAmountWidget->parse(array('tableless' => true)) . '</div>';
			}
			// remove from notelist button
			$id=$widgetBaseId.'_remove';
			$arrData=array();
			$objRemoveWidget=new FormSubmit($this->prepareForWidget($arrData, $id, '', $id, $formid));
			$objRemoveWidget->slabel=$GLOBALS['TL_LANG']['notelistvariants']['removeLabel'];
			$items[$itemIndex]['input_remove']='<div class="notelistremove">' . $objRemoveWidget->parse(array('tableless' => true)) . '</div>';
		}
		// now fetch all items of interest from that catalog
		$objItems=$this->Database->prepare('SELECT * FROM '.$objInlineCatalog->tableName().' WHERE id IN (\''.implode('\',\'', $ids).'\')')->execute($this->catalog);
		while($objItems->next())
		{
			$arrCatalog=$objInlineCatalog->prepareFields($objItems->row());
			foreach($this->catalog_visible as $field)
			{
				foreach($items as $k=>$item)
				{
					if($item['id']==$objItems->id)
					{
						$items[$k]['fields'][$field] = array(
													'title' => &$GLOBALS['TL_DCA'][$objInlineCatalog->tableName()]['fields'][$field]['label'][0], 
													'value' => $arrCatalog[$field]
												);
					}
				}
			}
		}
		if($forMail)
			$strTemplate=$this->strSubTemplateMail;
		else
			$strTemplate=$this->strSubTemplate;
		$objTemplate=new FrontendTemplate($strTemplate);
		$objTemplate->description=strip_tags($this->text);
		$objTemplate->items=$items;
		$objTemplate->strId='ctrl_'.$this->id;
		
		return $objTemplate->parse();
	}

	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### CATALOG NOTE LIST ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			if (version_compare(VERSION.'.'.BUILD, '2.9.0', '>='))
				$objTemplate->href = 'contao/main.php?do=form&amp;table=tl_form_field&amp;act=edit&amp;id=' . $this->id;
			else
				$objTemplate->href = 'typolight/main.php?do=form&amp;table=tl_form_field&amp;act=edit&amp;id=' . $this->id;
			return $objTemplate->parse();
		}
		return $this->calculateValue();
	}

	/**
	 * Handle item deletions in here
	 * @return string
	 */
	public function validate()
	{
		$notelist=$this->Session->get('catalog_notelist');
		$items=$notelist[$this->catalog];
		// no items, nothing to do.
		if(!$items)
			return;
		$notelistModified=false;
		foreach($items as $k=>$item)
		{
			$widgetBaseId=$this->strId.'_'.$item['id'];
			if(is_array($item['variants']))
			{
				foreach($item['variants'] as $variantId=>$variant)
				{
					$widgetBaseId.='_'.$variant['id'];
				}
			}
			$id=$widgetBaseId.'_remove';
			if($this->Input->post($id))
			{
				unset($items[$k]);
				$notelistModified=true;
			} else {
				// only when an amount has been specified, we allow the user to specify another one.
				if($items[$k]['amount'])
				{
					// validate amounts, might got changed in input.
					$id=$widgetBaseId.'_amount';
					$amount=(strlen($this->Input->post($id))?$this->Input->post($id):$items[$k]['amount']);
					$arrData=array('eval'=>array('rgxp' => 'digit', 'mandatory'=>true));
					$objAmountWidget=new FormTextField($this->prepareForWidget($arrData, $id, $amount, $id, $formid));
					$objAmountWidget->validate();
					if($objAmountWidget->hasErrors())
					{
						$this->class = 'error';
						$this->addError('');
					} else {
						if($items[$k]['amount']!=$objAmountWidget->value)
						{
							$items[$k]['amount']=$objAmountWidget->value;
							$notelistModified=true;
						}
					}
				}
			}
		}
		$notelist[$this->catalog]=$items;
		$this->Session->set('catalog_notelist', $notelist);
		if($notelistModified)
			$this->reload();
	}
}


?>