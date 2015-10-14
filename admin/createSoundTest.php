<?php 
    session_start();
    require_once ("../config/global.php");
    require_once ($header);

    $tests = decodeJSON ($soundTests);
    $error = "";
    
    // redirectToLogin();
    
    if (!empty($_POST['chooseType'])) {
        $_SESSION['type'] = $_POST['type'];
        $_SESSION['trials'] = $_POST['type'] == 'practice' ? 20 : 120;
        $_SESSION['blocks'] = $_POST['type'] == 'practice' ? 1 : 4;
    } elseif (isset($_POST["block"])) {
        $json = decodeJSON($soundTests);
        
        $test = $json[$_SESSION["type"]];;
        $test["Date"] = date("m-d-y h:i:s a");
        $c = (($_POST["block"]-1) * $_SESSION["trials"]) + 1;
        $trials = array ();
        $k = 0;
        
        for ($i=1; $i<=$_SESSION['trials']; $i++) {
            $image = $_POST['image'.$i];
            $trials["$i"] = array (
                "image" => $image,
                "tone" => $_POST['tone'][$k++]
            );
            $test["Right Answers"]["$c"] = $_POST['correct'.$i];
            $c++;
        }
        
        $test["Block"][$_POST['block']] = $trials;
        $json[$_SESSION['type']] = $test;
        
        encodeJSON ($soundTests, $json);
        $error = "Test Created!";
        $count++; 
    }
        
    $block = empty($_POST['blockChosen']) ? 1 : $_POST['blockChosen'];
    $currTest = $tests[$_SESSION['type']];
    backNavigation ();
?>

<h1>Generate Test 1</h1>

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
                    </select>
                </td>
            </tr>
        </table>
        <input type="submit" name="chooseBlock" value="Continue">
    </form>
    
<?php else: ?>

    <h2>Block <?php echo $block; ?></h2>
    
    <!-- Generate Test -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
        <input type="hidden" name="block" value="<?php echo $block; ?>">
        
        <!-- For Each Trial -->
        <?php 
            for ($i=0; $i < $_SESSION['trials']; $i++) : 
                $num = $i+1;
                $img = (empty($currTest) or empty($currTest["Block"]["$block"]["$num"]["image"])) ? "left.png" : $currTest["Block"]["$block"]["$num"]["image"];
                $tone = (empty($currTest) or empty($currTest["Block"]["$block"]["$num"]["tone"])) ? "" : $currTest["Block"]["$block"]["$num"]["tone"];
                $c = (($block-1) * $_SESSION["trials"]) + 1;
                $rightAnswer = (empty($currTest) or empty($currTest["Right Answers"][$c])) ? "left" : $currTest["Right Answers"][$c];
        ?>
            
                <h2>Trial <?php echo $num; ?></h2>
                
                <table class='form'>
                    <tr>
                        <!-- Image -->
                        <td><label for="image<?php echo $num; ?>">Image:</label></td>
                    </tr>
                    <tr>
                        <td><label>
                            <input id="<?php echo 'leftIMG_'.$i; ?>" required type="radio" name="image<?php echo $num; ?>" value="left.png" onchange="selectImg(<?php echo "'".$i."'"; ?>)" <?php if ($img == "left.png") echo "checked"; ?> />
                            <img class="form_img" src="<?php echo $imageURL."left.png"; ?>">
                        </label></td>
                        <td><label>
                            <input id="<?php echo 'rightIMG_'.$i; ?>" required type="radio" name="image<?php echo $num; ?>" value="right.png" onchange="selectImg(<?php echo "'".$i."'"; ?>)" <?php if ($img == "right.png") echo "checked"; ?> />
                            <img class="form_img" src="<?php echo $imageURL."right.png"; ?>">
                        </label></td>
                    <tr>
                    <tr>
                        <!-- Tone -->
                        <td><label for="tone[]">Tone Delay in ms:</label></td>
                        <td><select id="select_<?php echo $i; ?>" name="tone[]" onchange="selectNone(<?php echo "'".$i."'"; ?>)">
                            <option value="" <?php if ($tone == "") echo 'selected'; ?>>No Tone</option>
                            <option value="125" <?php if ($tone == "125") echo 'selected'; ?>>125ms</option>
                            <option value="200" <?php if ($tone == "200") echo 'selected'; ?>>200ms</option>
                        </select>
                        </td>
                    </tr>
                    <tr>
                        <!-- Correct Answer -->
                        <td><label for="correct; ?>">Correct Answer:</label></td>
                        <td><input id="left_<?php echo $i; ?>" required type="radio" name="correct<?php echo $num; ?>" value="left" <?php if ($img == "left.png" and $tone == "") echo "checked"; ?>>Left</input></td>
                        <td><input id="right_<?php echo $i; ?>" required type="radio" name="correct<?php echo $num; ?>" value="right" <?php if ($img == "right.png" and $tone == "") echo "checked"; ?>>Right</input></td>
                        <td><input id="none_<?php echo $i; ?>" required type="radio" name="correct<?php echo $num; ?>" value="no response" <?php if ($tone != "") echo "checked"; ?>>None</input></td>
                    </tr>
                </table>
                <br>
        <?php 
            endfor; 
        ?>
                
        <!-- Save Button -->
        <input type="submit" name="submit" value="Save">
    </form>
<?php 
    endif; 
    require_once ($footer);
?>

<script type="text/javascript">
    function selectNone (i) {
        var value = document.getElementById("select_"+i).value;
        if (value != "") {
            document.getElementById("none_"+i).checked = true;
        } else  {
            document.getElementById("none_"+i).checked = false;
        }
    }
    
    function selectImg (i) {
        var left = document.getElementById("leftIMG_"+i);
        if (left.checked) {
            document.getElementById("left_"+i).checked = true;
            document.getElementById("right_"+i).checked = false;
        } else {
            document.getElementById("left_"+i).checked = false;
            document.getElementById("right_"+i).checked = true;
        }
    }
</script>


<br>
<br>