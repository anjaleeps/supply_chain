var id = document.getElementById("customerId").innerHTML;
    var name = "cartKeyOf:"
    var key = name.concat(id);

    var products = [];
    let stor = localStorage.getItem(key);
    products = JSON.parse(stor);

    var tot_quantity = 0;
    var tot_price = 0;

    products.forEach(item=>{
      tot_quantity = tot_quantity + item.quantity;
      tot_price = tot_price + (item.unitPrice * item.quantity)
    })

    var r_id = document.getElementById("route").value;
    _contents = {};
    _contents.products = products;
    _contents.route_id = r_id;
    
document.addEventListener('DOMContentLoaded', ()=>{
    showOrder();
});

function showOrder(){
  let tot_q = document.getElementById('tot_q');
  tot_q.textContent = tot_quantity;
  let tot_p = document.getElementById('tot_p');
  tot_p.textContent = tot_price;
}

function checkout(){ 
  if(confirm("confirm to place order?")){
    localStorage.removeItem(key);
    var contents = JSON.stringify(_contents);

    $.ajax({
        url: `/orders/placeOrder/${id}`,
        type: "POST",
        dataType: "json",
        data: {
            'details' : contents,                    
        },
        success: function () {
            alert("Order placed successfully");
            window.location.href = "/customerHome";

        },
        error: function () {
            alert('error');
        }
    });
  }
}