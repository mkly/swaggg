<?php defined('C5_EXECUTE') or die('Access Denied.') ?>
<div class="swaggg-upload">

<?php if($upload_successful): ?>

	<h2><?php echo t('Upload Successful') ?></h2>
	<img src="<?php echo $swaggg->getComputed('thumbnail', 400, 300)->src ?>" />

<?php else: ?>

	<?php if($errors): ?>
	
		<ul class="errors">
			<?php foreach($errors as $error): ?>
				<li><?php echo $error ?></li>	
			<?php endforeach ?>
		</ul>

	<?php endif ?>

	<form method="post" enctype="multipart/form-data" action="<?php echo $this->action('upload') ?>">
		<div class="swaggg-form swaggg-form-label">
			<?php echo $form->label('image', t('Swagggin Image')) ?>
		</div>
		<div class="swaggg-form swaggg-form-input">
			<input type="file" name="image" />
		</div>
		<div class="swaggg-form swaggg-form-label">
			<?php echo $form->label('description', t('Why it\'s swagggin')) ?>
		</div>
		<div class="swaggg-form swaggg-form-input">
			<?php echo $form->textarea('description', $description) ?>
		</div>
		<div class="swaggg-form swaggg-form-submit">
			<?php echo $form->submit('Submit', t('Upload Swaggg')) ?>
		</div>
	</form>

<?php endif ?>

</div><!-- .swaggg-upload -->
