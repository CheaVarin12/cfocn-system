<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FTPConnectionService
{
    public function ServerLogin($cb = null)
    {
        $ftp_server = env('FTP_HOST');
        $ftp_user = env('FTP_USERNAME');
        $ftp_pass = env('FTP_PASSWORD');

        try {
            $result = $this->checkConnection($ftp_server, $ftp_user, $ftp_pass);
        } catch (Exception $e) {
            $result = $e->getMessage();
        }
        return $result;
    }
    public function checkConnection($host, $username, $password, $port = 21, $timeout = 5)
    {
        $con = ftp_connect($host, $port, $timeout);
        if (false === $con) {
            throw new Exception('unable_to_connect');
        }
        $loggedIn = ftp_login($con,  $username,  $password);
        ftp_close($con);
        if (true === $loggedIn) {
            return 'login_success';
        } else {
            throw new Exception('unable to log in.');
        }
    }
    public function dmcFile($path, $file, $fileName, $cb)
    {
        $disk = Storage::disk('ftp');
        try {
            $uploadFile = $disk->putFileAs($path, $file, $fileName);
            $cb($uploadFile, ($uploadFile ? 'success' : 'unsuccess'));
        } catch (\Exception $e) {
            $cb(false, $e->getMessage());
        }
    }
}
