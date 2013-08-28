<?php
/**
 * Pagination links for index views
 */
?>
<div class="row text-center">
	<ul class="pagination">
		<?php echo "<?php
		if (\$this->Paginator->hasPrev()):
			echo \$this->Paginator->first('<i class=\"icon-step-backward\"></i>', array('tag' => 'li', 'escape' => false));
			echo \$this->Paginator->prev('<i class=\"icon-backward\"></i>', array('tag' => 'li', 'escape' => false));
		else:
			?>"; ?>
			<li class="disabled"><span><i class="icon-step-backward"></i></span></li>
			<li class="disabled"><span><i class="icon-backward"></i></span></li>
		<?php echo "<?php
		endif;
		echo \$this->Paginator->numbers(array(
			'tag' => 'li',
			'modulus' => '4',
			'separator' => '',
			'currentClass' => 'active',
		));
		if (\$this->Paginator->hasNext()):
			echo \$this->Paginator->next('<i class=\"icon-forward\"></i>', array('tag' => 'li', 'escape' => false));
			echo \$this->Paginator->last('<i class=\"icon-step-forward\"></i>', array('tag' => 'li', 'escape' => false));
		else:
			?>
			<li class=\"disabled\"><span><i class=\"icon-forward\"></i></span></li>
			<li class=\"disabled\"><span><i class=\"icon-step-forward\"></i></span></li>
<?php endif; ?>"; ?>
	</ul>
	<br />
	<small class="hidden-sm"><?php echo "<?php echo \$this->Paginator->counter('Page {:page}/{:pages}') ?>"; ?></small>
</div>