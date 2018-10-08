<?php
/*
 * $Id$
*/
?>
<!-- menus général -->


<div class="panel-body">
	 <div class="col-xs-6 col-md-4"><a<?php if (isset($newentree['ancre'])){?> name="<?php echo $newentree['ancre']?>"<?php } ?>
		href="<?php echo $newentree['lien']?>"> <?php echo $newentree['titre']?></a>
		</div>
		 <div class="col-xs-12 col-md-8">	<?php echo $newentree['expli']?> </div>
</div>


<!-- Fin menu	général -->	
