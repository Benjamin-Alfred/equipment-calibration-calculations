<?php
function my_theme_enqueue_styles() {

    $parent_style = 'twentyseventeen-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );

    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );

    wp_enqueue_style(
        'jquery-ui',
        get_stylesheet_directory_uri() . '/js/jquery-ui-1.12.1.custom/jquery-ui.min.css',
        array($parent_style, 'child-style')
    );

    wp_enqueue_style(
        'bootstrap',
        get_stylesheet_directory_uri() . '/js/bootstrap/css/bootstrap.min.css',
        array($parent_style, 'child-style', 'jquery-ui')
    );

    wp_enqueue_style(
        'datatables',
        get_stylesheet_directory_uri() . '/js/datatables.net/css/jquery.dataTables.min.css',
        array($parent_style, 'child-style', 'bootstrap')
    );

    wp_enqueue_style(
        'open-iconic',
        get_stylesheet_directory_uri() . '/i/open-iconic/font/css/open-iconic.css',
        array($parent_style, 'child-style', 'bootstrap')
    );

    wp_enqueue_style(
        'open-iconic-bootstrap',
        get_stylesheet_directory_uri() . '/i/open-iconic/font/css/open-iconic-bootstrap.css',
        array($parent_style, 'child-style', 'bootstrap')
    );

    wp_enqueue_script(
        'jquery-ui',
        get_stylesheet_directory_uri() . '/js/jquery-ui-1.12.1.custom/jquery-ui.min.js',
        array('jquery')
    );

    wp_enqueue_script(
        'bootstrap',
        get_stylesheet_directory_uri() . '/js/bootstrap/js/bootstrap.bundle.min.js',
        array('jquery','jquery-ui')
    );

    wp_enqueue_script(
        'datatables',
        get_stylesheet_directory_uri() . '/js/datatables.net/js/jquery.dataTables.min.js',
        array('jquery')
    );

    wp_enqueue_script(
        'feather-icons',
        get_stylesheet_directory_uri() . '/js/feather-icons/feather.min.js',
        array('jquery')
    );

    wp_enqueue_script(
        'chart-js',
        get_stylesheet_directory_uri() . '/js/Chart.js/Chart.min.js',
        array('jquery')
    );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

// Equipment types
const CONDITIONED_CHAMBERS = 1;
const THERMOMETERS = 2;
const CENTRIFUGES = 3;
const TIMERS = 4;

const NO_PERMISSION_ERROR = "<div style='padding-top:30px;'>You do not have permission to access this data! Contact your system administrator.</div>";

function addCOEManufacturer($name){
    global $wpdb;
    if ($name != false) {
        $result = $wpdb->insert("wp_coe_manufacturers", array('name' => trim($name)));
    }
}

function getCOEManufacturers(){
    global $wpdb;
    
    return $wpdb->get_results("SELECT * FROM wp_coe_manufacturers ORDER BY name;");
}

function addCOEEquipment($name, $equipmentType=NULL){
    global $wpdb;
    $newEquipment = ['name' => trim($name)];
    if(isset($equipmentType)) $newEquipment['equipment_type_id'] = $equipmentType;
    if ($name != false) {
        $result = $wpdb->insert("wp_coe_equipment", $newEquipment);
    }
}

function getCOEEquipment($equipmentType=NULL){
    global $wpdb;
    
    $whereClause = "";
    if($equipmentType)
        $whereClause = "WHERE equipment_type_id = $equipmentType";

    return $wpdb->get_results("SELECT * FROM wp_coe_equipment $whereClause ORDER BY name;");
}

function getCOEEquipmentTypes(){
    global $wpdb;
    
    return $wpdb->get_results("SELECT * FROM wp_coe_equipment_types ORDER BY name;");
}

function addCOESTEquipment($name){
    global $wpdb;
    if ($name != false) {
        $result = $wpdb->insert("wp_coe_standard_test_equipment", array('name' => trim($name)));
    }
}

function getCOESTEquipment(){
    global $wpdb;
    
    return $wpdb->get_results("SELECT * FROM wp_coe_standard_test_equipment ORDER BY name;");
}

function addCOEClient($name){
    global $wpdb;
    if ($name != false) {
        $result = $wpdb->insert("wp_coe_clients", array('name' => trim($name)));
    }
}

function getCOEClients(){
    global $wpdb;
    
    return $wpdb->get_results("SELECT * FROM wp_coe_clients ORDER BY name;");
}

function activateCOEClientContact($contactID, $enable = 1){
    global $wpdb;
    $inputArray = [];
    $where = [];
    $result = false;
    if($enable == 0)$enable = 1;
    else if($enable == 1)$enable = 0;

    if ($clientID !== false) {
        $where["id"] = $contactID;
        $inputArray["can_login"] = $enable;
        $result = $wpdb->update("wp_coe_client_contacts", $inputArray, $where);
    }
    return $result;
}

function addCOEClientContact($clientID, $name, $email, $phone, $password = ""){
    global $wpdb;
    $inputArray = [];
    $result = false;
    if ($clientID !== false && $name !== false && $email !== false) {
        $inputArray["client_id"] = $clientID;
        $inputArray["name"] = trim($name);
        $inputArray["email"] = trim($email);
        $inputArray["phone"] = trim($phone);
        if($password !== false && strcmp(trim($password), "") != 0){
            $inputArray["password"] = md5(trim($password));
        }else{
            $randomPassword = substr(md5(rand()), 0, 7);
            $inputArray["password"] = md5($randomPassword);
        }
        $result = $wpdb->insert("wp_coe_client_contacts", $inputArray);

        log2File("Email: {$inputArray['email']} Random Password: $randomPassword");
    }
    return $result;
}

function updateCOEClientContact($contactID, $facilityID, $name, $email, $phone, $password = ""){
    global $wpdb;
    $inputArray = $where = [];
    $result = false;
    if ($contactID !== false && $facilityID !== false && $name !== false && $email !== false) {
        $where["id"] = $contactID;
        $inputArray["name"] = trim($name);
        $inputArray["client_id"] = trim($facilityID);
        $inputArray["phone"] = trim($phone);
        if($password !== false && strcmp(trim($password), "") != 0){
            $inputArray["password"] = md5(trim($password));
        }
        $result = $wpdb->update("wp_coe_client_contacts", $inputArray, $where);

        log2File("Email: {$inputArray['email']} updated");
    }
    return $result;
}

function getCOEClientContact($contactID){
    global $wpdb;

    $query = "SELECT f.code, c.* FROM wp_coe_client_contacts c LEFT JOIN wp_coe_facilities f ON c.client_id = f.id WHERE c.id = '$contactID'";
    log2File($query);
    return $wpdb->get_row($query);
}

function getCOEClientContacts($withFacilityDetails = false){
    global $wpdb;

    if($withFacilityDetails)
        $results = $wpdb->get_results("SELECT f.code, f.name AS facility_name, cc.* FROM wp_coe_client_contacts cc INNER JOIN wp_coe_facilities f ON cc.client_id = f.id ORDER BY f.name, cc.name;");
    else
        $results = $wpdb->get_results("SELECT * FROM wp_coe_client_contacts ORDER BY name;");
    return $results;
}

function getCOEFacility($facilityCode){
    global $wpdb;
    $query = "SELECT f.id, f.code, f.name, s.name AS sub_county, c.name AS county FROM wp_coe_facilities f ";
    $query .= "INNER JOIN wp_coe_sub_counties s ON f.sub_county_id = s.id ";
    $query .= "INNER JOIN wp_coe_counties c ON s.county_id = c.id WHERE code = $facilityCode;";
    return $wpdb->get_results($query);
}

function getCOECCCertificate($certificateID){
    global $wpdb;

    $query = "SELECT wp_coe_conditioned_chamber_calculations.*,
                wp_coe_clients.name AS client_name,
                wp_coe_client_contacts.name AS client_contact_name,
                wp_coe_client_contacts.email AS client_contact_email,
                wp_coe_equipment.name AS equipment_name,
                wp_coe_manufacturers.name AS manufacturer_name,
                DATE_FORMAT(DATE_ADD(wp_coe_conditioned_chamber_calculations.date_performed, INTERVAL 1 YEAR),'%M %Y') AS certificate_validity
            FROM wp_coe_conditioned_chamber_calculations 
            LEFT JOIN wp_coe_clients 
                ON wp_coe_conditioned_chamber_calculations.client_id = wp_coe_clients.id
            LEFT JOIN wp_coe_client_contacts 
                ON wp_coe_conditioned_chamber_calculations.client_contact_id = wp_coe_client_contacts.id
            LEFT JOIN wp_coe_equipment 
                ON wp_coe_conditioned_chamber_calculations.equipment_id = wp_coe_equipment.id
            LEFT JOIN wp_coe_manufacturers
                ON wp_coe_conditioned_chamber_calculations.manufacturer_id = wp_coe_manufacturers.id
            WHERE wp_coe_conditioned_chamber_calculations.id = $certificateID;";

    $result = $wpdb->get_row($query);
    
    $subQuery = "SELECT * FROM wp_coe_conditioned_chamber_calculation_readings WHERE conditioned_chamber_calculation_id = ".$result->id;

    $result->readings = $wpdb->get_results($subQuery, ARRAY_A);

    // Creators, verifiers and approvers
    $subQuery = "SELECT display_name FROM wp_users WHERE ID = ".$result->created_by;

    $result->creator = $wpdb->get_row($subQuery, ARRAY_A);

    $subQuery = "SELECT display_name FROM wp_users WHERE ID = ".$result->verified_by;

    $result->verifier = $wpdb->get_row($subQuery, ARRAY_A);

    $subQuery = "SELECT display_name FROM wp_users WHERE ID = ".$result->approved_by;

    $result->approver = $wpdb->get_row($subQuery, ARRAY_A);

    // Standard Test Equipment Info: Name and manufacturer
    $subQuery = "SELECT name FROM wp_coe_standard_test_equipment WHERE id = ".$result->standard_test_equipment_id;

    $result->ste_equipment = $wpdb->get_row($subQuery, ARRAY_A);

    $subQuery = "SELECT name FROM wp_coe_manufacturers WHERE id = ".$result->standard_test_equipment_manufacturer_id;

    $result->ste_manufacturer = $wpdb->get_row($subQuery, ARRAY_A);

    return $result;
}

function hasRole($role){
    global $wpdb;

    $roles = ['CALIBRATOR' => 1, 'REVIEWER' => 2, 'APPROVER' => 3, 'USER_ADMIN' => 4, 'DATA_ANALYST' => 5];
    $currentUser = wp_get_current_user();

    $query = "SELECT COUNT(*) hits FROM wp_coe_user_roles WHERE user_id = ".$currentUser->ID." AND role_id = ".$roles[$role];
    $result = $wpdb->get_row($query, ARRAY_A);

    return intval($result['hits']) == 1;
}

/*
 * Source: http://php.net/manual/en/function.stats-standard-deviation.php
 */
function sd_square($x, $mean) { return pow($x - $mean,2); }

function sd($array) { 
    
    // square root of sum of squares devided by N-1 
    return sqrt(
        array_sum(
            array_map("sd_square", $array, array_fill(0, count($array), (array_sum($array) / count($array))))
            ) / (count($array)-1)
        ); 
}

/*
 * Log to file
 */
function log2File($logText, $logFile = "coesite.log"){
    $now = new DateTime("now", new DateTimeZone('Africa/Nairobi'));
    error_log("{$now->format('Y-m-d G:i:s')} $logText\n", 3, $logFile);
}

function cleanFormInput($input, $type = "") {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);

    switch ($type) {
        case 'DATE':
            $input = (new DateTime($input, new DateTimeZone("Africa/Nairobi")))->format('Y-m-d');
            break;
    }

    return $input;
}
?>
