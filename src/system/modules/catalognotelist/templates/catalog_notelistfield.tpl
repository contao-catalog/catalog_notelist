<?php 

/***
 * Sample "Add to notelist" template file
 * This sample shows how to display the add to notelist fieldtype, if you want to alter the 
 * appearance, please keep in mind to provide a proper form sending mechanism to this very same URL.
 * To see the data structure, uncomment the print_r() 
 */
 
?>
<?php //print_r($this->arrData); ?>
<?php //print_r(deserialize($this->widgets['filter'][0]['options'])); ?>
<?php if($this->addMessage): ?><?php echo $this->addMessage; ?><?php endif; ?>
<form method="post" id="<?php echo $this->formid; ?>" action="<?php echo $this->action; ?>">	
<div class="addtonotelist">
<input type="hidden" name="FORM_SUBMIT" value="<?php echo $this->formid; ?>" />
<input type="hidden" name="catid" value="<?php echo $this->catid; ?>" />
<input type="hidden" name="itemid" value="<?php echo $this->itemid; ?>" />
<div class="notelistvariants">
<?php foreach($this->variants as $variant): ?>
<?php if($variant['optioncount']): ?>
<?php echo $variant['widget']; ?>
<?php endif; ?>
<?php endforeach; ?>
<?php if($this->description): ?>
<?php echo $this->description; ?>
<?php endif; ?>
</div>
<?php if($this->amount): ?>
<?php echo $this->amount; ?>
<?php endif; ?>
<input type="submit" class="submit" value="<?php echo $this->addSumbit; ?>" />
</div>
</form>