<?php include($_SERVER['DOCUMENT_ROOT'] ."/utilities/process/admin_security.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>HTML5 Casino - Panel de Control</title>

<!-- Bootstrap Core CSS -->
<link href="../utilities/css/admin/template/bootstrap.min.css" rel="stylesheet">

<!-- MetisMenu CSS -->
<link href="../utilities/css/admin/template/metisMenu.min.css" rel="stylesheet">

<!-- Timeline CSS -->
<link href="../utilities/css/admin/template/timeline.css" rel="stylesheet">

<!-- Custom CSS -->
<link href="../utilities/css/admin/template/startmin.css" rel="stylesheet">

<!-- Morris Charts CSS -->
<link href="../utilities/css/admin/template/morris.css" rel="stylesheet">

<!-- Custom Fonts -->
<link href="../utilities/css/admin/template/font-awesome.min.css" rel="stylesheet" type="text/css">

<!-- DataTables CSS -->
<link href="../utilities/css/admin/template/dataTables/dataTables.bootstrap.css" rel="stylesheet">

<!-- DataTables Responsive CSS -->
<link href="../utilities/css/admin/template/dataTables/dataTables.responsive.css" rel="stylesheet">

<!-- Custom Fonts -->
<link href="../utilities/css/admin/template/font-awesome.min.css" rel="stylesheet" type="text/css">

<!-- RcZun Styles -->
<link href="../utilities/css/admin/template/custome.css" rel="stylesheet" type="text/css">

<?php include($_SERVER['DOCUMENT_ROOT'] ."/utilities/header_includes.php"); ?>

</head>

<body itemscope="" itemtype="http://schema.org/WebApplication">

<div id="wrapper">

    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="navbar-header">
            <a class="navbar-brand" href="dash.php"><img src="../utilities/images/admin/logo.png" width="200" height="30" alt="HTML5 Casino" /></a>
        </div>

        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>

        <!-- Top Navigation: Left Menu -->
        <?php /*?><ul class="nav navbar-nav navbar-left navbar-top-links">
            <li><a href="#"><i class="fa fa-home fa-fw"></i> Website</a></li>
        </ul><?php */?>

        <!-- Top Navigation: Right Menu -->
        <ul class="nav navbar-right navbar-top-links">
            <?php /*?><li class="dropdown navbar-inverse">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-bell fa-fw"></i> <b class="caret"></b>
                </a>
                <ul class="dropdown-menu dropdown-alerts">
                    <li>
                        <a href="#">
                            <div>
                                <i class="fa fa-comment fa-fw"></i> 1 Orden Pendiente
                                <span class="pull-right text-muted small">Hace 4 minutos</span>
                            </div>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a class="text-center" href="#">
                            <strong>Ver todas las ordenes</strong>
                            <i class="fa fa-angle-right"></i>
                        </a>
                    </li>
                </ul>
            </li><?php */?>
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-user fa-fw"></i> <? echo $_admin ->vars["name"] ?> <b class="caret"></b>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <?php /*?><li><a href="#"><i class="fa fa-user fa-fw"></i> My Profile</a>
                    </li><?php */?>
                    <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
                    </li>
                    <li class="divider"></li>
                    <li><a href="../utilities/process/logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                    </li>
                </ul>
            </li>
        </ul>

        <!-- Sidebar -->
        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">

                <ul class="nav" id="side-menu">
                     
                    
                    <? include("menu_a.php"); ?>
                    
                </ul>

            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div id="page-wrapper">
        <div class="container-fluid">
        

            

        