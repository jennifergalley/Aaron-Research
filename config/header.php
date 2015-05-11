<?php 
    require_once ("../config/global.php"); 
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <!-- Title -->
        <title>Test Center</title>
    
        <!-- Bootstrap -->
        <script src="/js/respond.js"></script>
        <link rel="stylesheet" href="/css/bootstrap.min.css">
    
        <!-- My Stylesheet -->
        <link rel="stylesheet" type="text/css" href="/css/style.css">
        
    </head>
    
    <body>
        <div class='container'> <!-- start container -->

            <!-- navigation -->
            <div class='row'>
                <nav class='navbar navbar-default navbar-fixed-top'>
                    <!-- Mobile Display -->
                    <div class='navbar-header'>
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#collapse">
                            <span class='sr-only'>Toggle Navigation</span> <!-- for screen reader - accessibility -->
                            <span class='icon-bar'></span>
                            <span class='icon-bar'></span>
                            <span class='icon-bar'></span>
                        </button>
                    </div>
        
                    <!-- Nav content -->
                    <div class='collapse navbar-collapse' id='collapse'>
                        <ul class='nav navbar-nav'>
                            <!-- <li><a class="nav" href="<?php echo $subdir; ?>index.php">Home</a></li> -->
                            <li><a class="nav" href="/test/soundTest.php">Test 1</a></li>
                            <li><a class="nav" href="/test/imageTest.php">Test 2</a></li>
                            <li><a class="nav" href="/test/soundTest.php?all=true">All Tests</a></li>
                            <li><a class="nav" href="/admin/admin.php">Admin</a></li>
                        </ul>
                    </div>
                </nav>
            </div>
        