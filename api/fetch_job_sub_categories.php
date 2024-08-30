<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../include_files.php";

// Set content type to JSON
header('Content-Type: application/json');

// Get category_id from GET request and validate it
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

// Initialize an empty array for subcategories
$subCategories = array();

if ($category_id > 0) {
    // Query to fetch job subcategories based on the selected job category
    $query = "SELECT * FROM job_sub_category WHERE job_category_id = $category_id ORDER BY priority";
    
    // Execute the query
    $result = tep_db_query($query);

    // Check for query execution errors
    if ($result === false) {
        // Output an error message in JSON format
        echo json_encode(array('error' => 'Database query failed.'));
        exit;
    }

    // Fetch and store the subcategories
    while ($row = tep_db_fetch_array($result)) {
        $subCategories[] = array(
            'id' => $row['id'],
            'name' => $row[TEXT_LANGUAGE.'sub_category_name']
        );
    }
} else {
    // If no valid category_id is provided, output an empty array
    echo json_encode(array('error' => 'Invalid category ID.'));
    exit;
}

// Output the subcategories in JSON format
echo json_encode($subCategories);
?>

