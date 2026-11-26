<?php

namespace App\Livewire\HakAkses;

use App\Helper\ConstHelper;
use App\Helper\ToolsHelper;
use App\Helper\WithComponentNameHelper;
use App\Http\Api\UserApi;
use App\Models\HakAksesModel;
use Livewire\Component;

class HakAksesLivewire extends Component
{
    use WithComponentNameHelper;

    public $auth;

    public $optionRoles = [];

    public $isEditor = false;

    public $search;

    public $searchPengguna;

    public $addUserId;

    public $addHakAkses;

    public $editId;

    public $editHakAkses;

    public $deleteId;

    public $deleteMessage;

    private function checkAkses()
    {
        if (! $this->isEditor) {
            abort(403, 'Unauthorized action.');
        }
        $this->resetErrorBag();
    }

    public function mount()
    {
        $auth = request()->attributes->get('auth');
        $isEditor = ToolsHelper::hasAppAccess($auth, ['Kerjasama.Admin']);
        if (! $isEditor) {
            return redirect()->route('app.beranda');
        }

        $this->isEditor = $isEditor;
        $this->auth = $auth;

        $options = ConstHelper::ALL_ROLES;
        sort($options);
        $this->optionRoles = $options;
    }

    public function render()
    {
        $aksesList = HakAksesModel::get();
        $response = UserApi::postReqUsersByIds(
            ToolsHelper::getAuthToken(),
            $aksesList->pluck('user_id')->unique()->toArray()
        );
        $usersList = [];
        if ($response && isset($response->data->users)) {
            $usersList = collect($response->data->users)->map(function ($user) {
                return (object) $user;
            })->all();
        }

        foreach ($aksesList as $akses) {
            $akses->user = collect($usersList)->firstWhere('id', $akses->user_id);
        }

        if ($this->search) {
            $aksesList = $aksesList->filter(function ($item) {
                $user = $item->user;
                if (! $user) {
                    return false;
                }

                return str_contains(strtolower($user->name), strtolower($this->search)) ||
                    str_contains(strtolower($user->username), strtolower($this->search));
            });
        }

        $searchPenggunaList = [];
        if ($this->searchPengguna) {
            $result = UserApi::getUsers(ToolsHelper::getAuthToken(), search: $this->searchPengguna, limit: 5, alias: '');
            if (isset($result->data->users)) {
                $searchPenggunaList = collect($result->data->users ?? [])->map(function ($user) {
                    return (object) $user;
                })->all();
            }
        }

        $breadcrumb = [
            'Home' => route('app.beranda'),
            'Hak Akses' => '',
        ];

        return view('features.hak-akses.livewire', [
            'breadcrumb' => $breadcrumb,
            'aksesList' => $aksesList,
            'searchPenggunaList' => $searchPenggunaList,
        ]);
    }

    public function prepareAdd()
    {
        $this->reset(['addUserId', 'addHakAkses', 'searchPengguna']);
        $this->dispatch('showModal', id: 'addModal');
    }

    public function add()
    {
        $this->checkAkses();

        $this->validate([
            'addUserId' => 'required',
            'addHakAkses' => 'required|array',
        ], [
            'addUserId.required' => 'Pengguna harus dipilih.',
            'addHakAkses.required' => 'Hak akses harus dipilih.',
            'addHakAkses.array' => 'Hak akses harus berupa array.',
        ]);

        HakAksesModel::where('user_id', $this->addUserId)->delete();

        HakAksesModel::create([
            'id' => ToolsHelper::generateId(),
            'user_id' => $this->addUserId,
            'akses' => implode(',', $this->addHakAkses),
        ]);

        $this->reset(['addUserId', 'addHakAkses']);
        $this->dispatch('closeModal', id: 'addModal');
    }

    public function prepareEdit($id)
    {
        $targetAkses = HakAksesModel::where('id', $id)->first();
        if (! $targetAkses) {
            return;
        }

        $this->editId = $targetAkses->id;
        $this->editHakAkses = explode(',', $targetAkses->akses);
        $this->dispatch('showModal', id: 'editModal');
    }

    public function edit()
    {
        $this->checkAkses();

        $this->validate([
            'editHakAkses' => 'required|array',
        ], [
            'editHakAkses.required' => 'Hak akses harus dipilih.',
            'editHakAkses.array' => 'Hak akses harus berupa array.',
        ]);

        $akses = HakAksesModel::where('id', $this->editId)->first();
        if (! $akses) {
            return;
        }

        $akses->akses = implode(',', $this->editHakAkses);
        $akses->save();

        $this->reset(['editId', 'editHakAkses']);
        $this->dispatch('closeModal', id: 'editModal');
    }

    public function prepareDelete($id)
    {
        $targetAkses = HakAksesModel::find($id);
        if (! $targetAkses) {
            return;
        }

        $response = UserApi::getUserById(
            ToolsHelper::getAuthToken(),
            $targetAkses->user_id,
        );
        $name = isset($response->data->user) ? $response->data->user->name : '-';

        $this->deleteId = $targetAkses->id;
        $this->deleteMessage = "Apakah Anda yakin ingin menghapus hak akses untuk pengguna <strong>'$name'</strong>?";
        $this->dispatch('showModal', id: 'deleteModal');
    }

    public function delete()
    {
        $this->checkAkses();

        HakAksesModel::destroy($this->deleteId);

        $this->reset(['deleteId', 'deleteMessage']);
        $this->dispatch('closeModal', id: 'deleteModal');
    }
}
