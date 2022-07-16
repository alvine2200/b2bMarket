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

</style>

<body>
    <div class="head-title">
        <h1 class="text-center m-0 p-0">B2BMarketplace Invoice</h1>
    </div>
    <div class="add-detail mt-10">
        <div class="w-50 float-left mt-10">
            <p class="m-0 pt-5 text-bold w-100">Invoice Number - <span
                    class="gray-color">{{ $invoice->invoice_number }}</span></p>
            <p class="m-0 pt-5 text-bold w-100">Order Number - <span
                    class="gray-color">{{ $order->order_number }}</span></p>
            <p class="m-0 pt-5 text-bold w-100">Order Date - <span class="gray-color">{{ $order->created_at }}</span>
            </p>
        </div>
        <div class="w-50 float-left logo mt-10">
            {{-- <img src="https://www.nicesnippets.com/image/imgpsh_fullsize.png"> --}}
        </div>
        <div style="clear: both;"></div>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">From</th>
                <th class="w-50">To</th>
            </tr>
            <tr>
                <td>
                    <div class="box-text">
                        <p>B2BMarketplace</p>
                    </div>
                </td>
                <td>
                    <div class="box-text">
                        <p>{{ $recipient->full_name }}</p>
                        <p>P.O. BOX : {{ $address->postal_code }}</p>
                        <p>{{ $address->city }}</p>
                        <p>{{ $address->country }}</p>
                        <p>Contact : {{ $address->phone }}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">Payment Mode</th>
                <th class="w-50">Delivery Mode</th>
            </tr>
            <tr>
                <td>{{ $order->payment_mode }}</td>
                <td>{{ $order->delivery_mode }}</td>
            </tr>
        </table>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">SKU</th>
                <th class="w-50">Product Name</th>
                <th class="w-50">Price</th>
                <th class="w-50">Qty</th>
                <th class="w-50">Subtotal</th>
                <th class="w-50">Tax Amount</th>
                <th class="w-50">Grand Total</th>
            </tr>
            @php
                $sub_total = 0;
                $tax = 0;
                $total = 0;
            @endphp
            @foreach ($order->products as $product)
                <tr align="center">
                    @php
                        $product_sub_total = $product->pivot->quantity * $product->unit_price;
                        $product_tax = $product->pivot->quantity * $product->tax;
                        $product_total = $product_sub_total + $product_tax;
                        
                        $sub_total += $product_sub_total;
                        $tax += $product_tax;
                        $total += $product_sub_total + $product_tax;
                    @endphp
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->unit_price }}</td>
                    <td>{{ $product->pivot->quantity }}</td>
                    <td>{{ $product_sub_total }}</td>
                    <td>{{ $product->tax }}</td>
                    <td>{{ $product_total }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="7">
                    <div class="total-part">
                        <div class="total-left w-85 float-left" align="right">
                            <p>Sub Total</p>
                            <p>Tax</p>
                            <p>Total Payable</p>
                        </div>
                        <div class="total-right w-15 float-left text-bold" align="right">
                            <p>{{ $sub_total }}</p>
                            <p>{{ $tax }}</p>
                            <p>{{ $total }}</p>
                        </div>
                        <div style="clear: both;"></div>
                    </div>
                </td>
            </tr>
        </table>

        <p>
            Thanks for your purchase!
        </p>
        <p>
            Please do not hesitate to contact us if you have any questions.
        </p>
    </div>

</html>
