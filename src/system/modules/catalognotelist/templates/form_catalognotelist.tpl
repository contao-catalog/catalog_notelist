<?php 

/***
 * Sample "View notelist" template file which get used to render a form in the frontend.
 * This sample shows how to display the notelist form element.
 * To see the data structure, uncomment the print_r() 
 */
 
?>
<?php // print_r($this->items); ?>
<div class="formnotelist">
<input type="hidden" value="" id="<?php echo $this->strId; ?>" />
<?php if($this->description): ?>
<?php echo $this->description; ?>
<?php endif; ?>
<?php foreach($this->items as $item): ?>
<div class="notelistitem">
<?php if($item['amount']): ?>
<?php echo $item['amount']; ?> x
<?php endif; ?>
<?php foreach($item['fields'] as $k=>$field): ?>
<div class="field">
<div class="title"><?php echo $field['title']; ?></div>
<div class="value"><?php echo $field['value']['value']; ?></div>
</div>
<?php endforeach; ?>
<?php if($item['variants']): ?>
<div class="notelistvariants">
<?php foreach($item['variants'] as $k=>$variant): ?>
<?php echo $variant['name']; ?>
<?php endforeach; ?>
</div>
<?php endif; ?>
<?php echo $item['input_amount']; ?>
<?php echo $item['input_update']; ?>
<?php echo $item['input_remove']; ?>
</div>
<?php endforeach; ?>
</div>
