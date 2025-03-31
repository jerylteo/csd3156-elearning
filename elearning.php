<?php include "../inc/dbinfo.inc"; ?>
<html>
<body>
<h1>E-Learning Dashboard</h1>
<?php

  /* Connect to MySQL and select the database. */
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

    if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

    $database = mysqli_select_db($connection, DB_DATABASE);

      /* Ensure that the attendance table exists. */
      VerifyTable($connection, DB_DATABASE);

      /* If input fields are populated, add a row to the EMPLOYEES table. */
      // $studentid = htmlentities($_POST['STUDENTID']);
      //   $name = htmlentities($_POST['NAME']);

      //   if (strlen($studentid) || strlen($name)) {
		  //   AddAttendance($connection, $studentid, $name);
		  //     }
?>

<!-- Input form -->
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <table border="0">
    <tr>
      <td>Course Name</td>
      <td>Category</td>
    </tr>
    <tr>
      <td>
        <input type="text" name="COURSENAME" maxlength="45" size="30" />
      </td>
      <td>
        <input type="text" name="CATEGORY" maxlength="90" size="60" />
      </td>
      <td>
        <input type="submit" value="Add" />
      </td>
    </tr>
  </table>
</form>

<h2> -------------------------------------------------------------------------------------------</h2>

<!-- Display table data. -->
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>ID</td>
    <td>COURSE NAME   </td>
    <td>CATEGORY     </td>
  </tr>

<?php

	$result = mysqli_query($connection, "SELECT * FROM COURSES");

	while($query_data = mysqli_fetch_row($result)) {
		  echo "<tr>";
		    echo "<td>",$query_data[0], "</td>";
			  echo "<td>",$query_data[1], "</td>";
			  echo "<td>",$query_data[2], "</td>";
		    echo "</tr>";
	}
?>

</table>

<!-- Clean up. -->
<?php

	  mysqli_free_result($result);
	  mysqli_close($connection);

?>

</body>
</html>


<?php
    /* Add a student to the table */
    function AddStudent($connection, $studentid, $name) {
        $n = mysqli_real_escape_string($connection, $studentid);
          $a = mysqli_real_escape_string($connection, $name);

          $query = "INSERT INTO STUDENTS (STUDENTID, NAME, COURSES) VALUES ('$n', '$a');";

            if(!mysqli_query($connection, $query)) echo("<p>Error adding data.</p>");

	  /* Add a course to the table */
    function AddCourse($connection, $coursename, $category) {
        $n = mysqli_real_escape_string($connection, $coursename);
          $a = mysqli_real_escape_string($connection, $category);

          $query = "INSERT INTO COURSES (COURSENAME, MODULES) VALUES ('$n', '$a');";

            if(!mysqli_query($connection, $query)) echo("<p>Error adding data.</p>");
    }

    /* Add a module to the table */
    function AddModule($connection, $modulename, $category) {
        $n = mysqli_real_escape_string($connection, $modulename);
          $a = mysqli_real_escape_string($connection, $category);

          $query = "INSERT INTO MODULES (MODULENAME, CATEGORY) VALUES ('$n', '$a');";

            if(!mysqli_query($connection, $query)) echo("<p>Error adding data.</p>");
    }

    /* Check whether the tables exists and, if not, create it. */
    function VerifyTable($connection, $dbName) {
        if(!TableExists("COURSES", $connection, $dbName))
        {
            $query = "CREATE TABLE COURSES (
                ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                COURSENAME VARCHAR(45),
                MODULES VARCHAR(90)
            )";

            if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
        }
        if(!TableExists("MODULES", $connection, $dbName))
        {
            $query = "CREATE TABLE MODULES (
                ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                MODULENAME VARCHAR(45),
                CATEGORY VARCHAR(90)
            )";

            if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
        }
        if(!TableExists("STUDENTS", $connection, $dbName))
        {
            $query = "CREATE TABLE STUDENTS (
                ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                STUDENTID VARCHAR(45),
                NAME VARCHAR(90),
                COURSES VARCHAR(90)
            )";

            if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
        }
    }


	  /* Check for the existence of a table. */
	  function TableExists($tableName, $connection, $dbName) {
		    $t = mysqli_real_escape_string($connection, $tableName);
		      $d = mysqli_real_escape_string($connection, $dbName);

		      $checktable = mysqli_query($connection,
			            "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

		        if(mysqli_num_rows($checktable) > 0) return true;

		        return false;
	  }
?>                        
                
