console.log('ghth');

$('.check').change(function(e){
    e.preventDefault();
    console.log('jer');
    $val = $(this).val();
    console.log($val);
    $status = $(this).is(':checked') ? 1 : 0;
    console.log($status);
    $id = $('#id').val();
    console.log($id);

    $.ajax({
        url: `/driver/${$id}/${$status}/change-status`,
        type: "POST",
        dataType: "json",
        data: {
            'status':$status,
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
