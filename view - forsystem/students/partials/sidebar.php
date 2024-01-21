<?php
$authUser = $_SESSION["user"];

$role = $authUser["role"];
?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="../students/default.php" class="brand-link">
        <img src="../../public/images/inovanav.svg" alt="InovaClass Logo" class="brand-image img-circle" style="">
        <span class="brand-text font-weight-light">InovaClass</span>
    </a>
    
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="../../public/images/user.png" class=" img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <?php
                // Adjust profile name based on role
                $profileName = $role == '0' ? "Admin" : $authUser["fullname"];
                echo "<a href='#' class='d-block admin-toggle'>{$profileName}</a>"
                ?>

                <ul class="admin-dropdown mt-2" style="display: none;">
                    <li>
                        <a href="../../../logout.php">
                            Logout
                            <i class="fa-solid fa-right-from-bracket ml-2"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- SidebarSearch Form -->

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item" id="dashboard-link">
                    <a href="../students/dashboard.php" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>

                <!-- Student-only section -->
                <li class="nav-item">
                    <a href="./../students/default.php" class="nav-link toggle-icon">
                        <i class="nav-icon fa-solid fa-folder"></i>
                        <p>
                            Files
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
<script>
    // Get all elements with the class 'toggle-icon'
    const toggleIcons = document.querySelectorAll('.toggle-icon');
    console.log(toggleIcons);
    // Add a click event listener to each element
    toggleIcons.forEach(function(icon) {
        icon.addEventListener('click', function() {
            // Toggle the class for the clicked element
            const iconElement = icon.querySelector('.right');
            iconElement.classList.toggle('fa-angle-left');
            iconElement.classList.toggle('fa-angle-down');

            // Toggle the 'active' class for the parent li element
            icon.closest('.nav-item').classList.toggle('active');
        });
    });

    // Add a click event handler to the "Admin" tab
    $('.admin-toggle').on('click', function(e) {
        e.preventDefault(); // Prevent the default behavior of the link
        // Toggle the visibility of the dropdown
        $('.admin-dropdown').slideToggle();
    });
</script>
