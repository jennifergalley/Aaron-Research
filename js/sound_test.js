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
        
        show(imageIndex); //show the image
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

    hide(imageIndex.toString()); //hide test image
    var pause = document.getElementById("pause"); //get the pause element
    
    //If the user pressed enter from the pause between blocks screen
    if (pause.offsetParent !== null && userResponse == enter) { //enter
        reset("imageIndex"); //reset the question index to 1
        imageIndex = 1; //set the question index to 1
        hide("pause"); //hide the pause between blocks screen
    } else if (pause.offsetParent !== null) {
        showPause();
        return;
    } else {
        //It was a response to a test image
        
        var r = getCookie("r");
        r += "r" + imageIndex.toString() + "=" + userResponse + "&"; //user response may be empty if they didn't respond
        var rt = getCookie("rt");
        rt += "rt" + imageIndex.toString() + "=" + response_time + "&"; //may be 750+ if they didn't respond
        
        setCookie("r", r, 1); //save response
        setCookie("rt", rt, 1); //save response time
        
        //If it was the last question
        if (imageIndex == numQuestions[blockNum - 1]) {
            
            //save the results
            saveResults("../results/saveSoundResponses.php", getCookie("key"));

            return; //Done with test, don't do anything else
        }
        
        increment("imageIndex"); //increment image index
    }
    
    //pause, then show next image
    setTimeout(function() {
        showQuestion(); //show next round of base image + image
    }, 1000); //wait 1 second
}

var start, end; //time variables
var imageIndex, instructionIndex; //index variables
var toneIndex = 0; //global tone index

//Keycode legend
var spacebar = 32;
var left = 37;
var right = 39;
var enter = 13;

if (notSaved) {
    setCookie("key", +new Date(), 1);
    setCookie("r", "", 1);
    setCookie("rt", "", 1);
    saveRecord("../results/saveSoundResponses.php", getCookie("key"));
} else {
    reset("imageIndex"); //set image index initially to 1

    //If this is a practice test
    if (typeTest == "practice") {
        reset("instructionIndex"); //set instruction index initially to 1
        showInstruction(); //show the first instruction
    }

    //If this is a real test
    else {
        //if this is not the first block, pause page
        if (blockNum != 1) {
            showPause(); //show the pause between blocks page
        }
        
        //it's the first block, just start the test
        else {
            showQuestion(); //start the test
        }
    }
}



function saveResults(saveLocation, key) {
    //save the results
    var url = saveLocation + "?key=" + key.toString() + "&typeTest=" + typeTest + '&block=' + blockNum; //URL of the save results page

    var parameters = getCookie("r"); //send the responses
    parameters += getCookie("rt"); //send the response times
    
    var mypostrequest = new ajaxRequest();
    mypostrequest.onreadystatechange = function(){
        if (mypostrequest.readyState == 4){
            if (mypostrequest.status == 200 || window.location.href.indexOf("http") == -1){
                // document.getElementById("result").innerHTML=mypostrequest.responseText;

                var newURL = "";
    
                if (typeTest == 'test') {
                    if (blockNum == 4) {
                        newURL = '/test/soundTest.php?done';
                    } else {
                        newURL = '/test/soundTest.php?type=' + typeTest + '&block=' + (blockNum+1).toString();
                    }
                } else {
                    newURL = '/test/soundTest.php?pdone';
                }
                
                window.location = newURL;
            }
            else {
                alert("An error has occured saving the responses");
            }
        }
    }
    
    // var parameters = "name="+namevalue+"&age="+agevalue;
    mypostrequest.open("POST", url, true);
    mypostrequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    mypostrequest.send(parameters);
}

function ajaxRequest() {
    var activexmodes = ["Msxml2.XMLHTTP", "Microsoft.XMLHTTP"]; //activeX versions to check for in IE
    if (window.ActiveXObject) { //Test for support for ActiveXObject in IE first (as XMLHttpRequest in IE7 is broken)
        for (var i = 0; i < activexmodes.length; i++) {
            try {
                return new ActiveXObject(activexmodes[i]);
            }
            catch (e) {
                //suppress error
            }
        }
    }
    else if (window.XMLHttpRequest) // if Mozilla, Safari etc
        return new XMLHttpRequest();
    else
        return false;
}


    