<?

include_once("../include_files.php");
include_once(PATH_TO_MAIN_ADMIN_PHYSICAL_LANGUAGE . $language . '/' . FILENAME_ADMIN1_ADMIN_ZONE_CITY);
$template->set_filenames(array('cities' => 'zone-city.htm'));
include_once(FILENAME_ADMIN_BODY);

$action = (isset($_GET['action']) ? $_GET['action'] : '');

if (tep_not_null($action)) {
    switch ($action) {
        case 'insert':
            $country_id = tep_db_prepare_input($_POST['country_id']);
            $city_zone_id = tep_db_prepare_input($_POST['zone_id']);
            $city_name = tep_db_prepare_input($_POST['city_name']);
            tep_db_query("insert into cities (country_id,city_zone_id,city_name) values ('".(int)$country_id."','" . (int)$city_zone_id . "', '" . tep_db_input($city_name) . "')");
            $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
            tep_redirect(tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_ADMIN_ZONE_CITY));
            break;
        case 'save':
            $city_id = tep_db_prepare_input($_GET['zID']);
            $country_id = tep_db_prepare_input($_POST['country_id']);
            $city_zone_id = tep_db_prepare_input($_POST['zone_id']);
            $city_name = tep_db_prepare_input($_POST['city_name']);
            tep_db_query("update cities set country_id = '".(int)$country_id."', city_zone_id = '" . (int)$city_zone_id . "', city_name = '" . tep_db_input($city_name) . "' where city_id = '" . (int)$city_id . "'");
            $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
            tep_redirect(tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_ADMIN_ZONE_CITY, 'page=' . $_GET['page'] . '&zID=' . $city_id));
            break;
        case 'deleteconfirm':
            $city_id = tep_db_prepare_input($_GET['zID']);
            tep_db_query("delete from cities where city_id = '" . (int)$city_id . "'");
            tep_redirect(tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_ADMIN_ZONE_CITY, 'page=' . $_GET['page']));
            break;
    }
}
///////////// Middle Values 
$zones_city_query_raw = "select c.*, z.zone_id, z.zone_name 
                    from cities as c
                    left join zones as z on z.zone_id = c.city_zone_id";

$zones_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $zones_city_query_raw, $zones_query_numrows);
$zones_city_query = tep_db_query($zones_city_query_raw);
if (tep_db_num_rows($zones_city_query) > 0) {
    $alternate = 1;
    while ($cities = tep_db_fetch_array($zones_city_query)) {
        if ((!isset($_GET['zID']) || (isset($_GET['zID']) && ($_GET['zID'] == $cities['city_id']))) && !isset($zInfo) && (substr($action, 0, 3) != 'new')) {
            $zInfo = new objectInfo($cities);
        }
        if (isset($zInfo) && is_object($zInfo) && ($cities['city_id'] == $zInfo->city_id)) {
            $row_selected = ' id="defaultSelected" class="table-secondary dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_ADMIN_ZONE_CITY, 'page=' . $_GET['page'] . '&zID=' . $zInfo->city_id . '&action=edit') . '\'"';
        } else {
            $row_selected = ' class="dataTableRow' . ($alternate % 2 == 1 ? '1' : '2') . '" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_ADMIN_ZONE_CITY, 'page=' . $_GET['page'] . '&zID=' . $cities['city_id']) . '\'"';
        }
        $alternate++;
        if ((isset($zInfo) && is_object($zInfo)) && ($cities['city_id'] == $zInfo->city_id)) {
            $action_image = tep_image(PATH_TO_IMAGE . 'icon_arrow_right.gif', IMAGE_EDIT);
        } else {
            $action_image = '<a href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_ADMIN_ZONE_CITY, 'page=' . $_GET['page'] . '&zID=' . $cities['city_id']) . '">' . tep_image(PATH_TO_IMAGE . 'icon_info.gif', IMAGE_INFO) . '</a>';
        }
        $template->assign_block_vars('cities', array(
            'row_selected' => $row_selected,
            'action' => $action_image,
            'zone_name' => tep_db_output($cities['zone_name']),
            'city_name' => tep_db_output($cities['city_name']),
            'fr_city_name' => tep_db_output($cities['fr_city_name']),
            'row_selected' => $row_selected
        ));
    }
}

// zones list
function tep_get_zones($countryId)
{
    $zones_array = array();

    if (!$countryId) {
        return json_encode('Not Found');
    }
    $cities_query = tep_db_query("select zone_id, " . TEXT_LANGUAGE . "zone_name from " . ZONES_TABLE . " where zone_country_id = $countryId order by " . TEXT_LANGUAGE . "zone_name");
    while ($cities = tep_db_fetch_array($cities_query)) {
        $zones_array[] = array('id' => $cities['zone_id'], 'text' => $cities[TEXT_LANGUAGE . 'zone_name']);
    }

    return $zones_array;
}

// country list
function tep_get_country($default = '')
{
    $zones_array = array();
    if ($default) {
        $zones_array[] = array('id' => '', 'text' => $default);
    }
    $country_query = tep_db_query("select id, " . TEXT_LANGUAGE . "country_name from " . COUNTRIES_TABLE . " order by " . TEXT_LANGUAGE . "country_name");
    while ($country = tep_db_fetch_array($country_query)) {
        $zones_array[] = array('id' => $country['id'], 'text' => $country[TEXT_LANGUAGE . 'country_name']);
    }
    return $zones_array;
}

//// for right side
$ADMIN_RIGHT_HTML = "";

$heading = array();
$contents = array();

switch ($action) {
    case 'new':
        $heading[] = array('text' => '<div class="list-group">
                <div class="font-weight-bold text-primary">
                ' . TEXT_INFO_HEADING_NEW_ZONE . '</div>
                </div>');
        $contents = array('form' => tep_draw_form('cities', PATH_TO_ADMIN . FILENAME_ADMIN1_ADMIN_ZONE_CITY, 'page=' . $_GET['page'] . '&action=insert'));
        $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
                <div class="mb-1 text-danger">' . TEXT_INFO_INSERT_INTRO . '</div>
                <div class="form-group">
                <label>' . TEXT_INFO_CITY_NAME . '</label>
                ' . tep_draw_input_field('city_name', '', 'class="form-control form-control-sm"') . '
                </div>
                <div class="form-group">
                <label>'.TEXT_INFO_COUNTRY_NAME.'</label>
                ' . tep_draw_pull_down_menu('country_id', tep_get_country(), '153', 'id="countryBox" class="form-control form-control-sm" onchange="countrySelectBoxSelect(this)"') . '
                </div>
                <div class="form-group">
                <label>' . TEXT_INFO_ZONES_NAME . '</label>
                ' . tep_draw_pull_down_menu('zone_id', [], '', 'id="zoneBox" class="form-control form-control-sm"') . '
                </div>
                ' . tep_draw_submit_button_field('', IMAGE_INSERT, 'class="btn btn-primary"') . '
                <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_ADMIN_ZONE_CITY, 'page=' . $_GET['page']) . '">'
                        . IMAGE_CANCEL . '</a>
                </div>');
    break;
    case 'edit':
        $heading[] = array('text' => '<div class="list-group">
                    <div class="font-weight-bold text-primary">
                    ' . TEXT_INFO_HEADING_EDIT_ZONE . '</div>
                    </div>');
        $contents = array('form' => tep_draw_form('cities', PATH_TO_ADMIN . FILENAME_ADMIN1_ADMIN_ZONE_CITY, 'page=' . $_GET['page'] . '&zID=' . $zInfo->city_id . '&action=save'));
        $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
                    <div class="mb-1 text-danger">' . TEXT_INFO_EDIT_INTRO . '</div>
                    <div class="form-group">
                    <label>' . TEXT_INFO_CITY_NAME . '</label>
                    ' . tep_draw_input_field('city_name', $zInfo->city_name, 'class="form-control form-control-sm"') . '
                    </div>
                    <div class="form-group">
                    <label>'.TEXT_INFO_COUNTRY_NAME.'</label>
                    ' . tep_draw_pull_down_menu('country_id', tep_get_country(), $zInfo->country_id, 'id="countryBox" class="form-control form-control-sm" onchange="countrySelectBoxSelect(this)"') . '
                    </div>
                    <input type="hidden" id="cityZoneId" value="'.$zInfo->city_zone_id.'" />
                    <div class="form-group">
                    <label>' . TEXT_INFO_ZONES_NAME . '</label>
                    ' . tep_draw_pull_down_menu('zone_id', [], '', 'id="zoneBox" class="form-control form-control-sm"') . '
                    </div>
                    ' . tep_draw_submit_button_field('', IMAGE_UPDATE, 'class="btn btn-primary"') . '
                    <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_ADMIN_ZONE_CITY, 'page=' . $_GET['page'] . '&zID=' . $zInfo->city_id) . '">'
                                . IMAGE_CANCEL . '</a>
                    </div>');
    break;
    case 'delete':
        $heading[] = array('text' => '<div class="list-group">
        <div class="font-weight-bold text-primary">
        ' . TEXT_INFO_HEADING_DELETE_ZONE_CITY . '</div>
        </div>');
                $contents = array('form' => tep_draw_form('zones', PATH_TO_ADMIN . FILENAME_ADMIN1_ADMIN_ZONE_CITY, 'page=' . $_GET['page'] . '&zID=' . $zInfo->city_id . '&action=deleteconfirm'));
                $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
        <div class="mb-1 text-danger">' . TEXT_INFO_DELETE_INTRO . '
        <p>' . $zInfo->city_name . '</p></div>
        ' . tep_draw_submit_button_field('', IMAGE_DELETE, 'class="btn btn-primary"') . '
        <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_ADMIN_ZONE_CITY, 'page=' . $_GET['page'] . '&zID=' . $zInfo->city_id) . '">'
                    . IMAGE_CANCEL . '</a>
        </div>');

        break;
    default:
        if (isset($zInfo) && is_object($zInfo)) {
            $heading[] = array('text' => '<div class="list-group"><div class="font-weight-bold text-primary">' . $zInfo->city_name . '</div></div>');
            $contents[] = array('align' => 'left', 'text' => '<div class="py-2">
            <div class="mb-1">' . tep_db_output($cInfo->country_name) . '</div>
            <a class="btn btn-primary" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_ADMIN_ZONE_CITY, 'page=' . $_GET['page'] . '&zID=' . $zInfo->city_id . '&action=edit') . '">'
                            . IMAGE_EDIT . '</a>
            <a class="btn btn-secondary" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_ADMIN_ZONE_CITY, 'page=' . $_GET['page'] . '&zID=' . $zInfo->city_id . '&action=delete') . '">'
                            . IMAGE_DELETE . '</a>
            <div class="mt-1">' .  TEXT_INFO_CITY_NAME . ' <span>' . $zInfo->city_name . '</span></div>
            <div class="mt-1">' . TEXT_INFO_ZONES_NAME . ' <span>' . $zInfo->zone_name . '</span></div>
            </div>');
        }
        break;
}

if ((tep_not_null($heading)) && (tep_not_null($contents))) {
    $box = new right_box;
    $ADMIN_RIGHT_HTML .= $box->infoBox($heading, $contents);
    $RIGHT_BOX_WIDTH = RIGHT_BOX_WIDTH;
} else {
    $RIGHT_BOX_WIDTH = '0';
}
/////
if (tep_not_null($_GET['q'])) {
    echo json_encode(tep_get_zones($_GET['q']));
}else{
    $template->assign_vars(array(
        'TABLE_HEADING_STATE_NAME' => TABLE_HEADING_STATE_NAME,
        'TABLE_HEADING_CITY_NAME' => TABLE_HEADING_CITY_NAME,
        'TABLE_HEADING_FR_CITY_NAME' => TABLE_HEADING_FR_CITY_NAME,
        'TABLE_HEADING_ACTION' => TABLE_HEADING_ACTION,
        'count_rows' => $zones_split->display_count($zones_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ZONES),
        'no_of_pages' => $zones_split->display_links($zones_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']),
        'new_button' => '<a class="btn btn-primary float-right" href="' . tep_href_link(PATH_TO_ADMIN . FILENAME_ADMIN1_ADMIN_ZONE_CITY, 'page=' . $_GET['page'] . '&action=new') . '"><i class="bi bi-plus-lg me-2"></i>' . IMAGE_NEW . '</a>',
        'HEADING_TITLE' => HEADING_TITLE,
        'RIGHT_BOX_WIDTH' => RIGHT_BOX_WIDTH,
        'ADMIN_RIGHT_HTML' => $ADMIN_RIGHT_HTML,
        'update_message' => $messageStack->output(),
        'zone_fectch_api_url' => tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_ADMIN_ZONE_CITY,'q=')
    ));
    $template->pparse('cities');
}
