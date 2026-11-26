<?php

namespace App\Http\Api;

use App\Helper\ApiHelper;
use Illuminate\Support\Facades\Http;

class DriveApi
{
    private static function getUrl($path)
    {
        return config('sdi.drive_api_url').$path;
    }

    // Fungsi umum untuk mengirimkan request API
    private static function sendRequest($method, $path, $data = [])
    {
        $url = self::getUrl($path);
        $method = strtolower($method);

        $authToken = config('sdi.drive_token', '');

        return ApiHelper::sendRequest($url, $method, $data, $authToken);
    }

    // Fungsi khusus untuk mengirimkan file (upload)
    private static function sendRequestWithFile($method, $path, $data = [], $fileKey = 'file', $file = null)
    {
        $url = self::getUrl($path);

        $authToken = config('sdi.drive_token', '');

        return ApiHelper::sendRequestWithFile($url, $method, $data, $authToken, $fileKey, $file);
    }

    // ## File Upload
    // ## ---------------------------------

    // Direct File Upload
    public static function uploadDirect($file, $parentId = null, $tag = null)
    {
        $data = [];
        if ($parentId !== null) {
            $data['parent_id'] = $parentId;
        }
        if ($tag !== null) {
            $data['tag'] = $tag;
        }

        return self::sendRequestWithFile('post', '/files/upload/direct', $data, 'file', $file);
    }

    // Initialize Chunk Upload Session
    public static function initChunkUpload($name, $size, $mimeType, $parentId = null, $chunkSize = null, $tag = null)
    {
        $data = [
            'name' => $name,
            'size' => $size,
            'mime_type' => $mimeType,
            'parent_id' => $parentId,
        ];
        if ($chunkSize !== null) {
            $data['chunk_size'] = $chunkSize;
        }
        if ($tag !== null) {
            $data['tag'] = $tag;
        }

        return self::sendRequest('post', '/files/upload/chunk/init', $data);
    }

    // Upload Chunk
    public static function uploadChunk($uploadId, $chunkNumber, $chunkContent)
    {
        $url = self::getUrl("/files/upload/chunk/upload?upload_id={$uploadId}&chunk_number={$chunkNumber}");
        $authToken = config('sdi.drive_token', '');

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$authToken,
            ])
                ->withBody($chunkContent, 'application/octet-stream')
                ->post($url);

            return $response->object();
        } catch (\Throwable $e) {
            return (object) [
                'status' => 'error',
                'message' => 'Gagal menghubungi server: '.$e->getMessage(),
            ];
        }
    }

    // Complete Chunk Upload and Merge
    public static function completeChunkUpload($uploadId)
    {
        $data = [
            'upload_id' => $uploadId,
        ];

        return self::sendRequest('post', '/files/upload/chunk/complete', $data);
    }

    // Abort Chunk Upload Session
    public static function abortChunkUpload($uploadId)
    {
        $data = [
            'upload_id' => $uploadId,
        ];

        return self::sendRequest('post', '/files/upload/chunk/abort', $data);
    }

    // ## Whitelist Automation
    // ## ---------------------------------

    // Add User to Whitelist
    public static function addUserToWhitelist($userId, $storageQuota)
    {
        $data = [
            'user_id' => $userId,
            'storage_quota' => $storageQuota,
        ];

        return self::sendRequest('post', '/admin/drive-users', $data);
    }

    // Modify Whitelist Quota
    public static function modifyWhitelistQuota($id, $storageQuota)
    {
        $data = [
            'storage_quota' => $storageQuota,
        ];

        return self::sendRequest('put', "/admin/drive-users/{$id}/quota", $data);
    }

    // Revoke Whitelist User
    public static function revokeWhitelistUser($id)
    {
        return self::sendRequest('delete', "/admin/drive-users/{$id}");
    }

    // ## Token Backup & Server Migration
    // ## ---------------------------------

    // Backup Token to Target Storage
    public static function backupToken($targetServerUrl, array $token)
    {
        $data = [
            'target_server_url' => $targetServerUrl,
            'token' => $token,
        ];

        return self::sendRequest('post', '/admin/drive-tokens/backup', $data);
    }

    // ## AI Metadata & Tag Indexing
    // ## ---------------------------------

    // Add Metadata Entry to a File
    public static function addMetadata($fileId, $namespace, $key, $value)
    {
        $data = [
            'namespace' => $namespace,
            'key' => $key,
            'value' => $value,
        ];

        return self::sendRequest('post', "/files/{$fileId}/embedded-metadata", $data);
    }

    // Get File Details
    public static function getFile($fileId)
    {
        return self::sendRequest('get', "/files/{$fileId}");
    }

    // Retrieve Files by Tag
    public static function getFilesByTag($tag)
    {
        return self::sendRequest('get', '/files/by-tag', ['tag' => $tag]);
    }

    // Index File for RAG
    public static function indexFileForRag($fileId)
    {
        return self::sendRequest('post', "/files/{$fileId}/rag");
    }

    // Query Qdrant via Semantic Search
    public static function semanticSearch($query = null, $limit = null, $fileIds = null, $threshold = null, $index = null, $pageNumber = null)
    {
        $data = [];
        if ($query !== null) {
            $data['q'] = $query;
        }
        if ($limit !== null) {
            $data['limit'] = $limit;
        }
        if ($fileIds !== null) {
            $data['file_ids'] = $fileIds;
        }
        if ($threshold !== null) {
            $data['threshold'] = $threshold;
        }
        if ($index !== null) {
            $data['index'] = $index;
        }
        if ($pageNumber !== null) {
            $data['page_number'] = $pageNumber;
        }

        return self::sendRequest('get', '/files/search/semantic', $data);
    }

    // Delete File
    public static function deleteFile($fileId)
    {
        return self::sendRequest('delete', "/files/{$fileId}");
    }

    /**
     * Ambil binary file langsung dari endpoint download Drive API. Return array
     * ['body' => string, 'content_type' => string, 'status' => int].
     *
     * Endpoint /files/download/{fileId} Drive API biasanya mengembalikan
     * Content-Disposition: attachment, sehingga browser otomatis download.
     * Method ini melakukan fetch binary langsung dari server dan mengembalikan
     * body + content-type agar caller bisa stream ke browser dengan header
     * inline.
     */
    public static function getFileBinary($fileId, $fileName = '')
    {
        $url = self::getUrl('/files/download/'.$fileId).'?name='.urlencode($fileName);
        $authToken = config('sdi.drive_token', '');

        try {
            $response = Http::withHeaders([
                'Accept' => '*/*',
                'Authorization' => 'Bearer '.$authToken,
            ])->timeout(5)->get($url);

            if ($response->successful() && !empty($response->body())) {
                return [
                    'body' => $response->body(),
                    'content_type' => $response->header('Content-Type') ?: 'application/octet-stream',
                    'status' => $response->status(),
                ];
            }
        } catch (\Throwable $e) {
            // Lanjut ke fallback storage lokal di bawah bila Drive API tidak dapat dihubungi
        }

        // Fallback: cari di sistem file lokal (mis. folder uploads .labs/itdel-drive atau storage Laravel)
        $localPath = self::findLocalDriveFilePath($fileId);
        if ($localPath && file_exists($localPath) && is_readable($localPath)) {
            $body = file_get_contents($localPath);
            $mime = null;
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $localPath);
                finfo_close($finfo);
            }
            if (!$mime && function_exists('mime_content_type')) {
                $mime = @mime_content_type($localPath);
            }

            return [
                'body' => $body,
                'content_type' => $mime ?: 'application/octet-stream',
                'status' => 200,
            ];
        }

        return [
            'body' => '',
            'content_type' => 'application/octet-stream',
            'status' => 404,
            'error' => 'Berkas tidak ditemukan di server drive maupun penyimpanan lokal.',
        ];
    }

    /**
     * Cari path berkas fisik di disk lokal jika service Drive offline
     */
    public static function findLocalDriveFilePath($fileId): ?string
    {
        if (empty($fileId)) {
            return null;
        }

        $searchDirs = [
            base_path('../../.labs/itdel-drive/uploads'),
            base_path('../.labs/itdel-drive/uploads'),
            'C:/Users/abdul/Repos/itdel/.labs/itdel-drive/uploads',
            storage_path('app'),
            storage_path('app/public'),
            storage_path('app/submissions'),
            public_path('uploads'),
        ];

        foreach ($searchDirs as $dir) {
            if (!is_dir($dir)) {
                continue;
            }

            // Cek langsung: $dir/$fileId
            $direct = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . $fileId;
            if (file_exists($direct) && is_file($direct)) {
                return $direct;
            }

            // Cek 1 level subfolder: $dir/*/$fileId (misal user_id/$fileId)
            $pattern = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . $fileId;
            $matches = glob($pattern);
            if (!empty($matches) && file_exists($matches[0]) && is_file($matches[0])) {
                return $matches[0];
            }
        }

        return null;
    }
}
