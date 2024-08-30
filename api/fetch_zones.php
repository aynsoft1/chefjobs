<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../include_files.php";

// Set content type to JSON
header('Content-Type: application/json');

if (isset($_GET['country_id'])) {
    $country_id = intval($_GET['country_id']);
    $query = "SELECT zone_id, zone_name FROM zones WHERE zone_country_id = '" . intval($country_id) . "' ORDER BY zone_name";
    $result = tep_db_query($query);

    $states = [];
    while ($row = tep_db_fetch_array($result)) {
        $states[] = [
            'zone_id' => $row['zone_id'],
            'zone_name' => stripslashes($row['zone_name']),
        ];
    }

    echo json_encode($states);
} else {
    echo json_encode(array('error' => 'Invalid country ID.'));
    exit;
}
?>
