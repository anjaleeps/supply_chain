$(document).on('click', 'button.ajax', function (e) {
    e.preventDefault();

    $order_id = $(this).parent().parent().find('.order_id').text();
    console.log($order_id);
    button = $(this)
    cell = $(this).parent()
    $.ajax({
        url: "/manager/train/schedule/new",
        type: "POST",
        dataType: "json",
        data: {
            'order_id': $order_id,
            // you can pass some parameters to the controller here
        },
        success: function (data) {
            // change button color
            console.log(data)
            button.hide()
            content = ""
            content += `<li>Train ID: ${data['id']}</li>`
            content += `<li>Date: ${data['date']}</li>`
            content += `<li>Start Time: ${data['start_time']}</li>`
        
            cell.html(content)
        },
        error: function () {
            // show alert or something
        }
    });

});
