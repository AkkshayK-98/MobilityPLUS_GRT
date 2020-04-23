
//reset error when user chages value
const inputs = document.querySelectorAll("input");
for (let i = 0; i < inputs.length; i++) {
    inputs[i].addEventListener("blur", function () {
        inputs[i].classList.remove("error-form");
    });
}

//confirm before leaving page with out submiting
const dashBtn = document.querySelector("#bookingNavBtnTdash")
dashBtn.addEventListener("click", function () {
    var answer = confirm("You have not submitted you booking yet, all entered information will be lost. Are you sure you want to go to the dashboard?")
    if (answer) {
        window.location.href = 'dashboard.html';
    }
});

const conBtn = document.querySelector("#bookingNavBtnCon")
conBtn.addEventListener("click", function () {
    var answer = confirm("You have not submitted you booking yet, all entered information will be lost. Are you sure you want to go to the contact page?")
    if (answer) {
        window.location.href = 'https://www.grt.ca/en/about-grt/contact-us.aspx';
    }
});

hasErrors = false;

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
    
    //sanitize inputs that user freely type in (postak checked later)
    pAdd.value=sanitize(pAdd.value)
    pNotes.value=sanitize(pNotes.value)
    rAdd.value=sanitize(rAdd.value)
    rNotes.value=sanitize(rNotes.value)
    
    
    //setup error style
    var styleError = document.createElement('style');
    styleError.type = 'text/css';
    styleError.innerHTML = '.error-form{border: 2pt solid red;  background-color: #FFCDD2; background-image: url(images/error.png);  background-repeat: no-repeat;  background-position: right;}';
    document.getElementsByTagName('head')[0].appendChild(styleError);


    hasErrors = false;
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

    try {
        pTime.value = timeConvertor(pTime.value)
        rTime.value = timeConvertor(rTime.value)
    } catch (error) {
        hasErrors = true;
    }

    //check for valid postal fotmat
    checkPostal(pPostal);
    checkPostal(rPostal);

    //check that pick date is one day advance
    dateInFuture(pDate);
    dateInFuture(rDate);

    //check that return date is greater or equal to start date
    pickBeforeReturnDate(pDate, rDate);

    //check if pick and return times are in hours of op
    timeInHours(pTime)
    timeInHours(rTime)

    //check if return time is an hour after pickup
    timeDifIs1H(pDate, pTime, rDate, rTime);
    //check check box



    var agreeBox = document.querySelector("#checkAgree");

    if (agreeBox.checked == false) {

        alert("You must agree to terms of the site to continue!");
        hasErrors = true;
    }

    //captcha
    var rcres = grecaptcha.getResponse();
    if (rcres.length) {
        grecaptcha.reset();
    } else {
        hasErrors = true;
        alert("Please check the 'I'm not a robot checkbox!")
    }
    console.log(hasErrors)
    if (hasErrors) {
        console.log("Error")
        event.preventDefault();

    } else {

        console.log("Good")
        // hasErrors = true;

    }


}

function sanitize(str){
    var replaced = str.replace(/[\+\>\<\'\/\\"]+/g, '');
    return replaced
}
function fieldEmpty(field) {
    //console.log(field)
    if (field.value === "") {
        field.classList.add("error-form")
        hasErrors = true;
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
        hasErrors = true;
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
        hasErrors = true;
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
        hasErrors = true;
    }

}
function timeInHours(field) {
    console.log(field.value)
    var time = new Date("2015-03-25T" + field.value + "Z");
    var openTime = new Date("2015-03-25T05:15:00Z");
    var closeTime = new Date("2015-03-25T23:59:59Z");
    console.log(time)
    if (time >= openTime && time <= closeTime) {
        return false;
    } else {
        field.classList.add("error-form")
        alert("Your trip times must be within our hours of operation!")
        hasErrors = true;
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
        hasErrors = true;
    }

}
function timeConvertor(time) {
    var PM = time.match('PM') ? true : false
    var AM = time.match('AM') ? true : false
    if(!AM && !PM){
        return time
    }
    time = time.split(':')

    var min = time[1]

    if (PM) {
        var hour = parseInt(time[0], 10)
        if (parseInt(time[0], 10) < 12) {
            hour=hour+12
        }
        var min = min.replace(' PM', '')
    } else {
        var hour = time[0]
        var min = min.replace(' AM', '')
    }
    t = hour + ':' + min + ":00"
    console.log(t)
    return t
}
