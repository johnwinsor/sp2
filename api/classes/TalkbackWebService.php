<?php

/**
 * TalkbackWebService - this class represents the talkback webservice
 *
 * @package SubjectsPlus API
 * @author dgonzalez
 * @copyright Copyright (c) 2012
 * @date November 2012
 * @version $Id$
 * @access public
 */
class TalkbackWebService extends sp_WebService implements WebService
{
	/**
	 * TalkbackWebService::__construct() - pass parameters to parent construct and
	 * set the service and tag properties
	 *
	 * @param array $lobjUrlParams
	 * @param sp_DBConnector $lobjDBConnector
	 */
	function __construct($lobjUrlParams, $lobjDBConnector)
	{
		parent::__construct($lobjUrlParams, $lobjDBConnector);
		$this->mstrService = 'talkbacks';
		$this->mstrTag = 'talkback';
	}

	/**
	 * TalkbackWebService::sanitizeParams() - goes through passed array parameter
	 * and sanitizes elements that are valid url parameters
	 *
	 * @param array $lobjParams
	 * @return array
	 */
	function sanitizeParams(Array $lobjParams)
	{
		$lobjFinalParams = array();

		foreach($lobjParams as $lstrKey => $lstrValue)
		{
			switch(strtolower($lstrKey))
			{
				case 'tag':
					$lobjSplit = explode(',', $lstrValue);

					foreach($lobjSplit as &$lstrUnScrubbed)
					{
						$lstrUnScrubbed = scrubData($lstrUnScrubbed);
					}

					$lobjFinalParams['tag'] = $lobjSplit;
					break;
				case 'max':
					$lstrValue = scrubData($lstrValue, 'integer');

					$lobjFinalParams['max'] = $lstrValue;
					break;
			}
		}

		return $lobjFinalParams;
	}

	/**
	 * TalkbackWebService::generateQuery() - returns a generated query based on all
	 * url parameters that will return desired talkback records
	 *
	 * @param array $lobjParams
	 * @return string
	 */
	function generateQuery(Array $lobjParams)
	{
		$lstrQuery = "SELECT talkback_id, question, q_from, date_submitted, answer, CONCAT( fname, ' ', lname ) AS answered_by, display, tbtags
          			FROM talkback, staff";

		$lobjConditions = array();

		foreach($lobjParams as $lstrKey => $lobjValues)
		{
			switch($lstrKey)
			{
				case 'tag':
					$lobjCondition = array();

					foreach ($lobjValues as $lstrTag)
					{
						array_push($lobjCondition, "tbtags LIKE '%$lstrTag%'\n");
					}

					$lstrCombine = implode(' OR ', $lobjCondition);

					array_push($lobjConditions, $lstrCombine);
					break;
			}
		}

		if(count($lobjConditions) > 0)
		{
			$lstrQuery .= "\nWHERE (" . implode(') AND (', $lobjConditions);
			$lstrQuery .= ')';
			$lstrQuery .= " AND talkback.a_from = staff.staff_id\n";
		}else
		{
			$lstrQuery .= " WHERE talkback.a_from = staff.staff_id";
		}

		if(isset($lobjParams['max']))
		{
			$lstrQuery .= " LIMIT 0,{$lobjParams['max']}";
		}

		return $lstrQuery;
	}
}


?>