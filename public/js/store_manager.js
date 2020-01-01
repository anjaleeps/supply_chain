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
    e.preventDefault()

    route = $('#route_selection')
    driver = $('#driver_selection')
    assistant = $('#assistant_selection')
    truck = $('#truck_selection')

    //console.log(route_id, driver_id, assistant_id, truck_id)

    if (route.val() != null && driver.val() != null && assistant.val() != null && truck.val() != null){

        $.ajax({
            url: '/store_manager/truck/schedule/new',
            type: 'POST',
            data: {
                'route_id': route.val(),
                'driver_id': driver.val(),
                'assistant_id': assistant.val(),
                'truck_id': truck.val()
            },
            success: function(data){
                route.find("option:selected").remove()
                driver.find("option:selected").remove()
                assistant.find("option:selected").remove()
                truck.find("option:selected").remove()

                route.find('option:eq(0)').prop('selected', true);                
                driver.find('option:eq(0)').prop('selected', true);                
                assistant.find('option:eq(0)').prop('selected', true);                
                truck.find('option:eq(0)').prop('selected', true);               
               
            },
            error: function(){

            }
        })
    }
    
})













