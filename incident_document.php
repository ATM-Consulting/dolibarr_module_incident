<?php
/* Copyright (C) 2007-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *  \file       incident_document.php
 *  \ingroup    incident
 *  \brief      Tab for documents linked to Incident
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--;
	$j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) {
	$res = @include "../main.inc.php";
}
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once __DIR__ . '/class/incident.class.php';
require_once __DIR__ . '/lib/incident_incident.lib.php';

// Load translation files required by the page
$langs->loadLangs(array("incident@incident", "companies", "other", "mails"));

// Get parameters
$action  = GETPOST('action', 'aZ09');
$confirm = GETPOST('confirm');
$id  = (GETPOST('socid', 'int') ? GETPOST('socid', 'int') : GETPOST('id', 'int'));
$ref = GETPOST('ref', 'alpha');

$limit = GETPOST('limit', 'int') ? GETPOST('limit', 'int') : $conf->liste_limit;
$sortfield = GETPOST('sortfield', 'aZ09comma');
$sortorder = GETPOST('sortorder', 'aZ09comma');
$page = GETPOSTISSET('pageplusone') ? (GETPOST('pageplusone') - 1) : GETPOST("page", 'int');
if (empty($page) || $page == -1) {
	$page = 0;
}     // If $page is not defined, or '' or -1
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (!$sortorder) {
	$sortorder = "ASC";
}
if (!$sortfield) {
	$sortfield = "name";
}
//if (! $sortfield) $sortfield="position_name";

// Initialize technical objects
$object = new Incident($db);
$extrafields = new ExtraFields($db);
$diroutputmassaction = $conf->incident->dir_output.'/temp/massgeneration/'.$user->id;
$hookmanager->initHooks(array($object->element.'document', 'globalcard')); // Note that conf->hooks_modules contains array

// Fetch optionals attributes and labels
$extrafields->fetch_name_optionals_label($object->table_element);

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php'; // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

if ($id > 0 || !empty($ref)) {
	$upload_dir = $conf->incident->multidir_output[$object->entity ? $object->entity : $conf->entity]."/incident/".get_exdir(0, 0, 0, 1, $object);
}

// Permissions
// (There are several ways to check permission.)
// Set $enablepermissioncheck to 1 to enable a minimum low level of checks
$enablepermissioncheck = 0;
if ($enablepermissioncheck) {
	$permissiontoread = $user->hasRight('incident', 'incident', 'read');
	$permissiontoadd  = $user->hasRight('incident', 'incident', 'write'); // Used by the include of actions_addupdatedelete.inc.php and actions_linkedfiles.inc.php
} else {
	$permissiontoread = 1;
	$permissiontoadd  = 1;
}

if (!isModEnabled("incident")) {
	accessforbidden();
}
if (!$permissiontoread) {
	accessforbidden();
}
if (empty($object->id)) {
	accessforbidden();
}



/*
 * Actions
 */

include DOL_DOCUMENT_ROOT.'/core/actions_linkedfiles.inc.php';


/*
 * View
 */

$form = new Form($db);

// Header
// ------
$title = $langs->trans("Incident")." - ".$langs->trans("Files");
//$title = $object->ref." - ".$langs->trans("Files");
$help_url = '';
//Example $help_url='EN:Module_Third_Parties|FR:Module_Tiers|ES:Empresas';
llxHeader('', $title, $help_url, '', 0, 0, '', '', '', 'mod-incident page-card_document');

// Show tabs
$head = incidentPrepareHead($object);

print dol_get_fiche_head($head, 'document', $langs->trans("Incident"), -1, $object->picto);


// Build file list
$filearray = dol_dir_list($upload_dir, "files", 0, '', '(\.meta|_preview.*\.png)$', $sortfield, (strtolower($sortorder) == 'desc' ? SORT_DESC : SORT_ASC), 1);
$totalsize = 0;
foreach ($filearray as $key => $file) {
	$totalsize += $file['size'];
}

// Object card
// ------------------------------------------------------------
if ((!empty($object->fk_element) && !empty($object->element_type))) $filter = '&originId='.$object->fk_element.'&type='.$object->element_type.'&filterPage=1';
else $filter = '';
$linkback = '<a href="'.dol_buildpath('/incident/incident_list.php', 1).'?id='.$object->id . $filter.'">'.$langs->trans("BackToList").'</a>';

$morehtmlref = '<div class="refidno">';
$morehtmlref .= '</div>';

dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);

print '<div class="fichecenter">';

print '<div class="underbanner clearboth"></div>';
print '<table class="border centpercent tableforfield">';

// Number of files
print '<tr><td class="titlefield">'.$langs->trans("NbOfAttachedFiles").'</td><td colspan="3">'.count($filearray).'</td></tr>';

// Total size
print '<tr><td>'.$langs->trans("TotalSizeOfAttachedFiles").'</td><td colspan="3">'.$totalsize.' '.$langs->trans("bytes").'</td></tr>';

print '</table>';

print '</div>';

print dol_get_fiche_end();

$modulepart = 'incident';
$param = '&id='.$object->id;

$relativepathwithnofile = 'incident/'.dol_sanitizeFileName($object->ref).'/';

include DOL_DOCUMENT_ROOT.'/core/tpl/document_actions_post_headers.tpl.php';

// End of page
llxFooter();
$db->close();
