    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires;
    }
    
    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i<ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1);
            if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
        }
        return "";
    }
    
    function show (elem) {
        document.getElementById(elem).style.display = "block"; //show
    }
    
    function hide (elem) {
        document.getElementById(elem).style.display = "none"; //hide
    }
    
    function timeout () {
        var event; // The custom event that will be created
        if (document.createEvent) {
          event = document.createEvent("HTMLEvents");
          event.initEvent("keydown", true, true);
        } else {
          event = document.createEventObject();
          event.eventType = "keydown";
        }
    
        event.eventName = "keydown";
    
        if (document.createEvent) {
          document.dispatchEvent(event);
        } else {
          document.fireEvent("on" + event.eventType, event);
        }
    }
    
    function increment(c) {
        var i = +(getCookie(c));
        setCookie (c, i+1, 1); //increment c
    }
    
    function decrement (c) {
        var i = +(getCookie(c));
        setCookie (c, i-1, 1); //decrement c
    }
    
    function reset(c) {
        setCookie(c, 1, 1);
    }
    
    function allowResponses () {
        document.onkeydown = response;
    }
    
    function disallowResponses () {
        document.onkeydown = "";
    }
    
    function getResponse () {
        var evtobj = window.event ? event : e; 
        var keycode = evtobj.keyCode;
        if (keycode == undefined) {
            keycode = 0;
        }
        return keycode;
    }
    
    function saveResults(saveLocation) {
        //If it was the real test, save the results
        var url = saveLocation + name + "&"; //URL of the save results page
        
        var totalCounter = 1; //total counter

        //For each block
        for (blockCounter = 1; blockCounter <= numBlocks; blockCounter++) { //block counter
            
            //For each question
            for(questionCounter = 1; questionCounter <= numQuestions[blockCounter - 1]; questionCounter++) { //question counter
                url += totalCounter + "=" + getCookie("response." + blockCounter + "." + questionCounter) + "&"; //send the response
                url += totalCounter + "_time=" + getCookie("response_time." + blockCounter + "." + questionCounter) + "&"; //send the response time
                totalCounter++; //increment the total counter
            }
        }
    
        window.location = url;//redirect to that page and save the results
    }
