<div>
    <form wire:submit.prevent="onAddMemberUnstrict">
        <div wire:ignore.self class="modal fade" id="addMemberUnstrictModal" tabindex="-1" data-bs-backdrop="static"
            data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{-- Modal Header --}}
                    <div class="modal-header modal-colored-header bg-primary text-white sticky-top">
                        <h4 class="modal-title text-white">
                            Tambah Anggota Nama Cantik: {{ $namaCantikSaya->nama ?? "-" }}
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

                    </div>

                    {{-- Modal Footer --}}
                    <div class="modal-footer sticky-bottom bg-light">
                        <button type="submit" class="btn btn-outline-primary" wire:loading.attr="disabled">
                            <span wire:loading wire:target="onAddMemberUnstrict"
                                class="spinner-border spinner-border-sm me-1"></span>
                            <span wire:loading.remove wire:target="onAddMemberUnstrict">Simpan</span>
                            <span wire:loading wire:target="onAddMemberUnstrict">Menyimpan...</span>
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
