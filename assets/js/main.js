document.addEventListener("DOMContentLoaded", function () {

    const stripe = Stripe(STRIPE_KEY);

    document.querySelector(".stripe-btn").addEventListener("click", function (e) {
        e.preventDefault();

        fetch(BASE_URL + "/checkout", {
            method: "POST"
        })
            .then(res => res.json())
            .then(session => {

                return stripe.redirectToCheckout({
                    sessionId: session.id
                });

            })
            .catch(err => console.log(err));

    });

});