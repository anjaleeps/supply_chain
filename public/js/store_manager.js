$('.arrived').on('click', function(e){
    e.preventDefault()
    train_id = $(this).data('id');
    console.log(train_id)
    button = $(this)

    $.ajax({
        url: '/store_manager/train/schedule/edit',
        type: 'POST',
        data: {
            'train_id': train_id
        },
        success: function (data){
            console.log(data)
            button.hide()
        },
        error: function(data){
         
        }
    })
})

$('.schedule').on('click', function(e){
    //e.preventDefault()

    route_id = $('#route_selection').val();
    driver_id = $('#driver_selection').val();
    assistant_id = $('#assistant_selection').val();
    truck_id = $('#truck_selection').val()

    console.log(route_id, driver_id, assistant_id, truck_id)

    if (route_id != null && driver_id != null && assistant_id != null && truck_id != null){

        $.ajax({
            url: '/store_manager/truck/schedule/new',
            type: 'POST',
            data: {
                'route_id': route_id,
                'driver_id': driver_id,
                'assistant_id': assistant_id,
                'truck_id': truck_id
            },
            success: function(data){

            },
            error: function(){

            }
        })
        return false
    }
    else{
        e.preventDefault()
    }
})













