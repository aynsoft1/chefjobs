window.setTimeout(function() {
    $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove();
     $( ".messageStackSuccess" ).addClass( "displayNone" );
     $( ".messageStackError" ).addClass( "displayNone" );
    });
}, 15000);