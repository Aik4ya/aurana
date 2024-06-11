function eMailValid (emailstring){
    var email = emailstring;
    var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/; // ^ signifie que l'expression doit commencer par ce qui suit, $ signifie que l'expression doit finir par ce qui précède
    return emailPattern.test(email);
}

function passwordValid (passwordstring){
    var password = passwordstring;
    var passwordPattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/; // 
    return passwordPattern.test(password);
}

function isvalidphone (phonestring){
    var phone = phonestring;
    var phonePattern = /^[0-9]{10}$/;
    return phonePattern.test(phone);
}

function isvaliddate (datestring){
    var date = datestring;
    var datePattern = /^\d{4}-\d{2}-\d{2}$/;
    return datePattern.test(date);
}
