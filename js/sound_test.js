function playTone() {
    document.getElementById("tone").play(); //play the sound
}

function showInstruction() {
    instructionIndex = getCookie("instructionIndex"); //get instruction index
    show("instruction" + instructionIndex); //show the next instruction
    allowResponses(); //allow them to navigate
}

var myTimeout;
function showTestImage() {
    setTimeout(function() {
        imageIndex = getCookie("imageIndex"); //get image index
        blockIndex = getCookie("blockIndex"); //get block index
        
        show(blockIndex + "." + imageIndex); //show the image
        start = +new Date(); //get the time
        allowResponses(); //allow a response
        
        //play sound
        if (tones[toneIndex] != "") { //if not empty
            setTimeout(function() { //delay tone
                playTone(); //play tone
            }, tones[toneIndex]); //delay in ms
        }
        
        toneIndex++; //increment global tone index
        
        myTimeout = setTimeout(function() { //timeout, call response with null input
            timeout(); //if they didn't respond
        }, 750); //timeout
        
    }, 500); //half a second between image + tone trials - ask aaron about this delay
}

//Show base image and then image
function showQuestion() {
    show("base"); //show base image - dot
    
    setTimeout(function() {
        hide("base"); //hide base image - dot
        showTestImage(); //show next image
    }, 500); //half a second between image + tone trials - ask aaron about this delay
}

//Show the pause screen between blocks
function showPause() {
    show("pause"); //show the pause prompt "Press Enter to continue"
    allowResponses(); //allow the user to press enter to move on
}

//Respond to user input
function response(e) {
    end = +new Date(); //get time
    var response_time = end - start; //calculate response time
    
    disallowResponses(); //stop allowing a user response
    clearTimeout(myTimeout); //clear the timeout
    
    var userResponse = getResponse(); //get the key they entered
    imageIndex = +(getCookie("imageIndex")); //get question index
    blockIndex = +(getCookie("blockIndex")); //get block index
    
    //if it's a practice test
    if (typeTest == "practice") {
        instructionIndex = +(getCookie("instructionIndex")); //get the instructions index
        
        //if cycling through instructions
        if (instructionIndex <= numInstructions) { 
            hide("instruction" + instructionIndex.toString()); //hide instruction
            
            //if it's the last instruction
            if (instructionIndex == numInstructions) { 
                //If they hit spacebar, continue to practice test
                if (userResponse == spacebar) { //spacebar
                    increment("instructionIndex"); //increment instruction index
                    
                    setTimeout(function() {
                        showQuestion();
                    }, 2000); //pause for 2s after they hit spacebar
                } else if (userResponse == left) { //left
                    if (instructionIndex > 1) { //can't go left any more than 1
                        decrement("instructionIndex"); //decrement instruction index to go back one page
                    } //else, do nothing - stay at this instruction
                    showInstruction(); //show instruction
                } else { //invalid key pressed
                    showInstruction(); //continue to allow a correct response
                }
            }
            //If the user pressed left arrow
            else if (userResponse == left) { //left
                if (instructionIndex > 1) { //can't go left any more than 1
                    decrement("instructionIndex"); //decrement instruction index to go back one page
                } //else, do nothing - stay at this instruction
                showInstruction(); //show instruction
            }
            //If the user pressed right arrow
            else if (userResponse == right) { //right
                increment("instructionIndex"); //increment the instruction index
                showInstruction(); //show instruction
            }
            //Anything else pressed, do nothing
            else { //invalid key pressed
                showInstruction(); //allow a correct response
            }
            
            //Don't do anything else, we're just navigating instructions
            return;
        }
    }

    hide(blockIndex.toString() + "." + imageIndex.toString()); //hide test image
    var pause = document.getElementById("pause"); //get the pause element
    
    //If the user pressed enter from the pause between blocks screen
    if (pause.offsetParent !== null && userResponse == enter) { //enter
        reset("imageIndex"); //reset the question index to 1
        imageIndex = 1; //set the question index to 1
        increment("blockIndex"); //increment the block index
        hide("pause"); //hide the pause between blocks screen
    } else {
        //It was a response to a test image
        setCookie("response." + blockIndex.toString() + "." + imageIndex.toString(), userResponse, 1); //save response
        setCookie("response_time." + blockIndex.toString() + "." + imageIndex.toString(), response_time, 1); //save response time
        
        //If it was the last question
        if (blockIndex == numBlocks && imageIndex == numQuestions[numBlocks - 1]) {
           
            //If it was the real test, save the results
            saveResults("../results/saveSoundResponses.php?typeTest="+typeTest+"&participant=");
 
            //Done with practice test - show "Notify Researcher" page
            if (typeTest == "practice") {
                show("practiceDone"); //show "Notify Researcher" page
                return; //Done with test, don't do anything else
            }
        }

        //If it was not the last question, continue the test
        else {
            //If it was the last question in that block, show the pause between blocks page
            if (imageIndex == numQuestions[blockIndex-1]) { //last question in block
                reset("imageIndex"); //reset image index
                increment("blockIndex"); //increment the block index
                showPause(); //show the pause between blocks page
                return; //Don't do anything else
            }
        }
        increment("imageIndex"); //increment image index
    }
    
    //pause, then show next image
    setTimeout(function() {
        showQuestion(); //show next round of base image + image
    }, 1000); //wait 1 second
}

var start, end; //time variables
var imageIndex, blockIndex, instructionIndex; //index variables
var toneIndex = 0; //global tone index

//Keycode legend
var spacebar = 32;
var left = 37;
var right = 39;
var enter = 13;

reset("imageIndex"); //set image index initially to 1
reset("blockIndex"); //set block index initially to 1

//If this is a practice test
if (typeTest == "practice") { 
    reset("instructionIndex"); //set instruction index initially to 1
    showInstruction(); //show the first instruction
}

//If this is a real test
else {
    showQuestion(); //start the test
}

