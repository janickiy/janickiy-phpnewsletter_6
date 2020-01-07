<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use File;

class LicenseHelpers
{
    private $language;
    private $url = 'http://license.janicky.com/';
    private $currenversion;
    const licenseKey = 'license/license_key';
    const KEY    = 'Tey6P1#$(13';
    const METHOD = 'aes-256-cbc';

    public function __construct($language, $currenversion)
    {
        $this->language = $language;
        $this->currenversion = $currenversion;
    }

    /**
     * @return bool
     */
    public function checkNewVersion()
    {
        $newversion = $this->getVersion();

        if ($newversion) {
            preg_match("/(\d+)\.(\d+)\.(\d+)/", $this->currenversion, $out1);
            preg_match("/(\d+)\.(\d+)\.(\d+)/", $newversion, $out2);

            if (!isset($out1[1]) || !isset($out2[1])) return false;

            $v1 = ($out1[1] * 10000 + $out1[2] * 100 + $out1[3]);
            $v2 = ($out2[1] * 10000 + $out2[2] * 100 + $out2[3]);

            if ($v2 > $v1)
                return true;
            else
                return false;
        } else
            return false;
    }

    /**
     * @return bool
     */
    public function checkUpgrade()
    {
        $newversion = $this->getUpgradeVersion();

        if ($newversion) {
            preg_match("/(\d+)\.(\d+)\.(\d+)/", $this->currenversion, $out1);
            preg_match("/(\d+)\.(\d+)\.(\d+)/", $newversion, $out2);

            $v1 = ($out1[1] * 10000 + $out1[2] * 100 + $out1[3]);
            $v2 = ($out2[1] * 10000 + $out2[2] * 100 + $out2[3]);

            if ($v2 > $v1)
                return true;
            else
                return false;
        } else
            return false;
    }

    /**
     * @return string
     */
    public function getUrlInfo()
    {
        return $this->url . '?id=3&version=' . urlencode($this->currenversion) . '&lang=' . $this->language . '&ip=' . $this->getIP();
    }

    /**
     * @param $url
     * @param int $timeout
     * @return mixed
     */
    public function getDataContents($url, $timeout = 10)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 0);
        curl_setopt($ch, CURLOPT_REFERER, isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        $data = curl_exec($ch);

        curl_close($ch);

        preg_match('/\{([^\}])+\}/',$data, $out);

        return isset($out[0]) ? json_decode($out[0], true) : null;
    }

    /**
     * @return bool
     */
    public function checkTree()
    {
        preg_match("/(\d+)\.(\d+)\.(\d+)/", $this->currenversion, $out);

        if ($out[1] < $out[2])
            return false;
        else
            return true;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        $out = $this->getDataContents($this->getUrlInfo());
        return $out["version"];
    }

    /**
     * @return mixed
     */
    public function getDownloadLink()
    {
        $out = $this->getDataContents($this->getUrlInfo());
        return $out['download'];
    }

    /**
     * @return mixed
     */
    public function getUpdateLink()
    {
        $out = $this->getDataContentsn($this->getUrlInfo());
        return $out['update'];
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        $out = $this->getDataContents($this->getUrlInfo());
        return $out['created'];
    }

    /**
     * @return mixed
     */
    public function getUpdate()
    {
        $out = $this->getDataContents($this->getUrlInfo());
        return $out['update'];
    }

    /**
     * @return mixed
     */
    public function getUpgradeVersion()
    {
        $out = $this->getDataContents($this->getUrlInfo());
        return $out['upgrade_version'];
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        $out = $this->getDataContents($this->getUrlInfo());
        return $out['message'];
    }

    /**
     * @return string
     */
    public function getIP()
    {
        if (getenv("HTTP_CLIENT_IP") and strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
            $ip = getenv("HTTP_CLIENT_IP");
        elseif (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        elseif (getenv("REMOTE_ADDR") and strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
            $ip = getenv("REMOTE_ADDR");
        elseif (!empty($_SERVER['REMOTE_ADDR']) and strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
            $ip = $_SERVER['REMOTE_ADDR'];
        else
            $ip = "unknown";

        return $ip;
    }


    /**
     * @return string|null
     */
    public function getLicenseInfo()
    {
        if (Storage::exists(self::licenseKey)) {
            $storagePath  = Storage::disk('local')->path(self::licenseKey);
            $contents = File::get($storagePath);

            return json_decode(self::decodeStr($contents), true);
        } else
            return null;
    }

    /**
     * @param $licenseKey
     * @return array
     */
    public function makeLicensekey($licenseKey)
    {
        $domain = (substr($_SERVER["SERVER_NAME"], 0, 4)) == "www." ? str_replace('www.','', $_SERVER["SERVER_NAME"]) : $_SERVER["SERVER_NAME"];
        $lisenseInfo = $this->getDataContents($this->url . '?t=licensekey&licensekey=' . $licenseKey . '&domain=' . $domain, 5);

        if (!isset($lisenseInfo['error'])) {
            $data = [
                'domain' => $domain,
                'license_type' => $lisenseInfo['license_type'],
                'licensekey'   => $licenseKey,
                'created'   => $lisenseInfo['date_created'],
                'date_from' => $lisenseInfo['date_active_from'],
                'date_to'   => $lisenseInfo['date_active_to']
            ];

            $encodeStr = self::encodeStr(json_encode($data));

            if (Storage::disk('local')->put(self::licenseKey, $encodeStr) === false) {
                return ['result' => false, 'msg' => trans('license.error.cannot_create_licensekey_file')];
            }
        } else {
            return ['result' => false, 'msg' => trans('license.error.check_licensekey')];
        }

        return ['result' => true];
    }

    /**
     * @param $str
     * @return string
     */
    static public function encodeStr($str)
    {
        $ivsize = openssl_cipher_iv_length(self::METHOD);
        $iv = openssl_random_pseudo_bytes($ivsize);
        $ciphertext = openssl_encrypt($str, self::METHOD, self::KEY, OPENSSL_RAW_DATA, $iv);

        return base64_encode($iv . $ciphertext);
    }

    /**
     * @param $str
     * @return string
     */
    static public function decodeStr($str)
    {
        $str =  base64_decode($str);
        $ivsize = openssl_cipher_iv_length(self::METHOD);
        $iv = substr($str, 0, $ivsize);
        $ciphertext = substr($str, $ivsize);

        return openssl_decrypt(
            $ciphertext,
            self::METHOD,
            self::KEY,
            OPENSSL_RAW_DATA,
            $iv
        );
    }

    /**
     * @param $licenseKey
     * @return array|mixed
     */
    public function checkLicenseKey($licenseKey)
    {
        $domain = (substr($_SERVER['SERVER_NAME'], 0, 4)) == "www." ? str_replace('www.','', $_SERVER['SERVER_NAME']) : $_SERVER['SERVER_NAME'];
        $data = $this->getDataContents($this->url . '?t=check_licensekey&licensekey=' . $licenseKey . '&domain=' . $domain . '&s=phpnewsletter&version=' . urlencode($this->currenversion), 5);

        if ($data)  {
            return $data;
        } else {
            return ['error' => 'ERROR_CHECKING_LICENSE'];
        }
    }

    /**
     * @param $licenseKey
     */
    public function updateLicensekey($licenseKey)
    {
        $lisense_info = $this->getLicenseInfo();

        if ($lisense_info['licensekey'] != $licenseKey) {
            $this->makeLicensekey($licenseKey);
        }
    }
}
