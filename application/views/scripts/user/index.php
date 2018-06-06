<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://www.paypalobjects.com/api/checkout.js"></script>
</head>

<body>
  <div id="paypal-button"></div>

  <script>
      paypal.Button.render({
      env: 'sandbox', // 'production' Or 'sandbox',

      commit: true, // Show a 'Pay Now' button
      locale: 'fr_FR',
      style: {
        color: 'blue', // gold blue silver black
        size: 'small',
        shape: 'pill' //pill rect
      },

      payment: function() {
        return paypal.request.post('payment.php').then(function(data) {
            return data.id;
        });
      },

      onAuthorize: function(data, actions) {
        return paypal.request.post('pay.php', {
            paymentID: data.paymentID,
            payerID:   data.payerID
        }).then(function(data) {
            console.log(data)
            alert('merci pour votre achat')
        }).catch(function (err) {
            console.log('erreur', err)
        });
      }
    }, '#paypal-button');
  </script>
</body>