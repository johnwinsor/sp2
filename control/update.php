<?php

/**
 *   @file control/update.php
 *   @brief help updating to SubjectsPlus 2.0
 *   @description This file will help a user walk through basic steps to update
 * 	 to SubjectsPlus 2.0
 *
 *   @author dgonzalez
 *   @date Feb 2013
 *   @todo
 */

//set varirables needed in header
$subcat = "update";
$page_title = "Update";
$sessionCheck = 'no';
$no_header = "yes";
$updateCheck = 'no';

include("includes/header.php");

//logo only header
displayLogoOnlyHeader();

//find what step we are in
$lintStep = ( isset( $_GET['step'] ) ? (int) $_GET['step'] : 0 );

//if at SubjectsPlus 2.0 already, display message and discontinue
if( isUpdated() )
{
	?>
	<div id="maincontent" style="max-width: 800px; margin-right: auto; margin-left: auto;">
		<h2 class="bw_head"><?php echo _( "Already Updated" ); ?></h2>
		<div class="box required_field">
			<p><?php echo _( 'Already at SubjectsPlus 2.0. No need to run updater.' ) ?></p>
			<p><a href="login.php"><?php echo _( 'Log In.' ) ?></a></p>
		</div>
	</div>
	<?php
}else
{
	//new of Updater
	$lobjUpdater = new sp_Updater();

	//depending on step, display content
	switch( $lintStep )
	{
		case 0:
			//first show updater message
			$lobjUpdater->displayStartingUpdaterPage();
			break;
		case 1:
			//update and on success show complete page
			if( $lobjUpdater->update( ) )
			{
				$lobjUpdater->displayUpdaterCompletePage();
				$_SESSION['firstUpdate'] = 1;
			}
			break;
	}
}

include("includes/footer.php");

?>