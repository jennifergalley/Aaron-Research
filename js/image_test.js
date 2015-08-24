function showQuestion() {
    show("base"); //show base image
    
    setTimeout(function() {
        hide("base"); //hide base image
        showTestImage();
    }, 250); //hide base image after 250 ms
}

function showInstruction() {
    instructionIndex = getCookie("instructionIndex"); //get instruction index
    var evenOdd = blockNum != 1 && blockNum % 2 == 0 ? "Even" : "Odd";
    show("instruction" + evenOdd + instructionIndex); //show the next instruction
    allowResponses(); //allow them to navigate
}

function showTestImage() {
    imageIndex = getCookie("imageIndex");
    
    show(imageIndex); //show the image
    allowResponses(); //allow responses
    start = +new Date();
}

function showInitialTarget() {
    show("initial_target"); //show initial target text
    
    setTimeout(function () {
        hide("initial_target"); //hide switch text
        showPause();
    }, initialTargetDuration); //hide after the specified number of milliseconds
}

function nextQuestion() {
    imageIndex = +(getCookie("imageIndex"));
    
    //If it's the last question in the block
    if (imageIndex == numQuestions[blockNum - 1]) {
        reset("imageIndex"); //reset image index
        setCookie("onInstructions", "true", 1); //reset on instructions flag
        showInstruction();
        return; //don't do anything else
    }
    
    //var question = imageIndex - (60 * (blockIndex - 1)); //question number in the current block (out of 60)
    
    //If we're switching targets
    if (imageIndex == switchAfter) {
        show("switch"); //show switch text
        
        setTimeout(function () {
            hide("switch"); //hide switch text
            //Don't factor out these two lines of code to after the if statement - they need to wait for the switch duration
            increment("imageIndex"); //increment imageIndex
            showPause();
        }, switchDuration); //hide after the specified number of milliseconds
    } else {
        increment("imageIndex"); //increment imageIndex 
        showPause();
    }
}

function showPause() {
    setTimeout(function () { //show blank screen for 1000 ms, then start over at base image
        showQuestion();
    }, 1000); //blank screen for 1000 ms
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
                    if (blockNum == 6) {
                        newURL = '/test/imageTest.php?done';
                    } else {
                        newURL = '/test/imageTest.php?type=' + typeTest + '&block=' + (blockNum+1).toString();
                    }
                } else {
                    newURL = '/test/imageTest.php?pdone';
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


//Respond to user input
function response(e) {
    end = +new Date();
    var response_time = end - start;
    
    disallowResponses(); //only allow response on specific pages
    
    var userResponse = getResponse();
    imageIndex = +(getCookie("imageIndex"));
    instructionIndex = +(getCookie("instructionIndex")); //get the instructions index

    //if cycling through instructions
    if (getCookie("onInstructions") == "true") {
        instructionIndex = +(getCookie("instructionIndex")); //get the instructions index
        var evenOdd = blockNum != 1 && blockNum % 2 == 0 ? "Even" : "Odd";
        
        hide("instruction" + evenOdd + instructionIndex.toString()); //hide instruction
        
        //if it's the last instruction
        if (instructionIndex == numInstructions) { 
            //If they hit spacebar, continue to practice test
            if (userResponse == spacebar) { //spacebar
                increment("instructionIndex"); //increment instruction index
                
                setTimeout(function () {
                    reset("instructionIndex"); //reset instruction index
                    setCookie("onInstructions", "false", 1); //reset instructions flag
                    showInitialTarget(); //show the initial target screen, then proceed to test
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
    
    //If it was a valid response to a test image        
    if (userResponse == left || userResponse == right) {
        hide(imageIndex.toString()); //hide test image

        var r = getCookie("r");
        r += "r" + imageIndex.toString() + "=" + userResponse + "&";
        var rt = getCookie("rt");
        rt += "rt" + imageIndex.toString() + "=" + response_time + "&";
        
        setCookie("r", r, 1); //save response
        setCookie("rt", rt, 1); //save response time
        
        //If it was the last question
        if (imageIndex == numQuestions[blockNum - 1]) {
            
            //save the results
            saveResults("../results/saveImageResponses.php", getCookie("key"));
            
            return; //Done with test, don't do anything else
        }     
        
        //Get the current question out of total number of questions
        var offset = (blockNum - 1) * 60;
        
        //If they got the question wrong
        if ((userResponse == left && correctAnswers[offset + imageIndex - 1] != "left")
            || (userResponse == right && correctAnswers[offset + imageIndex - 1] != "right")) {

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
var imageIndex, instructionIndex; //index variables
var toneIndex = 1; //global tone index

//Keycode legend
var spacebar = 32;
var left = 37;
var right = 39;

if (notSaved) {
    setCookie("key", +new Date(), 1);
    setCookie("r", "", 1);
    setCookie("rt", "", 1);
    saveRecord("../results/saveImageResponses.php", getCookie("key"));
} else {
    reset("imageIndex"); //set image index initially to 1
    reset("instructionIndex"); //set instruction index initially to 1

    //Practice Test
    if (typeTest == "practice") {
        setCookie("onInstructions", "true", 1); //set on instructions flag initially to true if practice test
        showInstruction(); //show the first instruction
    }

    //Real Test
    else {
        //First Block
        if (blockNum == 1) {
            setCookie("onInstructions", "false", 1); //set on instructions flag initially to false if test round 1
            showInitialTarget();
        }
    
        //Not first block
        else {
            setCookie("onInstructions", "true", 1); //set on instructions flag initially to true if practice test
            showInstruction(); //show the first instruction
        }
    }
}

