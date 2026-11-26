<div>
    <form wire:submit.prevent="delete">
        <div wire:ignore.self class="modal fade" id="deleteModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header modal-colored-header bg-danger text-white">
                        <h4 class="modal-title text-white">Hapus Hak Akses</h4>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">{!! $deleteMessage !!}</div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger" wire:loading.attr="disabled" wire:target="delete">
                            <span wire:loading wire:target="delete"
                                class="spinner-border spinner-border-sm me-1"></span>
                            <span wire:loading.remove wire:target="delete">Hapus</span>
                            <span wire:loading wire:target="delete">Menghapus...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
