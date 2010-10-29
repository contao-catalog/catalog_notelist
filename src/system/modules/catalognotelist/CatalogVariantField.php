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
 * The Catalog extension allows the creation of multiple catalogs of custom items,
 * each with its own unique set of selectable field types, with field extendability.
 * The Front-End modules allow you to build powerful listing and filtering of the 
 * data in each catalog.
 * 
 * PHP version 5
 * @copyright  Christian Schiffler 2010
 * @author     Christian Schiffler  <c.schiffler@cyberspectrum.de> 
 * @package    CatalogVariantField
 * @license    LGPL 
 * @filesource
 */


// class to inject the field data into the page META-tags.
class CatalogVariantField extends Frontend
{
	public function parseValue($id, $fieldname, $raw, $blnImageLink, $objCatalog, $objCatalogInstance)
	{
		$items=array();
		if(!$objCatalog)
			return array('items'=>$items, 'values'=>false, 'html'=>'');


		$this->Template=new FrontendTemplate('catalog_notelistfield');
		$formid='notelist_'.$objCatalog->pid.'_'.$objCatalog->id;
		$this->Template->formid=$formid;
		$this->Template->action=$this->Environment->request;
		$this->Template->addSumbit=$GLOBALS['TL_LANG']['notelistvariants']['addlabel'];
		$this->Template->catid=$objCatalog->pid;
		$this->Template->itemid=$objCatalog->id;
		// if we want to provide an descriptiontext, it will be present within "raw".
		$items['description']=$raw;
		$this->Template->description=$items['description'];

		$data=$objCatalog->row();
		$items['variants']=array();
		$submitOk=$this->Input->post('FORM_SUBMIT')==$formid;

		// find out what options we want to provide.
		$objFieldConf=$this->Database->prepare('SELECT *,(SELECT tableName FROM tl_catalog_types WHERE id=?) AS tableName FROM tl_catalog_fields WHERE pid=? AND colName=?')->execute($objCatalog->pid, $objCatalog->pid, $fieldname);
		$desiredOptions=deserialize($objFieldConf->notelistvariants);
		if(!$desiredOptions)
			$desiredOptions=array('');
		// determine foreign key values.
		$objVariantValues=$this->Database->prepare('SELECT * FROM tl_catalog_fields WHERE pid=? AND colName IN (\''.implode('\',\'', $desiredOptions).'\')')->execute($objCatalog->pid);
		$arrData=array('eval'=> array('rgxp' => 'digit'));
		// generate widgets and everything else for all the variants.
		while($objVariantValues->next())
		{
			$items['variants'][$objVariantValues->colName]['colmeta']=$objVariantValues->row();
			$objOptions=$this->Database->prepare('SELECT * FROM '.$objVariantValues->itemTable.' WHERE FIND_IN_SET(id, (SELECT '.$objVariantValues->colName.' FROM '.$objFieldConf->tableName.' WHERE id=?))>0')->execute($objCatalog->id);
			if($objOptions->numRows)
			{
				$options=array(0 => array('id' => 0, 'NOTELIST_VALUE' => $GLOBALS['TL_LANG']['notelistvariants']['emptyLabel']));
				while($objOptions->next())
				{
					$options[$objOptions->id]=$objOptions->row();
					$options[$objOptions->id]['NOTELIST_VALUE']=$objOptions->{$objVariantValues->itemTableValueCol};
				}
			}
			else
				$options=array();
			
			$items['variants'][$objVariantValues->colName]['options']=$options;
			$items['variants'][$objVariantValues->colName]['id']=$id.$objVariantValues->colName;
			$items['variants'][$objVariantValues->colName]['name']=$objVariantValues->name;

			$arrData['label']=$objVariantValues->name;
			$arrData['options']=array();
			foreach($options as $v)
			{
				$arrData['options'][$v['id']]=$v['NOTELIST_VALUE'];
			}
			$id=$formid.'_'.$objVariantValues->colName;
			$objVariantWidget=new FormSelectMenu($this->prepareForWidget($arrData, $id, $this->Input->post($id), $id, $formid));
			if($submitOk && $objVariantWidget->validate() && $objVariantWidget->hasErrors())$submitOk=false;
			// would love to use $this->Input here but sadly it does not provide something like isset()
			if(array_key_exists($id, $_POST) && !$objVariantWidget->value)
			{
				$objVariantWidget->addError($GLOBALS['TL_LANG']['notelistvariants']['pleaseSelect']);
				$submitOk = false;
			}
			$items['variants'][$objVariantValues->colName]['widget']='<div class="notelistvariant">' . $objVariantWidget->parse(array('tableless' => true)) . '</div>';
			$items['variants'][$objVariantValues->colName]['objWidget']=$objVariantWidget;
			$items['variants'][$objVariantValues->colName]['optioncount']=count($options);
		}

		$this->Template->variants=$items['variants'];
		if($this->notelistselamount)
		{
			// amount widget
			$id=$formid.'_amount';
			$arrData=array('label'=>&$GLOBALS['TL_LANG']['notelistvariants']['amount'],'eval'=>array('rgxp' => 'digit', 'mandatory'=>true));
			$amount=(strlen($this->Input->post($id))?$this->Input->post($id):'1');
			$objAmountWidget=new FormTextField($this->prepareForWidget($arrData, $id, $amount, $id, $formid));
			if($submitOk && $objAmountWidget->validate() && $objAmountWidget->hasErrors())$submitOk=false;
			$this->Template->amount='<div class="notelistamount">' . $objAmountWidget->parse(array('tableless' => true)) . '</div>';
		}
		if($submitOk)
		{
			// form submit ok, we want to add to notelist now.
			$all_notelist=$this->Session->get('catalog_notelist');
			if(!is_array($all_notelist))$all_notelist=array();
			$notelist=$all_notelist[$objCatalog->pid];
			
			// check if item is already in notelist.
			$newitem=array
			(
				'catId' => $objCatalog->pid,
				'id' => $objCatalog->id,
				'amount' => $objAmountWidget->value
			);
			foreach($items['variants'] as $variantId=>$v)
			{
				$newitem['variants'][$variantId]=array('id'=>$v['objWidget']->value, 'name' => $v['options'][$v['objWidget']->value]['NOTELIST_VALUE'], 'colname' => $v['colmeta']['colName'], 'description' => $v['colmeta']['description'], 'title' => $v['colmeta']['name']);
			}
			if(is_array($notelist))
			{
				foreach($notelist as $k=>$notelistItem)
				{
					$isSame=true;
					if($notelistItem['catId']==$objCatalog->pid && $notelistItem['id']==$objCatalog->id)
					{
						// check if variants are the same.
						foreach($items['variants'] as $v=>$x)
						{
							if($newitem['variants'][$v]!=$notelistItem['variants'][$v])
							{
								$isSame=false;
							}
						}
					}
					else
					{
						$isSame=false;
					}
					if($isSame)
						break;
				}
				if($isSame)
					$notelist[$k]=$newitem;
				else
					$notelist[]=$newitem;
			} else {
				$notelist=array($newitem);
			}
			$all_notelist[$objCatalog->pid]=$notelist;
			$this->Session->set('catalog_notelist', $all_notelist);
			$this->Template->addMessage = $GLOBALS['TL_LANG']['notelistvariants']['itemadded'];
		}
		return array
				(
				 	'items'	=> $items,
					'values' => true,
				 	'html'  => $this->Template->parse(),
				);
	}
}
?>