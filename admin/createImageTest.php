<?php 
    session_start();
    require_once ("../config/global.php");
    require_once ($header);

    $tests = decodeJSON ($imageTests);
    $error = "";
    
    redirectToLogin();
    
    if (!empty($_POST['chooseType'])) {
        $_SESSION['type'] = $_POST['type'];
        $_SESSION['trials'] = $_POST['type'] == 'practice' ? 20 : 60;
        $_SESSION['blocks'] = $_POST['type'] == 'practice' ? 1 : 6;
    } elseif (isset($_POST["block"])) {
        $json = decodeJSON($imageTests);
        
        $test = $json[$_SESSION["type"]];
        $test["Date"] = date("m-d-y h:i:s a");
        $c = (($_POST["block"]-1) * $_SESSION["trials"]) + 1;
        $trials = array ();
        
        $trials["Switch"]["after"] = $_SESSION['type'] == 'practice' ? 13 : 40;
        $trials["Switch"]["text"] = $_POST['switch_text'];
        $trials["Switch"]["duration"] = 3000;
        
        $trials["InitialTarget"]["text"] = $_POST['initial_target'];
        $trials["InitialTarget"]["duration"] = 3000;
        
        for ($i=0; $i<$_SESSION['trials']; $i++) {
            $index = $i+1;
            
            $trials["$index"]["left"]["character"] = $_POST['first'][$i];
            $trials["$index"]["left"]["color"] = $_POST['first_color'][$i];
            
            $trials["$index"]["right"]["character"] = $_POST['second'][$i];
            $trials["$index"]["right"]["color"] = $_POST['second_color'][$i];
            
            $test["Right Answers"]["$c"] = $_POST['correct'][$i];
            $c++;
        }
        
        $test["Block"][$_POST['block']] = $trials;
        $json[$_SESSION['type']] = $test;
        
        encodeJSON ($imageTests, $json);
        $error = "Test Created!";
        $count++; 
    }
    
    $block = empty($_POST['blockChosen']) ? 1 : $_POST['blockChosen'];
    $currTest = $tests[$_SESSION['type']];
    backNavigation ();
?>

<h1>Generate Test 2</h1>

<?php if ((empty($_POST['chooseType']) and empty($_POST['chooseBlock'])) or !empty($error)): 
    displayError(); 
?>
    
    <!-- Type of Test -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <table class='form'>
            <tr>
                <td><label for="type">Type of test:</label></td>
                <td>
                    <select name="type">
                        <option value="practice">Practice</option>
                        <option value="test">Test</option>
                    </select>
                </td>
            </tr>
        </table>
        <input type="submit" name="chooseType" value="Continue">
    </form>
    
<?php elseif (!empty($_POST['chooseType']) and $_POST['type'] == 'test') : ?>
    
    <!-- Block number -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <table class='form'>
            <tr>
                <td><label for="blockChosen">Block:</label></td>
                <td>
                    <select name="blockChosen">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                    </select>
                </td>
            </tr>
        </table>
        <input type="submit" name="chooseBlock" value="Continue">
    </form>
    
<?php else: ?>

    <h2>Block <?php echo $block; ?></h2>
    
    <!-- Generate Test -->
    <form method="post" action="<?php echo $upload_url; ?>" enctype="multipart/form-data">
        <input type="hidden" name="block" value="<?php echo $block; ?>">
        <table>
            <tr>
                <!-- Initial Target -->
                <td><label for="initial_target">Initial Target: </label></td>
                <td><input name="initial_target" value="<?php echo (empty($currTest) or empty($currTest["Block"][$block]["InitialTarget"]["text"])) ? 'The first target is' : $currTest["Block"][$block]["InitialTarget"]["text"]; ?>" type="text"></td>
            </tr>
            <tr>
                <!-- Switch Text -->
                <td><label for="switch_text">Switch Text: </label></td>
                <td><input name="switch_text" value="<?php echo (empty($currTest) or empty($currTest["Block"][$block]["Switch"]["text"])) ? 'The new target is' : $currTest["Block"][$block]["Switch"]["text"]; ?>" type="text"></td>
            </tr>
        </table>
        
    <!-- For Each Trial -->
    <?php for ($i=1; $i <= $_SESSION['trials']; $i++) : 
            $firstChar = (empty($currTest) or empty($currTest["Block"][$block][$i]["left"]["character"])) ? "" : $currTest["Block"][$block][$i]["left"]["character"];
            $secondChar = (empty($currTest) or empty($currTest["Block"][$block][$i]["right"]["character"])) ? "" : $currTest["Block"][$block][$i]["right"]["character"];
            $firstColor = (empty($currTest) or empty($currTest["Block"][$block][$i]["left"]["color"])) ? "" : $currTest["Block"][$block][$i]["left"]["color"];
            $secondColor = (empty($currTest) or empty($currTest["Block"][$block][$i]["right"]["color"])) ? "" : $currTest["Block"][$block][$i]["right"]["color"];
            $qCount = (($block-1) * $_SESSION["trials"]) + $i;
            $rightAnswer = (empty($currTest) or empty($currTest["Right Answers"][$qCount])) ? "" : $currTest["Right Answers"][$qCount];
        ?>
        <h3>Trial <?php echo $i; ?></h3>
        <table class='form'>
            <tr>
                <!-- First Character -->
                <td><label for="first[]">First Character:</label></td>
                <td>
                    <select name="first[]">
                        <option value="A" <?php if ($firstChar == "A") echo "selected"; ?>>A</option>
                        <option value="E" <?php if ($firstChar == "E") echo "selected"; ?>>E</option>
                        <option value="O" <?php if ($firstChar == "O") echo "selected"; ?>>O</option>
                        <option value="U" <?php if ($firstChar == "U") echo "selected"; ?>>U</option>
                        <option value="K" <?php if ($firstChar == "K") echo "selected"; ?>>K</option>
                        <option value="M" <?php if ($firstChar == "M") echo "selected"; ?>>M</option>
                        <option value="R" <?php if ($firstChar == "R") echo "selected"; ?>>R</option>
                        <option value="S" <?php if ($firstChar == "S") echo "selected"; ?>>S</option>
                        <option value="2" <?php if ($firstChar == "2") echo "selected"; ?>>2</option>
                        <option value="3" <?php if ($firstChar == "3") echo "selected"; ?>>3</option>
                        <option value="4" <?php if ($firstChar == "4") echo "selected"; ?>>4</option>
                        <option value="5" <?php if ($firstChar == "5") echo "selected"; ?>>5</option>
                        <option value="6" <?php if ($firstChar == "6") echo "selected"; ?>>6</option>
                        <option value="7" <?php if ($firstChar == "7") echo "selected"; ?>>7</option>
                        <option value="8" <?php if ($firstChar == "8") echo "selected"; ?>>8</option>
                        <option value="9" <?php if ($firstChar == "9") echo "selected"; ?>>9</option>
                    </select>
                </td>
            </tr>
            <tr>
                <!-- First Color -->
                <td><label for="first_color[]">First Character Color:</label></td>
                <td><select name="first_color[]">
                        <option value="red" <?php if ($firstColor == "red") echo "selected"; ?>>Red</option>
                        <option value="blue" <?php if ($firstColor == "blue") echo "selected"; ?>>Blue</option>
                        <option value="yellow" <?php if ($firstColor == "yellow") echo "selected"; ?>>Yellow</option>
                        <option value="DarkGreen" <?php if ($firstColor == "DarkGreen") echo "selected"; ?>>Dark Green</option>
                        <option value="purple" <?php if ($firstColor == "purple") echo "selected"; ?>>Purple</option>
                        <option value="gray" <?php if ($firstColor == "gray") echo "selected"; ?>>Gray</option>
                    </select>
                </td>
            </tr>
            <tr>
                <!-- Second Character -->
                <td><label for="second[]">Second Character:</label></td>
                <td>
                    <select name="second[]">
                        <option value="A" <?php if ($secondChar == "A") echo "selected"; ?>>A</option>
                        <option value="E" <?php if ($secondChar == "E") echo "selected"; ?>>E</option>
                        <option value="O" <?php if ($secondChar == "O") echo "selected"; ?>>O</option>
                        <option value="U" <?php if ($secondChar == "U") echo "selected"; ?>>U</option>
                        <option value="K" <?php if ($secondChar == "K") echo "selected"; ?>>K</option>
                        <option value="M" <?php if ($secondChar == "M") echo "selected"; ?>>M</option>
                        <option value="R" <?php if ($secondChar == "R") echo "selected"; ?>>R</option>
                        <option value="S" <?php if ($secondChar == "S") echo "selected"; ?>>S</option>
                        <option value="2" <?php if ($secondChar == "2") echo "selected"; ?>>2</option>
                        <option value="3" <?php if ($secondChar == "3") echo "selected"; ?>>3</option>
                        <option value="4" <?php if ($secondChar == "4") echo "selected"; ?>>4</option>
                        <option value="5" <?php if ($secondChar == "5") echo "selected"; ?>>5</option>
                        <option value="6" <?php if ($secondChar == "6") echo "selected"; ?>>6</option>
                        <option value="7" <?php if ($secondChar == "7") echo "selected"; ?>>7</option>
                        <option value="8" <?php if ($secondChar == "8") echo "selected"; ?>>8</option>
                        <option value="9" <?php if ($secondChar == "9") echo "selected"; ?>>9</option>
                    </select>
                </td>
            </tr>
            <tr>
                <!-- Second Color -->
                <td><label for="second_color[]">Second Character Color:</label></td>
                <td>
                        <select name="second_color[]">
                        <option value="red" <?php if ($secondColor == "red") echo "selected"; ?>>Red</option>
                        <option value="blue" <?php if ($secondColor == "blue") echo "selected"; ?>>Blue</option>
                        <option value="yellow" <?php if ($secondColor == "yellow") echo "selected"; ?>>Yellow</option>
                        <option value="DarkGreen" <?php if ($secondColor == "DarkGreen") echo "selected"; ?>>Dark Green</option>
                        <option value="purple" <?php if ($secondColor == "purple") echo "selected"; ?>>Purple</option>
                        <option value="gray" <?php if ($secondColor == "gray") echo "selected"; ?>>Gray</option>
                    </select>
                </td>
            </tr>
            <tr>
                <!-- Correct Answer -->
                <td><label for="correct[]">Correct Answer:</label></td>
                <td>
                    <select name="correct[]">
                        <option value="left" <?php if ($rightAnswer == "left") echo "selected"; ?>>Left</option>
                        <option value="right" <?php if ($rightAnswer == "right") echo "selected"; ?>>Right</option>
                    </select>
                </td>
            </tr>
        </table>
        
        <br>
        
        <?php endfor; ?>
    
        <!-- Save Button -->
        <input type="submit" name="submit" value="Save">
    </form>
        
<?php 
    endif; 
    
    require_once ($footer);
?>

<br>
<br>
