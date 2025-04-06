<?php
session_start();
include "../inc/dbinfo.inc"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
  <title>E-Learning Dashboard</title>
</head>
<body>

<?php
$togglePopup = false;

/* Connect to MySQL and select the database. */
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  exit();
}

mysqli_select_db($connection, DB_DATABASE);

// Get the course ID from the URL
$course_id = isset($_GET['course']) ? intval($_GET['course']) : 0;

if ($course_id > 0) {
  $togglePopup = true;

  // Get course info
  $courseQuery = mysqli_prepare($connection, "SELECT * FROM COURSES WHERE ID = ?");
  mysqli_stmt_bind_param($courseQuery, "i", $course_id);
  mysqli_stmt_execute($courseQuery);
  $courseResult = mysqli_stmt_get_result($courseQuery);
  $courseInfo = mysqli_fetch_assoc($courseResult); // ✅ this is your associative array

  mysqli_stmt_close($courseQuery);

  // Get modules linked to the course
  $stmt = mysqli_prepare($connection, "SELECT M.* 
                                       FROM MODULES M
                                       JOIN COURSE_MODULES CM ON M.ID = CM.MODULE_ID
                                       WHERE CM.COURSE_ID = ?");
  mysqli_stmt_bind_param($stmt, "i", $course_id);
  mysqli_stmt_execute($stmt);
  $moduleResult = mysqli_stmt_get_result($stmt); 
  $modules = [];

  if ($moduleResult) {
    while ($row = mysqli_fetch_assoc($moduleResult)) {
      $modules[] = $row;
    }
  } else {
  }

  mysqli_stmt_close($stmt);
}

?>

  <nav class="border-gray-200 bg-gray-900">
    <div class="container mx-auto">
      <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
        <a href="#" class="flex items-center space-x-3 rtl:space-x-reverse">
            <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">CSD3156 - E-Learning (Team 8)</span>
        </a>
        <button data-collapse-toggle="navbar-default" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-default" aria-expanded="false">
            <span class="sr-only">Open main menu</span>
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
            </svg>
        </button>
        <div class="hidden w-full md:block md:w-auto" id="navbar-default">
          <ul class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
            <li>
              <a href="elearning.php" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Home</a>
            </li>
            <li>
              <a href="courses.php" class="block py-2 px-3 text-white bg-blue-700 rounded-sm md:bg-transparent md:text-blue-700 md:p-0 dark:text-white md:dark:text-blue-500" aria-current="page">Courses</a>
            </li>
            <li>
              <a href="login.php" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Login</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>

  <main class="border-gray-200 bg-gray-950 text-white py-8">
    <section class="container mx-auto">
      <!-- Courses section -->
      <h1 class="text-3xl font-bold mt-8 mb-4">Enrolled Courses</h1>


      <!-- Popup -->
      <?php if ($togglePopup && $courseInfo): ?>
      <!-- Main modal -->
      <div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
          <div class="relative p-4 w-full max-w-2xl max-h-full">
              <!-- Modal content -->
              <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                  <!-- Modal header -->
                  <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                      <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        <?php echo htmlspecialchars($courseInfo['COURSENAME']); ?>
                      </h3>
                      <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal">
                          <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                          </svg>
                          <span class="sr-only">Close modal</span>
                      </button>
                  </div>
                  <!-- Modal body -->
                  <div class="p-4 md:p-5 space-y-4">
                      <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                          Modules
                      </p>

                      <dl class="max-w-md text-gray-900 divide-y divide-gray-200 dark:text-white dark:divide-gray-700">
                        <!-- Display Modules -->
                         <?php
                            foreach ($modules as $module) {
                                echo '<div class="flex flex-col pb-3">';
                                echo '<dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">' . htmlspecialchars($module['MODULENAME']) . '</dt>';
                                echo '<dd class="text-lg font-semibold">' . htmlspecialchars($module['DESCRIPTION']) . '</dd>';
                                echo '</div>';
                            }
                         ?>
                    </dl>

                  </div>
                  <!-- Modal footer -->
                  <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                      <button data-modal-hide="default-modal" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">I accept</button>
                      <button data-modal-hide="default-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Decline</button>
                  </div>
              </div>
          </div>
      </div>
      <?php endif; ?>
  
      <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <?php
          $result = mysqli_query($connection, "SELECT * FROM COURSES");

          while($row = mysqli_fetch_assoc($result)) {
            echo '
            <div class="max-w-sm bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 m-4">
              <a href="#">
                <img class="rounded-t-lg" src="https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=900&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8Y29tcHV0ZXJ8ZW58MHx8MHx8fDA%3D" alt="" />
              </a>
              <div class="p-5">
                <a href="#">
                  <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">' . htmlspecialchars($row['COURSENAME']) . '</h5>
                </a>
                <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">' . htmlspecialchars($row['CATEGORY']) . '</p>
                <a href="" data-course-id="' . htmlspecialchars($row['ID']) .'" data-course-name="'. htmlspecialchars($row['COURSENAME']).'" class="view-course-btn inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                  View Modules
                  <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                  </svg>
                </a>
              </div>
            </div>';
          }

          mysqli_free_result($result);
          mysqli_close($connection);
        ?>
      </div>
    </section>

  </main>

  <footer class="bg-gray-900 text-white py-4">
    <div class="container mx-auto text-center">
      <p>&copy; 2025 CSD3156 E-Learning Team 8. All rights reserved.</p>
    </div>

  <script>
  document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const courseId = urlParams.get("course");

    if (courseId) {
      const modalElement = document.getElementById('default-modal');
      if (modalElement) {
        const modal = new Modal(modalElement);
        modal.show();
      }
    }
  });

  document.querySelectorAll('.view-course-btn').forEach(btn => {
  btn.addEventListener('click', e => {
    e.preventDefault();
    const id = btn.getAttribute('data-course-id');
    const name = btn.getAttribute('data-course-name');


    // Update modal content dynamically
    document.querySelector('#default-modal h3').innerText = name;

    // Show modal
    const modal = new Modal(document.getElementById('default-modal'));
    modal.show();
  });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

</body>
</html>
