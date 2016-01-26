<?php
/*
 * CENTREON
 *
 * Source Copyright 2005-2015 CENTREON
 *
 * Unauthorized reproduction, copy and distribution
 * are not allowed.
 *
 * For more information : contact@centreon.com
 *
*/

$path = "./modules/centreon-open-tickets/views/rules/";
$tpl = new Smarty();
$tpl = initSmartyTpl($path, $tpl);
$rows = 0;
$nbRule = 0;
include("./include/common/autoNumLimit.php");

$form = new HTML_QuickForm('select_form', 'POST', "?p=".$p);

$query = "SELECT r.rule_id, r.activate, r.alias FROM mod_open_tickets_rule r";
if ($search) {
    $query .= " WHERE r.alias LIKE '%".$search."%' ";
}
$queryCount = $query;
$query .= " ORDER BY r.alias";
$query .= " LIMIT ".$num * $limit.", ".$limit;

$resCount = $db->query($queryCount);
$rows = $resCount->numRows();

$res = $db->query($query);
$elemArr = array();
$tdStyle = "list_one";
$ruleStr = "";
while ($row = $res->fetchRow()) {
    $selectedElements = $form->addElement('checkbox', "select[".$row['rule_id']."]");
	$elemArr[$row['rule_id']]['select'] = $selectedElements->toHtml();
	$elemArr[$row['rule_id']]['url_edit'] = "./main.php?p=".$p."&o=c&rule_id=".$row['rule_id'];
	$elemArr[$row['rule_id']]['name'] = $row['alias'];
	$elemArr[$row['rule_id']]['status'] = $row['activate'] ? _('Enabled') : _('Disabled');
	$elemArr[$row['rule_id']]['style'] = $tdStyle;
	$dupElements = $form->addElement('text', 'duplicateNb['.$row['rule_id'].']', NULL, array("id" => "duplicateNb[".$row['rule_id']."]", "size" => "3", "value" => "1"));
    $moptions = "";
    if ($row['activate']) {
        $moptions .= "<a href='main.php?p=".$p."&o=ds&rule_id=".$row['rule_id']."&limit=".$limit."&num=".$num."'><img src='img/icones/16x16/element_previous.gif' border='0' alt='"._("Disabled")."'></a>&nbsp;&nbsp;";
    } else {
        $moptions .= "<a href='main.php?p=".$p."&o=e&rule_id=".$row['rule_id']."&limit=".$limit."&num=".$num."'><img src='img/icones/16x16/element_next.gif' border='0' alt='"._("Enabled")."'></a>&nbsp;&nbsp;";
    }
	$elemArr[$row['rule_id']]['dup'] = $moptions . "&nbsp;" . $dupElements->toHtml();
	if ($tdStyle == "list_one") {
		$tdStyle = "list_two";
	} else {
		$tdStyle = "list_one";
	}
	if ($ruleStr) {
	    $ruleStr .= ",";
	}
	$ruleStr .= "'".$row['rule_id']."'";
    $nbRule++;
}

$tpl->assign('msg', array ("addL"=>"?p=".$p."&o=a", "add"=>_("Add"), "delConfirm"=>_("Do you confirm the deletion ?"), "img" => "./modules/centreon-autodiscovery-server/images/add2.png"));
?>
<script type="text/javascript">
	function setO(_i) {
		document.forms['form'].elements['o1'].value = _i;
		document.forms['form'].elements['o2'].value = _i;
	}
</script>
<?php
$attrs1 = array(
		'onchange'=>"javascript: " .
			"if (this.form.elements['o1'].selectedIndex == 1 && confirm('"._("Do you confirm the deletion ?")."')) {" .
			" 	setO(this.form.elements['o1'].value); submit();} " .
			"else if (this.form.elements['o1'].selectedIndex == 2) {" .
			" 	setO(this.form.elements['o1'].value); submit();} " .
			"else if (this.form.elements['o1'].selectedIndex == 3) {" .
			" 	setO(this.form.elements['o1'].value); submit();} " .
			"else if (this.form.elements['o1'].selectedIndex == 4) {" .
			" 	setO(this.form.elements['o1'].value); submit();} " .
			"this.form.elements['o1'].selectedIndex = 0");
$form->addElement('select', 'o1', null, array(null => _("More actions..."), "d"=>_("Delete"), "e"=>_("Enable"), "ds"=>_("Disable"), "dp"=>_("Duplicate")), $attrs1);

$attrs2 = array(
		'onchange'=>"javascript: " .
			"if (this.form.elements['o2'].selectedIndex == 1 && confirm('"._("Do you confirm the deletion ?")."')) {" .
			" 	setO(this.form.elements['o2'].value); submit();} " .
			"else if (this.form.elements['o2'].selectedIndex == 2) {" .
			" 	setO(this.form.elements['o2'].value); submit();} " .
			"else if (this.form.elements['o2'].selectedIndex == 3) {" .
			" 	setO(this.form.elements['o2'].value); submit();} " .
			"else if (this.form.elements['o2'].selectedIndex == 4) {" .
			" 	setO(this.form.elements['o2'].value); submit();} " .
			"this.form.elements['o1'].selectedIndex = 0");
$form->addElement('select', 'o2', null, array(null => _("More actions..."), "d"=>_("Delete"), "e"=>_("Enable"), "ds"=>_("Disable"), "dp"=>_("Duplicate")), $attrs2);

$o1 = $form->getElement('o1');
$o1->setValue(null);
$o2 = $form->getElement('o2');
$o2->setValue(null);

$renderer = new HTML_QuickForm_Renderer_ArraySmarty($tpl);
$form->accept($renderer);
$tpl->assign('form', $renderer->toArray());

$tpl->assign("elemArr", $elemArr);
$tpl->assign('searchLabel', _('Search'));
$tpl->assign('statusLabel', _('Status'));
$tpl->assign('ruleLabel', _('Rules'));
$tpl->assign('optionLabel', _('Options'));
$tpl->assign('search', $search);
$tpl->assign('nbRule', $nbRule);
$tpl->assign('no_rule_defined', _('No rule found'));
$tpl->assign('limit', $limit);

$tpl->display("list.ihtml");
?>
