<div>
    <form wire:submit.prevent="add">
        <div wire:ignore.self class="modal fade" id="addModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-primary text-white">
                        <h4 class="modal-title text-white">Tambah Hak Akses</h4>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Cari Pengguna</label>
                            <input type="text" class="form-control" wire:model.live.debounce.500ms="searchPengguna">
                            <div wire:loading wire:target="searchPengguna" class="text-muted small mt-1">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                Mencari pengguna...
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pilih Pengguna</label>
                            @if ($searchPenggunaList && count($searchPenggunaList) > 0)
                                <select wire:model="addUserId" class="form-select" size="5">
                                    @foreach ($searchPenggunaList as $pengguna)
                                        <option value="{{ $pengguna->id }}">[{{ $pengguna->username }}] {{ $pengguna->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <div class="text-muted">Cari pengguna berdasarkan nama atau username.</div>
                            @endif
                            @error('addUserId') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pilih Hak Akses</label>
                            <select wire:model="addHakAkses" class="form-select" multiple size="{{ count($optionRoles) + 1 }}">
                                @foreach ($optionRoles as $role)
                                    <option value="{{ $role }}">{{ $role }}</option>
                                @endforeach
                            </select>
                            @error('addHakAkses') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="add">
                            <span wire:loading wire:target="add"
                                class="spinner-border spinner-border-sm me-1"></span>
                            <span wire:loading.remove wire:target="add">Simpan</span>
                            <span wire:loading wire:target="add">Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
