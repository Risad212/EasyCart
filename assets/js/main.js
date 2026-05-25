document.addEventListener("DOMContentLoaded", function () {

    const stripe = Stripe(STRIPE_KEY);
    const button = document.querySelector(".stripe-btn");

    if (!button) {
        console.error("Stripe button not found");
        return;
    }

    button.addEventListener("click", async function (e) {
        e.preventDefault();

        try {
         const response = await fetch(BASE_URL + "/checkout", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                name:        document.querySelector(".checkout-form [name=name]").value,
                email:       document.querySelector(".checkout-form [name=email]").value,
                address:     document.querySelector(".checkout-form [name=address]").value,
                city:        document.querySelector(".checkout-form [name=city]").value,
                postal_code: document.querySelector(".checkout-form [name=postal_code]").value
            })
          })
           const data = await res.json();
           
           if(!data){
             throw new Error(data.error || "Session ID missing")
           }
           
           await stripe.redirectToCheckout({ sessionId: data.id });
        }
         catch (err) {
            console.error("Checkout Error:", err.message);
        }

    });

});
