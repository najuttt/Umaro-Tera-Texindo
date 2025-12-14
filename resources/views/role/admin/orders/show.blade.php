<div class="modal-header">
    <h5 class="modal-title">Order #{{ $order->id }} - {{ ucfirst($order->status) }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <p><strong>Nama:</strong> {{ $order->customer_name }}</p>
    <p><strong>HP:</strong> {{ $order->customer_phone }}</p>
    <p><strong>Alamat:</strong> {{ $order->customer_address }}</p>
    <p><strong>Dibuat:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>

    <hr>

    <h6>Items</h6>
    <table class="table table-sm">
        <thead>
            <tr><th>Nama</th><th>Jumlah</th></tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $oi)
                <tr>
                    <td>{{ $oi->item->name ?? '(deleted item)' }}</td>
                    <td>{{ $oi->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
</div>
