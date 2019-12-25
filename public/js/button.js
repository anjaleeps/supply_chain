$(document).on('click','button.ajax',function(e){
    e.preventDefault();
    console.log('jer');

    $that = $(this).parent().parent().parent().find('.order_id').text();
    $day= new Date($(this).parent().find('input').val());
    console.log($that);
    console.log($day);

    $.ajax({
        url:"{{(path('manager_change_transport'))}}",
        type: "POST",
        dataType: "json",
        data: {
            'order_id':$that,
            'date':$day
            // you can pass some parameters to the controller here
        },
        success: function(data) {
            // change button color
            console.log(data)
            $('div#ajax-results').html(data.output);
        },
        error: function() {
            // show alert or something
        }
    });

});
