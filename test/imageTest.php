<?php 
    session_start();
    require_once ("../config/global.php");
    if (empty($_POST["version"])) {
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
    <h1>Test 1</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
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
    $test = decodeJSON ($imageTests);  
    $test = $test[$_POST['version']];
?>

<!-- Test -->

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
    var participant = "<?php echo $_POST['name']; ?>";
    var testVersion = "<?php echo $_POST['version']; ?>";
    var switchAfter = <?php echo $test["Switch"]["after"]; ?>;
    var switchDuration = <?php echo $test["Switch"]["duration"]; ?>;
</script>

<!-- Javascript Functions -->
<script type="text/javascript" src="<?php echo $subdir.'js/functions.js';?>"></script>
<script type="text/javascript" src="<?php echo $subdir.'js/image_test.js';?>"></script>

<?php 
    endif; 
    if (empty($_POST["version"])) {
        require_once ($footer);
    }
?>
