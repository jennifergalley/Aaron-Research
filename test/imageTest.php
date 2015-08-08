<?php 
    session_start();
    require_once ("../config/global.php");
    
    $instructions1 = [
        "img1.png",
        "img2.png",
        "img3.png",
        "img4.png",
        "img5.png",
        "img6.png",
        "img7.png",
        "img8.png",
        "img9.png"
    ];
    
    $instructions2 = [
        "img10.png",
        "img11.png",
        "img12.png",
        "img13.png",
        "img14.png",
        "img15.png",
        "img16.png",
        "img17.png",
        "img18.png"
    ];
    
    if (empty($_POST["name"]) and empty($_GET['n'])) {
        require_once ($header);
        logout ();
    } else { ?>
        <!-- My Stylesheet -->
        <link rel="stylesheet" type="text/css" href='/css/style.css'>
    <?php }

    if (empty($_POST["name"]) and empty($_GET)) : ?>
    <div id="start">
<?php 
    $test = decodeJSON ($imageTests);    
    if (empty($test)) :
        echo "<h2>Error - no tests available.</h2>";
    else : ?>
    <!-- Name Submit and Start Test -->
    <h1>Test 2</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
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
    $test = decodeJSON ($imageTests);  
    $name = !empty($_POST['name']) ? $_POST['name'] : $_GET['n'];
    $type = isset($_GET['test']) ? "test" : "practice";
    $test = $test[$type];
?>

<!-- Test -->
<!-- Instruction -->
<?php foreach ($instructions as $index => $instr) : ?>
<div id="instructions<?php echo $index+1; ?>" style="display:none">
    <img class="instr" src="<?php echo $imageURL.$instr;?>">
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

<!-- Start page -->
<div id="start" style="display:none">
    <h1>Press the space bar to start the practice round</h1>
</div>

<!-- Switch Screen -->
<div id="switch" style="display:none">
    <h1><?php echo $test["Switch"]["text"]; ?></h1>
</div>

<!-- Base Image - crosshair -->
<div id="base" style="display:none">
    <img class="test" src="<?php echo $imageURL.'cross.png';?>">
</div>

<!-- Wrong Image - X -->
<div id="wrong" style="display:none">
    <img class="test" src="<?php echo $imageURL.'wrong.png';?>">
</div>

<?php 
    //Populate Questions
    foreach ($test["Block"] as $b => $block) :
        foreach ($block as $num => $image) :
            $left = $image["left"];
            $right = $image["right"];
         ?>
        <!-- Image -->
        <div id="<?php echo $b.".".$num; ?>" style="display:none">
            <span class="left <?php if ($left['color']=='white' or $left["color"]=='#ffffff') echo ', white' ?>" style="color:<?php echo $left["color"]; ?>;">
                    <?php echo $left["character"]; ?>
            </span>
            <span class="right <?php if ($right['color']=='white' or $right["color"]=='#ffffff') echo ', white' ?>" style="color:<?php echo $right["color"]; ?>;">
                    <?php echo $right["character"]; ?>
            </span>
        </div>
<?php endforeach; 
    endforeach; ?>

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
    var correctAnswers = [<?php 
        $arr = "";
        foreach ($test["Right Answers"] as $num => $answer) {
            $arr .= "'".$answer."',";
        }
        $arr = rtrim($arr, ",");
        echo $arr;
    ?>];
    var participant = "<?php echo $name; ?>";
    var switchAfter = <?php echo $test["Switch"]["after"]; ?>;
    var switchDuration = <?php echo $test["Switch"]["duration"]; ?>;
    var numInstructions = <?php echo count($instructions); ?>;
    var typeTest = "<?php echo $type; ?>";
</script>

<!-- Javascript Functions -->
<script type="text/javascript" src="<?php echo $subdir.'js/functions.js';?>"></script>
<script type="text/javascript" src="<?php echo $subdir.'js/image_test.js';?>"></script>

<?php 
    endif; 
    if (empty($name)) {
        require_once ($footer);
    }
?>
