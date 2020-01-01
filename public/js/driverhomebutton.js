$(document).on('click','.ajax',function(e){
    e.preventDefault();
    console.log('jer');

    $clicked= $(this).val();
    console.log($clicked);
    $user_id = $('#user_id').val();
    $truck_schedule_id = $('#truck_schedule_id').val();
    console.log($user_id);
    console.log($truck_schedule_id);

    button = $(this)

    $.ajax({
        url: `/driver/${$user_id}/${$truck_schedule_id}/status`,
        type: "POST",
        dataType: "json",
        data: {
            'status':$clicked
            // you can pass some parameters to the controller here
        },
        success: function(data) {
            // change button color
            console.log(data)
            button.hide()
        },
        error: function() {
            // show alert or something
        }
    });

});
