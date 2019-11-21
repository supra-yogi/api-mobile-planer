<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MPlanner - Admin</title>

    <!--Bootstrap  -->
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>assets/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>assets/node_modules/font-awesome/css/font-awesome.min.css">
    <!--Custom Stylesheet  -->
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>assets/css/admin.css">
    <!--Plugins  -->
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>assets/node_modules/datatables/dataTables.bootstrap.min.css">

    <!-- Script -->
	<script src="<?php echo URL_BASE; ?>assets/node_modules/jquery/dist/jquery.min.js"></script>
	<script src="<?php echo URL_BASE; ?>assets/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="<?php echo URL_BASE; ?>assets/node_modules/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo URL_BASE; ?>assets/node_modules/datatables/dataTables.bootstrap4.min.js"></script>
</head>
<body>
    <header class="main-header dark-bg">

       <!-- Logo -->
       <a href="<?php echo URL_BASE;?>Admin/dashboard" class="logo dark-bg">
            <span class="logo-lg">PANEL ADMIN</span>
        </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" role="button"> <span class="sr-only">Toggle navigation</span> </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="hidden-xs"><?php echo $this->session->userdata('username'); ?></span> <i class="caret"></i></a>
                    <ul class="dropdown-menu animated scaleInDownLeft">
                        <li><a href="<?php echo URL_BASE;?>admin/myprofile""><i class="fa fa-user-circle-o"></i> My Profile</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="<?php echo URL_BASE;?>admin/logout"><i class="fa fa-sign-out"></i> Logout</a></li>
                    </ul>
                </li> 
            </ul>
        </div>
    </nav>
</header>
