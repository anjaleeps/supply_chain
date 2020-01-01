var id = document.getElementById("customerId").innerHTML;
var name = "cartKeyOf:"
var key = name.concat(id);

const CART = {
    KEY: key,
    contents: [],
    init(){
        let stor = localStorage.getItem(CART.KEY);
        if(stor){
            CART.contents = JSON.parse(stor);
        }                
    },
    async sync(){
        let _cart = JSON.stringify(CART.contents);
        await localStorage.setItem(CART.KEY, _cart);
    },
    add(){
                let obj = {
                    id: "12",
                    title: "name",
                    qty: 1,
                    itemPrice: 120
                };
                CART.contents.push(obj);
                CART.sync();
    },
    remove(id){
        CART.contents = CART.contents.filter(item=>{
            if(item.id !== id)
                return true;
        });
        CART.sync()
    },
    empty(){
        //empty whole cart
        CART.contents = [];
        CART.sync()
    },
    increase(id, qty=1){
        CART.contents = CART.contents.map(item=>{
            if(item.id === id)
                item.quantity++;
            return item;
        });
        CART.sync()
    },
    reduce(id, qty=1){
        CART.contents = CART.contents.map(item=>{
            if(item.id === id)
                item.quantity--;
            return item;   
            
            return item;             
        });
        CART.contents.forEach(async item=>{
            if(item.id === id && item.quantity === 0)
                await CART.remove(id);
        });
        CART.sync()
    }
}

document.addEventListener('DOMContentLoaded', ()=>{
    CART.init();
    showCart();
});

function removeItem(ev){
    ev.preventDefault();
    let id = parseInt(ev.target.getAttribute('data-id'));
    CART.remove(id);
    CART.init();
    showCart();
}

function increment(ev){
    ev.preventDefault();
    let id = parseInt(ev.target.getAttribute('data-id'));
    CART.increase(id, 1);
    CART.init();
    showCart();
}

function decrement(ev){
    ev.preventDefault();
    let id = parseInt(ev.target.getAttribute('data-id'));
    CART.reduce(id, 1);
    CART.init();
    showCart();
}

function emptyCart(){
    localStorage.removeItem(CART.KEY);
    CART.contents = [];
    CART.init();
    showCart();
}

function showCart(){
    let cartitems = CART.contents;
    if(cartitems.length===0){
        let main = document.getElementById('main');
        let body = document.getElementById('cart_content');
        body.innerHTML = '';
        let empty = document.createElement('h4');
        empty.textContent = "Your cart is Empty";
        main.appendChild(empty);
    }
    else{
    let cartTable = document.getElementById('cartTable');
    cartTable.innerHTML = '';
    let pr_name = document.createElement('th');
    pr_name.textContent = 'Product Name';
    cartTable.appendChild(pr_name);

    let pr_q = document.createElement('th');
    pr_q.textContent = 'Quantity';
    cartTable.appendChild(pr_q);

    let pr_up = document.createElement('th');
    pr_up.textContent = 'Unit Price';
    cartTable.appendChild(pr_up);

    let pr_p = document.createElement('th');
    pr_p.textContent = 'Product Price';
    cartTable.appendChild(pr_p);

    let pr_r = document.createElement('th');
    pr_r.textContent = 'Remove';
    cartTable.appendChild(pr_r);

    cartitems.forEach( item =>{
        let one = document.createElement('tbody');
        one.className = "tbl-cart";

        let name = document.createElement('td');
        let image = document.createElement('img');
        image.setAttribute('src', '/images/item.picture');
        image.setAttribute('alt', 'image');
        name.appendChild(image);
        name.textContent = item.name;
        one.appendChild(name);

        let quantity = document.createElement('td');
        quantity.textContent = item.quantity;
        one.appendChild(quantity);

        let uprice = document.createElement('td');
        uprice.textContent = item.unitPrice;
        one.appendChild(uprice);

        let price = document.createElement('td');
        price.textContent = quantity.textContent*uprice.textContent;
        one.appendChild(price);

        let row = document.createElement('td');
        let remove = document.createElement('button');
        remove.textContent = "remove";
        remove.setAttribute('data-id', item.id)
        remove.addEventListener('click', removeItem);
        row.appendChild(remove);

        let inc = document.createElement('button');
        inc.textContent = "+";
        inc.setAttribute('data-id', item.id)
        inc.addEventListener('click', increment);
        row.appendChild(inc);

        let dec = document.createElement('button');
        dec.textContent = "-";
        dec.setAttribute('data-id', item.id)
        dec.addEventListener('click', decrement);
        row.appendChild(dec);

        one.appendChild(row);
    
        cartTable.appendChild(one);

    })

}
    
}






