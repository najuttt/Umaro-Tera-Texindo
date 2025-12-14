@extends('layouts.index')
@section('title', 'Pembukuan')
@section('content')

<div class="container-fluid py-4">

    <h4 class="mb-4">📒 Input Pembukuan</h4>

    {{-- INPUT HPP --}}
    <div class="card mb-4">
        <div class="card-header fw-bold">Input HPP</div>
        <div class="card-body">
            <form action="{{ route('super_admin.pembukuan.storeHpp') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label>Tanggal</label>
                    <input type="date" name="date" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Total HPP</label>
                    <input type="number" name="hpp_total" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Catatan</label>
                    <input type="text" name="note" class="form-control">
                </div>

                <button class="btn btn-primary">Simpan HPP</button>
            </form>
        </div>
    </div>

    {{-- INPUT PENGELUARAN --}}
    <div class="card mb-4">
        <div class="card-header fw-bold">Input Pengeluaran</div>
        <div class="card-body">
            <form action="{{ route('super_admin.pembukuan.storeExpense') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label>Tanggal</label>
                    <input type="date" name="date" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Keterangan</label>
                    <input type="text" name="description" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Nominal</label>
                    <input type="number" name="amount" class="form-control" required>
                </div>

                <button class="btn btn-danger">Simpan Pengeluaran</button>
            </form>
        </div>
    </div>
    {{-- EXPORT --}}
    <div class="card">
        <div class="card-header fw-bold">Export Pembukuan</div>
        <div class="card-body">
            <form action="{{ route('super_admin.pembukuan.export') }}" method="GET">
                <div class="mb-3">
                    <label>Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Sampe Tanggal</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
                <button class="btn btn-success">Download PDF</button>
            </form>
        </div>
    </div>
</div>

@endsection
