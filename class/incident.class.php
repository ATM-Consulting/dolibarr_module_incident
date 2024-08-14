<?php
/* Copyright (C) 2017       Laurent Destailleur      <eldy@users.sourceforge.net>
 * Copyright (C) 2023-2024  Frédéric France          <frederic.france@free.fr>
 * Copyright (C) 2024		MDW                      <mdeweerd@users.noreply.github.com>
 * Copyright (C) 2024 SuperAdmin
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file        class/incident.class.php
 * \ingroup     incident
 * \brief       This file is a CRUD class file for Incident (Create/Read/Update/Delete)
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class for Incident
 */
class Incident extends CommonObject
{
	/**
	 * @var string ID of module.
	 */
	public $module = 'incident';

	/**
	 * @var string ID to identify managed object.
	 */
	public $element = 'incident';

	/**
	 * @var string Name of table without prefix where object is stored. This is also the key used for extrafields management (so extrafields know the link to the parent table).
	 */
	public $table_element = 'incident_incident';

	/**
	 * @var string 	If permission must be checkec with hasRight('incident', 'read') and not hasright('mymodyle', 'incident', 'read'), you can uncomment this line
	 */
	//public $element_for_permission = 'incident';

	/**
	 * @var string String with name of icon for incident. Must be a 'fa-xxx' fontawesome code (or 'fa-xxx_fa_color_size') or 'incident@incident' if picto is file 'img/object_incident.png'.
	 */
	public $picto = 'fa-exclamation-triangle';


	const STATUS_DRAFT = 0;
	const STATUS_VALIDATED = 1;
	const STATUS_CANCELLED= 2;
	const STATUS_FINISH = 9;

	// BEGIN MODULEBUILDER PROPERTIES
	/**
	 * @var array  Array with all fields and their property. Do not use it as a static var. It may be modified by constructor.
	 */
	public $fields=array(
		"rowid" => array("type"=>"integer", "label"=>"TechnicalID", "enabled"=>"1", 'position'=>1, 'notnull'=>1, "visible"=>"0", "noteditable"=>"1", "index"=>"1", "css"=>"left", "comment"=>"Id"),
		"ref" => array("type"=>"varchar(128)", "label"=>"Ref", "enabled"=>"1", 'position'=>20, 'notnull'=>1, "visible"=>"4", "index"=>"1", "searchall"=>"1", "validate"=>"1", "comment"=>"Reference of object", 'default'=>'(PROV)') ,
		"label" => array("type"=>"varchar(255)", "label"=>"Label", "enabled"=>"1", 'position'=>30, 'notnull'=>1, "visible"=>"1", "alwayseditable"=>"0", "searchall"=>"1", "css"=>"minwidth300", "cssview"=>"wordbreak", "showoncombobox"=>"2", "validate"=>"1",),
		"description" => array("type"=>"html", "label"=>"Description", "enabled"=>"1", 'position'=>60, 'notnull'=>0, "visible"=>"3", "validate"=>"1",),
		"note_public" => array("type"=>"html", "label"=>"NotePublic", "enabled"=>"1", 'position'=>61, 'notnull'=>0, "visible"=>"0", "cssview"=>"wordbreak", "validate"=>"1",),
		"note_private" => array("type"=>"html", "label"=>"NotePrivate", "enabled"=>"1", 'position'=>62, 'notnull'=>0, "visible"=>"0", "cssview"=>"wordbreak", "validate"=>"1",),
		"date_creation" => array("type"=>"datetime", "label"=>"DateCreation", "enabled"=>"1", 'position'=>500, 'notnull'=>1, "visible"=>"-2",),
		"tms" => array("type"=>"timestamp", "label"=>"DateModification", "enabled"=>"1", 'position'=>501, 'notnull'=>0, "visible"=>"-2",),
		"fk_user_creat" => array("type"=>"integer:User:user/class/user.class.php", "label"=>"UserAuthor", "picto"=>"user", "enabled"=>"1", 'position'=>510, 'notnull'=>1, "visible"=>"-2", "csslist"=>"tdoverflowmax150",),
		"fk_user_modif" => array("type"=>"integer:User:user/class/user.class.php", "label"=>"UserModif", "picto"=>"user", "enabled"=>"1", 'position'=>511, 'notnull'=>-1, "visible"=>"-2", "csslist"=>"tdoverflowmax150",),
		'status' => array('type'=>'integer', 'label'=>'Status', 'enabled'=>'1', 'position'=>2000, 'notnull'=>1, 'visible'=>5, 'noteditable'=>'1', 'index'=>1, 'arrayofkeyval'=>array('0'=>'Brouillon', '1'=>'Valid&eacute;', '2'=>'Annulée', '9'=>'Terminé'), 'validate'=>'1',),
		"corrective_element" => array("type"=>"html", "label"=>"correctiveElement", "enabled"=>"1", 'position'=>63, 'notnull'=>0, "visible"=>"1",),
		"fk_type_incident" => array("type"=>"chkbxlst:c_type_incident:label:rowid::active=1", "label"=>"typeOfIncident", "picto"=>"category", "enabled"=>"1", 'position'=>155, 'notnull'=>0, "visible"=>"1", "css"=>"minwidth150 maxwidth500 widthcentpercentminusx",),
		"element_type" => array("type"=>"varchar(128)", "label"=>"elementType", "enabled"=>"1", 'position'=>512, 'notnull'=>0, "visible"=>"5",),
		"fk_element" => array("type"=>"integer", "label"=>"originElement", "enabled"=>"1", 'position'=>513, 'notnull'=>0, "visible"=>"5",),
		"user_valid" => array("type"=>"integer:user:user/class/user.class.php", "label"=>"userValid", 'notnull'=>1, "enabled"=>"1", 'position'=>514, "visible"=>"1",),
	);

	public $rowid;
	public $ref;
	public $label;
	public $description;
	public $note_public;
	public $note_private;
	public $date_creation;
	public $tms;
	public $fk_user_creat;
	public $fk_user_modif;
	public $status;
	public $corrective_element;
	public $fk_type_incident;
	public $element_type;
	public $fk_element;
	public $user_valid;
	// END MODULEBUILDER PROPERTIES

	/**
	 * Constructor
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		global $langs;

		$this->db = $db;
		$this->ismultientitymanaged = 0;
		$this->isextrafieldmanaged = 1;

		if (!getDolGlobalInt('MAIN_SHOW_TECHNICAL_ID') && isset($this->fields['rowid']) && !empty($this->fields['ref'])) {
			$this->fields['rowid']['visible'] = 0;
		}
		if (!isModEnabled('multicompany') && isset($this->fields['entity'])) {
			$this->fields['entity']['enabled'] = 0;
		}

		// Unset fields that are disabled
		foreach ($this->fields as $key => $val) {
			if (isset($val['enabled']) && empty($val['enabled'])) {
				unset($this->fields[$key]);
			}
		}

		// Translate some data of arrayofkeyval
		if (is_object($langs)) {
			foreach ($this->fields as $key => $val) {
				if (!empty($val['arrayofkeyval']) && is_array($val['arrayofkeyval'])) {
					foreach ($val['arrayofkeyval'] as $key2 => $val2) {
						$this->fields[$key]['arrayofkeyval'][$key2] = $langs->trans($val2);
					}
				}
			}
		}
	}

	/**
	 * Create object into database
	 *
	 * @param  User $user      User that creates
	 * @param  int 	$notrigger 0=launch triggers after, 1=disable triggers
	 * @return int             Return integer <0 if KO, Id of created object if OK
	 */
	public function create(User $user, int $notrigger = 0) :int
	{
		$originId = GETPOST('originId', 'alphanohtml');
		$type = 	GETPOST('type', 'alphanohtml');
		if (!empty($originId) && !empty($type)){
			$this->element_type = $type;
			$this->fk_element = $originId;
		}
		$resultcreate = $this->createCommon($user, $notrigger);
		return $resultcreate;
	}

	/**
	 * Return HTML string to show a field into a page
	 * Code very similar with showOutputField of extra fields
	 *
	 * @param array{type:string,label:string,enabled:int<0,2>|string,position:int,notnull?:int,visible:int,noteditable?:int,default?:string,index?:int,foreignkey?:string,searchall?:int,isameasure?:int,css?:string,csslist?:string,help?:string,showoncombobox?:int,disabled?:int,arrayofkeyval?:array<int,string>,comment?:string}	$val	Array of properties of field to show
	 * @param  string  	$key            	Key of attribute
	 * @param  string  	$value          	Preselected value to show (for date type it must be in timestamp format, for amount or price it must be a php numeric value)
	 * @param  string  	$moreparam      	To add more parameters on html tag
	 * @param  string  	$keysuffix      	Prefix string to add into name and id of field (can be used to avoid duplicate names)
	 * @param  string  	$keyprefix      	Suffix string to add into name and id of field (can be used to avoid duplicate names)
	 * @param  mixed   	$morecss        	Value for CSS to use (Old usage: May also be a numeric to define a size).
	 * @return string
	 */
	public function showOutputField($val, $key, $value, $moreparam = '', $keysuffix = '',  $keyprefix = '',  $morecss = '') :string
	{
		global $conf, $langs, $form;

		if (!is_object($form)) {
			require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
			$form = new Form($this->db);
		}

		//$label = empty($val['label']) ? '' : $val['label'];
		$type  = empty($val['type']) ? '' : $val['type'];
		$size  = empty($val['css']) ? '' : $val['css'];
		$reg = array();

		// Convert var to be able to share same code than showOutputField of extrafields
		if (preg_match('/varchar\((\d+)\)/', $type, $reg)) {
			$type = 'varchar'; // convert varchar(xx) int varchar
			$size = $reg[1];
		} elseif (preg_match('/varchar/', $type)) {
			$type = 'varchar'; // convert varchar(xx) int varchar
		}
		if (!empty($val['arrayofkeyval']) && is_array($val['arrayofkeyval'])) {
			if (empty($this->fields[$key]['multiinput'])) {
				$type = (($this->fields[$key]['type'] == 'checkbox') ? $this->fields[$key]['type'] : 'select');
			}
		}
		if (preg_match('/^integer:(.*):(.*)/i', $val['type'], $reg)) {
			$type = 'link';
		}

		$default = empty($val['default']) ? '' : $val['default'];
		$computed = empty($val['computed']) ? '' : $val['computed'];
		$unique = empty($val['unique']) ? '' : $val['unique'];
		$required = empty($val['required']) ? '' : $val['required'];
		$param = array();
		$param['options'] = array();

		if (!empty($val['arrayofkeyval']) && is_array($val['arrayofkeyval'])) {
			$param['options'] = $val['arrayofkeyval'];
		}
		if (preg_match('/^integer:([^:]*):([^:]*)/i', $val['type'], $reg)) {	// ex: integer:User:user/class/user.class.php
			$type = 'link';
			$stringforoptions = $reg[1].':'.$reg[2];
			// Special case: Force addition of getnomurlparam1 to -1 for users
			if ($reg[1] == 'User') {
				$stringforoptions .= ':#getnomurlparam1=-1';
			}
			$param['options'] = array($stringforoptions => $stringforoptions);
		} elseif (preg_match('/^sellist:(.*):(.*):(.*):(.*)/i', $val['type'], $reg)) {
			$param['options'] = array($reg[1].':'.$reg[2].':'.$reg[3].':'.$reg[4] => 'N');
			$type = 'sellist';
		} elseif (preg_match('/^sellist:(.*):(.*):(.*)/i', $val['type'], $reg)) {
			$param['options'] = array($reg[1].':'.$reg[2].':'.$reg[3] => 'N');
			$type = 'sellist';
		} elseif (preg_match('/^sellist:(.*):(.*)/i', $val['type'], $reg)) {
			$param['options'] = array($reg[1].':'.$reg[2] => 'N');
			$type = 'sellist';
		} elseif (preg_match('/^chkbxlst:(.*)/i', $val['type'], $reg)) {
			$param['options'] = array($reg[1] => 'N');
			$type = 'chkbxlst';
		}

		$langfile = empty($val['langfile']) ? '' : $val['langfile'];
		$list = (empty($val['list']) ? '' : $val['list']);
		$help = (empty($val['help']) ? '' : $val['help']);
		$hidden = (($val['visible'] == 0) ? 1 : 0); // If zero, we are sure it is hidden, otherwise we show. If it depends on mode (view/create/edit form or list, this must be filtered by caller)

		if ($hidden) {
			return '';
		}

		// If field is a computed field, value must become result of compute
		if ($computed) {
			// Make the eval of compute string
			$value = dol_eval($computed, 1, 0, '2');
		}

		if (empty($morecss)) {
			if ($type == 'date') {
				$morecss = 'minwidth100imp';
			} elseif ($type == 'datetime' || $type == 'timestamp') {
				$morecss = 'minwidth200imp';
			} elseif (in_array($type, array('int', 'double', 'price'))) {
				$morecss = 'maxwidth75';
			} elseif ($type == 'url') {
				$morecss = 'minwidth400';
			} elseif ($type == 'boolean') {
				$morecss = '';
			} else {
				if (is_numeric($size) && round((float) $size) < 12) {
					$morecss = 'minwidth100';
				} elseif (is_numeric($size) && round((float) $size) <= 48) {
					$morecss = 'minwidth200';
				} else {
					$morecss = 'minwidth400';
				}
			}
		}
		if ($key == 'element_type' && !empty($value)) $value = $langs->trans($value);

		if($key == 'fk_element') {
				$TElementProperties = !is_null($this->element_type) ? getElementProperties($this->element_type) : false;
				if ($TElementProperties){
					if ($this->element_type == "agefodd_agsession")$dir = dirname(__DIR__, 2);
					else $dir = DOL_DOCUMENT_ROOT;
					$res = file_exists($dir.'/'.$TElementProperties['classpath'].'/'.$TElementProperties['classfile'].'.class.php');
					if ($res) require_once $dir.'/'.$TElementProperties['classpath'].'/'.$TElementProperties['classfile'].'.class.php';
					else dol_buildpath('/'.$TElementProperties['classpath'].'/'.$TElementProperties['classfile'].'.class.php');
					$originObject = new $TElementProperties['classname']($this->db);
					$originObject->fetch($this->fk_element);
					if ($this->element_type == "agefodd_agsession")$originObject->formintitule = $originObject->ref;
					$value = $originObject->getNomUrl();
				}
		}elseif (in_array($key, array('rowid', 'ref')) && method_exists($this, 'getNomUrl')) {
			if ($key != 'rowid' || empty($this->fields['ref'])) {	// If we want ref field or if we want ID and there is no ref field, we show the link.
				$value = $this->getNomUrl(1, '', 0, '', 1);

			}
		} elseif ($key == 'status' && method_exists($this, 'getLibStatut')) {
			$value = $this->getLibStatut(3);
		} elseif ($type == 'date') {
			if (!empty($value)) {
				$value = dol_print_date($value, 'day');	// We suppose dates without time are always gmt (storage of course + output)
			} else {
				$value = '';
			}
		} elseif ($type == 'datetime' || $type == 'timestamp') {
			if (!empty($value)) {
				$value = dol_print_date($value, 'dayhour', 'tzuserrel');
			} else {
				$value = '';
			}
		} elseif ($type == 'duration') {
			include_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
			if (!is_null($value) && $value !== '') {
				$value = convertSecondToTime($value, 'allhourmin');
			}
		} elseif ($type == 'double' || $type == 'real') {
			if (!is_null($value) && $value !== '') {
				$value = price($value);
			}
		} elseif ($type == 'boolean') {
			$checked = '';
			if (!empty($value)) {
				$checked = ' checked ';
			}
			if (getDolGlobalInt('MAIN_OPTIMIZEFORTEXTBROWSER') < 2) {
				$value = '<input type="checkbox" '.$checked.' '.($moreparam ? $moreparam : '').' readonly disabled>';
			} else {
				$value = yn($value ? 1 : 0);
			}
		} elseif ($type == 'mail' || $type == 'email') {
			$value = dol_print_email($value, 0, 0, 0, 64, 1, 1);
		} elseif ($type == 'url') {
			$value = dol_print_url($value, '_blank', 32, 1);
		} elseif ($type == 'phone') {
			$value = dol_print_phone($value, '', 0, 0, '', '&nbsp;', 'phone');
		} elseif ($type == 'ip') {
			$value = dol_print_ip($value, 0);
		} elseif ($type == 'price') {
			if (!is_null($value) && $value !== '') {
				$value = price($value, 0, $langs, 0, 0, -1, $conf->currency);
			}
		} elseif ($type == 'select') {
			$value = isset($param['options'][(string) $value]) ? $param['options'][(string) $value] : '';
			if (strpos($value, "|") !== false) {
				$value = $langs->trans(explode('|', $value)[0]);
			} elseif (! is_numeric($value)) {
				$value = $langs->trans($value);
			}
		} elseif ($type == 'sellist') {
			$param_list = array_keys($param['options']);
			$InfoFieldList = explode(":", $param_list[0]);

			$selectkey = "rowid";
			$keyList = 'rowid';

			if (count($InfoFieldList) > 4 && !empty($InfoFieldList[4])) {
				$selectkey = $InfoFieldList[2];
				$keyList = $InfoFieldList[2].' as rowid';
			}

			$fields_label = explode('|', $InfoFieldList[1]);
			if (is_array($fields_label)) {
				$keyList .= ', ';
				$keyList .= implode(', ', $fields_label);
			}

			$filter_categorie = false;
			if (count($InfoFieldList) > 5) {
				if ($InfoFieldList[0] == 'categorie') {
					$filter_categorie = true;
				}
			}

			$sql = "SELECT ".$keyList;
			$sql .= ' FROM '.$this->db->prefix().$InfoFieldList[0];
			if (strpos($InfoFieldList[4], 'extra') !== false) {
				$sql .= ' as main';
			}
			if ($selectkey == 'rowid' && empty($value)) {
				$sql .= " WHERE ".$selectkey." = 0";
			} elseif ($selectkey == 'rowid') {
				$sql .= " WHERE ".$selectkey." = ".((int) $value);
			} else {
				$sql .= " WHERE ".$selectkey." = '".$this->db->escape($value)."'";
			}

			//$sql.= ' AND entity = '.$conf->entity;

			dol_syslog(get_class($this).':showOutputField:$type=sellist', LOG_DEBUG);
			$resql = $this->db->query($sql);
			if ($resql) {
				if (!$filter_categorie) {
					$value = ''; // value was used, so now we reste it to use it to build final output
					$numrows = $this->db->num_rows($resql);
					if ($numrows) {
						$obj = $this->db->fetch_object($resql);

						// Several field into label (eq table:code|libelle:rowid)
						$fields_label = explode('|', $InfoFieldList[1]);

						if (is_array($fields_label) && count($fields_label) > 1) {
							foreach ($fields_label as $field_toshow) {
								$translabel = '';
								if (!empty($obj->$field_toshow)) {
									$translabel = $langs->trans($obj->$field_toshow);
								}
								if ($translabel != $field_toshow) {
									$value .= dol_trunc($translabel, 18) . ' ';
								} else {
									$value .= $obj->$field_toshow . ' ';
								}
							}
						} else {
							$translabel = '';
							if (!empty($obj->{$InfoFieldList[1]})) {
								$translabel = $langs->trans($obj->{$InfoFieldList[1]});
							}
							if ($translabel != $obj->{$InfoFieldList[1]}) {
								$value = dol_trunc($translabel, 18);
							} else {
								$value = $obj->{$InfoFieldList[1]};
							}
						}
					}
				} else {
					require_once DOL_DOCUMENT_ROOT . '/categories/class/categorie.class.php';

					$toprint = array();
					$obj = $this->db->fetch_object($resql);
					$c = new Categorie($this->db);
					$c->fetch($obj->rowid);
					$ways = $c->print_all_ways(); // $ways[0] = "ccc2 >> ccc2a >> ccc2a1" with html formatted text
					foreach ($ways as $way) {
						$toprint[] = '<li class="select2-search-choice-dolibarr noborderoncategories"' . ($c->color ? ' style="background: #' . $c->color . ';"' : ' style="background: #aaa"') . '>' . img_object('', 'category') . ' ' . $way . '</li>';
					}
					$value = '<div class="select2-container-multi-dolibarr" style="width: 90%;"><ul class="select2-choices-dolibarr">'.implode(' ', $toprint).'</ul></div>';
				}
			} else {
				dol_syslog(get_class($this).'::showOutputField error '.$this->db->lasterror(), LOG_WARNING);
			}
		} elseif ($type == 'radio') {
			$value = $param['options'][(string) $value];
		} elseif ($type == 'checkbox') {
			$value_arr = explode(',', (string) $value);
			$value = '';
			if (is_array($value_arr) && count($value_arr) > 0) {
				$toprint = array();
				foreach ($value_arr as $keyval => $valueval) {
					if (!empty($valueval)) {
						$toprint[] = '<li class="select2-search-choice-dolibarr noborderoncategories" style="background: #bbb">' . $param['options'][$valueval] . '</li>';
					}
				}
				if (!empty($toprint)) {
					$value = '<div class="select2-container-multi-dolibarr" style="width: 90%;"><ul class="select2-choices-dolibarr">' . implode(' ', $toprint) . '</ul></div>';
				}
			}
		} elseif ($type == 'chkbxlst') {
			$value_arr = (isset($value) ? explode(',', $value) : array());

			$param_list = array_keys($param['options']);
			$InfoFieldList = explode(":", $param_list[0]);

			$selectkey = "rowid";
			$keyList = 'rowid';

			if (count($InfoFieldList) >= 3) {
				$selectkey = $InfoFieldList[2];
				$keyList = $InfoFieldList[2].' as rowid';
			}

			$fields_label = explode('|', $InfoFieldList[1]);
			if (is_array($fields_label)) {
				$keyList .= ', ';
				$keyList .= implode(', ', $fields_label);
			}

			$filter_categorie = false;
			if (count($InfoFieldList) > 5) {
				if ($InfoFieldList[0] == 'categorie') {
					$filter_categorie = true;
				}
			}

			$sql = "SELECT ".$keyList;
			$sql .= ' FROM '.$this->db->prefix().$InfoFieldList[0];
			if (strpos($InfoFieldList[4], 'extra') !== false) {
				$sql .= ' as main';
			}
			// $sql.= " WHERE ".$selectkey."='".$this->db->escape($value)."'";
			// $sql.= ' AND entity = '.$conf->entity;

			dol_syslog(get_class($this).':showOutputField:$type=chkbxlst', LOG_DEBUG);
			$resql = $this->db->query($sql);
			if ($resql) {
				if (!$filter_categorie) {
					$value = ''; // value was used, so now we reset it to use it to build final output
					$toprint = array();
					while ($obj = $this->db->fetch_object($resql)) {
						// Several field into label (eq table:code|libelle:rowid)
						$fields_label = explode('|', $InfoFieldList[1]);
						if (is_array($value_arr) && in_array($obj->rowid, $value_arr)) {
							if (is_array($fields_label) && count($fields_label) > 1) {
								foreach ($fields_label as $field_toshow) {
									$translabel = '';
									if (!empty($obj->$field_toshow)) {
										$translabel = $langs->trans($obj->$field_toshow);
									}
									if ($translabel != $field_toshow) {
										$toprint[] = '<li class="select2-search-choice-dolibarr noborderoncategories" style="background: #bbb">' . dol_trunc($translabel, 18) . '</li>';
									} else {
										$toprint[] = '<li class="select2-search-choice-dolibarr noborderoncategories" style="background: #bbb">' . $obj->$field_toshow . '</li>';
									}
								}
							} else {
								$translabel = '';
								if (!empty($obj->{$InfoFieldList[1]})) {
									$translabel = $langs->trans($obj->{$InfoFieldList[1]});
								}
								if ($translabel != $obj->{$InfoFieldList[1]}) {
									$toprint[] = '<li class="select2-search-choice-dolibarr noborderoncategories" style="background: #bbb">' . dol_trunc($translabel, 18) . '</li>';
								} else {
									$toprint[] = '<li class="select2-search-choice-dolibarr noborderoncategories" style="background: #bbb">' . $obj->{$InfoFieldList[1]} . '</li>';
								}
							}
						}
					}
				} else {
					require_once DOL_DOCUMENT_ROOT . '/categories/class/categorie.class.php';

					$toprint = array();
					while ($obj = $this->db->fetch_object($resql)) {
						if (is_array($value_arr) && in_array($obj->rowid, $value_arr)) {
							$c = new Categorie($this->db);
							$c->fetch($obj->rowid);
							$ways = $c->print_all_ways(); // $ways[0] = "ccc2 >> ccc2a >> ccc2a1" with html formatted text
							foreach ($ways as $way) {
								$toprint[] = '<li class="select2-search-choice-dolibarr noborderoncategories"' . ($c->color ? ' style="background: #' . $c->color . ';"' : ' style="background: #aaa"') . '>' . img_object('', 'category') . ' ' . $way . '</li>';
							}
						}
					}
				}
				$value = '<div class="select2-container-multi-dolibarr" style="width: 90%;"><ul class="select2-choices-dolibarr">'.implode(' ', $toprint).'</ul></div>';
			} else {
				dol_syslog(get_class($this).'::showOutputField error '.$this->db->lasterror(), LOG_WARNING);
			}
		} elseif ($type == 'link') {
			$out = '';

			// only if something to display (perf)
			if ($value) {
				$param_list = array_keys($param['options']);

				$InfoFieldList = explode(":", $param_list[0]);

				$classname = $InfoFieldList[0];
				$classpath = $InfoFieldList[1];

				// Set $getnomurlparam1 et getnomurlparam2
				$getnomurlparam = 3;
				$getnomurlparam2 = '';
				$regtmp = array();
				if (preg_match('/#getnomurlparam1=([^#]*)/', $param_list[0], $regtmp)) {
					$getnomurlparam = $regtmp[1];
				}
				if (preg_match('/#getnomurlparam2=([^#]*)/', $param_list[0], $regtmp)) {
					$getnomurlparam2 = $regtmp[1];
				}

				if (!empty($classpath)) {
					dol_include_once($InfoFieldList[1]);

					if ($classname && !class_exists($classname)) {
						// from V19 of Dolibarr, In some cases link use element instead of class, example project_task
						// TODO use newObjectByElement() introduce in V20 by PR #30036 for better errors management
						$element_prop = getElementProperties($classname);
						if ($element_prop) {
							$classname = $element_prop['classname'];
						}
					}


					if ($classname && class_exists($classname)) {
						$object = new $classname($this->db);
						if ($object->element === 'product') {	// Special case for product because default valut of fetch are wrong
							$result = $object->fetch($value, '', '', '', 0, 1, 1);
						} else {
							$result = $object->fetch($value);
						}
						if ($result > 0) {
							if ($object->element === 'product') {
								$get_name_url_param_arr = array($getnomurlparam, $getnomurlparam2, 0, -1, 0, '', 0);
								if (isset($val['get_name_url_params'])) {
									$get_name_url_params = explode(':', $val['get_name_url_params']);
									if (!empty($get_name_url_params)) {
										$param_num_max = count($get_name_url_param_arr) - 1;
										foreach ($get_name_url_params as $param_num => $param_value) {
											if ($param_num > $param_num_max) {
												break;
											}
											$get_name_url_param_arr[$param_num] = $param_value;
										}
									}
								}

								/**
								 * @var Product $object
								 */
								$value = $object->getNomUrl($get_name_url_param_arr[0], $get_name_url_param_arr[1], $get_name_url_param_arr[2], $get_name_url_param_arr[3], $get_name_url_param_arr[4], $get_name_url_param_arr[5], $get_name_url_param_arr[6]);
							} else {
								$value = $object->getNomUrl($getnomurlparam, $getnomurlparam2);
							}
						} else {
							$value = '';
						}
					}
				} else {
					dol_syslog('Error bad setup of extrafield', LOG_WARNING);
					return 'Error bad setup of extrafield';
				}
			} else {
				$value = '';
			}
		} elseif ($type == 'password') {
			$value = '<span class="opacitymedium">'.$langs->trans("Encrypted").'</span>';

		} elseif ($type == 'array') {
			if (is_array($value)) {
				$value = implode('<br>', $value);
			} else {
				dol_syslog(__METHOD__.' Expected array from dol_eval, but got '.gettype($value), LOG_ERR);
				return 'Error unexpected result from code evaluation';
			}
		} else {	// text|html|varchar
			$value = dol_htmlentitiesbr($value);
		}

		//print $type.'-'.$size.'-'.$value;
		$out = $value;

		return is_null($out) ? '' : $out;
	}

	/**
	 * Clone an object into another one
	 *
	 * @param  	User 	$user      	User that creates
	 * @param  	int 	$fromid     Id of object to clone
	 * @return 	mixed 				New object created, <0 if KO
	 */
	public function createFromClone(User $user, int $fromid)
	{
		global $langs, $extrafields;
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		$object = new self($this->db);

		$this->db->begin();

		// Load source object
		$result = $object->fetchCommon($fromid);
		if ($result > 0 && !empty($object->table_element_line)) {
			$object->fetchLines();
		}

		// Reset some properties
		unset($object->id);
		unset($object->fk_user_creat);
		unset($object->import_key);

		// Clear fields
		if (property_exists($object, 'ref')) {
			$object->ref = empty($this->fields['ref']['default']) ? "Copy_Of_".$object->ref : $this->fields['ref']['default'];
		}
		if (property_exists($object, 'label')) {
			$object->label = empty($this->fields['label']['default']) ? $langs->trans("CopyOf")." ".$object->label : $this->fields['label']['default'];
		}
		if (property_exists($object, 'status')) {
			$object->status = self::STATUS_DRAFT;
		}
		if (property_exists($object, 'date_creation')) {
			$object->date_creation = dol_now();
		}
		if (property_exists($object, 'date_modification')) {
			$object->date_modification = null;
		}
		// ...
		// Clear extrafields that are unique
		if (is_array($object->array_options) && count($object->array_options) > 0) {
			$extrafields->fetch_name_optionals_label($this->table_element);
			foreach ($object->array_options as $key => $option) {
				$shortkey = preg_replace('/options_/', '', $key);
				if (!empty($extrafields->attributes[$this->table_element]['unique'][$shortkey])) {
					unset($object->array_options[$key]);
				}
			}
		}

		// Create clone
		$object->context['createfromclone'] = 'createfromclone';
		$result = $object->createCommon($user);
		if ($result < 0) {
			$error++;
			$this->setErrorsFromObject($object);
		}

		if (!$error) {
			// copy internal contacts
			if ($this->copy_linked_contact($object, 'internal') < 0) {
				$error++;
			}
		}

		if (!$error) {
			// copy external contacts if same company
			if (!empty($object->socid) && property_exists($this, 'fk_soc') && $this->fk_soc == $object->socid) {
				if ($this->copy_linked_contact($object, 'external') < 0) {
					$error++;
				}
			}
		}

		unset($object->context['createfromclone']);

		// End
		if (!$error) {
			$this->db->commit();
			return $object;
		} else {
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param 	int    	$id   			Id object
	 * @param 	string 	$ref  			Ref
	 * @param	int		$noextrafields	0=Default to load extrafields, 1=No extrafields
	 * @param	int		$nolines		0=Default to load extrafields, 1=No extrafields
	 * @return 	int     				Return integer <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetch(int $id, string $ref = null, int $noextrafields = 0, int $nolines = 0) :int
	{
		$result = $this->fetchCommon($id, $ref, '', $noextrafields);
		if ($result > 0 && !empty($this->table_element_line) && empty($nolines)) {
			$this->fetchLines($noextrafields);
		}
		return $result;
	}

	/**
	 * Load object lines in memory from the database
	 *
	 * @param	int		$noextrafields	0=Default to load extrafields, 1=No extrafields
	 * @return 	int         			Return integer <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetchLines(int $noextrafields = 0):int
	{
		$this->lines = array();

		$result = $this->fetchLinesCommon('', $noextrafields);
		return $result;
	}


	/**
	 * Load list of objects in memory from the database.
	 * Using a fetchAll() with limit = 0 is a very bad practice. Instead try to forge yourself an optimized SQL request with
	 * your own loop with start and stop pagination.
	 *
	 * @param  string      	$sortorder    	Sort Order
	 * @param  string      	$sortfield    	Sort field
	 * @param  int         	$limit        	Limit the number of lines returned
	 * @param  int         	$offset       	Offset
	 * @param  string		$filter       	Filter as an Universal Search string.
	 * 										Example: '((client:=:1) OR ((client:>=:2) AND (client:<=:3))) AND (client:!=:8) AND (nom:like:'a%')'
	 * @param  string      	$filtermode   	No more used
	 * @return array|int                 	int <0 if KO, array of pages if OK
	 */
	public function fetchAll(string $sortorder = '', string $sortfield = '', int $limit = 1000, int $offset = 0, string $filter = '', string $filtermode = 'AND')
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$records = array();

		$sql = "SELECT ";
		$sql .= $this->getFieldList('t');
		$sql .= " FROM ".$this->db->prefix().$this->table_element." as t";
		if (isset($this->isextrafieldmanaged) && $this->isextrafieldmanaged == 1) {
			$sql .= " LEFT JOIN ".$this->db->prefix().$this->table_element."_extrafields as te ON te.fk_object = t.rowid";
		}
		if (isset($this->ismultientitymanaged) && $this->ismultientitymanaged == 1) {
			$sql .= " WHERE t.entity IN (".getEntity($this->element).")";
		} else {
			$sql .= " WHERE 1 = 1";
		}

		// Manage filter
		$errormessage = '';
		$sql .= forgeSQLFromUniversalSearchCriteria($filter, $errormessage);
		if ($errormessage) {
			$this->errors[] = $errormessage;
			dol_syslog(__METHOD__.' '.implode(',', $this->errors), LOG_ERR);
			return -1;
		}

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield, $sortorder);
		}
		if (!empty($limit)) {
			$sql .= $this->db->plimit($limit, $offset);
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);
			$i = 0;
			while ($i < ($limit ? min($limit, $num) : $num)) {
				$obj = $this->db->fetch_object($resql);

				$record = new self($this->db);
				$record->setVarsFromFetchObj($obj);

				if (!empty($record->isextrafieldmanaged)) {
					$record->fetch_optionals();
				}

				$records[$record->id] = $record;

				$i++;
			}
			$this->db->free($resql);

			return $records;
		} else {
			$this->errors[] = 'Error '.$this->db->lasterror();
			dol_syslog(__METHOD__.' '.implode(',', $this->errors), LOG_ERR);

			return -1;
		}
	}

	/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  int 	$notrigger 0=launch triggers after, 1=disable triggers
	 * @return int             Return integer <0 if KO, >0 if OK
	 */
	public function update(User $user, int $notrigger = 0):int
	{
		return $this->updateCommon($user, $notrigger);
	}

	/**
	 * Delete object in database
	 *
	 * @param User $user       	User that deletes
	 * @param int 	$notrigger  0=launch triggers, 1=disable triggers
	 * @return int             	Return integer <0 if KO, >0 if OK
	 */
	public function delete(User $user, int $notrigger = 0):int
	{
		return $this->deleteCommon($user, $notrigger);
		//return $this->deleteCommon($user, $notrigger, 1);
	}

	/**
	 *  Delete a line of object in database
	 *
	 *	@param  User	$user       User that delete
	 *  @param	int		$idline		Id of line to delete
	 *  @param 	int 	$notrigger  0=launch triggers after, 1=disable triggers
	 *  @return int         		>0 if OK, <0 if KO
	 */
	public function deleteLine(User $user, int $idline, int $notrigger = 0):int
	{
		if ($this->status < 0) {
			$this->error = 'ErrorDeleteLineNotAllowedByObjectStatus';
			return -2;
		}

		return $this->deleteLineCommon($user, $idline, $notrigger);
	}


	/**
	 *	Validate object
	 *
	 *	@param		User	$user     		User making status change
	 *  @param		int		$notrigger		1=Does not execute triggers, 0= execute triggers
	 *	@return  	int						Return integer <=0 if OK, 0=Nothing done, >0 if KO
	 */
	public function validate(User $user, int $notrigger = 0):int
	{
		global $conf;

		require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

		$error = 0;

		// Protection
		if ($this->status == self::STATUS_VALIDATED) {
			dol_syslog(get_class($this)."::validate action abandoned: already validated", LOG_WARNING);
			return 0;
		}

		$now = dol_now();

		$this->db->begin();

		// Define new ref
		if (!$error && (preg_match('/^[\(]?PROV/i', $this->ref) || empty($this->ref))) { // empty should not happened, but when it occurs, the test save life
			$num = $this->getNextNumRef();
		} else {
			$num = $this->ref;
		}
		$this->newref = $num;
		if (!empty($num)) {
			// Validate
			$sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element;
			$sql .= " SET ";
			if (!empty($this->fields['ref'])) {
				$sql .= " ref = '".$this->db->escape($num)."',";
			}
			$sql .= " status = ".self::STATUS_VALIDATED;
			if (!empty($this->fields['date_validation'])) {
				$sql .= ", date_validation = '".$this->db->idate($now)."'";
			}
			if (!empty($this->fields['fk_user_valid'])) {
				$sql .= ", fk_user_valid = ".((int) $user->id);
			}
			$sql .= " WHERE rowid = ".((int) $this->id);
			dol_syslog(get_class($this)."::validate()", LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (!$resql) {
				dol_print_error($this->db);
				$this->error = $this->db->lasterror();
				$error++;
			}

			if (!$error && !$notrigger) {
				// Call trigger
				$result = $this->call_trigger('INCIDENT_VALIDATE', $user);
				if ($result < 0) {
					$error++;
				}
				// End call triggers
			}
		}

		if (!$error) {
			$this->oldref = $this->ref;

			// Rename directory if dir was a temporary ref
			if (preg_match('/^[\(]?PROV/i', $this->ref)) {
				// Now we rename also files into index
				$sql = 'UPDATE '.MAIN_DB_PREFIX."ecm_files set filename = CONCAT('".$this->db->escape($this->newref)."', SUBSTR(filename, ".(strlen($this->ref) + 1).")), filepath = 'incident/".$this->db->escape($this->newref)."'";
				$sql .= " WHERE filename LIKE '".$this->db->escape($this->ref)."%' AND filepath = 'incident/".$this->db->escape($this->ref)."' and entity = ".$conf->entity;
				$resql = $this->db->query($sql);
				if (!$resql) {
					$error++;
					$this->error = $this->db->lasterror();
				}
				$sql = 'UPDATE '.MAIN_DB_PREFIX."ecm_files set filepath = 'incident/".$this->db->escape($this->newref)."'";
				$sql .= " WHERE filepath = 'incident/".$this->db->escape($this->ref)."' and entity = ".$conf->entity;
				$resql = $this->db->query($sql);
				if (!$resql) {
					$error++;
					$this->error = $this->db->lasterror();
				}

				// We rename directory ($this->ref = old ref, $num = new ref) in order not to lose the attachments
				$oldref = dol_sanitizeFileName($this->ref);
				$newref = dol_sanitizeFileName($num);
				$dirsource = $conf->incident->dir_output.'/incident/'.$oldref;
				$dirdest = $conf->incident->dir_output.'/incident/'.$newref;
				if (!$error && file_exists($dirsource)) {
					dol_syslog(get_class($this)."::validate() rename dir ".$dirsource." into ".$dirdest);

					if (@rename($dirsource, $dirdest)) {
						dol_syslog("Rename ok");
						// Rename docs starting with $oldref with $newref
						$listoffiles = dol_dir_list($conf->incident->dir_output.'/incident/'.$newref, 'files', 1, '^'.preg_quote($oldref, '/'));
						foreach ($listoffiles as $fileentry) {
							$dirsource = $fileentry['name'];
							$dirdest = preg_replace('/^'.preg_quote($oldref, '/').'/', $newref, $dirsource);
							$dirsource = $fileentry['path'].'/'.$dirsource;
							$dirdest = $fileentry['path'].'/'.$dirdest;
							@rename($dirsource, $dirdest);
						}
					}
				}
			}
		}

		// Set new ref and current status
		if (!$error) {
			$this->ref = $num;
			$this->status = self::STATUS_VALIDATED;
		}

		if (!$error) {
			$this->db->commit();
			return 1;
		} else {
			$this->db->rollback();
			return -1;
		}
	}


	/**
	 *	Set draft status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						Return integer <0 if KO, >0 if OK
	 */
	public function setDraft(User $user, int $notrigger = 0):int
	{
		// Protection
		if ($this->status <= self::STATUS_DRAFT) {
			return 0;
		}

		return $this->setStatusCommon($user, self::STATUS_DRAFT, $notrigger, 'INCIDENT_INCIDENT_UNVALIDATE');
	}

	/**
	 *	Set cancel status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						Return integer <0 if KO, 0=Nothing done, >0 if OK
	 */
	public function cancel(User $user, int $notrigger = 0):int
	{
		// Protection
		if ($this->status != self::STATUS_VALIDATED) {
			return 0;
		}

		return $this->setStatusCommon($user, self::STATUS_FINISH, $notrigger, 'INCIDENT_INCIDENT_CANCEL');
	}

	/**
	 *	Set back to validated status
	 *
	 *	@param	User	$user			Object user that modify
	 *  @param	int		$notrigger		1=Does not execute triggers, 0=Execute triggers
	 *	@return	int						Return integer <0 if KO, 0=Nothing done, >0 if OK
	 */
	public function reopen(User $user, int $notrigger = 0):int
	{
		// Protection
		if ($this->status == self::STATUS_VALIDATED) {
			return 0;
		}
		return $this->setStatusCommon($user, self::STATUS_VALIDATED, $notrigger, 'INCIDENT_INCIDENT_REOPEN');
	}

	/**
	 * getTooltipContentArray
	 *
	 * @param 	array 	$params 	Params to construct tooltip data
	 * @since 	v18
	 * @return 	array
	 */
	public function getTooltipContentArray($params):array
	{
		global $langs;

		$datas = [];

		if (getDolGlobalInt('MAIN_OPTIMIZEFORTEXTBROWSER')) {
			return ['optimize' => $langs->trans("ShowIncident")];
		}
		$datas['picto'] = img_picto('', $this->picto).' <u>'.$langs->trans("Incident").'</u>';
		if (isset($this->status)) {
			$datas['picto'] .= ' '.$this->getLibStatut(5);
		}
		if (property_exists($this, 'ref')) {
			$datas['ref'] = '<br><b>'.$langs->trans('Ref').':</b> '.$this->ref;
		}
		if (property_exists($this, 'label')) {
			$datas['ref'] = '<br>'.$langs->trans('Label').':</b> '.$this->label;
		}

		return $datas;
	}

	/**
	 *  Return a link to the object card (with optionally the picto)
	 *
	 *  @param  int     $withpicto                  Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *  @param  string  $option                     On what the link point to ('nolink', ...)
	 *  @param  int     $notooltip                  1=Disable tooltip
	 *  @param  string  $morecss                    Add more css on link
	 *  @param  int     $save_lastsearch_value      -1=Auto, 0=No save of lastsearch_values when clicking, 1=Save lastsearch_values whenclicking
	 *  @return	string                              String with URL
	 */
	public function getNomUrl(int $withpicto = 0, string $option = '', int $notooltip = 0, string $morecss = '', int $save_lastsearch_value = -1):string
	{
		global $conf, $langs, $hookmanager;

		if (!empty($conf->dol_no_mouse_hover)) {
			$notooltip = 1; // Force disable tooltips
		}

		$result = '';
		$params = [
			'id' => $this->id,
			'objecttype' => $this->element.($this->module ? '@'.$this->module : ''),
			'option' => $option,
		];
		$classfortooltip = 'classfortooltip';
		$dataparams = '';
		if (getDolGlobalInt('MAIN_ENABLE_AJAX_TOOLTIP')) {
			$classfortooltip = 'classforajaxtooltip';
			$dataparams = ' data-params="'.dol_escape_htmltag(json_encode($params)).'"';
			$label = '';
		} else {
			$label = implode($this->getTooltipContentArray($params));
		}
		$filterPage = GETPOST('originId', 'alphanohtml');
		$url = dol_buildpath('/incident/incident_card.php', 1).'?id='.$this->id . ($filterPage ? '&filterPage=1' : '');

		if ($option !== 'nolink') {
			// Add param to save lastsearch_values or not
			$add_save_lastsearch_values = ($save_lastsearch_value == 1 ? 1 : 0);
			if ($save_lastsearch_value == -1 && isset($_SERVER["PHP_SELF"]) && preg_match('/list\.php/', $_SERVER["PHP_SELF"])) {
				$add_save_lastsearch_values = 1;
			}
			if ($url && $add_save_lastsearch_values) {
				$url .= '&save_lastsearch_values=1';
			}
		}

		$linkclose = '';
		if (empty($notooltip)) {
			if (getDolGlobalInt('MAIN_OPTIMIZEFORTEXTBROWSER')) {
				$label = $langs->trans("ShowIncident");
				$linkclose .= ' alt="'.dol_escape_htmltag($label, 1).'"';
			}
			$linkclose .= ($label ? ' title="'.dol_escape_htmltag($label, 1).'"' : ' title="tocomplete"');
			$linkclose .= $dataparams.' class="'.$classfortooltip.($morecss ? ' '.$morecss : '').'"';
		} else {
			$linkclose = ($morecss ? ' class="'.$morecss.'"' : '');
		}

		if ($option == 'nolink' || empty($url)) {
			$linkstart = '<span';
		} else {
			$linkstart = '<a href="'.$url.'"';
		}
		$linkstart .= $linkclose.'>';
		if ($option == 'nolink' || empty($url)) {
			$linkend = '</span>';
		} else {
			$linkend = '</a>';
		}

		$result .= $linkstart;

		if (empty($this->showphoto_on_popup)) {
			if ($withpicto) {
				$result .= img_object(($notooltip ? '' : $label), ($this->picto ? $this->picto : 'generic'), (($withpicto != 2) ? 'class="paddingright"' : ''), 0, 0, $notooltip ? 0 : 1);
			}
		} else {
			if ($withpicto) {
				require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

				list($class, $module) = explode('@', $this->picto);
				$upload_dir = $conf->$module->multidir_output[$conf->entity]."/$class/".dol_sanitizeFileName($this->ref);
				$filearray = dol_dir_list($upload_dir, "files");
				$filename = $filearray[0]['name'];
				if (!empty($filename)) {
					$pospoint = strpos($filearray[0]['name'], '.');

					$pathtophoto = $class.'/'.$this->ref.'/thumbs/'.substr($filename, 0, $pospoint).'_mini'.substr($filename, $pospoint);
					if (!getDolGlobalString(strtoupper($module.'_'.$class).'_FORMATLISTPHOTOSASUSERS')) {
						$result .= '<div class="floatleft inline-block valignmiddle divphotoref"><div class="photoref"><img class="photo'.$module.'" alt="No photo" border="0" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$module.'&entity='.$conf->entity.'&file='.urlencode($pathtophoto).'"></div></div>';
					} else {
						$result .= '<div class="floatleft inline-block valignmiddle divphotoref"><img class="photouserphoto userphoto" alt="No photo" border="0" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$module.'&entity='.$conf->entity.'&file='.urlencode($pathtophoto).'"></div>';
					}

					$result .= '</div>';
				} else {
					$result .= img_object(($notooltip ? '' : $label), ($this->picto ? $this->picto : 'generic'), ($notooltip ? (($withpicto != 2) ? 'class="paddingright"' : '') : 'class="'.(($withpicto != 2) ? 'paddingright ' : '').'"'), 0, 0, $notooltip ? 0 : 1);
				}
			}
		}

		if ($withpicto != 2) {
			$result .= $this->ref;
		}

		$result .= $linkend;
		//if ($withpicto != 2) $result.=(($addlabel && $this->label) ? $sep . dol_trunc($this->label, ($addlabel > 1 ? $addlabel : 0)) : '');

		global $action, $hookmanager;
		$hookmanager->initHooks(array($this->element.'dao'));
		$parameters = array('id' => $this->id, 'getnomurl' => &$result);
		$reshook = $hookmanager->executeHooks('getNomUrl', $parameters, $this, $action); // Note that $action and $object may have been modified by some hooks
		if ($reshook > 0) {
			$result = $hookmanager->resPrint;
		} else {
			$result .= $hookmanager->resPrint;
		}

		return $result;
	}

	/**
	 *	Return a thumb for kanban views
	 *
	 *	@param      string	    $option                 Where point the link (0=> main card, 1,2 => shipment, 'nolink'=>No link)
	 *  @param		array		$arraydata				Array of data
	 *  @return		string								HTML Code for Kanban thumb.
	 */
	public function getKanbanView(string $option = '', $arraydata = null) :string
	{
		global $conf, $langs;

		$selected = (empty($arraydata['selected']) ? 0 : $arraydata['selected']);

		$return = '<div class="box-flex-item box-flex-grow-zero">';
		$return .= '<div class="info-box info-box-sm">';
		$return .= '<span class="info-box-icon bg-infobox-action">';
		$return .= img_picto('', $this->picto);
		$return .= '</span>';
		$return .= '<div class="info-box-content">';
		$return .= '<span class="info-box-ref inline-block tdoverflowmax150 valignmiddle">'.(method_exists($this, 'getNomUrl') ? $this->getNomUrl() : $this->ref).'</span>';
		if ($selected >= 0) {
			$return .= '<input id="cb'.$this->id.'" class="flat checkforselect fright" type="checkbox" name="toselect[]" value="'.$this->id.'"'.($selected ? ' checked="checked"' : '').'>';
		}
		if (property_exists($this, 'label')) {
			$return .= ' <div class="inline-block opacitymedium valignmiddle tdoverflowmax100">'.$this->label.'</div>';
		}
		if (property_exists($this, 'thirdparty') && is_object($this->thirdparty)) {
			$return .= '<br><div class="info-box-ref tdoverflowmax150">'.$this->thirdparty->getNomUrl(1).'</div>';
		}
		if (property_exists($this, 'amount')) {
			$return .= '<br>';
			$return .= '<span class="info-box-label amount">'.price($this->amount, 0, $langs, 1, -1, -1, $conf->currency).'</span>';
		}
		if (method_exists($this, 'getLibStatut')) {
			$return .= '<br><div class="info-box-status">'.$this->getLibStatut(3).'</div>';
		}
		$return .= '</div>';
		$return .= '</div>';
		$return .= '</div>';

		return $return;
	}

	/**
	 *  Return the label of the status
	 *
	 *  @param  int		$mode          0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
	 *  @return	string 			       Label of status
	 */
	public function getLabelStatus(int $mode = 0):string
	{
		return $this->LibStatut($this->status, $mode);
	}

	/**
	 *  Return the label of the status
	 *
	 *  @param  int		$mode          0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
	 *  @return	string 			       Label of status
	 */
	public function getLibStatut(int $mode = 0):string
	{
		return $this->LibStatut($this->status, $mode);
	}

	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
	/**
	 *  Return the label of a given status
	 *
	 *  @param	int		$status        Id status
	 *  @param  int		$mode          0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
	 *  @return string 			       Label of status
	 */
	public function LibStatut(int $status, int $mode = 0):string
	{
		// phpcs:enable
		if (is_null($status)) {
			return '';
		}

		if (empty($this->labelStatus) || empty($this->labelStatusShort)) {
			global $langs;
			//$langs->load("incident@incident");
			$this->labelStatus[self::STATUS_DRAFT] = $langs->transnoentitiesnoconv('Draft');
			$this->labelStatus[self::STATUS_VALIDATED] = $langs->transnoentitiesnoconv('Validated');
			$this->labelStatus[self::STATUS_FINISH] = $langs->transnoentitiesnoconv('finish');
			$this->labelStatusShort[self::STATUS_DRAFT] = $langs->transnoentitiesnoconv('Draft');
			$this->labelStatusShort[self::STATUS_VALIDATED] = $langs->transnoentitiesnoconv('Validated');
			$this->labelStatusShort[self::STATUS_FINISH] = $langs->transnoentitiesnoconv('finish');
		}

		$statusType = 'status'.$status;
		//if ($status == self::STATUS_VALIDATED) $statusType = 'status1';
		if ($status == self::STATUS_FINISH) {
			$statusType = 'status6';
		}

		return dolGetStatus($this->labelStatus[$status], $this->labelStatusShort[$status], '', $statusType, $mode);
	}

	/**
	 *	Load the info information in the object
	 *
	 *	@param  int		$id       Id of object
	 *	@return	void
	 */
	public function info(int $id):void
	{
		$sql = "SELECT rowid,";
		$sql .= " date_creation as datec, tms as datem";
		if (!empty($this->fields['date_validation'])) {
			$sql .= ", date_validation as datev";
		}
		if (!empty($this->fields['fk_user_creat'])) {
			$sql .= ", fk_user_creat";
		}
		if (!empty($this->fields['fk_user_modif'])) {
			$sql .= ", fk_user_modif";
		}
		if (!empty($this->fields['fk_user_valid'])) {
			$sql .= ", fk_user_valid";
		}
		$sql .= " FROM ".MAIN_DB_PREFIX.$this->table_element." as t";
		$sql .= " WHERE t.rowid = ".((int) $id);

		$result = $this->db->query($sql);
		if ($result) {
			if ($this->db->num_rows($result)) {
				$obj = $this->db->fetch_object($result);

				$this->id = $obj->rowid;

				if (!empty($this->fields['fk_user_creat'])) {
					$this->user_creation_id = $obj->fk_user_creat;
				}
				if (!empty($this->fields['fk_user_modif'])) {
					$this->user_modification_id = $obj->fk_user_modif;
				}
				if (!empty($this->fields['fk_user_valid'])) {
					$this->user_validation_id = $obj->fk_user_valid;
				}
				$this->date_creation     = $this->db->jdate($obj->datec);
				$this->date_modification = empty($obj->datem) ? '' : $this->db->jdate($obj->datem);
				if (!empty($obj->datev)) {
					$this->date_validation   = empty($obj->datev) ? '' : $this->db->jdate($obj->datev);
				}
			}

			$this->db->free($result);
		} else {
			dol_print_error($this->db);
			dol_syslog(get_class($this)."::info ", LOG_ERR);
		}
	}

	/**
	 * Initialise object with example values
	 * Id must be 0 if object instance is a specimen
	 *
	 * @return int
	 */
	public function initAsSpecimen():int
	{
		// Set here init that are not commonf fields
		// $this->property1 = ...
		// $this->property2 = ...

		return $this->initAsSpecimenCommon();
	}

	/**
	 * 	Create an array of lines
	 *
	 * 	@return array|int		array of lines if OK, <0 if KO
	 */
	public function getLinesArray()
	{
		$this->lines = array();

		$objectline = new IncidentLine($this->db);
		$result = $objectline->fetchAll('ASC', 'position', 0, 0, '(fk_incident:=:'.((int) $this->id).')');

		if (is_numeric($result)) {
			$this->setErrorsFromObject($objectline);
			return $result;
		} else {
			$this->lines = $result;
			return $this->lines;
		}
	}

	/**
	 *  Returns the reference to the following non used object depending on the active numbering module.
	 *
	 *  @return string      		Object free reference
	 */
	public function getNextNumRef():string
	{
		global $langs, $conf;
		$langs->load("incident@incident");

		if (!getDolGlobalString('INCIDENT_INCIDENT_ADDON')) {
			$conf->global->INCIDENT_INCIDENT_ADDON = 'mod_incident_standard';
		}
		if (getDolGlobalString('INCIDENT_INCIDENT_ADDON')) {
			$mybool = false;

			$file = getDolGlobalString('INCIDENT_INCIDENT_ADDON').".php";
			$classname = getDolGlobalString('INCIDENT_INCIDENT_ADDON');
			// Include file with class
			$dirmodels = array_merge(array('/'), (array) $conf->modules_parts['models']);
			foreach ($dirmodels as $reldir) {
				$dir = dol_buildpath($reldir."core/modules/incident/");

				// Load file with numbering class (if found)
				$mybool |= @include_once $dir.$file;
			}
			if ($mybool === false) {
				dol_print_error(null, "Failed to include file ".$file);
				return '';
			}

			if (class_exists($classname)) {
				$obj = new $classname();
				$numref = $obj->getNextValue($this);
				if ($numref != '' && $numref != '-1') {
					return $numref;
				} else {
					$this->error = $obj->error;
					dol_syslog(get_class($this)."::getNextNumRef ".$this->error, LOG_ERR);
					return "";
				}
			} else {
				print $langs->trans("Error")." ".$langs->trans("ClassNotFound").' '.$classname;
				dol_syslog(get_class($this)."::getNextNumRef ".$langs->trans("ClassNotFound"), LOG_ERR);
				return "";
			}
		} else {
			print $langs->trans("ErrorNumberingModuleNotSetup", $this->element);
			return "";
		}
	}

	/**
	 *  Create a document onto disk according to template module.
	 *
	 *  @param	    string		$modele			Force template to use ('' to not force)
	 *  @param		Translate	$outputlangs	object lang a utiliser pour traduction
	 *  @param      int			$hidedetails    Hide details of lines
	 *  @param      int			$hidedesc       Hide description
	 *  @param      int			$hideref        Hide ref
	 *  @param      null|array  $moreparams     Array to provide more information
	 *  @return     int         				0 if KO, 1 if OK
	 */
	public function generateDocument($modele, $outputlangs, $hidedetails = 0, $hidedesc = 0, $hideref = 0, $moreparams = null):int
	{
		global $langs;

		$result = 0;
		$includedocgeneration = 0;

		$langs->load("incident@incident");

		if (!dol_strlen($modele)) {
			$modele = 'standard_incident';

			if (!empty($this->model_pdf)) {
				$modele = $this->model_pdf;
			} elseif (getDolGlobalString('INCIDENT_ADDON_PDF')) {
				$modele = getDolGlobalString('INCIDENT_ADDON_PDF');
			}
		}

		$modelpath = "core/modules/incident/doc/";

		if ($includedocgeneration && !empty($modele)) {
			$result = $this->commonGenerateDocument($modelpath, $modele, $outputlangs, $hidedetails, $hidedesc, $hideref, $moreparams);
		}

		return $result;
	}
}
