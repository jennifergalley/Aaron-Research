<?php 
    session_start();
    require_once ("../config/global.php");
    if (empty($_POST["version"])) {
        require_once ($header);
        logout ();
    } else { ?>
        <!-- My Stylesheet -->
        <link rel="stylesheet" type="text/css" href="<?php echo $subdir.'css/style.css';?>">
    <?php }

    if (empty($_POST["name"]) and empty($_GET['done'])) : ?>
    <div id="start">
<?php 
    $test = decodeJSON ($soundTests);    
    if (empty($test)) :
        echo "<h2>Error - no tests available.</h2>";
    else : ?>
    <!-- Name Submit and Start Test -->
    <h1>Test 1</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); if (!empty($_GET['all'])) echo "?all=true"; ?>">
        <table class='form'>
            <tr>
                <td><label for="version">Test Version:</label></td>
                <td><select required name="version">
                    <?php foreach ($test as $num => $test) : ?>
                        <option value="<?php echo $num; ?>"><?php echo $num; ?></option>
                    <?php endforeach; ?>
                </select></td>
            </tr>
            <tr>
                <td><label for="name">Please enter your name:</label></td>
                <td><input required type="text" name="name"></td>
            </tr>
        </table>
        <br>
        <input type="submit" name="start" value="Start">
    </form>
    </div>
<?php endif;

    elseif (isset($_GET['done'])) : 
        thankYou ();
    endif; ?>

<!-- Populate Test -->
<?php if (!empty($_POST["version"])) : 
    $test = decodeJSON ($soundTests);  
    $test = $test[$_POST['version']];
?>

<!-- Tone Element -->
<audio id='tone' src="tone.wav" preload="auto"></audio>

<!-- Test -->

<!-- Base Image - dot -->
<div id="base">
    <img class="test" src="<?php echo $imageURL.'dot.jpg';?>">
</div>

<!-- Pause -->
<div id="pause" style="display:none">
    <h1>Press enter to continue</h1>
</div>

<?php 
    //Populate Questions
    foreach ($test["Block"] as $b => $block) :
        $i = 1;
        foreach ($block as $question) : ?>
    
        <!-- Image -->
        <div id="<?php echo $b.".".$i++; ?>" style="display:none">
            <img class="test" src="<?php echo $imageURL.$question['image'];?>">
        </div>
    <?php endforeach; ?>
<?php endforeach; ?>

<!-- Specialized Variables -->
<script type="text/javascript">
    var blocks = <?php echo count($test["Block"]); ?>;
    var numberQuestions = [<?php 
        $arr = "";
        foreach ($test["Block"] as $block) {
            $arr .= count($block).",";
        }
        $arr = rtrim ($arr, ",");
        echo $arr;
    ?>];
    var participant = "<?php echo $_POST['name']; ?>";
    var testVersion = "<?php echo $_POST['version']; ?>";
    var tones = [ <?php 
        $j = 1;
        foreach ($test["Block"] as $b => $block) {
            $i = 1;
            foreach ($block as $question) {
                echo '"'.$question['tone'].'"';
                if (array_key_exists($i+1, $block)) echo ", ";
                $i++;
            }
            if (array_key_exists ($j+1, $test["Block"])) echo ", ";
            $j++;
        }
    ?> ];
    var all = <?php echo !empty($_GET['all']); ?>;
</script>

<!-- Javascript Functions -->
<script type="text/javascript" src="<?php echo $subdir.'js/functions.js';?>"></script>
<script type="text/javascript" src="<?php echo $subdir.'js/sound_test.js';?>"></script>

<?php 
    endif; 
    if (empty($_POST["version"])) {
        require_once ($footer);
    }
?>
