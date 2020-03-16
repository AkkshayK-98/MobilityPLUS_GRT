
//reset error when user chages value
const inputs = document.querySelectorAll("input");
for (let i = 0; i < inputs.length; i++) {
    inputs[i].addEventListener("blur", function () {
        inputs[i].classList.remove("error-form");
    });
}



function validateForm() {
   // event.preventDefault();
    //get all feilds
    var pDate = document.querySelector("input[name=\"pickupDate\"]");
    var pTime = document.querySelector("input[name=\"pickupTime\"]");
    var pAdd = document.querySelector("input[name=\"pickupaddress\"]");
    var pCity = document.querySelector("input[name=\"pickupCity\"]");
    var pPostal = document.querySelector("input[name=\"pickupPostalcode\"]");
    var pNotes = document.querySelector("input[name=\"pickupNotes\"]");
    var rDate = document.querySelector("input[name=\"returnTripDate\"]");
    var rTime = document.querySelector("input[name=\"returnTripTime\"]");
    var rAdd = document.querySelector("input[name=\"returnTripaddress\"]");
    var rCity = document.querySelector("input[name=\"returnTripCity\"]");
    var rPostal = document.querySelector("input[name=\"returnTripPostalcode\"]");
    var rNotes = document.querySelector("input[name=\"returnTripNotes\"]");
    var device = document.querySelector("input[name=\"device\"]");
    var guest = document.querySelector("input[name=\"guest\"]");
    var hasErrors = false;
    //setup error style
    var styleError = document.createElement('style');
    styleError.type = 'text/css';
    styleError.innerHTML = '.error-form{border: 2pt solid red;  background-color: #FFCDD2; background-image: url(images/error.png);  background-repeat: no-repeat;  background-position: right;}';
    document.getElementsByTagName('head')[0].appendChild(styleError);



    //Check for empty feilds 
    var hasEmpty = false;
    hasEmpty = fieldEmpty(pDate);
    hasEmpty = fieldEmpty(pTime);
    hasEmpty = fieldEmpty(pAdd);
    //hasEmpty=fieldEmpty(pCity);
    hasEmpty = fieldEmpty(pPostal);

    hasEmpty = fieldEmpty(rDate);
    hasEmpty = fieldEmpty(rTime);
    hasEmpty = fieldEmpty(rAdd);
    // hasEmpty=fieldEmpty(rCity);
    hasEmpty = fieldEmpty(rPostal);

    if (hasEmpty) {
        alert("One or more fields that cannot be empty are empty!");
        hasErrors = true;
    }

    //check for valid postal fotmat
    hasErrors = checkPostal(pPostal);
    hasErrors = checkPostal(rPostal);

    //check that pick date is one day advance
    hasErrors = dateInFuture(pDate);
    hasErrors = dateInFuture(rDate);

    //check that return date is greater or equal to start date
    hasErrors = pickBeforeReturnDate(pDate, rDate);

    //check if pick and return times are in hours of op
    hasErrors = timeInHours(pTime)
    hasErrors = timeInHours(rTime)

    //check if return time is an hour after pickup
    hasErrors = timeDifIs1H(pDate, pTime, rDate, rTime);
    //check check box

    var agreeBox = document.querySelector(".agreeCheckBox");

    if (agreeBox.checked == false) {

        alert("You must agree to terms of the site to continue!");
        hasErrors = true;
    }
    if (hasErrors) {
        console.log("Error")
        event.preventDefault();
        
    } else {
        
        console.log("Good")
       // return true;
        
    }

}

function fieldEmpty(field) {
    //console.log(field)
    if (field.value === "") {
        field.classList.add("error-form")
        return true;
    } else {
        return false;
    }
}

function checkPostal(field) {
    var regex = /^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/;

    var match = regex.exec(field.value);

    if (match) {
        return false;//no error
    } else {
        field.classList.add("error-form")
        alert("Please ensure your postal codes are of a valid format.")
        return true;
    }

}

function dateInFuture(field) {
    var today = new Date();
    today.setHours(0, 0, 0, 0);
    var date = new Date(field.value);
    if (date > today) {
        return false;
    } else {
        field.classList.add("error-form")
        alert("You are trying to book for a date in the past or today! Please ensure you book at least 1 day in advanced!")
        return true;
    }

}

function pickBeforeReturnDate(pDate, rDate) {
    var date1 = new Date(pDate.value);
    var date2 = new Date(rDate.value);
    if (date2 >= date1) {
        return false;
    } else {
        rDate.classList.add("error-form")
        alert("You return date is before your pickup date!")
        return true;
    }

}
function timeInHours(field) {

    var time = new Date("2015-03-25T" + field.value + "Z");
    var openTime = new Date("2015-03-25T05:15:00Z");
    var closeTime = new Date("2015-03-25T23:59:59Z");

    if (time >= openTime && time <= closeTime) {
        return false;
    } else {
        field.classList.add("error-form")
        alert("Your trip times must be within our hours of operation!")
        return true;
    }

}

function timeDifIs1H(pDate, pTime, rDate, rTime) {

    var time1 = new Date(pDate.value + "T" + pTime.value + "Z");
    var time2 = new Date(rDate.value + "T" + rTime.value + "Z");
    if (time2 - time1 >= 3600000) {
        return false;
    } else {
        rTime.classList.add("error-form")
        alert("Your trip times must be within our hours of operation!")
        return true;
    }

}