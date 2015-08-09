<?php 
    session_start();
    require_once ("../config/global.php");
    
    $instructions = [
        "Instruction page 1",
        "Instruction page 2",
        "Instruction page 3"
    ];
    
    //If no test selected
    if (empty($_POST["name"])) :
    
        require_once ($header);
        logout ();
            
    // If a test is selected
    else : ?>
    
        <!-- My Stylesheet -->
        <link rel="stylesheet" type="text/css" href='/css/style.css'>
<?php 
    endif;
    
    //If no test selected
    if (empty($_POST["name"])) : ?>
       
        <div id="start">
        
            <!-- Heading -->
            <h1>Test 2</h1>
            
            <!-- Test Selection -->
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <table class='form'>
                    
                    <!-- Participant Name -->
                    <tr>
                        <td><label for="name">Please enter your name:</label></td>
                        <td><input required type="text" name="name"></td>
                    </tr>
                    
                    <!-- Test Selection -->
                    <tr>
                        <td></td>
                        <td>
                            <select name="type">
                                <option value="practice">Practice</option>
                                <option value="test">Test</option>
                            </select>
                        </td>
                    </tr>
                </table>
                
                <br>
                
                <!-- Submit Button -->
                <input type="submit" name="start" value="Start">
            </form>
            
        </div>
<?php
    elseif (isset($_GET['done'])) :
        
        // Thank them for participating  
        thankYou ();
        
    endif; ?>

    <!-- Populate Test -->
<?php 
    // If a test is selected
    if (!empty($_POST["name"])) : 
        $name = $_POST['name'];
        $type = $_POST['type'];
        
        $test = decodeJSON ($imageTests);  
        $test = $test[$type];
    
        //If it doesn't exist
        if (empty($test)) : ?>
            <h2>Error - no test available.</h2>
            <a href="http://aaron-landau.appspot.com/test/imageTest.php">Back to Test Selection</a>
    <?php
        endif; 
     
        //Instructions
        foreach ($instructions as $index => $instr) : ?>
            <div id="instruction<?php echo $index+1; ?>" style="display:none">
                
                <!-- Instructions Image -->
                <h1><?php echo $instr; ?></h1>
                <!--<img src="<?php echo $imageURL.$instr;?>">-->
                
                <!-- Right Arrow -->
                <?php 
                    if ($index != 0) : ?>
                        <span style="float:left;font-size:3em;">&lt;</span>
                <?php 
                    endif; ?>
                
                <!-- Left Arrow -->
                <?php 
                    if ($index+1 != count($instructions)) : ?>
                        <span style="float:right;font-size:3em;">&gt;</span>
                <?php 
                    endif; ?>
            </div>
    <?php 
        endforeach; ?>
        
        <!-- Done With Practice Test Page -->
        <div id="practiceDone" style="display:none">
            <h1>Notify the researcher that you have completed the practice session.</h1>
            <a href="http://aaron-landau.appspot.com/test/soundTest.php">Back to Test Selection</a>
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
    
    <!-- Test Images -->
    <?php 
        //For each block
        foreach ($test["Block"] as $b => $block) :
            
            //For each question
            foreach ($block as $num => $image) :
                $left = $image["left"];
                $right = $image["right"]; ?>
                
                <!-- Image -->
                <div id="<?php echo $b.".".$num; ?>" style="display:none">
                    
                    <!-- Left character -->
                    <span class="left <?php if ($left['color']=='white' or $left["color"]=='#ffffff') echo ', white' ?>" style="color:<?php echo $left["color"]; ?>;">
                            <?php echo $left["character"]; ?>
                    </span>
                    
                    <!-- Right character -->
                    <span class="right <?php if ($right['color']=='white' or $right["color"]=='#ffffff') echo ', white' ?>" style="color:<?php echo $right["color"]; ?>;">
                            <?php echo $right["character"]; ?>
                    </span>
                    
                </div>
        <?php 
            endforeach; 
        endforeach; ?>
    
    <!-- Javascript Variables -->
    <script type="text/javascript">
    
        //Number of blocks
        var numBlocks = <?php echo count($test["Block"]); ?>;
        
        //Number of questions in each block
        var numQuestions = [<?php 
            $arr = "";
            
            //for each block get count of questions
            foreach ($test["Block"] as $block) {
                $arr .= count($block).",";
            }
            
            $arr = rtrim ($arr, ",");
            echo $arr;
        ?>];
        
        //Correct answers
        var correctAnswers = [<?php 
            $arr = "";
            
            //For each question in the test
            foreach ($test["Right Answers"] as $num => $answer) {
                $arr .= "'".$answer."',";
            }
            
            $arr = rtrim($arr, ",");
            echo $arr;
        ?>];
        
        //Participant name
        var name = "<?php echo $name; ?>";
        
        //Switch after delay
        var switchAfter = <?php echo $test["Switch"]["after"]; ?>;
        
        //Switch duration
        var switchDuration = <?php echo $test["Switch"]["duration"]; ?>;
        
        //Number of instruction pages
        var numInstructions = <?php echo count($instructions); ?>;
        
        //Type of test - practice or test
        var typeTest = "<?php echo $type; ?>";
        
    </script>
    
    <!-- Javascript Functions -->
    <script type="text/javascript" src="<?php echo $subdir.'js/functions.js';?>"></script>
    <script type="text/javascript" src="<?php echo $subdir.'js/image_test.js';?>"></script>

<?php 
    endif; 
    
    //If showing header at top, show footer at bottom
    if (empty($name)) {
        require_once ($footer);
    }
?>
