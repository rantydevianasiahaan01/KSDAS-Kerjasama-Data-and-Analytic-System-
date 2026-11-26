<div>
    <form wire:submit.prevent="onJoin">
        <div wire:ignore.self class="modal fade" id="joinModal" tabindex="-1" data-bs-backdrop="static"
            data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{-- Modal Header --}}
                    <div class="modal-header modal-colored-header bg-primary text-white sticky-top">
                        <h4 class="modal-title text-white">
                            Masuk ke Nama Cantik
                        </h4>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="modal-body">

                        <div class="alert alert-danger">
                            Setelah masuk ke <strong>Nama Cantik</strong>, kamu tidak dapat lagi keluar. Pastikan kamu masuk ke <strong>Nama Cantik</strong> kamu sendiri.
                        </div>

                        {{-- Cari Nama Cantik --}}
                        <div class="mb-3">
                            <label class="form-label">Cari Nama Cantik</label>
                            <input type="text" class="form-control" placeholder="Cari Nama Cantik"
                                wire:model.live.debounce.500ms="searchNamaCantik">
                        </div>
                        {{-- Pilih Nama Cantik --}}
                        <div class="mb-3">
                            <label class="form-label">Pilih Nama Cantik</label>
                            @if ($searchNamaCantikList && sizeof($searchNamaCantikList) > 0)
                                <select wire:model="dataNamaCantikId" class="form-select" size="5">
                                    @foreach ($searchNamaCantikList as $nc)
                                        <option value="{{ $nc->id }}">
                                            {{ $nc->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <div class="text-muted">
                                    Tidak ada nama cantik yang ditemukan. Silakan cari dengan nama cantik kamu.
                                </div>
                            @endif
                            @error('dataNamaCantikId')
                                <div>
                                    <span class="text-danger">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                    </div>

                    {{-- Modal Footer --}}
                    <div class="modal-footer sticky-bottom bg-light">
                        <button type="submit" class="btn btn-outline-primary" wire:loading.attr="disabled">
                            <span wire:loading wire:target="onJoin"
                                class="spinner-border spinner-border-sm me-1"></span>
                            <span wire:loading.remove wire:target="onJoin">Simpan</span>
                            <span wire:loading wire:target="onJoin">Menyimpan...</span>
                        </button>
                    </div>

                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    </form>
</div>
