<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/auth.php';
requireLogin();
$currentAdmin = getCurrentAdmin();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin Panel'; ?> - Nexora Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>NEXORA</h2>
                <p>Admin Panel</p>
            </div>
            
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="contacts.php" class="nav-item <?php echo $currentPage === 'contacts' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i>
                    <span>Contact Messages</span>
                </a>
                <a href="subscribers.php" class="nav-item <?php echo $currentPage === 'subscribers' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Newsletter Subscribers</span>
                </a>
                <a href="settings.php" class="nav-item <?php echo $currentPage === 'settings' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <a href="logout.php" class="nav-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <div class="admin-info">
                    <i class="fas fa-user-circle"></i>
                    <div>
                        <strong><?php echo htmlspecialchars($currentAdmin['full_name']); ?></strong>
                        <span><?php echo htmlspecialchars($currentAdmin['role']); ?></span>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
                </div>
                <div class="topbar-right">
                    <a href="../index.html" target="_blank" class="btn-view-site">
                        <i class="fas fa-external-link-alt"></i> View Website
                    </a>
                    <div class="admin-dropdown">
                        <button class="admin-btn">
                            <i class="fas fa-user"></i>
                            <?php echo htmlspecialchars($currentAdmin['username']); ?>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <div class="page-content">
                <?php
                $flashMessage = getFlashMessage();
                if ($flashMessage):
                ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($flashMessage); ?>
                </div>
                <?php endif; ?>
