<?php 
    session_start();
    require_once ("../config/global.php");
    
    $instructions = [
        "img19.png",
        "img20.png", 
        "img21.png",
        "img22.png",
        "img23.png",
        "img24.png",
        "img25.png"
    ];
    
    // If no test selected
    if (empty($_POST["name"])) :
    
        require_once ($header);
        logout ();
        
    // If a test is selected
    else : ?>
    
        <!-- My Stylesheet -->
        <link rel="stylesheet" type="text/css" href="<?php echo $subdir.'css/style.css';?>">
<?php
    endif;

    // If no test selected
    if (empty($_POST["name"]) and !isset($_GET['done']) and !isset($_GET['pdone'])) : ?>
    
        <div id="start">
    
            <!-- Heading -->
            <h1>Test 1</h1>
            
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
        
    elseif (isset($_GET['pdone'])) : 
?>
        <!-- Done With Practice Test Page -->
        <div id="practiceDone">
            <h1>Notify the researcher that you have completed the practice session.</h1>
            <a href="http://aaron-landau.appspot.com/test/soundTest.php">Back to Test Selection</a>
        </div>
<?php
    endif; 
?>

    <!-- Populate Test -->
<?php 
    // If a test is selected
    if (!empty($_POST["start"])) : 
        $name = $_POST['name']; //participant name
        $type = $_POST['type']; //type of the test
        
        $test = decodeJSON ($soundTests); //decode the JSON tests
        $test = $test[$type]; //get the correct test
        
        //If it doesn't exist
        if (empty($test)) : ?>
            <h2>Error - no test available.</h2>
            <a href="http://aaron-landau.appspot.com/test/soundTest.php">Back to Test Selection</a>
    <?php
        endif; ?>
    
    <!-- Sound Element -->
    <audio id='tone' src="tone.wav" preload="auto"></audio>
    
    <?php 
        // If it's a practice test
        if ($type == 'practice') : ?>
        
            <!-- Instructions -->
        <?php
            foreach ($instructions as $index => $instr) : ?>
                <div id="instruction<?php echo $index+1; ?>" style="display:none">
                    
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
                        if ($index+1 != count($instructions)) : ?>
                            <span style="float:right;font-size:3em;">&gt;</span>
                    <?php 
                        endif; ?>
                </div>
        <?php 
            endforeach;

        endif; ?>
    
        <!-- Base Image - dot -->
        <div id="base" style="display:none">
            <img class="test" src="<?php echo $imageURL.'dot.jpg';?>">
        </div>
        
        <!-- Pause Between Blocks -->
        <div id="pause" style="display:none">
            <h1>Press enter to continue</h1>
        </div>
        
        <!-- Test Images -->
        <?php 
            // For each block
            foreach ($test["Block"] as $b => $block) :
                $i = 1;
                
                // For each question
                foreach ($block as $question) : ?>

                    <!-- Image -->
                    <div id="<?php echo $b.".".$i++; ?>" style="display:none">
                        <img class="test" src="<?php echo $imageURL.$question['image'];?>">
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
            
            //Participant name
            var name = "<?php echo $name; ?>";
            
            //Tones to play
            var tones = [<?php 
                $blockIndex = 1;
                
                //For each block
                foreach ($test["Block"] as $b => $block) {
                    $questionIndex = 1;
                    
                    //For each question
                    foreach ($block as $question) {
                        //If there is a tone, then MS delay. Else, empty
                        echo '"'.$question['tone'].'"';
                        
                        //if there is a next question
                        if (array_key_exists($questionIndex+1, $block)) {
                            echo ", ";
                        }
                        
                        $questionIndex++;
                    }
                    
                    //If there is a next block
                    if (array_key_exists ($blockIndex+1, $test["Block"])) {
                        echo ", ";
                    }
                    
                    $blockIndex++;
                }
            ?>];
            
            //Number of instruction pages
            var numInstructions = <?php echo count($instructions); ?>;
            
            //Type of test - practice or test
            var typeTest = "<?php echo $type; ?>";
            
        </script>
        
        <!-- Javascript Functions -->
        <script type="text/javascript" src="<?php echo $subdir.'js/functions.js';?>"></script>
        <script type="text/javascript" src="<?php echo $subdir.'js/sound_test.js';?>"></script>

<?php 
    endif; 
    
    //If showing header at top, show footer at bottom
    if (empty($name)) {
        require_once ($footer);
    }
?>
