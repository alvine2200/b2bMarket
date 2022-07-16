<!DOCTYPE html>
<html>

<head>
    <title>Larave Generate Invoice PDF</title>
</head>
<style type="text/css">
    body {
        font-family: 'Roboto Condensed', sans-serif;
    }

    .m-0 {
        margin: 0px;
    }

    .p-0 {
        padding: 0px;
    }

    .pt-5 {
        padding-top: 5px;
    }

    .mt-10 {
        margin-top: 10px;
    }

    .text-center {
        text-align: center !important;
    }

    .w-100 {
        width: 100%;
    }

    .w-50 {
        width: 50%;
    }

    .w-85 {
        width: 85%;
    }

    .w-15 {
        width: 15%;
    }

    .logo img {
        width: 45px;
        height: 45px;
        padding-top: 30px;
    }

    .logo span {
        margin-left: 8px;
        top: 19px;
        position: absolute;
        font-weight: bold;
        font-size: 25px;
    }

    .gray-color {
        color: #5D5D5D;
    }

    .text-bold {
        font-weight: bold;
    }

    .border {
        border: 1px solid black;
    }

    table tr,
    th,
    td {
        border: 1px solid #d2d2d2;
        border-collapse: collapse;
        padding: 7px 8px;
    }

    table tr th {
        background: #F4F4F4;
        font-size: 15px;
    }

    table tr td {
        font-size: 13px;
    }

    table {
        border-collapse: collapse;
    }

    .box-text p {
        line-height: 10px;
    }

    .float-left {
        float: left;
    }

    .total-part {
        font-size: 16px;
        line-height: 12px;
    }

    .total-right p {
        padding-right: 20px;
    }

    .text-green { color: #30c730 }

    .text-red {color: #f70000}

</style>

<body>
    <div class="head-title">
        <h1 class="text-center m-0 p-0">B2BMarketplace Receipt</h1>
    </div>
    <div class="add-detail mt-10">
        <div class="w-50 float-left mt-10">
            <p class="m-0 pt-5 text-bold w-100">Payee - <span class="gray-color">{{ $payee->full_name }}</span></p>
            <p class="m-0 pt-5 text-bold w-100">Payment Number - <span
                    class="gray-color">{{ $payment->payment_number }}</span>
            <p class="m-0 pt-5 text-bold w-100">Payment Date - <span
                    class="gray-color">{{ $payment->created_at }}</span>
            <p class="m-0 pt-5 text-bold w-100">Order Number - <span
                    class="gray-color">{{ $order->order_number }}</span></p>
            <p class="m-0 pt-5 text-bold w-100">Order Date - <span
                    class="gray-color">{{ $order->created_at }}</span>
            </p>
        </div>
        <div class="w-50 float-left logo mt-10">
            {{-- <img src="https://www.nicesnippets.com/image/imgpsh_fullsize.png"> --}}
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="total-part">
        <div class="total-left w-85 float-left" align="right">
            <p>Paid</p>
            <p>Unpaid</p>
        </div>
        <div class="total-right w-15 float-left text-bold" align="right">
            <p class="text-green">{{ $payment->amount }}</p>
            <p class="text-red">{{$order->unpaid}}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

    <p></p>
    <p>
        Thanks for your purchase!
    </p>
    <p>
        Please do not hesitate to contact us if you have any questions.
    </p>
    </div>

</html>
