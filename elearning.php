<?php include "../inc/dbinfo.inc"; ?>
<html>
<head>
  <title>E-Learning Dashboard</title>
</head>
<body>

<h1>E-Learning Dashboard</h1>

<?php
  /* Connect to MySQL and select the database. */
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

  if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
  }

  $database = mysqli_select_db($connection, DB_DATABASE);

  /* Ensure that required tables exist */
  VerifyTable($connection, DB_DATABASE);

  /* Handle course submission */
  if (!empty($_POST['COURSENAME']) && !empty($_POST['CATEGORY'])) {
    $coursename = htmlentities($_POST['COURSENAME']);
    $category = htmlentities($_POST['CATEGORY']);
    AddCourse($connection, $coursename, $category);
  }
?>

<!-- Input form -->
<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="POST">
  <table border="0">
    <tr>
      <td>Course Name</td>
      <td>Category</td>
    </tr>
    <tr>
      <td><input type="text" name="COURSENAME" maxlength="45" size="30" required /></td>
      <td><input type="text" name="CATEGORY" maxlength="90" size="60" required /></td>
      <td><input type="submit" value="Add" /></td>
    </tr>
  </table>
</form>

<hr />

<!-- Display table data. -->
<h2>Existing Courses</h2>
<table border="1" cellpadding="5" cellspacing="0">
  <thead>
    <tr>
      <th>ID</th>
      <th>Course Name</th>
      <th>Category</th>
    </tr>
  </thead>
  <tbody>
<?php
  $result = mysqli_query($connection, "SELECT * FROM COURSES");

  while($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['ID']) . "</td>";
    echo "<td>" . htmlspecialchars($row['COURSENAME']) . "</td>";
    echo "<td>" . htmlspecialchars($row['CATEGORY']) . "</td>";
    echo "</tr>";
  }

  mysqli_free_result($result);
  mysqli_close($connection);
?>
  </tbody>
</table>

</body>
</html>

<?php
/* Add a course to the table */
function AddCourse($connection, $coursename, $category) {
  $n = mysqli_real_escape_string($connection, $coursename);
  $a = mysqli_real_escape_string($connection, $category);

  $query = "INSERT INTO COURSES (COURSENAME, CATEGORY) VALUES ('$n', '$a')";

  if (!mysqli_query($connection, $query)) {
    echo "<p>Error adding course: " . mysqli_error($connection) . "</p>";
  }
}

/* Ensure required tables exist */
function VerifyTable($connection, $dbName) {
  if (!TableExists("COURSES", $connection, $dbName)) {
    $query = "CREATE TABLE COURSES (
      ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      COURSENAME VARCHAR(45),
      CATEGORY VARCHAR(90)
    )";
    if (!mysqli_query($connection, $query)) {
      echo "<p>Error creating COURSES table: " . mysqli_error($connection) . "</p>";
    }
  }

  if (!TableExists("MODULES", $connection, $dbName)) {
    $query = "CREATE TABLE MODULES (
      ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      MODULENAME VARCHAR(45),
      CATEGORY VARCHAR(90)
    )";
    if (!mysqli_query($connection, $query)) {
      echo "<p>Error creating MODULES table: " . mysqli_error($connection) . "</p>";
    }
  }

  if (!TableExists("STUDENTS", $connection, $dbName)) {
    $query = "CREATE TABLE STUDENTS (
      ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      STUDENTID VARCHAR(45),
      NAME VARCHAR(90),
      COURSES VARCHAR(90)
    )";
    if (!mysqli_query($connection, $query)) {
      echo "<p>Error creating STUDENTS table: " . mysqli_error($connection) . "</p>";
    }
  }
}

/* Check if a table exists in the database */
function TableExists($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);

  $checktable = mysqli_query($connection,
    "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  return (mysqli_num_rows($checktable) > 0);
}
?>