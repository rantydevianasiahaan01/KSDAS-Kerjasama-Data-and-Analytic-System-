<div>
    <div class="card">
        <div class="card-header">
            <div class="d-flex">
                <div class="flex-fill">
                    <h3 class="mb-0">
                        <i class="ti ti-home"></i>
                        Beranda
                    </h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            @php($nama = trim((string) data_get($auth, 'name')))
            <p class="fs-5 fw-semibold mb-0">Selamat datang{{ $nama !== '' ? ', '.$nama : '' }}</p>
        </div>
    </div>
</div>
