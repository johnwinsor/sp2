<?php

/**
 *   @file export_contacts.php
 *   @brief View and update emergency contact info
 *
 *   @author adarby
 *   @date mar 2012
 *   @todo 
 */

$subcat = "";
$header = "noshow";
include("../includes/header.php");

switch ($_GET["type"]) {
  case "all_staff":
    
    break;
  case "direct":
    // get only those reporting DIRECTLY to this person 
    $and = "AND supervisor_id = " . $_SESSION["staff_id"];
   
    break;
  
  case "all_reports":
     // Get the whole chain of folks reporting to this person
    
     //$and = "AND supervisor_id IN (" . $_GET["ids"] . ")";
    $and = "AND supervisor_id IN (" . $_GET["ids"] . ")";
    break;
  default:
    $and = "AND user_type_id = '1' ";
}
print_r($_SESSION);

// Boot them out if they shouldn't be viewing this file 
if ($check_credentials == FALSE) {

  echo "<p style=\"background-color: red; color: white;\">You probably should not be here.  Please use the back button.  If you think you should be able to access this part of the site, please contact an administrator";
  include("../includes/footer.php");
  
  exit;
}

$header = "";
$data = "";

$select = "SELECT lname AS 'Last Name', fname AS 'First Name', tel AS 'Work Phone #', cell_phone AS 'Cell Phone #', home_phone as 'Home Phone',  staff.email AS 'Email',
emergency_contact_name AS 'Contact Name', emergency_contact_phone AS 'Contact Phone #', emergency_contact_relation AS 'Relationship', name AS 'Department',
  street_address AS 'Street Address', city as 'City', state AS 'State', zip as 'Zip Code', supervisor_id AS Super_ID, (SELECT lname from staff where staff.staff_id = Super_ID) AS 'Supervisor LName', (SELECT 
  fname from staff where staff.staff_id = Super_ID) AS 'Supervisor FName'
  FROM staff, department
  WHERE active = '1'
  AND staff.department_id = department.department_id
  $and
  ORDER BY lname";

$export = mysql_query ( $select ) or die ( "Sql error : " . mysql_error( ) );

$fields = mysql_num_fields ( $export );

for ( $i = 0; $i < $fields; $i++ )
{
    $header .= mysql_field_name( $export , $i ) . "\t";
}

while( $row = mysql_fetch_row( $export ) )
{
    $line = '';
    foreach( $row as $value )
    {                                            
        if ( ( !isset( $value ) ) || ( $value == "" ) )
        {
            $value = "\t";
        }
        else
        {
            $value = str_replace( '"' , '""' , $value );
            $value = '"' . $value . '"' . "\t";
        }
        $line .= $value;
    }
    $data .= trim( $line ) . "\n";
}
$data = str_replace( "\r" , "" , $data );

if ( $data == "" )
{
    $data = "\n(0) Records Found!\n";                        
}

header("Content-type: .xls application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=contacts.xls");
header("Pragma: no-cache");
header("Expires: 0");
print "$header\n$data";

    

exit; 
?>