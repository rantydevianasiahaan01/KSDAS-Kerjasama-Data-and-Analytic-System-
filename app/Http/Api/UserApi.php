<?php

namespace App\Http\Api;

use App\Helper\ApiHelper;

class UserApi
{
    private static function getUrl($path)
    {
        return config('sdi.api_users_url').$path;
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

    // Post Register
    public static function postRegister($username, $password)
    {
        $data = [
            'username' => $username,
            'password' => $password,
        ];

        return self::sendRequest('post', '/auth/register', $data);
    }

    // Post Login
    public static function postLogin($system_id, $platform, $info, $username, $password)
    {
        $data = [
            'system_id' => $system_id,
            'platform' => $platform,
            'info' => $info,
            'username' => $username,
            'password' => $password,
        ];

        return self::sendRequest('post', '/auth/login', $data);
    }

    // Get Login Info
    public static function getLoginInfo($authToken)
    {
        $data = [];

        return self::sendRequest('get', '/auth/login/info', $data, $authToken);
    }

    // Get Login Info All
    public static function getLoginInfoAll($authToken)
    {
        $data = [];

        return self::sendRequest('get', '/auth/login/info/all', $data, $authToken);
    }

    // Post Logout
    public static function postLogout($authToken)
    {
        $data = [];

        return self::sendRequest('post', '/auth/logout', $data, $authToken);
    }

    // Post Logout All
    public static function postLogoutAll($authToken)
    {
        $data = [];

        return self::sendRequest('post', '/auth/logout/all', $data, $authToken);
    }

    // Post Logout By Token Id
    public static function postLogoutByTokenId($authToken, $loginTokenId)
    {
        $data = [];

        return self::sendRequest('post', "/auth/logout/$loginTokenId", $data, $authToken);
    }

    // Post Forget Password Info
    public static function postForgetPasswordInfo($email)
    {
        $data = [
            'email' => $email,
        ];

        return self::sendRequest('post', '/auth/forget/info', $data);
    }

    // Post Forget Password Send Email
    public static function postForgetPasswordSendEmail($email)
    {
        $data = [
            'email' => $email,
        ];

        return self::sendRequest('post', '/auth/forget/send-email', $data);
    }

    // Post Forget Password Reset
    public static function postForgetPasswordReset($email, $token, $password)
    {
        $data = [
            'email' => $email,
            'token' => $token,
            'password' => $password,
        ];

        return self::sendRequest('post', '/auth/forget/reset-password', $data);
    }

    // Post TOTP Setup
    public static function postTotpSetup($authToken)
    {
        $data = [];

        return self::sendRequest('post', '/auth/totp/setup', $data, $authToken);
    }

    // Post TOTP Verify
    public static function postTotpVerify($authToken, $code)
    {
        $data = [
            'code' => $code,
        ];

        return self::sendRequest('post', '/auth/totp/verify', $data, $authToken);
    }

    // Roles
    // ------------------------------

    // Get All Roles
    public static function getRoles($authToken)
    {
        $data = [];

        return self::sendRequest('get', '/roles', $data, $authToken);

        /*
        {
            "status": "success",
            "message": "Berhasil mengambil data roles.",
            "data": {
                "roles": [
                    {
                        "id": "8357fda6-67f7-4a99-8f01-9847d6920596",
                        "name": "Admin",
                        "group": "",
                        "created_at": "2026-06-14T12:32:57.444Z"
                    },
                    {
                        "id": "34f397b7-1f5c-426b-a6ef-b618f1e5f866",
                        "name": "Admin",
                        "group": "Super",
                        "created_at": "2026-08-16T00:12:51.687Z"
                    }
                ]
            }
        }
        */
    }

    // Get Roles By Group
    public static function getRolesByGroup($authToken, $group)
    {
        $data = [];

        return self::sendRequest('get', '/roles/group/'.rawurlencode($group), $data, $authToken);

        /*
        {
            "status": "success",
            "message": "Berhasil mengambil data roles.",
            "data": {
                "roles": [
                    {
                        "id": "8357fda6-67f7-4a99-8f01-9847d6920596",
                        "name": "Admin",
                        "group": "Kemahasiswaan",
                        "created_at": "2026-06-14T12:32:57.444Z"
                    },
                    {
                        "id": "34f397b7-1f5c-426b-a6ef-b618f1e5f866",
                        "name": "Anggota",
                        "group": "Kemahasiswaan",
                        "created_at": "2026-08-16T00:12:51.687Z"
                    }
                ]
            }
        }
        */
    }

    // Get All Roles Users
    public static function getRolesUsers($authToken)
    {
        $data = [];

        return self::sendRequest('get', '/roles/users', $data, $authToken);

        /*
        {
        "status": "success",
        "message": "Berhasil mengambil data users.",
        "data": {
                "userRoles": [
                    {
                        "id": "5264290c-e0e6-4e9c-ab85-3fac07de34b4",
                        "user_id": "8357fda6-67f7-4a99-8f01-9847d6920596",
                        "role_id": "8357fda6-67f7-4a99-8f01-9847d6920596",
                        "created_at": "2026-06-14T12:32:59.829Z",
                        "list_role": [
                        {
                            "id": "8357fda6-67f7-4a99-8f01-9847d6920596",
                            "name": "Admin",
                            "group": "",
                            "created_at": "2026-06-14T12:32:57.444Z"
                        }
                        ],
                        "data_user": {
                            "id": "8357fda6-67f7-4a99-8f01-9847d6920596",
                            "name": "Abdullah xxx",
                            "gender": "M",
                            "username": "abdullxxx",
                            "email": "abdullah.xxxx",
                            "alias": "Pegawai",
                            "photo": "http://localhost:6610/img/users/user-8357fxxxxx920596.png",
                            "created_at": "2025-08-04T06:10:51.794Z",
                            "updated_at": "2026-08-05T07:32:11.951Z",
                            "password_updated_at": "2025-10-25T21:16:58.723Z",
                            "nim": "",
                            "gender_id": "Laki-Laki",
                            "gender_en": "Male",
                            "path_photo": "user-8357fda6-67f7-4a99-8f0xxxx96.png",
                            "have_rfid": false,
                            "nidn": "",
                            "nip": "0309xxxx",
                            "cis_prodi_id": null
                        }
                    }
                ]
            }
        }
        */
    }

    // Get Roles Users By Group
    public static function getRolesUsersByGroup($authToken, $group)
    {
        $data = [];

        return self::sendRequest('get', "/roles/users/group/{$group}", $data, $authToken);

        /*
        {
        "status": "success",
        "message": "Berhasil mengambil data users.",
        "data": {
                "userRoles": [
                    {
                        "id": "5264290c-e0e6-4e9c-ab85-3fac07de34b4",
                        "user_id": "8357fda6-67f7-4a99-8f01-9847d6920596",
                        "role_id": "8357fda6-67f7-4a99-8f01-9847d6920596",
                        "created_at": "2026-06-14T12:32:59.829Z",
                        "list_role": [
                        {
                            "id": "8357fda6-67f7-4a99-8f01-9847d6920596",
                            "name": "Admin",
                            "group": "Kemahasiswaan",
                            "created_at": "2026-06-14T12:32:57.444Z"
                        }
                        ],
                        "data_user": {
                            "id": "8357fda6-67f7-4a99-8f01-9847d6920596",
                            "name": "Abdullah xxx",
                            "gender": "M",
                            "username": "abdullxxx",
                            "email": "abdullah.xxxx",
                            "alias": "Pegawai",
                            "photo": "http://localhost:6610/img/users/user-8357fxxxxx920596.png",
                            "created_at": "2025-08-04T06:10:51.794Z",
                            "updated_at": "2026-08-05T07:32:11.951Z",
                            "password_updated_at": "2025-10-25T21:16:58.723Z",
                            "nim": "",
                            "gender_id": "Laki-Laki",
                            "gender_en": "Male",
                            "path_photo": "user-8357fda6-67f7-4a99-8f0xxxx96.png",
                            "have_rfid": false,
                            "nidn": "",
                            "nip": "0309xxxx",
                            "cis_prodi_id": null
                        }
                    }
                ]
            }
        }
        */
    }

    // Post Roles
    public static function postRole($authToken, $name, $group)
    {
        $data = [
            'name' => $name,
            'group' => $group,
        ];

        return self::sendRequest('post', '/roles', $data, $authToken);

        /*
        {
            "status": "success",
            "message": "Berhasil menambahkan role baru."
        }
        */
    }

    // Post role users
    public static function postRoleUser($authToken, $user_id, $role_id)
    {
        $data = [
            'user_id' => $user_id,
            'role_id' => $role_id,
        ];

        return self::sendRequest('post', '/roles/users', $data, $authToken);

        /*
        {
            "status": "success",
            "message": "Berhasil menambahkan role ke user."
        }
        */
    }

    // Post role users by name
    public static function postRoleUserByName($authToken, $user_id, $role_name, $role_group)
    {
        $data = [
            'user_id' => $user_id,
            'name' => $role_name,
            'group' => $role_group,
        ];

        return self::sendRequest('post', '/roles/users/name', $data, $authToken);

        /*
        {
            "status": "success",
            "message": "Berhasil menambahkan role ke user."
        }
        */
    }

    // Delete Roles
    public static function deleteRole($authToken, $roleId)
    {
        $data = [];

        return self::sendRequest('delete', "/roles/$roleId", $data, $authToken);

        /*
        {
            "status": "success",
            "message": "Berhasil menghapus role."
        }
        */
    }

    // Delete Roles Users
    public static function deleteRoleUser($authToken, $roleId, $userId)
    {
        $data = [];

        return self::sendRequest('delete', "/roles/$roleId/users/$userId", $data, $authToken);

        /*
        {
            "status": "success",
            "message": "Berhasil menghapus role dari user."
        }
        */
    }

    // Delete Role From User Group
    public static function deleteRoleUserGroup($authToken, $group, $userId)
    {
        $data = [];

        return self::sendRequest('delete', "/roles/group/$group/users/$userId", $data, $authToken);

        /*
        {
            "status": "success",
            "message": "Berhasil menghapus role dari user."
        }
        */
    }

    // Users
    // ------------------------------

    // Get Users
    public static function getUsers($authToken, $search = '', $limit = 100, $alias = '')
    {
        $data = [
            'search' => $search,
            'limit' => $limit,
            'alias' => $alias,
        ];

        return self::sendRequest('get', '/users', $data, $authToken);
    }

    // Get Users Online
    public static function getUsersOnline($authToken)
    {
        $data = [];

        return self::sendRequest('get', '/users/online', $data, $authToken);
    }

    // Get Users Birthday
    public static function getUsersBirthday($authToken, $timeBirthday)
    {
        $data = [];

        return self::sendRequest('get', "/users/birthday/$timeBirthday", $data, $authToken);
    }

    // Get Me
    public static function getMe($authToken)
    {
        $data = [];

        return self::sendRequest('get', '/users/me', $data, $authToken);
    }

    // Put Me Photo
    public static function putMePhoto($authToken, $photo)
    {
        $data = [];

        return self::sendRequestWithFile('put', '/users/me/photo', $data, $authToken, 'photo', $photo);
    }

    // Post Request Users By User IDs
    public static function postReqUsersByIds($authToken, $userIds)
    {
        if (is_array($userIds)) {
            $userIds = implode(',', $userIds);
        }
        // dd($userIds);

        $data = [
            'user_ids' => $userIds,
        ];

        return self::sendRequest('post', '/users/request', $data, $authToken);
    }

    // Post Request Users By Usernames
    public static function postReqUsersByUsernames($authToken, $usernames)
    {
        if (is_array($usernames)) {
            $usernames = implode(',', $usernames);
        }

        $data = [
            'usernames' => $usernames,
        ];

        return self::sendRequest('post', '/users/request-by-usernames', $data, $authToken);
    }

    // Post Request Users By NIM
    public static function postReqUsersByNIM($authToken, $nim_lists)
    {
        if (is_array($nim_lists)) {
            $nim_lists = implode(',', $nim_lists);
        }

        $data = [
            'nim_lists' => $nim_lists,
        ];

        return self::sendRequest('post', '/users/request-by-nim-list', $data, $authToken);
    }

    // Put Me Password
    public static function putMePassword($authToken, $password, $oldPassword)
    {
        $data = [
            'password' => $password,
            'old_password' => $oldPassword,
        ];

        return self::sendRequest('put', '/users/me/password', $data, $authToken);
    }

    // Delete Me
    public static function deleteMe($authToken, $password, $code)
    {
        $data = [
            'password' => $password,
            'code' => $code,
        ];

        return self::sendRequest('delete', '/users/me', $data, $authToken);
    }

    // Get Users Name by IDs
    public static function getUsersNameByIds($authToken, $userIds)
    {
        $data = [];

        return self::sendRequest('get', "/users/name/$userIds", $data, $authToken);
    }

    // Get User by user id
    public static function getUserById($authToken, $userId)
    {
        $data = [];

        return self::sendRequest('get', "/users/$userId", $data, $authToken);
    }

    // Get User by Username
    public static function getUserByUsername($authToken, $username)
    {
        $data = [];

        return self::sendRequest('get', "/users/username/$username", $data, $authToken);
    }

    // Delete User TOTP
    public static function deleteUserTotp($authToken, $userId, $password)
    {
        $data = [
            'password' => $password,
        ];

        return self::sendRequest('delete', "/users/totp/$userId", $data, $authToken);
    }

    // Get User by Identifier
    public static function getUserByIdentifier($authToken, $identifier)
    {
        $data = [];

        return self::sendRequest('get', "/users/identifier/$identifier/scan", $data, $authToken);
    }

    // Login QRCode
    // ---------------------------------

    // postLoginQRCodeSetup
    public static function postLoginQRCodeSetup($system_id, $platform, $info)
    {
        $data = [
            'system_id' => $system_id,
            'platform' => $platform,
            'info' => $info,
        ];

        return self::sendRequest('post', '/auth/login-qrcode', $data);
    }

    // getLoginQRCodeById
    public static function getLoginQRCodeById($login_id)
    {
        $data = [];

        return self::sendRequest('get', "/auth/login-qrcode/{$login_id}", $data);
    }

    // Login Redirect
    // ---------------------------------

    // Post Login Redirect
    public static function postLoginRedirect($authToken)
    {
        $data = [];

        return self::sendRequest('post', '/auth/login/redirect', $data, $authToken);

        /*
         * {
         *     "status": "success",
         *     "message": "Berhasil mengambil informasi login.",
         *     "data": {
         *         "redirect_token": "6f35a103-c963-xxxx-xxxx-xxxxxxxxxxxx",
         *     }
         * }
         */

    }

    // Post Login Redirect Verify
    public static function postLoginRedirectVerify($redirectToken)
    {
        $data = [
            'redirect_token' => $redirectToken,
        ];

        return self::sendRequest('post', '/auth/login/redirect/verify', $data);

        /*
         * {
         *     "status": "success",
         *     "message": "Berhasil mengambil informasi login.",
         *     "data": {
         *         "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6xxxxx-xxxx-xxxx-xxxxxxxxxxxx",
         *     }
         * }
         */

    }

    // Websites
    // ---------------------------------

    // get Websites
    public static function getWebsites($authToken)
    {
        $data = [];

        return self::sendRequest('get', '/websites', $data, $authToken);

        /*
         * {
         *   "status": "success",
         *   "message": "Berhasil mengambil data websites.",
         *   "data": {
         *     "websites": [
         *       {
         *         "id": "c9dac08d-d4f7-4b29-982d-e3b78b738bc4",
         *         "name": "Del AC ID",
         *         "url": "https://del.ac.id",
         *         "is_allow_login_redirect": false,
         *         "created_at": "2026-06-19T04:40:36.823Z",
         *         "updated_at": "2026-06-19T04:40:36.823Z"
         *       }
         *     ]
         *   }
         * }
         */

    }

    // post Websites
    public static function postWebsite($authToken, $name, $url, $isAllowLoginRedirect)
    {
        $data = [
            'name' => $name,
            'url' => $url,
            'is_allow_login_redirect' => $isAllowLoginRedirect,
        ];

        return self::sendRequest('post', '/websites', $data, $authToken);

        /*
         * {
         *   "status": "success",
         *   "message": "Berhasil menambahkan website baru."
         * }
         */

    }

    // update Websites
    public static function updateWebsite($authToken, $websiteId, $name, $url, $isAllowLoginRedirect)
    {
        $data = [
            'name' => $name,
            'url' => $url,
            'is_allow_login_redirect' => $isAllowLoginRedirect,
        ];

        return self::sendRequest('put', "/websites/$websiteId", $data, $authToken);

        /*
         * {
         *   "status": "success",
         *   "message": "Berhasil mengupdate website."
         * }
         */

    }

    // delete Websites
    public static function deleteWebsite($authToken, $websiteId)
    {
        $data = [];

        return self::sendRequest('delete', "/websites/$websiteId", $data, $authToken);

        /*
         * {
         *   "status": "success",
         *   "message": "Berhasil menghapus website."
         * }
         */

    }

    // get users mahasiswa
    // examples-json: .examples\user-services\get-users-mahasiswa.json
    public static function getUsersMahasiswa($authToken, $perPage = 20, $page = 1, $angkatan = '', $cisProdiIds = [], $familyConnected = '', $search = '')
    {
        if (is_array($cisProdiIds)) {
            $cisProdiIds = implode(',', $cisProdiIds);
        }

        $data = [
            'perPage' => $perPage,
            'page' => $page,
            'angkatan' => $angkatan,
            'cisProdiIds' => $cisProdiIds,
            'search' => $search,
            'familyConnected' => $familyConnected,
        ];

        return self::sendRequest('get', '/users/mahasiswa', $data, $authToken);

    }

    // Device Login
    // ---------------------------------

    // GetDeviceSign
    public static function getDeviceSign($authToken)
    {
        $data = [];

        return self::sendRequest('get', '/auth/login/device', $data, $authToken);

        /*
        {
            "status": "success",
            "message": "Berhasil mengambil informasi device sign.",
            "data": {
                "users": [
                {
                    "id": "8357fda6-67f7-xxxx",
                    "name": "Abdullah xxxx",
                    "gender": "M",
                    "username": "abdulxxx",
                    "email": "abdullah.xxxx",
                    "alias": "Pegawai",
                    "photo": "http://localhost:6610/img/users/user-8357fda6-67f7-xxxx.png",
                    "created_at": "2025-08-04T06:10:51.794Z",
                    "updated_at": "2026-08-05T07:32:11.951Z",
                    "password_updated_at": "2025-10-25T21:16:58.723Z",
                    "nim": "",
                    "gender_id": "Laki-Laki",
                    "gender_en": "Male",
                    "path_photo": "user-8357fda6-67f7-xxxx.png",
                    "have_rfid": false,
                    "nidn": "",
                    "nip": "0309220455",
                    "cis_prodi_id": null,
                    "device_signs": [
                    {
                        "id": "6dcd94be-5eb5-4f00-b6f7-aec7a7679634",
                        "device_id": "1234567890",
                        "creator_id": "8357fda6-67f7-xxxx",
                        "user_id": "8357fda6-67f7-xxxx",
                        "expired_at": "2026-08-17T07:28:36.522Z",
                        "code": "DKMC8U",
                        "total_attempts": 0,
                        "created_at": "2026-08-17T07:13:36.523Z",
                        "updated_at": "2026-08-17T07:13:36.523Z"
                    }
                    ]
                }
                ],
                "creators": [
                {
                    "id": "8357fda6-67f7-xxxx",
                    "name": "Abdullah xxxx",
                    "gender": "M",
                    "username": "abdulxxx",
                    "email": "abdullah.xxxx",
                    "alias": "Pegawai",
                    "photo": "http://localhost:6610/img/users/user-8357fda6-67f7-xxxx.png",
                    "created_at": "2025-08-04T06:10:51.794Z",
                    "updated_at": "2026-08-05T07:32:11.951Z",
                    "password_updated_at": "2025-10-25T21:16:58.723Z",
                    "nim": "",
                    "gender_id": "Laki-Laki",
                    "gender_en": "Male",
                    "path_photo": "user-8357fda6-67f7-xxxx.png",
                    "have_rfid": false,
                    "nidn": "",
                    "nip": "0309220455",
                    "cis_prodi_id": null
                }
                ]
            }
            }
         */
    }

    // PostDeviceSign
    public static function postDeviceSign($authToken, $deviceId, $userId)
    {
        $data = [
            'deviceId' => $deviceId,
            'userId' => $userId,
        ];

        return self::sendRequest('post', '/auth/login/device', $data, $authToken);

        /*
         {
            "status": "success",
            "message": "Berhasil menambahkan device sign."
         }
         */

    }

    // DeleteDeviceSign
    public static function deleteDeviceSign($authToken, $deviceSignId)
    {
        $data = [];

        return self::sendRequest('delete', "/auth/login/device/$deviceSignId", $data, $authToken);

        /*
        {
            "status": "success",
            "message": "Berhasil menghapus device sign."
        }
         */

    }

    // Saudara
    public static function getMeAndSaudara($authToken)
    {
        $data = [];

        return self::sendRequest('get', '/users/me/saudara', $data, $authToken);
        // contoh-response: .examples\user-services\get-me-and-saudara.json
    }

    public static function getSaudara($authToken)
    {
        $data = [];

        return self::sendRequest('get', '/users/saudara', $data, $authToken);
        // contoh-response: .examples\user-services\get-saudara.json
    }

    public static function postSaudara($authToken, $userId1, $userId2)
    {

        $data = [
            'user_id_1' => $userId1,
            'user_id_2' => $userId2,
        ];

        return self::sendRequest('post', '/users/saudara', $data, $authToken);
        /*
        {
            "status": "success",
            "message": "Berhasil menambahkan saudara."
        }
        */
    }

    public static function deleteSaudara($authToken, $saudaraId)
    {
        $data = [];

        return self::sendRequest('delete', "/users/saudara/$saudaraId", $data, $authToken);
        /*
        {
            "status": "success",
            "message": "Berhasil menghapus saudara."
        }
        */
    }
}
