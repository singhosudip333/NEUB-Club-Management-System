<?php
// Get the current script name
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="col-md-2 sidebar">
    <a href="./manageclub.php" class="<?php echo ($current_page == 'manageclub.php') ? 'active' : ''; ?>">Manage Clubs</a>
    <a href="./add_commiteeadmin.php" class="<?php echo ($current_page == 'add_commiteeadmin.php') ? 'active' : ''; ?>">Add Club Admin</a>
    <a href="./manage_post.php" class="<?php echo ($current_page == 'manage_post.php') ? 'active' : ''; ?>">Manage Posts</a>
    <a href="./manage_event.php" class="<?php echo ($current_page == 'manage_event.php') ? 'active' : ''; ?>">Manage Events</a>
    <a href="./view_report.php" class="<?php echo ($current_page == 'view_report.php') ? 'active' : ''; ?>">View Reports</a>
    <a href="./manage_user.php" class="<?php echo ($current_page == 'manage_user.php') ? 'active' : ''; ?>">User Management</a>
</div> 