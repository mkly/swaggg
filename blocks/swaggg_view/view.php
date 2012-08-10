<?php defined('C5_EXECUTE') or die('Access Denied.') ?>
<div class="swaggg-view">

<?php if($error): ?>
	<div class="error">
		<?php echo $error ?>
	</div>
<?php endif ?>

<img src="<?php echo $swaggg->getComputed('thumbnail', 400,300)->src ?>" />
<p><?php echo $swaggg->get('description') ?></p>

<div class="swaggg-controls cf">

	<form method="post" action="<?php echo $this->action('swaggg') ?>" class="swaggg-increment">
		<?php echo $form->hidden('swaggg_id', $swaggg->get('id')) ?>
		<?php echo $form->submit('swaggg', t('Swaggg')) ?>
	</form>

	<form method="post" action="<?php echo $this->action('swoggg') ?>" class="swoggg-increment">
		<?php echo $form->hidden('swaggg_id', $swaggg->get('id')) ?>
		<?php echo $form->submit('swoggg', t('Swoggg')) ?>
	</form>

</div><!-- .swaggg-controls -->

<div class="cf"></div>

<div class="swaggg-bar-outer cf">
	<div class="swaggg-bar">

		<?php if(!$swaggg->getComputed('swaggg_percentage') && !$swaggg->getComputed('swoggg_percentage')): ?>
			<div class="swaggg-bar-begin swaggg-bar"><?php echo t('First Judgement') ?></div>

		<?php else: ?>
			<div class="swaggg-bar-swaggg swaggg-bar" style="width: <?php echo $swaggg->getComputed('swaggg_percentage') - 1?>%"><span><?php echo t('Swaggg') ?></span></div>

			<div class="swaggg-bar-swoggg swaggg-bar" style="width: <?php echo $swaggg->getComputed('swoggg_percentage') - 1?>%"><span><?php echo t('Swoggg') ?></span></div>
		<?php endif ?>

	</div><!-- .swaggg-bar -->
</div><!-- .swaggg-bar-outer -->

<a href="<?php echo $next_swagg_link ?>" class="next-swaggg-link cf"><?php echo t('Next Swaggg') ?></a>

</div><!-- .swaggg-view -->
