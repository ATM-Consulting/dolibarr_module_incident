<?php
/* Copyright (C) 2024 SuperAdmin
 * Copyright (C) 2024		MDW							<mdeweerd@users.noreply.github.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    incident/class/actions_incident.class.php
 * \ingroup incident
 * \brief   Example hook overload.
 *
 * Put detailed description here.
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonhookactions.class.php';

/**
 * Class ActionsIncident
 */
class ActionsIncident extends CommonHookActions
{
	/**
	 * @var DoliDB Database handler.
	 */
	public $db;

	/**
	 * @var string Error code (or message)
	 */
	public $error = '';

	/**
	 * @var array Errors
	 */
	public $errors = array();


	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var ?string String displayed by executeHook() immediately after return
	 */
	public $resprints;


	/**
	 * Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}
	/**
	 * count tab incident
	 *
	 * @param   array           $parameters     Array of parameters
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         'add', 'update', 'view'
	 * @param   Hookmanager     $hookmanager    hookmanager
	 * @return  int                             Return integer <0 if KO,
	 *                                          =0 if OK but we want to process standard actions too,
	 *                                          >0 if OK and we want to replace standard actions.
	 */
	public function completeTabsHead(&$parameters, &$object, &$action, $hookmanager)
	{
		if (!isset($object->tabHeadLoaded)){
			foreach ($parameters['head'] as $h => $headV) if(!empty($headV) && $headV[2] == 'incident'){
				$nbRules = 0;
				$sql = 'SELECT COUNT(*) as nbRules FROM ' . MAIN_DB_PREFIX.'incident_incident drule WHERE element_type = "'.$object->element.'" AND fk_element = ' . intval($object->id);
				$resql = $object->db->query($sql);
				if ($resql > 0){
					$obj = $object->db->fetch_object($resql);
					$nbRules = $obj->nbRules;
				}
				if ($nbRules > 0) {
					$parameters['head'][$h][1] = $parameters['head'][$h][1]  . ' <span class="badge">' . $nbRules . '</span>';
					$object->tabHeadLoaded = 1;
					break;
				}
			}
		}
		return 0;
	}
}
