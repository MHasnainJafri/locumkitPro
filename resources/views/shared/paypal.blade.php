<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pay with Paypal</title>
</head>

<body>
    <!-- Replace "test" with your own sandbox Business account app client ID -->
    <script src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.sandbox.client_id') }}&currency={{ config('paypal.currency') }}"></script>
    <!-- Set up a container element for the button -->
    <div style="display: flex; justify-content: center; align-items: center; width: 100%; height: 100vh;">
        <div id="paypal-button-container"></div>
    </div>

    <form method="post" style="display: none;" id="paypal-order-completed-form" aria-hidden="true" hidden>
        @csrf
        @method('PUT')
        <input type="hidden" name="package_id" value="{{ $user_acl_package->id }}">
        <input type="hidden" name="transaction_details" id="paypal_transaction_details">
    </form>

    <script>
        paypal.Buttons({
            // Sets up the transaction when a payment button is clicked
            createOrder: (data, actions) => {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: "{{ $amount }}" // Can also reference a variable or function
                        }
                    }]
                });
            },
            // Finalize the transaction after payer approval
            onApprove: (data, actions) => {
                return actions.order.capture().then(function(orderData) {
                    // When ready to go live, remove the alert and show a success message within this page. For example:
                    // Or go to another URL:  actions.redirect("thank_you.html");
                    document.getElementById("paypal_transaction_details").value = JSON.stringify(orderData);
                    document.getElementById("paypal-order-completed-form").submit();
                });
            }
        }).render("#paypal-button-container");
    </script>
</body>

</html>
