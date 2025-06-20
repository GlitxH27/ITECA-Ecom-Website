<?php include('layouts/header.php'); ?>

<?php



if(isset($_POST['order_pay_btn']) ){
    $order_status = $_POST['order_status'];
    $order_total_price = $_POST['order_total_price'];
} 


?>



<!--Payment-->
<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="form-weight-bold">Payment</h2>
        <hr class="mx-auto">
    </div>
    <div class="mx-auto container text-center">

         <?php if(isset($_POST['order_status']) && $_POST['order_status'] == "not paid"){ ?>
            <?php $amount = strval($_POST['order_total_price']); ?>
            <?php $order_id = $_POST['order_id'] ?>

            <p>Total payment: R<?php echo $_POST['order_total_price']; ?></p>
            <!--<input class="btn btn-primary" type="submit" value="Pay Now">-->
            <div class="d-flex justify-content-center mt-3">
                <div id="paypal-button-container" style="width: 350px;"></div>
            </div>


        <?php } else if(isset($_SESSION['total']) && $_SESSION['total'] != 0){ ?>
            <?php $amount = strval($_SESSION['total']); ?>
            <?php $order_id = $_SESSION['order_id']; ?>

         <p>Total payment: R<?php echo $_SESSION['total'];?></p>
         <!-- <input class="btn btn-primary" type="submit" value="Pay Now">-->
         <div class="d-flex justify-content-center mt-3">
            <div id="paypal-button-container" style="width: 350px;"></div>
         </div>


         <?php } else{ ?>
            <p>You dont have an order</p>
         <?php } ?>  

         

    </div>
       
</section>

        <!-- Initialize the JS-SDK -->
        <script
            src="https://www.paypal.com/sdk/js?client-id=AQZrlocH9_Yw4TAJS-Ap5hwk67CnyOAfvp_WGsK63sehhiWh6IvaPBXfGrPx0AU_pKrv07w4gjmnttlL&currency=USD"
            data-sdk-integration-source="developer-studio"
        ></script>

<script>
        // Get PHP amount into JavaScript
        //const amountToPay = "<?php echo $amount; ?>";

        // Render PayPal Buttons
        paypal.Buttons({
            style: {
                shape: 'rect',
                layout: 'vertical',
                color: 'gold',
                label: 'paypal'
            },
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?php echo $amount; ?>'
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    // ✅ Extract transaction ID safely
                const transaction = details.purchase_units[0].payments.captures[0];
                    // Show confirmation or redirect
                    alert('Transaction completed: ID ' + transaction.id + ' | Payer: ' + details.payer.name.given_name);
                    console.log(details);

                    window.location.href = "server/complete_payment.php?transaction_id="+ transaction.id +"&order_id="+<?php echo $order_id;?>;
                    // Optionally: redirect to a thank-you page
                    // window.location.href = "thank_you.php";
                });
            },
            onError: function(err) {
                console.error('PayPal Checkout Error:', err);
            }
        }).render('#paypal-button-container');
</script>





<?php include('layouts/footer.php'); ?>