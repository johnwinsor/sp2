<?php

/**
 *   @file sp_Pluslet_3
 *   @brief The number corresponds to the ID in the database.  Numbered pluslets are UNEDITABLE clones
 * 	this one displays FAQs for that subject
 *
 *   @author agdarby
 *   @date Mar 2011
 *   @todo
 */
class sp_Pluslet_4 extends sp_Pluslet {

    public function __construct($pluslet_id, $flag="", $subject_id) {
        parent::__construct($pluslet_id, $flag, $subject_id);

        $this->_editable = FALSE;
        $this->_subject_id = $subject_id;
        $this->_pluslet_id = 4;
        $this->_pluslet_id_field = "pluslet-" . $this->_pluslet_id;
        $this->_title = _("FAQs");
    }

    public function output($action="", $view="public") {
        global $PublicPath;

        // public vs. admin
        parent::establishView($view);

        // Get librarians associated with this guide
        $querier = new sp_Querier();
        $qs = "SELECT f.faq_id, question, answer from faq f, faq_subject fs WHERE f.faq_id = fs.faq_id and fs.subject_id = " . $this->_subject_id . " ORDER BY question";

        //print $qs;

        $faqArray = $querier->getResult($qs);


        if ($faqArray) {
            $this->_body = "<ul>";
            foreach ($faqArray as $value) {
                $short_q = Truncate($value[question], 150, '');

                $this->_body .= "<li><a target=\"_blank\" href=\"$PublicPath" . "faq.php?faq_id=$value[0]\">$short_q</a></li>\n";
            }

            $this->_body .= "</ul>";
        } else {
            $this->_body = _("There are no FAQs linked for this guide");
        }

        parent::assemblePluslet();

        return $this->_pluslet;
    }

}

?>