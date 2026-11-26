<div>
    <form wire:submit.prevent="onChange">
        <div wire:ignore.self class="modal fade" id="changeModal" tabindex="-1" data-bs-backdrop="static"
            data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{-- Modal Header --}}
                    <div class="modal-header modal-colored-header bg-primary text-white sticky-top">
                        <h4 class="modal-title text-white">
                            {{ $infoJudulModal }}
                        </h4>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="modal-body">
                        {{-- Nama --}}
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" wire:model="dataNama"
                                class="form-control @error('dataNama') is-invalid @enderror">
                            @error('dataNama')
                                <div>
                                    <span class="text-danger">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea rows="3" wire:model="dataDeskripsi" class="form-control @error('dataDeskripsi') is-invalid @enderror"></textarea>
                            @error('dataDeskripsi')
                                <div>
                                    <span class="text-danger">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="modal-footer sticky-bottom bg-light">
                        <button type="submit" class="btn btn-outline-primary" wire:loading.attr="disabled">
                            <span wire:loading wire:target="onChange"
                                class="spinner-border spinner-border-sm me-1"></span>
                            <span wire:loading.remove wire:target="onChange">Simpan</span>
                            <span wire:loading wire:target="onChange">Menyimpan...</span>
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
