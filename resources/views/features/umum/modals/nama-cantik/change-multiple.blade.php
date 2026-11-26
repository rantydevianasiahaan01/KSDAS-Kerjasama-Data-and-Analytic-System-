<div>
    <form wire:submit.prevent="onChangeMultiple">
        <div wire:ignore.self class="modal fade" id="changeMultipleModal" tabindex="-1" data-bs-backdrop="static"
            data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{-- Modal Header --}}
                    <div class="modal-header modal-colored-header bg-primary text-white sticky-top">
                        <h4 class="modal-title text-white">
                            Tambah Nama Cantik (Multiple)
                        </h4>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="modal-body">
                        {{-- Daftar Nama --}}
                        <div class="mb-3">
                            <label class="form-label">Daftar Nama</label>
                            <textarea rows="6" wire:model="dataDaftarNama" class="form-control @error('dataDaftarNama') is-invalid @enderror"></textarea>
                            @error('dataDaftarNama')
                                <div>
                                    <span class="text-danger">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="modal-footer sticky-bottom bg-light">
                        <button type="submit" class="btn btn-outline-primary" wire:loading.attr="disabled">
                            <span wire:loading wire:target="onChangeMultiple"
                                class="spinner-border spinner-border-sm me-1"></span>
                            <span wire:loading.remove wire:target="onChangeMultiple">Simpan</span>
                            <span wire:loading wire:target="onChangeMultiple">Menyimpan...</span>
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
