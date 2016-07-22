<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  use OSC\OM\HTML;
  use OSC\OM\OSCOM;

  require('includes/application_top.php');

  if (!isset($_GET['spage']) || !is_numeric($_GET['spage'])) {
    $_GET['spage'] = 1;
  }

  $saction = (isset($_GET['saction']) ? $_GET['saction'] : '');

  if (tep_not_null($saction)) {
    switch ($saction) {
      case 'insert_sub':
        $zID = HTML::sanitize($_GET['zID']);
        $zone_country_id = HTML::sanitize($_POST['zone_country_id']);
        $zone_id = HTML::sanitize($_POST['zone_id']);

        $OSCOM_Db->save('zones_to_geo_zones', [
          'zone_country_id' => (int)$zone_country_id,
          'zone_id' => (int)$zone_id,
          'geo_zone_id' => (int)$zID,
          'date_added' => 'now()'
        ]);

        $new_subzone_id = $OSCOM_Db->lastInsertId();

        OSCOM::redirect(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $new_subzone_id);
        break;
      case 'save_sub':
        $sID = HTML::sanitize($_GET['sID']);
        $zID = HTML::sanitize($_GET['zID']);
        $zone_country_id = HTML::sanitize($_POST['zone_country_id']);
        $zone_id = HTML::sanitize($_POST['zone_id']);

        $OSCOM_Db->save('zones_to_geo_zones', [
          'geo_zone_id' => (int)$zID,
          'zone_country_id' => (int)$zone_country_id,
          'zone_id' => (tep_not_null($zone_id) ? (int)$zone_id : 'null'),
          'last_modified' => 'now()'
        ], [
          'association_id' => (int)$sID
        ]);

        OSCOM::redirect(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $_GET['sID']);
        break;
      case 'deleteconfirm_sub':
        $sID = HTML::sanitize($_GET['sID']);

        $OSCOM_Db->delete('zones_to_geo_zones', ['association_id' => (int)$sID]);

        OSCOM::redirect(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage']);
        break;
    }
  }

  if (!isset($_GET['zpage']) || !is_numeric($_GET['zpage'])) {
    $_GET['zpage'] = 1;
  }

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'insert_zone':
        $geo_zone_name = HTML::sanitize($_POST['geo_zone_name']);
        $geo_zone_description = HTML::sanitize($_POST['geo_zone_description']);

        $OSCOM_Db->save('geo_zones', [
          'geo_zone_name' => $geo_zone_name,
          'geo_zone_description' =>  $geo_zone_description,
          'date_added' => 'now()'
        ]);

        $new_zone_id = $OSCOM_Db->lastInsertId();

        OSCOM::redirect(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $new_zone_id);
        break;
      case 'save_zone':
        $zID = HTML::sanitize($_GET['zID']);
        $geo_zone_name = HTML::sanitize($_POST['geo_zone_name']);
        $geo_zone_description = HTML::sanitize($_POST['geo_zone_description']);

        $OSCOM_Db->save('geo_zones', [
          'geo_zone_name' => $geo_zone_name,
          'geo_zone_description' => $geo_zone_description,
          'last_modified' => 'now()'
        ], [
          'geo_zone_id' => (int)$zID
        ]);

        OSCOM::redirect(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID']);
        break;
      case 'deleteconfirm_zone':
        $zID = HTML::sanitize($_GET['zID']);

        $OSCOM_Db->delete('geo_zones', ['geo_zone_id' => (int)$zID]);
        $OSCOM_Db->delete('zones_to_geo_zones', ['geo_zone_id' => (int)$zID]);

        OSCOM::redirect(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage']);
        break;
    }
  }

  require(DIR_WS_INCLUDES . 'template_top.php');

  if (isset($_GET['zID']) && (($saction == 'edit') || ($saction == 'new'))) {
?>
<script type="text/javascript"><!--
function resetZoneSelected(theForm) {
  if (theForm.state.value != '') {
    theForm.zone_id.selectedIndex = '0';
    if (theForm.zone_id.options.length > 0) {
      theForm.state.value = '<?php echo JS_STATE_SELECT; ?>';
    }
  }
}

function update_zone(theForm) {
  var NumState = theForm.zone_id.options.length;
  var SelectedCountry = "";

  while(NumState > 0) {
    NumState--;
    theForm.zone_id.options[NumState] = null;
  }

  SelectedCountry = theForm.zone_country_id.options[theForm.zone_country_id.selectedIndex].value;

<?php echo tep_js_zone_list('SelectedCountry', 'theForm', 'zone_id'); ?>

}
//--></script>
<?php
  }
?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; if (isset($_GET['zone'])) echo '<br /><span class="smallText">' . tep_get_geo_zone_name($_GET['zone']) . '</span>'; ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
<?php
  if ($action == 'list') {
?>
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_COUNTRY; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_COUNTRY_ZONE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $Qzones = $OSCOM_Db->prepare('select SQL_CALC_FOUND_ROWS a.association_id, a.zone_country_id, c.countries_name, a.zone_id, a.geo_zone_id, a.last_modified, a.date_added, z.zone_name from :table_zones_to_geo_zones a left join :table_countries c on a.zone_country_id = c.countries_id left join :table_zones z on a.zone_id = z.zone_id where a.geo_zone_id = :geo_zone_id order by association_id limit :page_set_offset, :page_set_max_results');
    $Qzones->bindInt(':geo_zone_id', $_GET['zID']);
    $Qzones->setPageSet(MAX_DISPLAY_SEARCH_RESULTS, 'spage');
    $Qzones->execute();

    while ($Qzones->fetch()) {
      if ((!isset($_GET['sID']) || (isset($_GET['sID']) && ((int)$_GET['sID'] === $Qzones->valueInt('association_id')))) && !isset($sInfo) && (substr($action, 0, 3) != 'new')) {
        $sInfo = new objectInfo($Qzones->toArray());

        if (is_null($sInfo->countries_name)) {
          $sInfo->countries_name = TEXT_ALL_COUNTRIES;
        }

        if (is_null($sInfo->zone_name)) {
          $sInfo->zone_name = PLEASE_SELECT;
        }
      }

      if (isset($sInfo) && is_object($sInfo) && ($Qzones->valueInt('association_id') === (int)$sInfo->association_id)) {
        echo '                  <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=edit') . '\'">' . "\n";
      } else {
        echo '                  <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $Qzones->valueInt('association_id')) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo $Qzones->hasValue('countries_name') ? $Qzones->value('countries_name') : TEXT_ALL_COUNTRIES; ?></td>
                <td class="dataTableContent"><?php echo $Qzones->hasValue('zone_name') ? $Qzones->value('zone_name') : PLEASE_SELECT; ?></td>
                <td class="dataTableContent" align="right"><?php if (isset($sInfo) && is_object($sInfo) && ($Qzones->valueInt('association_id') === (int)$sInfo->association_id)) { echo HTML::image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $Qzones->valueInt('association_id')) . '">' . HTML::image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $Qzones->getPageSetLabel(TEXT_DISPLAY_NUMBER_OF_COUNTRIES); ?></td>
                    <td class="smallText" align="right"><?php echo $Qzones->getPageSetLinks('zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list'); ?></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td class="smallText" align="right" colspan="3"><?php if (empty($saction)) echo HTML::button(IMAGE_BACK, 'fa fa-chevron-left', OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'])) . HTML::button(IMAGE_INSERT, 'fa fa-plus', OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&' . (isset($sInfo) ? 'sID=' . $sInfo->association_id . '&' : '') . 'saction=new')); ?></td>
              </tr>
            </table>
<?php
  } else {
?>
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX_ZONES; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $Qzones = $OSCOM_Db->prepare('select SQL_CALC_FOUND_ROWS geo_zone_id, geo_zone_name, geo_zone_description, last_modified, date_added from :table_geo_zones order by geo_zone_name limit :page_set_offset, :page_set_max_results');
    $Qzones->setPageSet(MAX_DISPLAY_SEARCH_RESULTS, 'zpage');
    $Qzones->execute();

    while ($Qzones->fetch()) {
      if ((!isset($_GET['zID']) || (isset($_GET['zID']) && ((int)$_GET['zID'] === $Qzones->valueInt('geo_zone_id')))) && !isset($zInfo) && (substr($action, 0, 3) != 'new')) {
        $Qtotal = $OSCOM_Db->prepare('select count(*) as num_zones from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id group by geo_zone_id');
        $Qtotal->bindInt(':geo_zone_id', $Qzones->valueInt('geo_zone_id'));
        $Qtotal->execute();

        $zInfo = new objectInfo(array_merge($Qzones->toArray(), $Qtotal->toArray()));
      }

      if (isset($zInfo) && is_object($zInfo) && ($Qzones->valueInt('geo_zone_id') === (int)$zInfo->geo_zone_id)) {
        echo '                  <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=list') . '\'">' . "\n";
      } else {
        echo '                  <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $Qzones->valueInt('geo_zone_id')) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo '<a href="' . OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $Qzones->valueInt('geo_zone_id') . '&action=list') . '">' . HTML::image(DIR_WS_ICONS . 'folder.gif', ICON_FOLDER) . '</a>&nbsp;' . $Qzones->value('geo_zone_name'); ?></td>
                <td class="dataTableContent" align="right"><?php if (isset($zInfo) && is_object($zInfo) && ($Qzones->valueInt('geo_zone_id') === (int)$zInfo->geo_zone_id)) { echo HTML::image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $Qzones->valueInt('geo_zone_id')) . '">' . HTML::image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $Qzones->getPageSetLabel(TEXT_DISPLAY_NUMBER_OF_TAX_ZONES); ?></td>
                    <td class="smallText" align="right"><?php echo $Qzones->getPageSetLinks(); ?></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td class="smallText" align="right" colspan="2"><?php if (!$action) echo HTML::button(IMAGE_INSERT, 'fa fa-plus', OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . (isset($zInfo) ? '&zID=' . $zInfo->geo_zone_id : '') . '&action=new_zone')); ?></td>
              </tr>
            </table>
<?php
  }
?>
            </td>
<?php
  $heading = array();
  $contents = array();

  if ($action == 'list') {
    switch ($saction) {
      case 'new':
        $heading[] = array('text' => '<strong>' . TEXT_INFO_HEADING_NEW_SUB_ZONE . '</strong>');

        $contents = array('form' => HTML::form('zones', OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&' . (isset($_GET['sID']) ? 'sID=' . $_GET['sID'] . '&' : '') . 'saction=insert_sub')));
        $contents[] = array('text' => TEXT_INFO_NEW_SUB_ZONE_INTRO);
        $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY . '<br />' . HTML::selectField('zone_country_id', tep_get_countries(TEXT_ALL_COUNTRIES), '', 'onchange="update_zone(this.form);"'));
        $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_ZONE . '<br />' . HTML::selectField('zone_id', tep_prepare_country_zones_pull_down()));
        $contents[] = array('align' => 'center', 'text' => '<br />' . HTML::button(IMAGE_SAVE, 'fa fa-save', null, 'primary') . HTML::button(IMAGE_CANCEL, 'fa fa-close', OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&' . (isset($_GET['sID']) ? 'sID=' . $_GET['sID'] : ''))));
        break;
      case 'edit':
        $heading[] = array('text' => '<strong>' . TEXT_INFO_HEADING_EDIT_SUB_ZONE . '</strong>');

        $contents = array('form' => HTML::form('zones', OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=save_sub')));
        $contents[] = array('text' => TEXT_INFO_EDIT_SUB_ZONE_INTRO);
        $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY . '<br />' . HTML::selectField('zone_country_id', tep_get_countries(TEXT_ALL_COUNTRIES), $sInfo->zone_country_id, 'onchange="update_zone(this.form);"'));
        $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_ZONE . '<br />' . HTML::selectField('zone_id', tep_prepare_country_zones_pull_down($sInfo->zone_country_id), $sInfo->zone_id));
        $contents[] = array('align' => 'center', 'text' => '<br />' . HTML::button(IMAGE_SAVE, 'fa fa-save', null, 'primary') . HTML::button(IMAGE_CANCEL, 'fa fa-close', OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id)));
        break;
      case 'delete':
        $heading[] = array('text' => '<strong>' . TEXT_INFO_HEADING_DELETE_SUB_ZONE . '</strong>');

        $contents = array('form' => HTML::form('zones', OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=deleteconfirm_sub')));
        $contents[] = array('text' => TEXT_INFO_DELETE_SUB_ZONE_INTRO);
        $contents[] = array('text' => '<br /><strong>' . $sInfo->countries_name . '</strong>');
        $contents[] = array('align' => 'center', 'text' => '<br />' . HTML::button(IMAGE_DELETE, 'fa fa-trash', null, 'primary') . HTML::button(IMAGE_CANCEL, 'fa fa-close', OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id)));
        break;
      default:
        if (isset($sInfo) && is_object($sInfo)) {
          $heading[] = array('text' => '<strong>' . $sInfo->countries_name . '</strong>');

          $contents[] = array('align' => 'center', 'text' => HTML::button(IMAGE_EDIT, 'fa fa-edit', OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=edit')) . HTML::button(IMAGE_DELETE, 'fa fa-trash', OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=delete')));
          $contents[] = array('text' => '<br />' . TEXT_INFO_DATE_ADDED . ' ' . tep_date_short($sInfo->date_added));
          if (tep_not_null($sInfo->last_modified)) $contents[] = array('text' => TEXT_INFO_LAST_MODIFIED . ' ' . tep_date_short($sInfo->last_modified));
        }
        break;
    }
  } else {
    switch ($action) {
      case 'new_zone':
        $heading[] = array('text' => '<strong>' . TEXT_INFO_HEADING_NEW_ZONE . '</strong>');

        $contents = array('form' => HTML::form('zones', OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=insert_zone')));
        $contents[] = array('text' => TEXT_INFO_NEW_ZONE_INTRO);
        $contents[] = array('text' => '<br />' . TEXT_INFO_ZONE_NAME . '<br />' . HTML::inputField('geo_zone_name'));
        $contents[] = array('text' => '<br />' . TEXT_INFO_ZONE_DESCRIPTION . '<br />' . HTML::inputField('geo_zone_description'));
        $contents[] = array('align' => 'center', 'text' => '<br />' . HTML::button(IMAGE_SAVE, 'fa fa-save', null, 'primary') . HTML::button(IMAGE_CANCEL, 'fa fa-close', OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'])));
        break;
      case 'edit_zone':
        $heading[] = array('text' => '<strong>' . TEXT_INFO_HEADING_EDIT_ZONE . '</strong>');

        $contents = array('form' => HTML::form('zones', OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=save_zone')));
        $contents[] = array('text' => TEXT_INFO_EDIT_ZONE_INTRO);
        $contents[] = array('text' => '<br />' . TEXT_INFO_ZONE_NAME . '<br />' . HTML::inputField('geo_zone_name', $zInfo->geo_zone_name));
        $contents[] = array('text' => '<br />' . TEXT_INFO_ZONE_DESCRIPTION . '<br />' . HTML::inputField('geo_zone_description', $zInfo->geo_zone_description));
        $contents[] = array('align' => 'center', 'text' => '<br />' . HTML::button(IMAGE_SAVE, 'fa fa-save', null, 'primary') . HTML::button(IMAGE_CANCEL, 'fa fa-close', OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id)));
        break;
      case 'delete_zone':
        $heading[] = array('text' => '<strong>' . TEXT_INFO_HEADING_DELETE_ZONE . '</strong>');

        $contents = array('form' => HTML::form('zones', OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=deleteconfirm_zone')));
        $contents[] = array('text' => TEXT_INFO_DELETE_ZONE_INTRO);
        $contents[] = array('text' => '<br /><strong>' . $zInfo->geo_zone_name . '</strong>');
        $contents[] = array('align' => 'center', 'text' => '<br />' . HTML::button(IMAGE_DELETE, 'fa fa-trash', null, 'primary') . HTML::button(IMAGE_CANCEL, 'fa fa-close', OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id)));
        break;
      default:
        if (isset($zInfo) && is_object($zInfo)) {
          $heading[] = array('text' => '<strong>' . $zInfo->geo_zone_name . '</strong>');

          $contents[] = array('align' => 'center', 'text' => HTML::button(IMAGE_EDIT, 'fa fa-edit', OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=edit_zone')) . HTML::button(IMAGE_DELETE, 'fa fa-trash', OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=delete_zone')) . HTML::button(IMAGE_DETAILS, 'fa fa-info', OSCOM::link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=list')));
          $contents[] = array('text' => '<br />' . TEXT_INFO_NUMBER_ZONES . ' ' . $zInfo->num_zones);
          $contents[] = array('text' => '<br />' . TEXT_INFO_DATE_ADDED . ' ' . tep_date_short($zInfo->date_added));
          if (tep_not_null($zInfo->last_modified)) $contents[] = array('text' => TEXT_INFO_LAST_MODIFIED . ' ' . tep_date_short($zInfo->last_modified));
          $contents[] = array('text' => '<br />' . TEXT_INFO_ZONE_DESCRIPTION . '<br />' . $zInfo->geo_zone_description);
        }
        break;
    }
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
    </table>

<?php
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
