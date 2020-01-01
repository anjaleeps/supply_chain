function addItem(id, pr_name, unitPrice, picture){

    var userid = document.getElementById("user").innerHTML;


    if(userid===""){
      alert("you have to log in first");
    }

    else{
    var customerID = document.getElementById("customerId").innerHTML;
    var name = "cartKeyOf:"
    var key = name.concat(customerID);

    contents = [];

    let _contents = localStorage.getItem(key);
    if(_contents){
      contents = JSON.parse(_contents);
    }

    if(contents.length===0){
      var productDetails = {id:id, name:pr_name, quantity:1, unitPrice:unitPrice, picture:picture};
      contents.push(productDetails);
    }
    else{
    var T = 0;
    contents.forEach( item =>{
        if(item.id==id){
          item.quantity++
          T = 1;
        }  
      })
    if(T===0){
        var productDetails = {id:id, name:pr_name, quantity:1, unitPrice:unitPrice, picture:picture};
        contents.push(productDetails);   
      }
    }

    let _cart = JSON.stringify(contents);
    localStorage.setItem(key, _cart);
    alert("Added to cart");
    }
    
    return false;
  }
