<?php
    session_start();
    require_once ("../config/global.php");
    require_once ($header);
    $error = "";
    
    if (!empty($_POST['login'])){
        //Form Validation
        if ($_POST["user"] == "aaron" and $_POST["pswd"] == "flower1") {
            $_SESSION['loggedIn'] = true;
        } else {
            $error = "Error - username or password incorrect.";
        }
    }

    if (isset($_GET['logout'])) {
        $_SESSION['loggedIn'] = NULL;
    }

    if (empty($_SESSION['loggedIn'])): ?>
    
    <!-- If Error -->
    <?php if (!empty($error)) : ?>
        <h3><?php echo $error; ?></h3>
    <?php endif; ?>
    
    <!-- Login Form -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h2>Please Login to Continue</h2>
        <table>
            <tr>
                <td><label for="user">Username</label></td>
                <td><input required type="text" name="user"></td>
            </tr>
            <tr>
                <td><label for="pswd">Password</label></td>
                <td><input type="password" name="pswd"></td>
            </tr>
        </table>
        <br>
        <input type="hidden" name="redirect" value="<?php echo $_GET['r']; ?>">
        <input type="submit" name="login" value="Login">
    </form>

<?php else: ?>    
    <div class='jumobtron'>
        <h1>Admin</h1>
    </div>
    
<!--    <h2>Upload Images</h2>
    <a class="nav" href="<?php echo $subdir; ?>admin/upload.php">Upload Images</a>
    
    <hr>-->
    
    
    <h2>Test 1</h2>
    <a class="nav" href="<?php echo $subdir; ?>admin/viewSoundResults.php">View Results</a>
    <a class="nav" href="<?php echo $subdir; ?>admin/viewSoundTests.php">View Tests</a>
    <a class="nav" href="<?php echo $subdir; ?>admin/createSoundTest.php">Create Test</a>
    
    <hr>
        
    <h2>Test 2</h2>
    <a class="nav" href="<?php echo $subdir; ?>admin/viewImageResults.php">View Results</a>
    <a class="nav" href="<?php echo $subdir; ?>admin/viewImageTests.php">View Tests</a>
    <a class="nav" href="<?php echo $subdir; ?>admin/createImageTest.php">Create Test</a>
    
    <hr>
    
    <h2>Logout</h2>
    <a class="nav" href="admin.php?logout" target="_self">Logout</a>
    
<?php 
    endif;
    require_once ($footer);
?>