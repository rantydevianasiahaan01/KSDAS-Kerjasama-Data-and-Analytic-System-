<div class="card-body">
    <a href="" class="text-nowrap logo-img text-center mb-4 d-block w-100">
        <img src="/img/logo-dark-text.png" class="dark-logo" style="height: 72px;" alt="Logo-dark" />
    </a>

    <div class="position-relative text-center">
        <p class="mb-0 fs-4 px-3 d-inline-block bg-body text-dark z-index-5 position-relative">
            Gunakan kredensial akun untuk melanjutkan
        </p>
        <span class="border-top w-100 position-absolute top-50 start-50 translate-middle"></span>
    </div>

    <div class="mb-4"></div>

    @if (!empty($globalErrorMessage))
        <div class="alert alert-danger" role="alert">
            {{ $globalErrorMessage }}
        </div>
    @endif

    <form wire:submit.prevent="submit">
        <!-- Elemen tersembunyi untuk binding Livewire -->
        <input type="hidden" wire:model="systemId" id="systemIdInput">
        <input type="hidden" wire:model="info" id="infoInput">

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control @error('username') is-invalid @enderror" wire:model="username">
            @error('username')
                <div>
                    <span class="text-danger">{{ $message }}</span>
                </div>
            @enderror
            @error('systemId')
                <div>
                    <span class="text-danger">{{ $message }}</span>
                </div>
            @enderror
            @error('info')
                <div>
                    <span class="text-danger">{{ $message }}</span>
                </div>
            @enderror
        </div>
        <div class="mb-4">
            <label class="form-label">Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" wire:model="password">
            @error('password')
                <div>
                    <span class="text-danger">{{ $message }}</span>
                </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100 py-8 rounded-2">
            Masuk
        </button>

        <div class="position-relative text-center my-2">
            <p class="mb-0 fs-4 px-3 d-inline-block bg-body text-dark z-index-5 position-relative">
                atau masuk dengan
            </p>
            <span class="border-top w-100 position-absolute top-50 start-50 translate-middle"></span>
        </div>

        <a href="{{ route('auth.login.qrcode') }}" class="btn btn-success w-100 py-8 mb-3 rounded-2">
            Scan QRCode
        </a>
    </form>

    <div class="d-flex align-items-center justify-content-center">
        <p class="mb-0 fw-medium">Belum Memiliki Akun?</p>
        <a class="text-primary fw-medium ms-2" href="{{ route('auth.register') }}">Daftar dengan Akun CIS</a>
    </div>
</div>

@section('others-js')
    <script src="/assets/vendor/crypto-js-4.2.0/crypto-js.js"></script>
    <script src="/scripts/auth/login-livewire-v1.js"></script>
@endsection
