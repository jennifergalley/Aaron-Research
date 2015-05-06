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
        $test["Switch"]["after"] = $_POST['switch_after'];
        $test["Switch"]["text"] = $_POST['switch_text'];
        $test["Switch"]["duration"] = $_POST['switch_duration'];
        $blocks = array ();
        $correct = array ();
        $b = 1; $c = 1;
        for ($j=0; $j<$_SESSION['blocks']; $j++) {
            $trials = array ();
            for ($i=0; $i<$_SESSION['trials']; $i++) {
                $index = $i+1;
                
                $trials["$index"]["left"]["character"] = $_POST['first_'.$j."_".$i];
                $trials["$index"]["left"]["color"] = $_POST['first_'.$j."_".$i."_color"];
                $trials["$index"]["right"]["character"] = $_POST['second_'.$j."_".$i];
                $trials["$index"]["right"]["color"] = $_POST['second_'.$j."_".$i."_color"];
                
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
                <td><input required type="number" name="blocks" value="6"></td>
            </tr>
            <tr>
                <td><label for="questions">Number of trials per block:</label></td>
                <td><input required type="number" name="trials" value="60"></td>
            </tr>
        </table>
        <input type="submit" name="continue" value="Continue">
    </form>
<?php else: ?>
    <!-- Generate Test Form -->
    <form method="post" action="<?php echo $upload_url; ?>" enctype="multipart/form-data">
        <?php for ($k=0; $k < $_SESSION['blocks']; $k++) : ?>
            <h2>Block <?php echo $k+1; ?></h2>
            <table>
                <tr>
                    <!-- Switch After -->
                    <td><label for="switch_after">Switch After: </label></td>
                    <td><input name="switch_after" value="40" type="number"></td>
                </tr>
                <tr>
                    <!-- Switch Text -->
                    <td><label for="switch_text">Switch Text: </label></td>
                    <td><input name="switch_text" value="The new target is" type="text"></td>
                </tr>
                <tr>
                    <!-- Switch Duration -->
                    <td><label for="switch_duration">Switch Duration (ms): </label></td>
                    <td><input name="switch_duration" value="3000" type="number"></td>
                </tr>
            </table>
        <?php for ($i=0; $i < $_SESSION['trials']; $i++) : ?>
            <h3>Trial <?php echo $i+1; ?></h3>
            <table class='form'>
                <tr>
                    <!-- First Character -->
                    <td><label for="<?php echo 'first_'.$k.'_'.$i; ?>">First Character:</label></td>
                    <td><input name="<?php echo 'first_'.$k.'_'.$i; ?>" type="text"></td>
                </tr>
                <tr>
                    <!-- First Color -->
                    <td><label for="<?php echo 'first_'.$k.'_'.$i.'_color'; ?>">First Character Color:</label></td>
                    <td><select name="<?php echo 'first_'.$k.'_'.$i.'_color'; ?>">
                            <option value="purple">Purple</option>
                            <option value="white">White</option>
                            <option value="green">Green</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <!-- Second Character -->
                    <td><label for="<?php echo 'second_'.$k.'_'.$i; ?>">Second Character:</label></td>
                    <td><input name="<?php echo 'second_'.$k.'_'.$i; ?>" type="text"></td>
                </tr>
                <tr>
                    <!-- Second Color -->
                    <td><label for="<?php echo 'second_'.$k.'_'.$i.'_color'; ?>">Second Character Color:</label></td>
                    <td>
                         <select name="<?php echo 'second_'.$k.'_'.$i.'_color'; ?>">
                            <option value="purple">Purple</option>
                            <option value="white">White</option>
                            <option value="green">Green</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <!-- Correct Answer -->
                    <td><label for="correct<?php echo $i; ?>">Correct Answer:</label></td>
                    <td>
                        <select name="correct_<?php echo $k.'_'.$i ?>">
                            <option value="left">Left</option>
                            <option value="right">Right</option>
                            <!--<option value="left">Even</option>
                            <option value="right">Odd</option>
                            <option value="left">Consonant</option>
                            <option value="right">Vowel</option>-->
                        </select>
                    </td>
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

