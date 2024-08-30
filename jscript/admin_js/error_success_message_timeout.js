
window.onload = function(){ 
    var button = document.querySelector(".close");
    var messageStack = document.querySelector(".messageStackError");
    // var successMessage = document.querySelector(".messageStackSuccess");
    // console.log(successMessage.className);
    
    button.onclick = function () { 
        
        if (messageStack.classList.contains("messageStackError")) {
            messageStack.classList.add("displayNone");   
        }
        // else if (successMessage.classList.contains("messageStackSuccess")) {
        //     successMessage.classList.add("displayNone");
        // } else if (div.classList.contains("alert")) {
        //     div.classList.add("displayNone");
        // } 
        // else {
        //     this.parentElement.classList.add("displayNone");
        // }
        
    };

};

function successMessg(){ 
    var button = document.querySelector(".close");
    var successMessage = document.querySelector(".messageStackSuccess");
    button.onclick = function () { 
        
        if (successMessage.classList.contains("messageStackSuccess")) {
            successMessage.classList.add("displayNone");
        } 
        
    };

};
window.addEventListener("click", successMessg);

window.setTimeout(function() {
    $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove();
     $( ".messageStackSuccess" ).addClass( "displayNone" );
     $( ".messageStackError" ).addClass( "displayNone" );
    });
}, 30000);
