<?php
    session_start();
    
    function redirectToLogin () {
        if (empty($_SESSION['loggedIn'])) { ?>
        <script>
           redirect ("admin.php"); 
        </script>
    <?php }
    }

    function backNavigation () { ?>
        <!-- Back Navigation -->
        <a href="admin.php" target="_self" class="back">Admin &lt;&lt;</a>
    <?php }

    function displayError () { ?>
        <!-- If Error -->
        <?php if (!empty($error)) : ?>
            <h3><?php echo $error; ?></h3>
        <?php endif; 
    }

    function logout () {
        $_SESSION['loggedIn'] = NULL;
    }
    
    function thankYou () {?>
        <!-- After Test Finished -->
        <h1>Your results have been recorded. <br>Thanks for participating!</h1>
    <?php }
?>