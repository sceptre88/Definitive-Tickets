<div class="container main-content clear">

	<?php 
	if ((substr(basename($PHP_SELF), 0, 5) != 'index') or ($cPath>0))
	{ 
		if (SHOW_BREADCRUMB == 'yes')
		{
			require_once(DIR_WS_TEMPLATES.TEMPLATE_NAME.'/content/breadcrumb.php');
		} 	
	}
	?>
<div class="row">
		<?php 
		if (DISPLAY_COLUMN_LEFT == 'yes') 
		{
		if (DOWN_FOR_MAINTENANCE =='false' || DOWN_FOR_MAINTENANCE_COLUMN_LEFT_OFF =='false') 
		{ 
		?>
		<div class="col-lg-3 order-lg-first order-last">
		<?php require_once(DIR_WS_INCLUDES.'column_left.php'); ?>
		</div><!-- end: sidebar-left -->
		<?php
		}
		?>
		<div class="col-lg-9">
		<?php 
		}elseif (DISPLAY_COLUMN_RIGHT == 'yes') 
		{
		?>
<div class="col-lg-3 order-last">
		<?php require_once(DIR_WS_INCLUDES.'column_left.php'); ?>
		</div><!-- end: sidebar-right -->
		<div class="col-lg-9 order-first">
		<?php
		}elseif ((DISPLAY_COLUMN_LEFT == 'no') && (DISPLAY_COLUMN_RIGHT == 'no'))
		{
		?>
<div class="col-md-12">
		<?php
		}
		if ($FREQUEST->getvalue('error_message')) 
		{ 
		?>
		<div class="headerError"><?php echo htmlspecialchars(urldecode($FREQUEST->getvalue('error_message')));?></div>
		<?php 
		}

		if (isset($content_template)) 
		{ 
		require(DIR_WS_CONTENT . $content_template);
		}
		else if (isset($GLOBALS["TPL_CONTENT"]))
		{
		echo $GLOBALS["TPL_CONTENT"];
		}
		///////////////////////////////////////////////////////////////////
		else
		{
		if (file_exists(DIR_FS_CATALOG . DIR_WS_TEMPLATES . TEMPLATE_NAME . "/content/" . $content . '.tpl.php'))
		{
		require(DIR_FS_CATALOG.DIR_WS_TEMPLATES.TEMPLATE_NAME."/content/".$content.'.tpl.php');
		}
		else
		{
		require(DIR_WS_CONTENT.$content.'.tpl.php');
		}
		}
		///////////////////////////////////////////////////////////////////
		?>
		</div>
		</div>
	</div>