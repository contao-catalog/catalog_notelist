<?php 

/***
 * Sample "View notelist" template file which get used to render a form in the frontend.
 * This sample shows how to display the notelist form element.
 * To see the data structure, uncomment the print_r() 
 */
 
?>
<?php // print_r($this->items); ?>
<div class="formnotelist">
<?php if($this->description): ?>
<?php echo $this->description; ?>
<?php endif; ?>
<?php foreach($this->items as $item): ?>
<div class="notelistitem">
<?php echo $item['amount']; ?> x
<?php foreach($item['fields'] as $k=>$field): ?>
<div class="field">
<span class="title"><?php echo $field['title']; ?></span>
<span class="value"><?php echo $field['value']['value']; ?></span>
</div>
<?php endforeach; ?>
<div class="notelistvariants">
<?php foreach($item['variants'] as $k=>$variant): ?>
<?php echo $variant['name']; ?>
<?php endforeach; ?>
</div>
<?php echo $item['input_amount']; ?>
<?php echo $item['input_update']; ?>
<?php echo $item['input_remove']; ?>
</div>
<?php endforeach; ?>
</div>
