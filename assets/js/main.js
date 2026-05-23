document.addEventListener("DOMContentLoaded", function () {

    const stripe = Stripe(STRIPE_KEY);
    const btn = document.querySelector(".stripe-btn");

    if (!btn) {
        console.error("Stripe button not found");
        return;
    }

    btn.addEventListener("click", function (e) {
        e.preventDefault();
        fetch(BASE_URL + "/checkout", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                name:        document.querySelector("[name=name]").value,
                email:       document.querySelector("[name=email]").value,
                address:     document.querySelector("[name=address]").value,
                city:        document.querySelector("[name=city]").value,
                postal_code: document.querySelector("[name=postal_code]").value
            })
        })
            .then(async (res) => {

                const text = await res.text(); // 👈 IMPORTANT DEBUG STEP
                console.log("RAW PHP RESPONSE:", text);

                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    throw new Error("PHP did not return JSON");
                }

                console.log("PARSED DATA:", data);

                if (!data.id) {
                    throw new Error(data.error || "Session ID missing from PHP");
                }

                return stripe.redirectToCheckout({
                    sessionId: data.id
                });
            })
            .catch(err => {
                console.error("Checkout Error:", err.message);
            });

    });

});
