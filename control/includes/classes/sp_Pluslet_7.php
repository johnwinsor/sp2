<?php

/**
 *   @file sp_Pluslet_7
 *   @brief The number corresponds to the ID in the database.  Numbered pluslets are UNEDITABLE clones
 * 		this one displays the Catalog Search Box
 *     YOU WILL NEED TO LOCALIZE THIS!
 *      JW - Added Zoho Pluslet
 *   @author agdarby
 *   @date Feb 2011
 *   @todo
 */

class sp_Pluslet_7 extends sp_Pluslet {

    public function __construct($pluslet_id, $flag="", $subject_id) {

        parent::__construct($pluslet_id, $flag, $subject_id);

        $this->_editable = FALSE;
        $this->_subject_id = $subject_id;
        $this->_pluslet_id = 7;
        $this->_pluslet_id_field = "pluslet-" . $this->_pluslet_id;
        $this->_title = _("Chat with a Librarian");
    }

    public function output($action="", $view="public") {

        // public vs. admin
        parent::establishView($view);
        // example form action:  http://icarus.ithaca.edu/cgi-bin/Pwebrecon.cgi?
        $this->_body = '<div style="height:400px;"><iframe style="overflow:hidden;width:100%;height:100%;" frameborder="0" border="0" src="http://chat.zoho.com/mychat.sas?u=d0c53c1b598eb3daf86aad0624e8cdf5&chaturl=Mills%20Librarian1&V=000000-70a9e1-eff4f9-70a9e1-Chat%20with%20a%20Librarian"></iframe></div>';



        parent::assemblePluslet();

        return $this->_pluslet;
    }

}

?>