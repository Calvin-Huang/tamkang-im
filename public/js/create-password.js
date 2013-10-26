$('#random-password').on('click', function(){
    console.log(getRandomString());
});


function getRandomString(length){
    var settingString = 'abcdefghijklmnopqrstuvwxyz1234567890';
    var returnString = '';

    for (var i = 0; i < length; i++) {
        returnString += settingString.substr(Math.round(Math.random() * settingString.length));
    }

    return returnString;
}