$(document).on('click','button.ajax',function(e){
    e.preventDefault();
    console.log('jer');

    $order_id = $(this).parent().parent().parent().find('.order_id').text();
    $date= $(this).parent().find('input').val();
    console.log($order_id);
    console.log($date);

    $.ajax({
        url: "/manager/dashboard/status",
        type: "POST",
        dataType: "json",
        data: {
            'order_id':$order_id,
            'date':$date
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
