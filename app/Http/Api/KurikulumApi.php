<?php

namespace App\Http\Api;

use App\Helper\ApiHelper;

class KurikulumApi
{
    private static function getUrl($path)
    {
        return config('sdi.api_kurikulum_url').$path;
    }

    // Fungsi umum untuk mengirimkan request API
    private static function sendRequest($method, $path, $data = [], $authToken = '')
    {
        $url = self::getUrl($path);
        $method = strtolower($method);

        return ApiHelper::sendRequest($url, $method, $data, $authToken);
    }

    // Fungsi khusus untuk mengirimkan file (upload)
    private static function sendRequestWithFile($method, $path, $data = [], $authToken = '', $fileKey = 'file', $file = null)
    {
        $url = self::getUrl($path);

        return ApiHelper::sendRequestWithFile($url, $method, $data, $authToken, $fileKey, $file);
    }

    // getInformasiUmumProdi
    // example-json: .examples\kurikulum-services\get-informasi-umum-prodi.json
    public static function getInformasiUmumProdi($authToken)
    {
        $data = [];

        return self::sendRequest('get', '/informasi-umum/prodi', $data, $authToken);
    }

    // getInformasiUmumFakultas
    // example-json: .examples\kurikulum-services\get-informasi-umum-fakultas.json
    public static function getInformasiUmumFakultas($authToken, $withProdi)
    {
        // $withProdi = string "true" or "false"
        $data = [
            'with_prodi' => $withProdi,
        ];

        return self::sendRequest('get', '/informasi-umum/fakultas', $data, $authToken);
    }
}
