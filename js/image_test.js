function showQuestion() {
    show("base"); //show base image
    
    setTimeout(function() {
        hide("base"); //hide base image
        showTestImage();
    }, 250); //hide base image after 250 ms
}

function showTestImage() {
    imageIndex = getCookie("imageIndex");
    blockIndex = getCookie("blockIndex");
    
    show(blockIndex + "." + imageIndex); //show the image
    allowResponses(); //allow responses
    start = +new Date();
}

function nextQuestion() {
    var question = imageIndex - (60 * (blockIndex - 1)); //question number in the current block (out of 60)
    
    //If we're switching targets
    if (question == switchAfter) {
        show("switch"); //show switch text
        
        setTimeout(function () {
            hide("switch"); //hide switch text
            //Don't factor out these two lines of code to after the if statement - they need to wait for the switch duration
            increment("imageIndex", imageIndex); //increment imageIndex
            showPause();
        }, switchDuration); //hide after the specified number of milliseconds
    } else {
        increment("imageIndex", imageIndex); //increment imageIndex 
        showPause();
    }
}

function showPause() {
    setTimeout(function () { //show blank screen for 1000 ms, then start over at base image
        showQuestion();
    }, 1000); //blank screen for 1000 ms
}

//Respond to user input
function response(e) {
    end = +new Date();
    var response_time = end - start;
    
    disallowResponses(); //only allow response on specific pages
    
    var userResponse = getResponse();
    imageIndex = +(getCookie("imageIndex"));
    blockIndex = +(getCookie("blockIndex"));
    instructionIndex = +(getCookie("instructionIndex")); //get the instructions index

    //if cycling through instructions
    if (instructionIndex <= numInstructions) {
        handleInstructions(userResponse);
        
        //Don't do anything else, we're just navigating instructions
        return;
    }
    
    //If it was a valid response to a test image        
    if (userResponse == left || userResponse == right) {
        hide(blockIndex.toString() + "." + imageIndex.toString()); //hide test image
        
        setCookie("response." + blockIndex.toString() + "." + imageIndex.toString(), userResponse, 1); //save response
        setCookie("response_time." + blockIndex.toString() + "." + imageIndex.toString(), response_time, 1); //save response time
        
        //If it was the last question
        if (blockIndex == numBlocks && imageIndex == numQuestions[numBlocks - 1]) {
            
            //If it was the real test, save the results
            if (typeTest == "test") { 
                saveResults("../results/saveImageResponses.php?participant=");
            }

            //Done with practice test - show "Notify Researcher" page
            else {
                show("practiceDone"); //show "Notify Researcher" page
                return; //Done with test, don't do anything else
            }
        }

        //If it was not the last question, continue the test
        else {
            if (imageIndex == numQuestions[blockIndex-1]) { //last question in this block
                return; //don't do anything else
            }
        }
        
        //If they got the question wrong
        if ((userResponse == left && correctAnswers[imageIndex - 1] != "left")
            || (userResponse == right && correctAnswers[imageIndex - 1] != "right")) {

            show("wrong"); //show the X
            
            setTimeout(function() {
                hide("wrong"); //hide the X
                nextQuestion();
            }, 1000); //show X for 1000 ms
        }

        //If they got the question right
        else {
            nextQuestion(); //check if switching targets, pause, and move to next question
        }
    }

    //Not a valid response
    else {
        showTestImage(); //continue allowing responses
    }
}

var start, end; //time variables
var imageIndex, blockIndex, instructionIndex; //index variables
var toneIndex = 1; //global tone index

//Keycode legend
var spacebar = 32;
var left = 37;
var right = 39;

setCookie("imageIndex", 1, 1); //set image index initially to 1
setCookie("blockIndex", 1, 1); //set block index initially to 1
setCookie("instructionIndex", 1, 1); //set instruction index initially to 1

showInstruction(); //show the first instruction

