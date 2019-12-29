$('.checkbox').change(function(e){
    e.preventDefault();
    console.log('jer');

    $order_id=$(this).val();
    console.log($order_id);

    $.ajax({
        url: `/driver/${$order_id}/orderdelivered`,
        type: "POST",
        dataType: "json",
        data: {
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
