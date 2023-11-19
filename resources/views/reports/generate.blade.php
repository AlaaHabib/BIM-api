<div class="container">
    <h1>Transaction and Payment Report</h1>

    @foreach ($reportData as $transaction)
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Transaction ID: {{ $transaction->id }}</h5>
            <p class="card-text">Transaction Description: {{ $transaction->description }}</p>
            <p class="card-text">Transaction Amount: ${{ $transaction->amount }}</p>

            <h6 class="mt-3">Payments:</h6>
            @foreach ($transaction->payments as $payment)
            <ul class="list-group">
                <li class="list-group-item">Payment ID: {{ $payment->id }}</li>
                <li class="list-group-item">Payment Amount: ${{ $payment->amount_paid }}</li>
            </ul>
            @endforeach
        </div>
    </div>
    @endforeach
</div>