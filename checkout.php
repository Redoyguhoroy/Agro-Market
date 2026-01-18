<h2 style="text-align:center; font-family:Arial; margin-top:20px;">Checkout</h2>

<style>
    body {
        background: #f2f2f2;
        font-family: Arial, sans-serif;
    }

    .checkout-box {
        width: 450px;
        margin: 40px auto;
        background: #ffffff;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        animation: fadeIn 0.8s ease-in-out;
    }

    .checkout-box h3 {
        text-align: center;
        font-size: 24px;
        margin-bottom: 25px;
        color: #28a745;
    }

    .input-group {
        margin-bottom: 18px;
    }

    .input-group label {
        font-weight: bold;
        display: block;
        margin-bottom: 6px;
    }

    .input-group input,
    .input-group select,
    .input-group textarea {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #ccc;
        transition: 0.3s;
        box-sizing: border-box;
    }

    .input-group input:focus,
    .input-group select:focus,
    .input-group textarea:focus {
        border-color: #28a745;
        box-shadow: 0 0 8px #28a74550;
    }

    .payment-methods {
        margin-bottom: 15px;
    }

    .payment-methods label {
        margin-right: 15px;
        font-weight: bold;
    }

    .payment-number {
        font-weight: bold;
        color: #e40b16;
        margin-bottom: 10px;
        font-size: 16px;
    }

    .pay-btn {
        width: 100%;
        padding: 14px;
        border: none;
        background: linear-gradient(45deg, #28a745, #2ecc71);
        color: white;
        font-size: 18px;
        border-radius: 10px;
        cursor: pointer;
        transition: 0.3s;
    }

    .pay-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 22px rgba(40, 167, 69, 0.5);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="checkout-box">
    <h3>Secure Payment</h3>

    <form method="POST">
        <div class="input-group">
            <label>Amount</label>
            <input type="text" name="amount" required>
        </div>

        <div class="input-group">
            <label>Full Name</label>
            <input type="text" name="name" required>
        </div>

        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="input-group">
            <label>Phone</label>
            <input type="text" name="phone" required>
        </div>

        <div class="input-group">
            <label>Address</label>
            <textarea name="address" rows="2" required></textarea>
        </div>

        <div class="input-group">
            <label>Product ID</label>
            <input type="number" name="product_id" required>
        </div>

        <div class="input-group">
            <label>Buyer ID</label>
            <input type="number" name="buyer_id" required>
        </div>

        <div class="input-group">
            <label>Quantity</label>
            <input type="number" name="qty" required>
        </div>

        <div class="payment-methods">
            <label><input type="radio" name="payment_method" value="bkash" required onclick="showPaymentNumber(this.value)"> bKash</label>
            <label><input type="radio" name="payment_method" value="nagad" required onclick="showPaymentNumber(this.value)"> Nagad</label>
            <label><input type="radio" name="payment_method" value="rocket" required onclick="showPaymentNumber(this.value)"> Rocket</label>
        </div>

        <div id="payment-number" class="payment-number"></div>

        <div class="input-group">
            <label>Transaction ID</label>
            <input type="text" name="trx_id" required placeholder="Enter your transaction ID">
        </div>

        <button type="submit" name="pay_now" class="pay-btn">Pay Now</button>
    </form>
</div>

<div class="payment-methods">
    <label><input type="radio" name="payment_method" value="bkash" onclick="showPaymentNumber(this.value)"> bKash</label><br>
    <label><input type="radio" name="payment_method" value="nagad" onclick="showPaymentNumber(this.value)"> Nagad</label><br>
    <label><input type="radio" name="payment_method" value="rocket" onclick="showPaymentNumber(this.value)"> Rocket</label>
</div>

<div id="payment-number" class="payment-number" style="text-align:center; margin:10px 0; font-weight:bold; color:#e40b16; font-size:16px;"></div>

<script>
function showPaymentNumber(method){
    let number = "";
    if(method === "bkash") number = "Send money to bKash: 01745985077";
    if(method === "nagad") number = "Send money to Nagad: 01313731493";
    if(method === "rocket") number = "Send money to Rocket: 01745985077";
    document.getElementById("payment-number").innerText = number;
}
</script>


