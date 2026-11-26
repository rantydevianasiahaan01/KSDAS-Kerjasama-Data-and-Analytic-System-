<div>
    @include('components.breadcrumb')

    <div class="card">
        <div class="card-header">
            <div class="d-flex">
                <div class="flex-fill">
                    <h3><i class="ti ti-lock"></i> Hak Akses</h3>
                </div>
                <div>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari..."
                            wire:model.live.debounce.500ms="search">
                        @if ($this->isEditor)
                            <button class="btn btn-primary" type="button" wire:click="prepareAdd"
                                wire:loading.attr="disabled" wire:target="prepareAdd">
                                <span wire:loading wire:target="prepareAdd"
                                    class="spinner-border spinner-border-sm me-1"></span>
                                <i wire:loading.remove wire:target="prepareAdd" class="ti ti-plus"></i>
                                Tambah
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-borderless mb-0">
                <tr class="table-info">
                    <th>Identitas</th>
                    <th>Akses</th>
                    <th>Tindakan</th>
                </tr>
                @forelse ($aksesList as $akses)
                    <tr class="border-bottom">
                        <td>
                            <small class="text-muted">{{ '@' . ($akses->user->username ?? '-') }}</small><br>
                            <span>{{ $akses->user->name ?? '-' }}</span>
                        </td>
                        <td>
                            @foreach (explode(',', (string) $akses->akses) as $hak)
                                <div><i class="ti ti-minus"></i> {{ $hak }}</div>
                            @endforeach
                        </td>
                        <td>
                            @if ($this->isEditor)
                                @php($rowTarget = "prepareEdit('{$akses->id}'), prepareDelete('{$akses->id}')")
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-info" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false" aria-label="Tindakan"
                                        wire:loading.attr="disabled"
                                        wire:target="{{ $rowTarget }}">
                                        <span wire:loading.remove wire:target="{{ $rowTarget }}">
                                            <i class="ti ti-dots-vertical"></i>
                                        </span>
                                        <span wire:loading wire:target="{{ $rowTarget }}"
                                            class="spinner-border spinner-border-sm" role="status"
                                            aria-label="Memuat"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a href="#" class="dropdown-item text-warning"
                                                wire:click.prevent="prepareEdit('{{ $akses->id }}')"
                                                wire:loading.class="disabled"
                                                wire:target="{{ $rowTarget }}">
                                                <i class="ti ti-edit me-2"></i> Ubah
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="dropdown-item text-danger"
                                                wire:click.prevent="prepareDelete('{{ $akses->id }}')"
                                                wire:loading.class="disabled"
                                                wire:target="{{ $rowTarget }}">
                                                <i class="ti ti-trash me-2"></i> Hapus
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-muted p-3">Belum ada data hak akses.</td>
                    </tr>
                @endforelse
            </table>
        </div>
    </div>

    @include('features.hak-akses.modals.add')
    @include('features.hak-akses.modals.edit')
    @include('features.hak-akses.modals.delete')
</div>
