// @todo close new box drop down immediately upon dropping something
// @todo if you click on a choice from the dropdown, insert it into left col (i.e.
// make the drag optional--or will that work?)

jQuery(document).ready(function(){

	makeDropable(".dropspotty");

    makeSortable(".sort-column");

    makeDraggable(".draggable");

    setupSaveButton('#save_guide');

    makeEditable('a[id*=edit]');

    makeDeleteable('a[id*=delete]');

    setupAllColorboxes();

    setupMiscLiveQueries();

    setupMiscClickEvents();

    makeHelpable("img[class*=help-]");

}); // End jQuery within document.ready

function makeDropable( lstrSelector )
{
	////////////////////////////////
	// SET UP DROP SPOTS
	// --makes divs with class of "dropspotty" droppables
	// accepts things with class of "draggable"
	//
	////////////////////////////////

	jQuery(lstrSelector).droppable({

		hoverClass: "drop_hover",
		accept: ".draggable",
		drop: function(event, props) {
			// if there can only be one, could remove from list items
			var drop_id = jQuery(this).attr("id");
			var drag_id = jQuery(props.draggable).attr("id");
			//alert(drag_id);
			var pluslet_title = jQuery(props.draggable).html();

			// Create new node below, using a random number

			var randomnumber=Math.floor(Math.random()*1000001);
			jQuery(this).next('div').prepend("<div class=\"dropspotty\" id=\"new-" + randomnumber + "\"></div>");
			//alert (drag_id + " drop: " + drop_id);

			// Load new data, on success (or failure!) change class of container to "pluslet", and thus draggable in theory
			jQuery("#new-" + randomnumber).fadeIn("slow").load("helpers/guide_data.php", {
				from: drag_id,
				to: drop_id,
				pluslet_title: pluslet_title,
				flag: 'drop',
				this_subject_id:subject_id
			},
			function() {

				// 1.  remove the wrapper
				// 2. put the contents of the div into a variable
				// 3.  replace parent div (i.e., id="new-xxxxxx") with the content made by loaded file
				var cnt = jQuery("#new-" + randomnumber).contents();
				jQuery("#new-" + randomnumber).replaceWith(cnt);
				//alert(jQuery(this).attr("id"));
				jQuery(this).addClass("unsortable");

				jQuery("#response").hide();
				//Make save button appear, since there has been a change to the page
				jQuery("#save_guide").fadeIn();


				jQuery("a[class*=showmedium]").colorbox({
					iframe: true,
					innerWidth:"90%",
					innerHeight:"80%",
					maxWidth: "1100px",
					maxHeight: "800px"
				});

				makeHelpable("img[class*=help-]");

			});

		}
	});
}

function makeSortable( lstrSelector )
{

	////////////////////////////
    // MAKE COLUMNS SORTABLE
    // Make "Save Changes" button appear on sorting
    ////////////////////////////

	jQuery(lstrSelector).livequery(function() {

		jQuery(this).sortable({

			connectWith :['#portal-column-0', '#portal-column-1', '#portal-column-2'],
			opacity: 0.7,
			tolerance: 'intersect',
			cancel: '.unsortable',
			update: function(event, ui) {
				jQuery("#response").hide();
				jQuery("#save_guide").fadeIn();

			}
		});
	});
}

function makeDraggable( lstrSelector )
{
	////////////////////////////////
	// SET UP DRAGGABLE
	// --makes anyting with class of "draggable" draggable
	////////////////////////////////

	jQuery(lstrSelector).livequery(function() {

		jQuery(this).draggable({
			ghosting:	true,
			opacity:	0.5,
			revert: true,
			fx:			300,
			cursor: 'pointer',
			helper: 'clone',
			zIndex: 350
		});
	});
}

function setupSaveButton( lstrSelector )
{
	////////////////////////////
	// SAVE GUIDE'S LAYOUT
	// -- this saves everything on page
	////////////////////////////

	jQuery(lstrSelector).livequery('click', function(event) {

		// make sure our required fields have values before continuing
		var test_req = checkRequired();

		if (test_req == 1) {
			alert("You must complete all required form fields.");
			return false;
		}

		// 1.  Look for new- or modified-pluslet
		// 2.  Check to make sure data is okay
		// 3.  Save to DB
		// 4.  Recreate pluslet with ID
		// 5.  Save layout

		////////////////////
		// modified-pluslet
		// loop through each pluslet
		///////////////////
		jQuery('div[name*=modified-pluslet]').each(function() {

			var update_id = jQuery(this).attr("id").split("-");
			var this_id = update_id[1];

			//prepare the pluslets for saving
			preparePluslets("modified", this_id, this);
		});

		////////////////////////
		// Now the new pluslets
		////////////////////////

		jQuery('div[name*=new-pluslet]').each(function() {

			var insert_id = jQuery(this).attr("id"); // just a random gen number

			//prepare pluslets for saving
			preparePluslets("new", insert_id, this);
		});

		//////////////////////
		// We're good, save the guide layout
		// insert a pause so the new pluslet is found
		//////////////////////
		jQuery("#response").hide();
		jQuery("#save_guide").fadeOut();
		//jQuery("#savour").append('<span class="loader"><img src="images/loading_animated.gif" height="30" /></span>');
		setTimeout(saveGuide, 1000);

		return false;

	});

	///////////////////////
	// preparePluslets FUNCTION
	// called to prepare all the pluslets before saving them
	///////////////////////

	function preparePluslets( lstrType, lintID, lobjThis )
	{
		//based on type set variables
		switch( lstrType.toLowerCase() )
		{
			case "modified":
			{
				//used to get contents of CKeditor box
				var lstrInstance = "pluslet-update-body-" + lintID;
				//Title of item
				var lstrTitle = addslashes(jQuery("#pluslet-update-title-" + lintID).attr('value'));
				//Div Selector
				var lstrDiv = "#pluslet-" + lintID;
				//depending update_id
				var lintUID = lintID;
				break;
			}
			case "new":
			{
				//used to get contents of CKeditor box
				var lstrInstance = "pluslet-new-body-" + lintID;
				//Title of item
				var lstrTitle = addslashes(jQuery("#pluslet-new-title-" + lintID).attr('value'));
				//Div Selector
				var lstrDiv = "#" + lintID;
				//depending update_id
				var lintUID = '';
				break;
			}
		}
		//////////////////////////////////////////////////////////////////
		// Check the pluslet's "name" value to see if there is a number
		// --If it is numeric, it's a "normal" item with a ckeditor instance
		// collecting the "body" information
		//////////////////////////////////////////////////////////////

		var item_type = jQuery(lobjThis).attr("name").split("-");

		// Loop through the box types
		switch (item_type[2]) {
			case "Basic":
				var pbody = addslashes(CKEDITOR.instances[lstrInstance].getData());
				var pitem_type = "Basic";
				var pspecial = '';
				break;
			case "Heading":
				var pbody = ""; // headings have no body
				var pitem_type = "Heading";
				var pspecial = '';
				break;
			case "TOC":
				var pbody = "";
				var pitem_type = "TOC";
				var tickedBoxes = new Array();
				jQuery('input[name=checkbox-'+lintID+']:checked').each(function() {
					// alert(this.value);
					tickedBoxes.push(this.value);

				});
				var pspecial = '{"ticked":"' + tickedBoxes + '"}';
				//alert(pspecial);
				break;
			case "Feed":
				var pbody = jQuery('input[name=' + lstrInstance + ']').attr('value');
				var pfeed_type = jQuery('select[name=feed_type-'+lintID+']').attr('value');
				var pnum_items = jQuery('input[name=displaynum-'+lintID+']').attr('value');
				var pshow_desc = jQuery('input[name=showdesc-'+lintID+']:checked').val();
				var pshow_feed = jQuery('input[name=showfeed-'+lintID+']:checked').val();

				var pspecial = '{"num_items":'
				+ pnum_items +
				',  "show_desc":'
				+ pshow_desc +
				', "show_feed": '
				+ pshow_feed +
				', "feed_type": "'
				+ pfeed_type +
				'"}';

				var pitem_type = "Feed";
				//pitems = " +pnum_items + " pshow_desc = " + pshow_desc + " pshow_feed = " +pshow_feed
				//alert ("Feed update:" + pspecial );
				break;
				default:
				alert ("no matching item type");
				break;
		}

		//only check clone if modified pluslet
		if( lstrType == 'modified')
		{
			//////////////////////
			// Clone?
			// If it's a clone, add a new entry to DB
			/////////////////////

			var clone = jQuery("#pluslet-" + lintID).attr("class");

			if (clone.indexOf("clone")!=-1) {
				var ourflag = 'insert';
				var isclone = 1;
				//alert("it's a clone!");
			} else {
				var ourflag = 'update';
				var isclone = 0;
			}
		}else
		{
			var ourflag = 'insert';
			var isclone = 0;
		}

		////////////////////////
		// Load the data into guide_data.php
		// which will do an insert or update as appropriate
		////////////////////////

		jQuery(lstrDiv).load("helpers/guide_data.php", {
			update_id: lintUID,
			pluslet_title:lstrTitle,
			pluslet_body:pbody,
			flag: ourflag,
			staff_id: user_id,
			item_type: pitem_type,
			clone:isclone,
			special: pspecial,
			this_subject_id: subject_id
		},
		function() {

			// check if it's an insert or an update, and name div accordingly
			if (ourflag == "update" || isclone == 1) {
				var this_div = '#pluslet-'+lintID;
			} else {
				var this_div = '#'+lintID;
			}

			// 1.  remove the wrapper
			// 2. put the contents of the div into a variable
			// 3.  replace parent div (i.e., id="xxxxxx") with the content made by loaded file
			var cnt = jQuery(this_div).contents();
			//alert(cnt);
			jQuery(this_div).replaceWith(cnt);

		});
	}

	///////////////////////
	// saveGuide FUNCTION
	// called at end of previous section
	//////////////////////

	function saveGuide() {

		jQuery('#portal-column-0').sortable();
		jQuery('#portal-column-1').sortable();
		jQuery('#portal-column-2').sortable();

		var left_data = jQuery('#portal-column-0').sortable('serialize');
		var center_data = jQuery('#portal-column-1').sortable('serialize');
		var sidebar_data = jQuery('#portal-column-2').sortable('serialize');

		jQuery("#response").load("helpers/save_guide.php", {
			this_subject_id: subject_id,
			user_name: user_name,
			left_data: left_data,
			center_data:center_data,
			sidebar_data:sidebar_data
		},
		function() {

			jQuery("#response").fadeIn();
			refreshFeeds();
			//jQuery(".sort-column").sortable();

		});

	}
}

function makeEditable( lstrSelector )
{
	////////////////////////////////
    // MODIFY PLUSLET -- on click of edit (pencil) icon
    ////////////////////////////////

    jQuery(lstrSelector).livequery('click', function(event) {

        var edit_id = jQuery(this).attr("id").split("-");
        //alert(edit_id[1]);
        ////////////
        // Clone?
        ////////////

        var clone = jQuery("#pluslet-" + edit_id[1]).attr("class");
        if (clone.indexOf("clone")!=-1) {
            var isclone = 1;
        } else {
            var isclone = 0;
        }

        /////////////////////////////////////
        // Load the form elements for editing
        /////////////////////////////////////

        jQuery('#' + 'pluslet-' + edit_id[1]).load("helpers/guide_data.php", {
            edit: edit_id[1],
            clone: isclone,
            flag: 'modify',
            type: edit_id[2],
            this_subject_id: subject_id
        },
        function() {
            ///////////////////////////////////////////////
            // 1.  remove the wrapper
            // 2. put the contents of the div into a variable
            // 3.  replace parent div (i.e., id="xxxxxx") with the content made by loaded file
            ///////////////////////////////////////////////

            var cnt = jQuery('#' + 'pluslet-' + edit_id[1]).contents();
            jQuery('#' + 'pluslet-' + edit_id[1]).replaceWith(cnt);

            /////////////////////////////////////
            // Make unsortable for the time being
            /////////////////////////////////////

            jQuery("#pluslet-" + edit_id[1]).addClass("unsortable");

            //////////////////////////////////////
            // We're changing the attribute here for the global save
            //////////////////////////////////////

            if (edit_id[2] != undefined) {
                var new_name = "modified-pluslet-" + edit_id[2];
                jQuery("#pluslet-" + edit_id[1]).attr("name", new_name);
            } else {
                jQuery("#pluslet-" + edit_id[1]).attr("name", "modified-pluslet");
            }

    		makeHelpable("img[class*=help-]");

        });

        //Make save button appear, since there has been a change to the page
        jQuery("#response").hide();
        jQuery("#save_guide").fadeIn();

        return false;
    });
}

function makeDeleteable( lstrSelector )
{
	////////////////////////////
    // DELETE PLUSLET
    // removes pluslet from DOM; change must be saved to persist
    /////////////////////////////

    jQuery(lstrSelector).livequery('click', function(event) {

        var delete_id = jQuery(this).attr("id").split("-");
        //jQuery(this).parent().hide();
        jQuery(this).after('<div class="rec_delete_confirm">Are you sure?  <a id="confirm-yes-' + delete_id[1] + '">Yes</a> | <a id="confirm-no">No</a></div>');

        return false;
    });


    jQuery('a[id*=confirm-yes]').livequery('click', function(event) {
        var this_record_id = jQuery(this).attr("id").split("-");



        // Delete pluslet from database
        jQuery('#response').load("helpers/guide_data.php", {
            delete_id: this_record_id[2],
            subject_id: subject_id,
            flag: 'delete'
        },
        function() {
            jQuery("#response").fadeIn();

        });

        // Remove node
        jQuery(this).parent().parent().parent().parent().remove();

        return false;

    });

    jQuery('a[id*=confirm-no]').livequery('click', function(event) {

        jQuery(this).parent().remove();

        return false;
    });
}

function setupAllColorboxes()
{
	/////////////////////////
    // SHOW PLUSLETS VIA SEARCH (Colorbox)
    // load colorbox file: discover.php (set in href of item)
    // when colorbox closes, it looks for the window.addItem variable
    // if it's a number other than 0, it adds the clone, and resets to 0
    /////////////////////////

    jQuery(".showdisco").colorbox({
        iframe: true,
        innerWidth:800,
        innerHeight:500,

        onClosed:function() {
            //alert("parent says item = " + window.addItem);
            // add item to page

            if (window.addItem != 0) {
                plantClone(window.addItem, window.addItemType);
                window.addItem = 0;
            }
        }
    });

    /////////////////
    // Load metadata window in modal window
    // maybe they need to have saved all changes before loading?
    ////////////////

    jQuery(".showmeta").colorbox({
        iframe: true,
        innerWidth:960,
        innerHeight:600,

        onClosed:function() {
        //reload window to show changes

        //window.location.href = window.location.href;

        }
    });


    /////////////////
    // Load new Record window in modal window
    // maybe they need to have saved all changes before loading?
    ////////////////

    jQuery(".showrecord").colorbox({
        iframe: true,
        innerWidth:"80%",
        innerHeight:"90%",

        onClosed:function() {
        //change title potentially & shortform for link


        }
    });


    /////////////////
    // Load metadata.php in modal window--to organize All Items by Source
    // called by ticking the pencil button
    ////////////////

    jQuery(".arrange_records").colorbox({
        iframe: true,
        innerWidth:"80%",
        innerHeight:"90%",

        onClosed:function() {
        //reload window to show changes
        //window.location.href = window.location.href;
        }
    });
}

function setupMiscLiveQueries()
{
	/////////////////////////
    // SHOW PLUSLETS VIA SUBJECT DROPDOWN
    /////////////////////////

    jQuery('#all_subs').livequery('change', function(event) {
        jQuery("#response").hide();
        jQuery("#find-results2").load("helpers/find_pluslets.php", {
            browse_subject_id: jQuery(this).val()
        });
    });

    /////////////////////////
    // HIDE NEW PLUSLETS DRAGGABLE SECTION
    /////////////////////////

    jQuery('#closer').livequery('click', function(event) {
        jQuery("#all-results").fadeOut("slow");
        return false;
    });

    /////////////////////////
    // HIDE FIND PLUSLETS DRAGGABLE SECTION
    /////////////////////////

    jQuery('#closer2').livequery('click', function(event) {
        jQuery("#find-results").fadeOut("slow");
        return false;
    });
    //////////////////////////////
    // .togglebody makes the body of a pluslet show or disappear
    //////////////////////////////

    jQuery('.togglebody').livequery('click', function(event) {

        jQuery(this).parent().parent().next('.pluslet_body').toggle('slow');
    });

    //////////////////////////////
    // .togglenew causes the all-results div to fade in
    // -- loads get_pluslets.php
    // mod agd OCT 2010
    //////////////////////////////

    jQuery('.togglenew').livequery('click', function(event) {
        //jQuery('#find-results').hide();
        //jQuery('#all-results').fadeIn();
        //jQuery("#all-results").load("get_pluslets.php", {base: 1 });
        jQuery("#all-results").fadeIn(2000);

    });

    ///////////////////////////////
    // Draw attention to TOC linked item
    ///////////////////////////////

    jQuery('a[id*=boxid-]').livequery('click', function(event) {
        var box_id = jQuery(this).attr("id").split("-");
        var selected_box = "#pluslet-" + box_id[1];

        jQuery(selected_box).effect("pulsate", {
            times:1
        }, 2000);
    //jQuery(selected_box).animateHighlight("#dd0000", 1000);

    });
}

function setupMiscClickEvents()
{
	//////////////////////////////
    // .togglenew causes the all-results div to fade in
    // -- loads get_pluslets.php
    // mod agd OCT 2010
    //////////////////////////////

    jQuery('#newbox1').click(function(event) {
        jQuery("#all-results").toggle('slow');
    });

    ///////////////////////////////////
    // #hide_header makes the SubsPlus header appear/disappear; it's hidden onload
    ///////////////////////////////////

    jQuery('#hide_header').click(function(event) {
        jQuery("#header, #subnavcontainer").toggle('slow');
    });
}

function makeHelpable( lstrSelector )
{
	////////////////
    // Help Buttons
    // unbind click events from class and redeclare click event
    ////////////////

      jQuery(lstrSelector).unbind('click');
      jQuery(lstrSelector).on('click', function(){
        var help_type = jQuery(this).attr("class").split("-");
        var popurl = "helpers/popup_help.php?type=" + help_type[1];

        jQuery(this).colorbox({
            href: popurl,
            iframe: true,
            innerWidth:"600px",
            innerHeight:"60%",
            maxWidth: "1100px",
            maxHeight: "800px"
        });
      });
}

///////////////////////
// checks whether all required inputs are not blank
///////////////////////

function checkRequired() {
	// If a required field is empty, set req_field to 1, and change the bg colour of the offending field
	var req_field = 0;

	jQuery("*[class=required_field]").each(function() {
		var check_this_field = jQuery(this).val();

		if (check_this_field == '' || check_this_field == null) {
			jQuery(this).attr("style", "background-color:#FFDFDF");
			req_field = 1;
		} else {
			jQuery(this).attr("style", "background-color:none");
		}

	});

	return req_field;

}

//////////////////
// addslashes called inside guide save, above
// ///////////////

function addslashes(string) {

    return (string+'').replace(/([\\"'])/g, "\\$1").replace(/\0/g, "\\0");
}

/////////////////////
// refreshFeeds
// --loads the various feeds after the page has loaded
/////////////////////

function refreshFeeds() {

    jQuery(".find_feed").each(function(n) {
        var feed = jQuery(this).attr("name").split("|");
        jQuery(this).load("../../subjects/includes/feedme.php", {
            type: feed[4],
            feed: feed[0],
            count: feed[1],
            show_desc: feed[2],
            show_feed: feed[3]
        });
    });

}

///////

function plantClone(clone_id, item_type) {

    // Create new node below, using a random number

    var randomnumber=Math.floor(Math.random()*1000001);
    jQuery("#portal-column-1").prepend("<div class=\"dropspotty\" id=\"new-" + randomnumber + "\"></div>");
    //alert (drag_id + " drop: " + drop_id);
    //alert(clone_id);

    // cloneid is used to tell us this is a clone
    var new_id = "pluslet-cloneid-" + clone_id;
    // Load new data, on success (or failure!) change class of container to "pluslet", and thus draggable in theory

    jQuery("#new-" + randomnumber).fadeIn("slow").load("helpers/guide_data.php", {
        from: new_id,
        flag: 'drop',
        item_type: item_type
    },
    function() {

        // 1.  remove the wrapper
        // 2. put the contents of the div into a variable
        // 3.  replace parent div (i.e., id="new-xxxxxx") with the content made by loaded file
        var cnt = jQuery("#new-" + randomnumber).contents();
        jQuery("#new-" + randomnumber).replaceWith(cnt);

        jQuery("#response").hide();
        //Make save button appear, since there has been a change to the page
        jQuery("#save_guide").fadeIn();

    });
}