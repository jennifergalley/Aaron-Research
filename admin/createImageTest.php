<?php 
    session_start();
    require_once ("../config/global.php");
    require_once ($header);

    $tests = decodeJSON ($imageTests);
    $count = count ($tests); //get number of test versions already
    $error = "";
    
    redirectToLogin();
    
    if (!empty($_POST['trials'])) {
        $_SESSION['trials'] = $_POST['trials'];
        $_SESSION['blocks'] = $_POST['blocks'];
        if ($_POST['version'] > $count) {
            $_SESSION['version'] = $_POST['version'];
        } else {
            $error = "Error: That test version already exists.";
        }
    } elseif (!empty($_POST['submit'])) {
        $json = decodeJSON($imageTests);
        $test = array ();
        $test["Date"] = date("m-d-y h:i:s a");
        $blocks = array ();
        $correct = array ();
        $b = 1; $c = 1;
        for ($j=0; $j<$_SESSION['blocks']; $j++) {
            $trials = array ();
            for ($i=0; $i<$_SESSION['trials']; $i++) {
                $index = $i+1;
                $first = $_POST['first_'.$j."_".$i];
                $second = $_POST['second_'.$j."_".$i];
                $trials["$index"] = array (
                    "first" => $first,
                    "second" => $second
                );
                $correct["$c"] = $_POST['correct_'.$j."_".$i];
                $c++;
            }
            $blocks[$b++] = $trials;
        }
        $test["Block"] = $blocks;
        $test["Right Answers"] = $correct;
        $json[$_SESSION['version']] = $test;
        encodeJSON ($imageTests, $json);
        $error = "Test Created!";
        $count++; 
    }
    
    backNavigation ();
?>

<h1>Generate Image Test</h1>

<?php if (empty($_POST['trials']) or !empty($error)): 
    displayError(); ?>
    <!-- Test Block & Number Trials -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <table class='form'>
            <tr>
                <td><label for="version">Test Version (must be unique):</label></td>
                <td><input required type="number" name="version" value="<?php echo $count+1; ?>"></td>
            </tr>
            <tr>
                <td><label for="blocks">Number of blocks:</label></td>
                <td><input required type="number" name="blocks"></td>
            </tr>
            <tr>
                <td><label for="questions">Number of trials per block:</label></td>
                <td><input required type="number" name="trials"></td>
            </tr>
        </table>
        <input type="submit" name="continue" value="Continue">
    </form>
<?php else: ?>
    <!-- Generate Test Form -->
    <form method="post" action="<?php echo $upload_url; ?>" enctype="multipart/form-data">
        <?php for ($k=0; $k < $_SESSION['blocks']; $k++) : ?>
            <h2>Block <?php echo $k+1; ?></h2>
        <?php for ($i=0; $i < $_SESSION['trials']; $i++) : ?>
            <h3>Trial <?php echo $i+1; ?></h3>
            <table class='form'>
                <tr>
                    <!-- First Image -->
                    <td><label for="<?php echo 'first_'.$k.'_'.$i; ?>">First Image:</label></td>
                </tr>
                <tr>
                <?php $j = 0;
                    if ($handle = opendir('gs://aarons-tests/images/')) {
                        while (false !== ($entry = readdir($handle))) : 
                            $j++;
                            $pic = $imageURL.$entry; ?>
                    <td><label><input required type="radio" name="<?php echo 'first_'.$k.'_'.$i; ?>" value="<?php echo $entry; ?>"><img class="form_img" src="<?php echo $pic; ?>"></label></td>
                    <?php if ($j % 6 == 0) echo "</tr><tr>"; 
                        endwhile;
                        closedir($handle);
                    } ?>
                <tr>
                    <!-- Second Image -->
                    <td><label for="<?php echo 'second_'.$k.'_'.$i; ?>">Second Image:</label></td>
                </tr>
                    <?php $j = 0; 
                        if ($handle = opendir('gs://aarons-tests/images/')) {
                        while (false !== ($entry = readdir($handle))) : 
                            $j++;
                            $pic = $imageURL.$entry; ?>
                    <td><input required type="radio" name="<?php echo 'second_'.$k.'_'.$i; ?>" value="<?php echo $entry; ?>"><img class="form_img" src="<?php echo $pic; ?>"></td>
                    <?php if ($j % 6 == 0) echo "</tr><tr>";  
                        endwhile;
                        closedir($handle);
                    } ?>
                <tr>
                    <!-- Correct Answer -->
                    <td><label for="correct<?php echo $i; ?>">Correct Answer:</label></td>
                    <td><input required type="radio" name="correct_<?php echo $k.'_'.$i; ?>" value="yes">True</input></td>
                    <td><input required type="radio" name="correct_<?php echo $k.'_'.$i; ?>" value="no">False</input></td>
                </tr>
            </table>
            <br>
            <?php endfor; ?>
        <?php endfor; ?>
        <input type="submit" name="submit" value="Submit">
    </form>
<?php 
    endif; 
    require_once ($footer);
?>

