<?php

namespace App\Helpers;

class UpdateHelpers
{
    private $language;
    private $url = 'http://license.janicky.com/';
    private $currenversion;

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

        return json_decode($out[0], true);
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
     * @param $license_key
     * @return array|mixed
     */
    public function checkLicenseKey($license_key)
    {
        $domain = (substr($_SERVER['SERVER_NAME'], 0, 4)) == "www." ? str_replace('www.','', $_SERVER['SERVER_NAME']) : $_SERVER['SERVER_NAME'];
        $url = $this->url . '?t=check_licensekey&licensekey=' . $license_key . '&domain=' . $domain . '&s=phpnewsletter&version=' . urlencode($this->currenversion);
        $data = $this->getDataContents($url, 5);

        if ($data)  {
            return $data;
        } else {
            return ['error' => 'ERROR_CHECKING_LICENSE'];
        }
    }
}
