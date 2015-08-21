<?php 
    session_start();
    require_once ("../config/global.php");
    
    $instructionsOdd = [
        "img1.png",
        "img2.png",
        "img3.png",
        "img4.png",
        "img5.png",
        "img6.png",
        "img7.png",
        "img8.png",
        "img9.png",
        "img26.png"
    ];
    
    $instructionsEven = [
        "img10.png",
        "img11.png",
        "img12.png",
        "img13.png",
        "img14.png",
        "img15.png",
        "img16.png",
        "img17.png",
        "img18.png",
        "img26.png"
    ];
    
    //If no test selected
    if (empty($_POST) and empty($_GET)) :
    
        require_once ($header);
        logout ();
            
    // If a test is selected
    else : ?>
    
        <!-- My Stylesheet -->
        <link rel="stylesheet" type="text/css" href='/css/style.css'>
<?php 
    endif;
    
    //If no test selected
    if (empty($_POST) and empty($_GET)) : ?>
       
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
    elseif (isset($_GET['done'])) : ?>
        
        <!-- After Test Finished -->
        <h1>Your results have been recorded. <br>Thanks for participating!</h1>
        <a href="http://aaron-landau.appspot.com/test/imageTest.php">Back to Test Selection</a>
        
<?php
    elseif (isset($_GET['pdone'])) : 
?>
        <!-- Done With Practice Test Page -->
        <div id="practiceDone">
            <h1>Notify the researcher that you have completed the practice session.</h1>
            <a href="http://aaron-landau.appspot.com/test/imageTest.php">Back to Test Selection</a>
        </div>
        
    <!-- Populate Test -->
<?php
    // If a test is selected
    elseif (!empty($_POST) or !empty($_GET)) : 
        $type = !empty($_POST['type']) ? $_POST['type'] : $_GET['type'];
        $block = !empty($_GET['block']) ? $_GET['block'] : 1;
        
        $test = decodeJSON ($imageTests);  
        $test = $test[$type];
    
        //If it doesn't exist
        if (empty($test)) : ?>
            <h2>Error - no test available.</h2>
            <a href="http://aaron-landau.appspot.com/test/imageTest.php">Back to Test Selection</a>
    <?php
        endif; 
     
        //Instructions
        foreach ($instructionsEven as $index => $instr) : ?>
            <div id="instructionEven<?php echo $index+1; ?>" style="display:none">
                
                <!-- Instructions Image -->
                <img src="<?php echo $imageURL.$instr;?>">
                
                <!-- Left Arrow -->
                <?php 
                    if ($index != 0) : ?>
                        <span style="float:left;font-size:3em;">&lt;</span>
                <?php 
                    endif; ?>
                
                <!-- Right Arrow -->
                <?php 
                    if ($index+1 != count($instructionsEven)) : ?>
                        <span style="float:right;font-size:3em;">&gt;</span>
                <?php 
                    endif; ?>
            </div>
    <?php 
        endforeach; 
        
        foreach ($instructionsOdd as $index => $instr) : ?>
            <div id="instructionOdd<?php echo $index+1; ?>" style="display:none">
                
                <!-- Instructions Image -->
                <img src="<?php echo $imageURL.$instr;?>">
                
                <!-- Left Arrow -->
                <?php 
                    if ($index != 0) : ?>
                        <span style="float:left;font-size:3em;">&lt;</span>
                <?php 
                    endif; ?>
                
                <!-- Right Arrow -->
                <?php 
                    if ($index+1 != count($instructionsOdd)) : ?>
                        <span style="float:right;font-size:3em;">&gt;</span>
                <?php 
                    endif; ?>
            </div>
    <?php 
        endforeach; 
    ?>
            
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
        //Get the block
        $blockContents = $test["Block"]["$block"];
    ?>
        <!-- Switch Screen -->
        <div id="switch" style="display:none">
            <h1><?php echo $blockContents["Switch"]["text"]; ?></h1>
        </div>
    <?php
        //For each question
        for ($num = 1; $num <= count($blockContents)-1; $num++) :
            $image = $blockContents[$num];
            $left = $image["left"];
            $right = $image["right"]; ?>
            
            <!-- Image -->
            <div id="<?php echo $num; ?>" style="display:none">
                
                <!-- Left character -->
                <span class="left <?php if ($left['color']=='white' or $left["color"]=='#ffffff') echo ', white' ?>" style="color:<?php echo $left["color"]; ?>;">
                        <?php echo $left["character"]; ?>
                </span>
                
                <!-- Right character -->
                <span class="right <?php if ($right['color']=='white' or $right["color"]=='#ffffff') echo ', white' ?>" style="color:<?php echo $right["color"]; ?>;">
                        <?php echo $right["character"]; ?>
                </span>
                
            </div>
    <?php endfor; ?>
    
    <!-- Javascript Variables -->
    <script type="text/javascript">
    
        //Number of blocks
        var numBlocks = <?php echo count($test["Block"]); ?>;
        
        //Number of questions in each block
        var numQuestions = [<?php 
            $arr = "";
            
            //for each block get count of questions
            foreach ($test["Block"] as $blockContents) {
                $arr .= (count($blockContents)-1).",";
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
        var name = "<?php echo !empty($_POST['name']) ? $_POST['name'] : ""; ?>";
        
        //Switch after delay - same for every block
        var switchAfter = <?php echo $test["Block"]["1"]["Switch"]["after"]; ?>;
        
        //Switch duration - same for every block
        var switchDuration = <?php echo $test["Block"]["1"]["Switch"]["duration"]; ?>;
        
        //Number of instruction pages
        var numInstructions = <?php echo count($instructionsEven); ?>;
        
        //Type of test - practice or test
        var typeTest = "<?php echo $type; ?>";
        
        var blockNum = <?php echo $block; ?>;
        
        var notSaved = <?php echo (!isset($_GET['record']) and empty($_GET['block'])) ? "true" : "false"; ?>;
        
    </script>
    
    <!-- Javascript Functions -->
    <script type="text/javascript" src="<?php echo $subdir.'js/functions.js';?>"></script>
    <script type="text/javascript" src="<?php echo $subdir.'js/image_test.js';?>"></script>

<?php 
    endif; 
    
    //If showing header at top, show footer at bottom
    if (empty($_POST) and empty($_GET)) {
        require_once ($footer);
    }
?>
