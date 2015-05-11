<?php 
    session_start();
    require_once ("../config/global.php");
    require_once ($header);

    $tests = decodeJSON ($soundTests);
    $error = "";
    
    redirectToLogin();
    
    if (!empty($_POST['continue'])) {
        $_SESSION['type'] = $_POST['type'];
        $_SESSION['trials'] = $_POST['type'] == 'practice' ? 20 : 120;
        $_SESSION['blocks'] = $_POST['type'] == 'practice' ? 1 : 4;
    } elseif (!empty($_POST['submit'])) {
        $json = decodeJSON($soundTests);
        $test = array ();
        $test["Type"] = $_SESSION['type'];
        $test["Date"] = date("m-d-y h:i:s a");
        $blocks = array ();
        $trials = array ();
        $correct = array ();
        $b = 1; $k = 0;
        for ($j=0; $j<$_SESSION['blocks']; $j++) {
            $trials = array ();
            for ($i=0; $i<$_SESSION['trials']; $i++) {
                $index = $i + 1;
                $image = $_POST['image_'.$j."_".$i];
                $trials["$index"] = array (
                    "image" => $image,
                    "tone" => $_POST['tone'][$k++]
                );
                $correct["$index"] = $_POST['correct_'.$j."_".$i];
            }
            $blocks[$b++] = $trials;
        }
        $test["Block"] = $blocks;
        $test["Right Answers"] = $correct;
        $json[] = $test;
        encodeJSON ($soundTests, $json);
        $error = "Test Created!";
        $count++; 
    
    }
    
    backNavigation ();
?>

<h1>Generate Sound Test</h1>

<?php if (empty($_POST['continue']) or !empty($error)): 
    displayError(); ?>
    <!-- Test Block & Number Trials -->
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
<!--            <tr>
                <td><label for="blocks">Number of blocks:</label></td>
                <td><input required type="number" name="blocks"></td>
            </tr>
            <tr>
                <td><label for="trials">Number of trials per block:</label></td>
                <td><input required type="number" name="trials"></td>
            </tr>-->
        </table>
        <input type="submit" name="continue" value="Continue">
    </form>
<?php else: ?>
    <!-- Generate Test Form -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
        <?php for ($k=0; $k < $_SESSION['blocks']; $k++) : ?>
            <h2>Block <?php echo $k+1; ?></h2>
        <?php for ($i=0; $i < $_SESSION['trials']; $i++) : ?>
        <h2>Trial <?php echo $i+1; ?></h2>
        <table class='form'>
            <tr>
                <!-- Image -->
                <td><label for="<?php echo 'image_'.$k.'_'.$i; ?>">Image:</label></td>
            </tr>
            <tr>
                <?php $arr = array("left.png", "right.png");
                    foreach ($arr as $p) : 
                        $pic = $imageURL.$p; ?>
                <td><label><input id="<?php echo $p == "left.png" ? 'leftIMG' : 'rightIMG'; echo '_'.$k.'_'.$i; ?>" required type="radio" name="<?php echo 'image_'.$k.'_'.$i; ?>" value="<?php echo $p; ?>" onchange="selectImg(<?php echo "'".$k.'_'.$i."'"; ?>)"><img class="form_img" src="<?php echo $pic; ?>"></label></td>
                <?php endforeach; ?>
            <tr>
            <tr>
                <!-- Tone -->
                <td><label for="tone[]">Tone Delay in ms:</label></td>
                <td><select id="select_<?php echo $k.'_'.$i; ?>" name="tone[]" onchange="selectNone(<?php echo "'".$k.'_'.$i."'"; ?>)">
                    <option value="">No Tone</option>
                    <option value="125">125ms</option>
                    <option value="200">200ms</option>
                </select>
                </td>
            </tr>
            <tr>
                <!-- Correct Answer -->
                <td><label for="correct_<?php echo $k.'_'.$i; ?>">Correct Answer:</label></td>
                <td><input id="left_<?php echo $k.'_'.$i; ?>" required type="radio" name="correct_<?php echo $k.'_'.$i; ?>" value="left">Left</input></td>
                <td><input id="right_<?php echo $k.'_'.$i; ?>" required type="radio" name="correct_<?php echo $k.'_'.$i; ?>" value="right">Right</input></td>
                <td><input id="none_<?php echo $k.'_'.$i; ?>" required type="radio" name="correct_<?php echo $k.'_'.$i; ?>" value="no response">None</input></td>
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

