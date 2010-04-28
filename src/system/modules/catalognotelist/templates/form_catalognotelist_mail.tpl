<?php 
/***
 * Sample "View notelist" template file which get used to render a form in the email/form saving.
 * This sample shows how to display the notelist form element.
 * To see the data structure, uncomment the print_r() 
 */
?>
<?php // print_r($this->items); ?>
<?php if($this->description): ?>
<?php echo $this->description; ?>
<?php endif; ?>
<?php foreach($this->items as $item): ?>
<?php foreach($item['fields'] as $k=>$field): ?>
<?php echo $field['title']; ?>: <?php echo $field['value']['raw']; ?> 
<?php endforeach; ?>
<?php foreach($item['variants'] as $k=>$variant): ?>

<?php echo $variant['title']; ?>: <?php echo $variant['name']; ?>

<?php endforeach; ?>
<?php endforeach; ?>
<?php echo $this->amount; ?>
