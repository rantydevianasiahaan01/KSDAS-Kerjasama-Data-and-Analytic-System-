<?php

namespace App\Http\Controllers\Api;

use App\Helper\ConstHelper;
use App\Helper\KursusHelper;
use App\Helper\SodiumHelper;
use App\Helper\ToolsHelper;
use App\Http\Controllers\Controller;
use App\Models\AplikasiModel;
use App\Models\KursusKontenApiGradingModel;
use App\Models\KursusKontenApiGradingSubmissionModel;
use App\Models\KursusKontenApiGradingTestSuiteModel;
use App\Models\KursusKontenApiGradingTestSuiteSubmissionModel;
use App\Models\KursusKontenCodeGradingFileModel;
use App\Models\KursusKontenCodeGradingModel;
use App\Models\KursusKontenCodeGradingSubmissionModel;
use App\Models\KursusKontenCodeGradingTestCaseModel;
use App\Models\KursusKontenCodeGradingTestCaseSubmissionModel;
use App\Models\KursusKontenModel;
use App\Models\KursusModel;
use App\Models\KursusStudentModel;
use App\Models\KursusTeacherModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isArray;

class ApiDelcomController extends Controller
{
    // public function testCryptoHelper()
    // {
    //     $keyPair = CryptoHelper::generateKey();

    //     $data = 'Data Rahasia dari Abdullah Ubaid';

    //     // Encrypt
    //     $encrypted = CryptoHelper::encrypt($keyPair->publicKey, $keyPair->privateKey, $data);

    //     // Sign ciphertext
    //     $signature = CryptoHelper::sign($keyPair->signPrivateKey, $encrypted);

    //     // Verify
    //     $isValid = CryptoHelper::verify($keyPair->signPublicKey, $encrypted, $signature);

    //     // Decrypt
    //     $decrypted = CryptoHelper::decrypt($keyPair->publicKey, $keyPair->privateKey, $encrypted);

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Berhasil mengambil data aplikasi',
    //         'data' => (object) [
    //             'keyPair' => $keyPair,
    //             'encrypted' => $encrypted,
    //             'signature' => $signature,
    //             'isValid' => $isValid,
    //             'decrypted' => $decrypted
    //         ]
    //     ]);
    // }

    public function postCheckUrl(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => ['required', 'url'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data yang dikirimkan tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        $url = $request->url;

        try {
            /** @var Response $response */
            $response = Http::timeout(10)
                ->connectTimeout(10)
                ->withHeaders([
                    'User-Agent' => 'Laravel-URL-Checker',
                ])
                ->withoutRedirecting()
                ->get($url);

            if ($response->successful() || $response->redirect()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'URL dapat diakses',
                    'http_code' => $response->status(),
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'URL merespon error',
                'http_code' => $response->status(),
            ], 400);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'URL tidak dapat diakses',
                'detail' => $e->getMessage(),
            ], 404);
        }
    }

    public function getAplikasiByJudul($judul)
    {
        $aplikasi = AplikasiModel::select(['judul', 'versi', 'url'])->where('judul', $judul)->first();

        if (!$aplikasi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aplikasi tidak tersedia'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil data aplikasi',
            'data' => (object) [
                'aplikasi' => $aplikasi
            ]
        ]);
    }

    public function getKursusMeRegistered(Request $request)
    {
        $auth = $request->get('auth');

        $kursusStudent = KursusStudentModel::where('user_id', $auth->id)->get();
        $kursusTeacher = KursusTeacherModel::where('user_id', $auth->id)->get();
        $kursusCreator = KursusModel::select(['id'])->where('user_id', $auth->id)->get();

        $kursusStudentIds = $kursusStudent->pluck('kursus_id')->toArray();
        $kursusTeacherIds = $kursusTeacher->pluck('kursus_id')->toArray();
        $kursusCreatorIds = $kursusCreator->pluck('id')->toArray();

        $kursusIds = array_unique(
            array_merge(
                $kursusStudentIds,
                $kursusTeacherIds,
                $kursusCreatorIds
            )
        );

        $kursusList = KursusModel::select(['id', 'judul', 'cover'])->whereIn('id', $kursusIds)->orderBy('judul', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil data kursus saya',
            'data' => (object) [
                'kursusList' => $kursusList
            ],
        ]);
    }

    public function getKursusKontenDelcomGrading(Request $request, $kursusId)
    {
        $kursus = KursusModel::where('id', $kursusId)->first();
        if (!$kursus) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data kursus tidak tersedia'
            ], 404);
        }

        $auth = $request->get('auth');

        $kursusStudent = KursusStudentModel::where('user_id', $auth->id)->where('kursus_id', $kursusId)->first();
        $kursusTeacher = KursusTeacherModel::where('user_id', $auth->id)->where('kursus_id', $kursusId)->first();
        $kursusCreator = KursusModel::select(['id'])->where('user_id', $auth->id)->where('id', $kursusId)->first();
        if (!$kursusStudent && !$kursusTeacher && !$kursusCreator) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kamu tidak terdaftar pada kursus ini'
            ], 404);
        }

        $kontenAllows = [ConstHelper::TIPE_KONTEN_TITLE, ConstHelper::TIPE_KONTEN_CODE_GRADING, ConstHelper::TIPE_KONTEN_API_GRADING];
        $kontenList = KursusKontenModel::where('kursus_id', $kursusId)->whereIn('tipe', $kontenAllows)->orderBy('urutan', 'asc')->orderBy('judul', 'asc')->get();

        if (!$kursusTeacher && !$kursusCreator) {
            $kontenList = $kontenList->where('is_visible', true);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil data konten kursus untuk code grading',
            'data' => (object) [
                'kursus' => $kursus,
                'kontenList' => $kontenList
            ],
        ]);
    }

    // CODE GRADING
    public function getKursusKontenDetailCodeGrading(Request $request, $kontenId)
    {
        $auth = $request->get('auth');

        $konten = KursusKontenModel::where('id', $kontenId)->where('tipe', ConstHelper::TIPE_KONTEN_CODE_GRADING)->first();
        if (!$konten) {
            return response()->json([
                'status' => 'error',
                'message' => 'Konten kursus untuk code grading ini tidak tersedia'
            ], 404);
        }

        $kontenDetail = KursusKontenCodeGradingModel::select(['id', 'command_setup', 'command_run', 'timeout', 'public_key', 'created_at', 'updated_at'])->where('konten_id', $kontenId)->first();
        if (!$kontenDetail) {
            return response()->json([
                'status' => 'error',
                'message' => 'Detail konten kursus untuk code grading ini tidak tersedia'
            ], 404);
        }

        $kursusId = $konten->kursus_id;

        $kursusStudent = KursusStudentModel::where('user_id', $auth->id)->where('kursus_id', $kursusId)->first();
        $kursusTeacher = KursusTeacherModel::where('user_id', $auth->id)->where('kursus_id', $kursusId)->first();
        $kursusCreator = KursusModel::select(['id'])->where('user_id', $auth->id)->where('id', $kursusId)->first();
        if (!$kursusStudent && !$kursusTeacher && !$kursusCreator) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kamu tidak terdaftar pada kursus ini'
            ], 404);
        }

        if (!$konten->is_visible && !$kursusTeacher && !$kursusCreator) {
            return response()->json([
                'status' => 'error',
                'message' => 'Konten kursus untuk code grading ini belum tersedia'
            ], 404);
        }

        $isEditor = (bool) $kursusTeacher || (bool) $kursusCreator;

        $kodeBawaanList = KursusKontenCodeGradingFileModel::where('konten_id', $kontenId)
            ->orderBy('path', 'asc')
            ->get();

        foreach ($kodeBawaanList as $kode) {
            $extFile = pathinfo($kode->path, PATHINFO_EXTENSION);
            $kode->regex_clear_comments = ToolsHelper::getRegexClearComments($extFile);
        }

        $testCaseList = KursusKontenCodeGradingTestCaseModel::select(['id', 'judul', 'score', 'type_matching', 'input'])
            ->where('konten_id', $kontenId)
            ->orderBy('judul', 'asc')
            ->get();

        $testCaseSubmissions = KursusKontenCodeGradingTestCaseSubmissionModel::where('konten_id', $kontenId)->where('user_id', $auth->id)->get();

        foreach ($testCaseList as $tc) {
            $submission = $testCaseSubmissions->where('tc_id', $tc->id)->first();
            if ($submission) {
                $tc->my_score = $submission->score ?? 0;
            } else {
                $tc->my_score = 0;
            }
        }

        $codeGradingSubmission = KursusKontenCodeGradingSubmissionModel::where('konten_id', $kontenId)->where('user_id', $auth->id)->first();

        $stats = (object) [
            'fileTotal' => $kodeBawaanList->count(),
            'tcTotal' => $testCaseList->count(),
            'tcTotalScore' => $testCaseList->sum('score'),
            'tcMyScore' => $codeGradingSubmission->score ?? 0,
            'tcMyScore100' => $codeGradingSubmission->score_100 ?? 0
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil konten kursus untuk code grading ini',
            'data' => (object) [
                'isEditor' => $isEditor,
                'konten' => $konten,
                'kontenDetail' => $kontenDetail,
                'kodeBawaanList' => $kodeBawaanList,
                'testCaseList' => $testCaseList,
                'stats' => $stats
            ],
        ]);
    }

    public function postKursusKontenDetailCodeGradingTestCases(Request $request, $kontenId)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data yang dikirimkan tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        $auth = $request->get('auth');

        $konten = KursusKontenModel::where('id', $kontenId)->where('tipe', ConstHelper::TIPE_KONTEN_CODE_GRADING)->first();
        if (!$konten) {
            return response()->json([
                'status' => 'error',
                'message' => 'Konten kursus untuk code grading ini tidak tersedia'
            ], 404);
        }

        $kontenDetail = KursusKontenCodeGradingModel::where('konten_id', $kontenId)->first();
        if (!$kontenDetail) {
            return response()->json([
                'status' => 'error',
                'message' => 'Detail konten kursus untuk code grading ini tidak tersedia'
            ], 404);
        }

        $kursusId = $konten->kursus_id;
        $kursusTeacher = KursusTeacherModel::where('user_id', $auth->id)->where('kursus_id', $kursusId)->first();
        $kursusCreator = KursusModel::select(['id'])->where('user_id', $auth->id)->where('id', $kursusId)->first();
        if (!$kursusTeacher && !$kursusCreator) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kamu tidak memiliki hak akses untuk mengelolah kursus ini'
            ], 404);
        }

        // Hapus test case lama
        KursusKontenCodeGradingTestCaseModel::where('konten_id', $kontenId)->delete();

        // Hapus hasil pengujian test case lama
        KursusKontenCodeGradingTestCaseSubmissionModel::where('konten_id', $kontenId)->delete();

        // Hapus hasil pengujian lama
        KursusKontenCodeGradingSubmissionModel::where('konten_id', $kontenId)->delete();

        // Tambahkan data baru
        foreach ($request->data as $itemArr) {
            $item = (object) $itemArr;
            if (!$item->title || !$item->type_matching || !$item->output) {
                continue;
            }

            KursusKontenCodeGradingTestCaseModel::create([
                'id' => ToolsHelper::generateId(),
                'konten_id' => $kontenId,
                'detail_konten_id' => $kontenDetail->id,
                'judul' => $item->title,
                'score' => $item->score,
                'type_matching' => $item->type_matching,
                'is_secret' => false,
                'input' => $item->input ?? '',
                'output' => $item->output,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil memperbarui Test Cases pada Code Grading',
        ]);
    }

    public function postKursusKontenDetailCodeGradingSubmit(Request $request, $kontenId)
    {
        $validator = Validator::make($request->all(), [
            'cipher' => 'required|string',
            'publicKey' => 'required|string',
            'signPublicKey' => 'required|string',
            'signature' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data yang dikirimkan tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        $auth = $request->get('auth');

        $konten = KursusKontenModel::where('id', $kontenId)->where('tipe', ConstHelper::TIPE_KONTEN_CODE_GRADING)->first();
        if (!$konten) {
            return response()->json([
                'status' => 'error',
                'message' => 'Konten kursus untuk code grading ini tidak tersedia'
            ], 404);
        }

        $currentTime = now()->toDateTimeString();
        $isDeadlineExpired = $konten->deadline
            ? strtotime($konten->deadline) < strtotime($currentTime)
            : true;
        $isDeadlineLateExpired = $konten->deadline_late
            ? strtotime($konten->deadline_late) < strtotime($currentTime)
            : true;

        if ($isDeadlineExpired) {
            if ($isDeadlineLateExpired) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Deadline pengumpulan telah berakhir.'
                ], 400);
            }
        }

        $kontenDetail = KursusKontenCodeGradingModel::where('konten_id', $kontenId)->first();
        if (!$kontenDetail) {
            return response()->json([
                'status' => 'error',
                'message' => 'Detail konten kursus untuk code grading ini tidak tersedia'
            ], 404);
        }

        $kursusId = $konten->kursus_id;

        $kursusStudent = KursusStudentModel::where('user_id', $auth->id)->where('kursus_id', $kursusId)->first();
        $kursusTeacher = KursusTeacherModel::where('user_id', $auth->id)->where('kursus_id', $kursusId)->first();
        $kursusCreator = KursusModel::select(['id'])->where('user_id', $auth->id)->where('id', $kursusId)->first();
        if (!$kursusStudent && !$kursusTeacher && !$kursusCreator) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kamu tidak terdaftar pada kursus ini'
            ], 404);
        }

        $isValid = SodiumHelper::verify($request->signPublicKey, $request->cipher, $request->signature);
        if (!$isValid) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data yang dikirimkan tidak valid'
            ], 400);
        }

        $decrypt = SodiumHelper::decrypt($request->publicKey, $kontenDetail->private_key, $request->cipher);
        if (!$decrypt) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data yang dikirimkan corrupt'
            ], 400);
        }

        $data = json_decode($decrypt);

        if (!isset($data->root_path) || !isset($data->tools) || !isset($data->outputs) || !is_array($data->outputs)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Format data yang dikirimkan tidak valid'
            ], 400);
        }

        $tcList = KursusKontenCodeGradingTestCaseModel::where('konten_id', $kontenId)->get();

        // Hapus hasil sebelumnya
        KursusKontenCodeGradingTestCaseSubmissionModel::where('konten_id', $kontenId)->where('user_id', $auth->id)->delete();

        KursusKontenCodeGradingSubmissionModel::where('konten_id', $kontenId)->where('user_id', $auth->id)->delete();

        $totalScore = 0;
        $totalScoreTc = 0;
        foreach ($data->outputs as $out) {
            $tc = $tcList->where('id', $out->tc_id)->first();
            if (!$tc) {
                continue;
            }

            // Exacts
            $isExacts = trim($tc->output) == trim($out->output);

            // Contains
            $isContains = str_contains($out->output, $tc->output);

            // Line By Line
            $resultLineByLine = ToolsHelper::checkTestCaseLineByLine($tc->output, $out->output);

            $hasil = (object) [
                'isExacts' => $isExacts,
                'percentageExacts' => $isExacts ? 100 : 0,
                'isContains' => $isContains,
                'percentageContains' => $isContains ? 100 : 0,
                'resultLineByLine' => $resultLineByLine
            ];

            $score_100 = 0;
            if ($tc->type_matching == 'Line By Line') {
                $score_100 = $hasil->resultLineByLine->percentageMatch ?? 0;
            } else if ($tc->type_matching == 'Contains') {
                $score_100 = $hasil->percentageContains ?? 0;
            } else {
                $score_100 = $hasil->percentageExacts ?? 0;
            }

            $totalScoreTc += $tc->score;
            $score = ($score_100 / 100) * $tc->score;
            $totalScore += $score;

            KursusKontenCodeGradingTestCaseSubmissionModel::create([
                'id' => ToolsHelper::generateId(),
                'konten_id' => $kontenId,
                'tc_id' => $tc->id,
                'user_id' => $auth->id,
                'output' => trim($out->output),
                'result_checking' => json_encode($hasil),
                'score' => $score
            ]);
        }

        KursusKontenCodeGradingSubmissionModel::create([
            'id' => ToolsHelper::generateId(),
            'konten_id' => $kontenId,
            'user_id' => $auth->id,
            'score' => round($totalScore, 0),
            'score_100' => round(($totalScore / $totalScoreTc) * 100, 0)
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil memperbarui penilaian code grading'
        ]);
    }

    // API GRADING
    public function getKursusKontenDetailApiGrading(Request $request, $kontenId)
    {
        $auth = $request->get('auth');

        $konten = KursusKontenModel::where('id', $kontenId)->where('tipe', ConstHelper::TIPE_KONTEN_API_GRADING)->first();
        if (!$konten) {
            return response()->json([
                'status' => 'error',
                'message' => 'Konten kursus untuk api grading ini tidak tersedia'
            ], 404);
        }

        $kontenDetail = KursusKontenApiGradingModel::select(['id', 'prefix_base_url', 'allow_localhost', 'public_key', 'created_at', 'updated_at'])->where('konten_id', $kontenId)->first();
        if (!$kontenDetail) {
            return response()->json([
                'status' => 'error',
                'message' => 'Detail konten kursus untuk api grading ini tidak tersedia'
            ], 404);
        }

        $kontenDetail->prefix_base_url = KursusHelper::changeVariabel($kontenDetail->prefix_base_url, $auth);

        $kursusId = $konten->kursus_id;

        $kursusStudent = KursusStudentModel::where('user_id', $auth->id)->where('kursus_id', $kursusId)->first();
        $kursusTeacher = KursusTeacherModel::where('user_id', $auth->id)->where('kursus_id', $kursusId)->first();
        $kursusCreator = KursusModel::select(['id'])->where('user_id', $auth->id)->where('id', $kursusId)->first();
        if (!$kursusStudent && !$kursusTeacher && !$kursusCreator) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kamu tidak terdaftar pada kursus ini'
            ], 404);
        }

        if (!$konten->is_visible && !$kursusTeacher && !$kursusCreator) {
            return response()->json([
                'status' => 'error',
                'message' => 'Konten kursus untuk api grading ini belum tersedia'
            ], 404);
        }

        $isEditor = (bool) $kursusTeacher || (bool) $kursusCreator;

        $testSuiteList = KursusKontenApiGradingTestSuiteModel::select(['id', 'judul', 'score', 'is_secret', 'test_scripts'])
            ->where('konten_id', $kontenId)
            ->orderBy('judul', 'asc')
            ->get();

        $testSuiteSubmissions = KursusKontenApiGradingTestSuiteSubmissionModel::where('konten_id', $kontenId)->where('user_id', $auth->id)->get();

        foreach ($testSuiteList as $ts) {
            $submission = $testSuiteSubmissions->where('ts_id', $ts->id)->first();
            if ($submission) {
                $ts->my_score = $submission->score ?? 0;
            } else {
                $ts->my_score = 0;
            }
        }

        $apiGradingSubmission = KursusKontenApiGradingSubmissionModel::where('konten_id', $kontenId)->where('user_id', $auth->id)->first();

        $stats = (object) [
            'tsTotal' => $testSuiteList->count(),
            'tsTotalScore' => $testSuiteList->sum('score'),
            'tsMyScore' => $apiGradingSubmission->score ?? 0,
            'tsMyScore100' => $apiGradingSubmission->score_100 ?? 0
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil konten kursus untuk api grading ini',
            'data' => (object) [
                'isEditor' => $isEditor,
                'konten' => $konten,
                'kontenDetail' => $kontenDetail,
                'testSuiteList' => $testSuiteList,
                'stats' => $stats
            ],
        ]);
    }

    public function postKursusKontenDetailApiGradingSubmit(Request $request, $kontenId)
    {
        $validator = Validator::make($request->all(), [
            'cipher' => 'required|string',
            'publicKey' => 'required|string',
            'signPublicKey' => 'required|string',
            'signature' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data yang dikirimkan tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        $auth = $request->get('auth');

        $konten = KursusKontenModel::where('id', $kontenId)->where('tipe', ConstHelper::TIPE_KONTEN_API_GRADING)->first();
        if (!$konten) {
            return response()->json([
                'status' => 'error',
                'message' => 'Konten kursus untuk api grading ini tidak tersedia'
            ], 404);
        }

        $currentTime = now()->toDateTimeString();
        $isDeadlineExpired = $konten->deadline
            ? strtotime($konten->deadline) < strtotime($currentTime)
            : true;
        $isDeadlineLateExpired = $konten->deadline_late
            ? strtotime($konten->deadline_late) < strtotime($currentTime)
            : true;

        if ($isDeadlineExpired) {
            if ($isDeadlineLateExpired) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Deadline pengumpulan telah berakhir.'
                ], 400);
            }
        }

        $kontenDetail = KursusKontenApiGradingModel::where('konten_id', $kontenId)->first();
        if (!$kontenDetail) {
            return response()->json([
                'status' => 'error',
                'message' => 'Detail konten kursus untuk api grading ini tidak tersedia'
            ], 404);
        }

        $kursusId = $konten->kursus_id;

        $kursusStudent = KursusStudentModel::where('user_id', $auth->id)->where('kursus_id', $kursusId)->first();
        $kursusTeacher = KursusTeacherModel::where('user_id', $auth->id)->where('kursus_id', $kursusId)->first();
        $kursusCreator = KursusModel::select(['id'])->where('user_id', $auth->id)->where('id', $kursusId)->first();
        if (!$kursusStudent && !$kursusTeacher && !$kursusCreator) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kamu tidak terdaftar pada kursus ini'
            ], 404);
        }

        $isValid = SodiumHelper::verify($request->signPublicKey, $request->cipher, $request->signature);
        if (!$isValid) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data yang dikirimkan tidak valid'
            ], 400);
        }

        $decrypt = SodiumHelper::decrypt($request->publicKey, $kontenDetail->private_key, $request->cipher);
        if (!$decrypt) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data yang dikirimkan corrupt'
            ], 400);
        }

        $data = json_decode($decrypt);

        if (!isset($data->tools) || !isset($data->baseUrl) || !isset($data->reports)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Format data yang dikirimkan tidak valid'
            ], 400);
        }

        $reports = json_decode($data->reports);
        if (!isset($reports->testResults) && isArray($reports->testResults)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data laporan hasil pengujian tidak valid'
            ], 400);
        }

        $testSuites = KursusKontenApiGradingTestSuiteModel::where('konten_id', $kontenId)->get();

        // Hapus submitan lama
        KursusKontenApiGradingSubmissionModel::where('konten_id', $kontenId)->where('user_id', $auth->id)->delete();
        KursusKontenApiGradingTestSuiteSubmissionModel::where('konten_id', $kontenId)->where('user_id', $auth->id)->delete();

        $clientPathTests = "";
        foreach ($testSuites as $ts) {
            foreach ($reports->testResults as $result) {
                if (str_contains($result->name, $ts->id)) {
                    $clientPathTests = str_replace("\\tests\\". $ts->id .".test.js", "", $result->name);
                    $result->name = $ts->judul;
                    $result->ts_id = $ts->id;

                    $report = (object) [
                        'duration' => $result->endTime - $result->startTime,
                        'total_asserts' => 0,
                        'total_pass_asserts' => 0,
                        'score_100' => 0,
                        'score' => 0,
                    ];

                    if (isset($result->assertionResults) && isArray($result->assertionResults)) {
                        $report->total_asserts += sizeof($result->assertionResults);
                        foreach ($result->assertionResults as $assert) {
                            if ($assert->status == 'passed') {
                                $report->total_pass_asserts += 1;
                            }
                        }
                    }

                    $report->score_100 = round(($report->total_pass_asserts / $report->total_asserts) * 100, 0);
                    $report->score = round(($report->score_100 / 100) * $ts->score, 0);

                    KursusKontenApiGradingTestSuiteSubmissionModel::create([
                        'id' => ToolsHelper::generateId(),
                        'konten_id' => $kontenId,
                        'ts_id' => $ts->id,
                        'user_id' => $auth->id,
                        'duration' => $report->duration,
                        'total_asserts' => $report->total_asserts,
                        'total_pass_asserts' => $report->total_pass_asserts,
                        'score_100' => $report->score_100,
                        'score' => $report->score
                    ]);
                    break;
                }
            }
        }

        $testSuiteSubmissions = KursusKontenApiGradingTestSuiteSubmissionModel::select(['score', 'score_100'])->where('konten_id', $kontenId)->where('user_id', $auth->id)->get();

        $score_100 = round($testSuiteSubmissions->sum('score_100') / $testSuites->count(), 0);
        $score = round($testSuiteSubmissions->sum('score'), 0);

        $clientPathTests = str_replace("\\", "\\\\", $clientPathTests);
        KursusKontenApiGradingSubmissionModel::create([
            'id' => ToolsHelper::generateId(),
            'konten_id' => $kontenId,
            'user_id' => $auth->id,
            'base_url' => $data->baseUrl,
            'reports' => str_replace($clientPathTests, "", json_encode($reports)),
            'score_100' => $score_100,
            'score' => $score,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil memperbarui penilaian api grading'
        ]);
    }
}
