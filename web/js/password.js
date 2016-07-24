function GeneratePassword() {

    if (parseInt(navigator.appVersion) <= 3) {
        alert("Sorry this only works in 4.0+ browsers");
        return true;
    }

    var length=12;
    var sPassword = "";

    var noPunction = true;
    var randomLength = false;

    if (randomLength) {
        length = Math.random();

        length = parseInt(length * 100);
        length = (length % 7) + 6
    }

    for (i=0; i < length; i++) {

        numI = getRandomNum();
        if (noPunction) { while (checkPunc(numI)) { numI = getRandomNum(); } }

        sPassword = sPassword + String.fromCharCode(numI);
    }

    return sPassword;
}

function algo_sha1(name, pass) {
    var hash = CryptoJS.SHA1(pass);
    return name + ':{SHA}' + hash.toString(CryptoJS.enc.Base64);
}    

function getRandomNum() {

    // between 0 - 1
    var rndNum = Math.random()

    // rndNum from 0 - 1000
    rndNum = parseInt(rndNum * 1000);

    // rndNum from 33 - 127
    rndNum = (rndNum % 94) + 33;

    return rndNum;
}

function checkPunc(num) {

    if ((num >=33) && (num <=47)) { return true; }
    if ((num >=58) && (num <=64)) { return true; }
    if ((num >=91) && (num <=96)) { return true; }
    if ((num >=123) && (num <=126)) { return true; }

    return false;
}
  
function S4() {
   return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
}
function guid() {
   return (S4()+S4()+"-"+S4()+"-"+S4()+"-"+S4()+"-"+S4()+S4()+S4());
}

