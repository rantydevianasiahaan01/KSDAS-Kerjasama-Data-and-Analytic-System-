<div>
    <form wire:submit.prevent="onAddMember">
        <div wire:ignore.self class="modal fade" id="addMemberModal" tabindex="-1" data-bs-backdrop="static"
            data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{-- Modal Header --}}
                    <div class="modal-header modal-colored-header bg-primary text-white sticky-top">
                        <h4 class="modal-title text-white">
                            Tambah Anggota Nama Cantik
                        </h4>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="modal-body">

                        {{-- Cari Pengguna --}}
                        <div class="mb-3">
                            <label class="form-label">Cari Pengguna</label>
                            <input type="text" class="form-control" placeholder="Cari Pengguna"
                                wire:model.live.debounce.500ms="searchPengguna">
                        </div>
                        {{-- Pilih Pengguna --}}
                        <div class="mb-3">
                            <label class="form-label">Pilih Pengguna</label>
                            @if ($searchPenggunaList && sizeof($searchPenggunaList) > 0)
                                <select wire:model="dataUserId" class="form-select" size="5">
                                    @foreach ($searchPenggunaList as $pengguna)
                                        <option value="{{ $pengguna->id }}">
                                            [{{ $pengguna->username }}] {{ $pengguna->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <div class="text-muted">
                                    Tidak ada pengguna yang ditemukan. Silakan cari dengan nama atau username.
                                </div>
                            @endif
                            @error('dataUserId')
                                <div>
                                    <span class="text-danger">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        {{-- Angkatan --}}
                        <div class="mb-3">
                            <label class="form-label">
                                Angkatan
                                <span class="text-muted">
                                    (0 untuk mengambil angkatan dari username)
                                </span>
                            </label>
                            <input type="number" wire:model="dataAngkatan"
                                class="form-control @error('dataAngkatan') is-invalid @enderror">
                            @error('dataAngkatan')
                                <div>
                                    <span class="text-danger">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                    </div>

                    {{-- Modal Footer --}}
                    <div class="modal-footer sticky-bottom bg-light">
                        <button type="submit" class="btn btn-outline-primary" wire:loading.attr="disabled">
                            <span wire:loading wire:target="onAddMember"
                                class="spinner-border spinner-border-sm me-1"></span>
                            <span wire:loading.remove wire:target="onAddMember">Simpan</span>
                            <span wire:loading wire:target="onAddMember">Menyimpan...</span>
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
