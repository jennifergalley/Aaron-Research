<?php 
    session_start();
    require_once ("../config/global.php");
    
    $instructions = [
        "Instruction page 1",
        "Instruction page 2", 
        "Instruction page 3"
    ];
    
    if (empty($_POST["name"]) and empty($_GET['n'])) {
        require_once ($header);
        logout ();
    } else { ?>
        <!-- My Stylesheet -->
        <link rel="stylesheet" type="text/css" href="<?php echo $subdir.'css/style.css';?>">
    <?php }

    if (empty($_POST["name"]) and empty($_GET['n']) and !isset($_GET['done'])) : ?>
    <div id="start">
<?php 
    $test = decodeJSON ($soundTests);    
    if (empty($test)) :
        echo "<h2>Error - no tests available.</h2>";
    else : ?>
    <!-- Name Submit and Start Test -->
    <?php if (isset($_GET["all"])) : ?>
        <h1>All Tests</h1>
    <?php else : ?>
        <h1>Test 1</h1>
    <?php endif; ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); if (isset($_GET['all'])) echo "?all"; ?>">
        <table class='form'>
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
<?php if (!empty($_POST["name"]) or !empty($_GET['n'])) : 
    $test = decodeJSON ($soundTests);  
    $name = !empty($_POST['name']) ? $_POST['name'] : $_GET['n'];
    $type = isset($_GET['test']) ? "test" : "practice";
    $test = $test[$type];
?>

<!-- Tone Element -->
<audio id='tone' src="tone.wav" preload="auto"></audio>

<!-- Test -->
<!-- Instruction -->
<?php foreach ($instructions as $index => $instr) : ?>
<div id="instructions<?php echo $index+1; ?>" style="display:none">
    <h1><?php echo $instr; ?></h1>
    <?php if ($index != 0) : ?>
        <span style="float:left;font-size:3em;">&lt;</span>
    <?php endif; ?>
    <span style="float:right;font-size:3em;">&gt;</span>
</div>
<?php endforeach; ?>

<!-- Start Over? -->
<div id="startOver" style="display:none">
    <h1>Press space bar to replay the instructions and practice round, or enter to continue.</h1>
</div>

<!-- Base Image - dot -->
<div id="base" style="display:none">
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
    var participant = "<?php echo $name; ?>";
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
    var all = <?php echo isset($_GET['all']) ? 1 : 0; ?>;
    var numInstructions = <?php echo count($instructions); ?>;
    var typeTest = "<?php echo $type; ?>";
</script>

<!-- Javascript Functions -->
<script type="text/javascript" src="<?php echo $subdir.'js/functions.js';?>"></script>
<script type="text/javascript" src="<?php echo $subdir.'js/sound_test.js';?>"></script>

<?php 
    endif; 
    if (empty($name)) {
        require_once ($footer);
    }
?>
