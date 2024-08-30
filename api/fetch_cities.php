<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../include_files.php";

// Set content type to JSON
header('Content-Type: application/json');

if (isset($_GET['state_id'])) {
    $state_id = intval($_GET['state_id']);
    $query = "SELECT city_id, city_name FROM cities WHERE city_zone_id = '" . intval($state_id) . "' ORDER BY city_name";
    $result = tep_db_query($query);

    $cities = [];
    while ($row = tep_db_fetch_array($result)) {
        $cities[] = [
            'city_id' => $row['city_id'],
            'city_name' => stripslashes($row['city_name']),
        ];
    }

    echo json_encode($cities);
}else{
        // If no valid category_id is provided, output an empty array
        echo json_encode(array('error' => 'Invalid state ID.'));
        exit;
}
?>