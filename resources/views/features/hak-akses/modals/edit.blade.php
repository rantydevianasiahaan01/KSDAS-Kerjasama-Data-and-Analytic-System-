<div>
    <form wire:submit.prevent="edit">
        <div wire:ignore.self class="modal fade" id="editModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-warning">
                        <h4 class="modal-title">Ubah Hak Akses</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pilih Hak Akses</label>
                            <select wire:model="editHakAkses" class="form-select" multiple size="{{ count($optionRoles) + 1 }}">
                                @foreach ($optionRoles as $role)
                                    <option value="{{ $role }}">{{ $role }}</option>
                                @endforeach
                            </select>
                            @error('editHakAkses') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning" wire:loading.attr="disabled" wire:target="edit">
                            <span wire:loading wire:target="edit"
                                class="spinner-border spinner-border-sm me-1"></span>
                            <span wire:loading.remove wire:target="edit">Simpan</span>
                            <span wire:loading wire:target="edit">Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
