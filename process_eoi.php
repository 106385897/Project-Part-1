<?php

require_once("settings.php");

/* Prevent direct access */

if ($_SERVER["REQUEST_METHOD"] != "POST")
{
    header("location: apply.php");
    exit();
}

/* Connect */

$conn = mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn)
{
    die("Database connection failed.");
}

/* Create table if it does not exist */

$table = "CREATE TABLE IF NOT EXISTS eoi (

EOInumber INT AUTO_INCREMENT PRIMARY KEY,

jobref VARCHAR(10) NOT NULL,

fname VARCHAR(20) NOT NULL,

lname VARCHAR(20) NOT NULL,

dob DATE NOT NULL,

gender VARCHAR(10) NOT NULL,

address VARCHAR(40) NOT NULL,

state VARCHAR(5) NOT NULL,

postcode CHAR(4) NOT NULL,

email VARCHAR(100) NOT NULL,

phone VARCHAR(12) NOT NULL,

skills TEXT,

otherskills TEXT,

status ENUM('New','Current','Final') DEFAULT 'New'

)";

mysqli_query($conn,$table);


/* Function to clean data */

function sanitise($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
}


/* Read form data */

$jobref = sanitise($_POST["jobref"]);
$fname = sanitise($_POST["fname"]);
$lname = sanitise($_POST["lname"]);
$dob = sanitise($_POST["dob"]);
$gender = sanitise($_POST["gender"]);
$address = sanitise($_POST["address"]);
$state = sanitise($_POST["state"]);
$postcode = sanitise($_POST["postcode"]);
$email = sanitise($_POST["email"]);
$phone = sanitise($_POST["phone"]);
$otherskills = sanitise($_POST["otherskills"]);


/* Skills */

$skills = "";

if(isset($_POST["skills"]))
{
    $skills = implode(",",$_POST["skills"]);
}


/* Validation */

$errors = array();

if($jobref=="")
{
    $errors[]="Please choose a job.";
}

if(!preg_match("/^[A-Za-z]{1,20}$/",$fname))
{
    $errors[]="Invalid first name.";
}

if(!preg_match("/^[A-Za-z]{1,20}$/",$lname))
{
    $errors[]="Invalid last name.";
}

if($dob=="")
{
    $errors[]="Enter date of birth.";
}

if($gender=="")
{
    $errors[]="Select gender.";
}

if($address=="")
{
    $errors[]="Enter address.";
}

if($state=="")
{
    $errors[]="Select state.";
}

if(!preg_match("/^[0-9]{4}$/",$postcode))
{
    $errors[]="Postcode must be 4 digits.";
}

if(!filter_var($email,FILTER_VALIDATE_EMAIL))
{
    $errors[]="Invalid email.";
}

if(!preg_match("/^[0-9]{8,12}$/",$phone))
{
    $errors[]="Phone must contain 8-12 digits.";
}


/* Show errors */

if(count($errors)>0)
{
    echo "<h2>Errors Found</h2>";

    foreach($errors as $error)
    {
        echo "<p>$error</p>";
    }

    echo "<a href='apply.php'>Go Back</a>";

    exit();
}


/* Insert */

$sql = "INSERT INTO eoi
(jobref,fname,lname,dob,gender,address,state,postcode,email,phone,skills,otherskills)

VALUES

('$jobref',
'$fname',
'$lname',
'$dob',
'$gender',
'$address',
'$state',
'$postcode',
'$email',
'$phone',
'$skills',
'$otherskills')";


$result = mysqli_query($conn,$sql);


/* Success */

if($result)
{
    $EOInumber = mysqli_insert_id($conn);

    echo "<h1>Application Submitted Successfully</h1>";

    echo "<p>Your EOI Number is <strong>$EOInumber</strong></p>";
}
else
{
    echo "Error inserting record.";
}

mysqli_close($conn);

?>