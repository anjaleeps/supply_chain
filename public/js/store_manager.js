$('.arrived').on('click', function(e){
    e.preventDefault()
    train_id = $(this).data('id');
    console.log(train_id)
    button = $(this)

    $.ajax({
        url: '/store_manager/train/update',
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