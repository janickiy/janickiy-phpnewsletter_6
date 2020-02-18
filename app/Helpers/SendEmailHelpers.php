<?php

namespace App\Helpers;

use PHPMailer\PHPMailer;
use App\Models\{Attach,Smtp, Customheaders};
use Illuminate\Support\Facades\Storage;

class SendEmailHelpers
{

    private static $subject;

    private static $body;

    private static $email;

    private static $prior;

    private static $name = 'USERNAME';

    private static $templateId = 0;

    private static $subscriberId = 0;

    private static $token = '';


    /**
     * @return mixed
     */
    public static function getSubject() {
        return self::$subject;
    }

    public static function getToken() {
        return self::$token;
    }

    /**
     * @return mixed
     */
    public static function getBody() {
        return self::$body;
    }

    /**
     * @return mixed
     */
    public static function getEmail() {
        return self::$email;
    }

    /**
     * @return mixed
     */
    public static function getPrior() {
        return self::$prior;
    }

    /**
     * @return string
     */
    public static function getName() {
        return self::$name;
    }

    /**
     * @return int
     */
    public static function getTemplateId() {
        return self::$templateId;
    }

    /**
     * @return int
     */
    public static function getSubscriberId() {
        return self::$subscriberId;
    }

    /**
     * @param $subject
     * @return mixed
     */
    public static function setSubject($subject)
    {
        return self::$subject = $subject;
    }

    /**
     * @param $body
     * @return mixed
     */
    public static function setBody($body)
    {
        return self::$body = $body;
    }

    /**
     * @param $token
     * @return mixed
     */
    public static function setToken($token)
    {
        return self::$token = $token;
    }

    /**
     * @param $email
     * @return mixed
     */
    public static function setEmail($email)
    {
        return self::$email = $email;
    }

    /**
     * @param $prior
     * @return mixed
     */
    public static function setPrior($prior)
    {
        return self::$prior = $prior;
    }

    /**
     * @param $name
     * @return mixed
     */
    public static function setName($name)
    {
        return self::$name = $name;
    }

    /**
     * @param $templateId
     * @return mixed
     */
    public static function setTemplateId($templateId)
    {
        return self::$templateId = $templateId;
    }

    /**
     * @param $subscriberId
     * @return mixed
     */
    public static function setSubscriberId($subscriberId)
    {
        return self::$subscriberId = $subscriberId;
    }

    /**
     * @param $attach
     * @return bool
     * @throws PHPMailer\Exception
     */
    public static function sendEmail($attach = null)
    {
        $subject = self::getSubject();
        $body = self::getBody();
        $email = self::getEmail();
        $prior = self::getPrior();
        $name = self::getName();
        $templateId = self::getTemplateId();
        $subscriberId = self::getSubscriberId();
        $token = self::getToken();

        $m = new PHPMailer\PHPMailer();

        if (SettingsHelpers::getSetting('ADD_DKIM') == 1) {
            $m->DKIM_domain = SettingsHelpers::getSetting('DKIM_DOMAIN');
            $m->DKIM_private_string = SettingsHelpers::getSetting('DKIM_PRIVATE');
            $m->DKIM_selector = SettingsHelpers::getSetting('DKIM_SELECTOR');
            $m->DKIM_passphrase = SettingsHelpers::getSetting('DKIM_PASSPHRASE');
            $m->DKIM_identity = SettingsHelpers::getSetting('DKIM_IDENTITY');
        }

        if (SettingsHelpers::getSetting('HOW_TO_SEND') == 'smtp') {
            $m->IsSMTP();
            $m->SMTPAuth = true;
            $m->SMTPKeepAlive = true;

            $smtp_q = Smtp::query();

            if ($smtp_q->count() > 1) {
                $smtp_r = $smtp_q->orderBy()->limit(1)->get();
            } else {
                $smtp_r = $smtp_q->limit(1)->get();
            }

            if ($smtp_r) $smtp = $smtp_r->toArray();

            if (isset($smtp[0]['host']) && isset($smtp[0]['port']) && isset($smtp[0]['port']) && isset($smtp[0]['username']) && isset($smtp[0]['password'])) {
                $m->Host = $smtp[0]['host'];
                $m->Port = $smtp[0]['port'];
                $m->From = $smtp[0]['email'];
                $m->Username = $smtp[0]['username'];
                $m->Password = $smtp[0]['password'];

                if ($smtp[0]['secure'] == 'ssl')
                    $m->SMTPSecure = 'ssl';
                elseif ($smtp[0]['secure'] == 'tls')
                    $m->SMTPSecure = 'tls';

                if ($smtp[0]['authentication'] == 'plain')
                    $m->AuthType = 'PLAIN';
                elseif ($smtp[0]['authentication'] == 'cram-md5')
                    $m->AuthType = 'CRAM-MD5';

                $m->Timeout = $smtp[0]['timeout'];
            }
        } elseif (SettingsHelpers::getSetting('HOW_TO_SEND') == 'sendmail' && SettingsHelpers::getSetting('SENDMAIL_PATH') != '') {
            $m->IsSendmail();
            $m->Sendmail = SettingsHelpers::getSetting('SENDMAIL_PATH');
        } else {
            $m->IsMail();
        }

        $m->CharSet = SettingsHelpers::getSetting('CHARSET');

        if ($prior == 1)
            $m->Priority = 1;
        elseif ($prior == 2)
            $m->Priority = 5;
        else $m->Priority = 3;

        if (SettingsHelpers::getSetting('HOW_TO_SEND') != 'smtp') {
            if (SettingsHelpers::getSetting('SHOW_EMAIL') == 0)
                $m->From = "noreply@" . StringHelpers::getDomain(SettingsHelpers::getSetting('URL'));
            else
                $m->From = SettingsHelpers::getSetting('EMAIL');
        }

        $m->FromName = SettingsHelpers::getSetting('FROM');

        if (SettingsHelpers::getSetting('LIST_OWNER') == '') $m->addCustomHeader("List-Owner: <" . SettingsHelpers::getSetting('LIST_OWNER') . ">");
        if (SettingsHelpers::getSetting('RETURN_PATH') != '') $m->addCustomHeader("Return-Path: <" . SettingsHelpers::getSetting('RETURN_PATH') . ">");
        if (SettingsHelpers::getSetting('CONTENT_TYPE') == 'html')
            $m->isHTML(true);
        else
            $m->isHTML(false);

        $subject = str_replace('%NAME%', $name, $subject);
        $subject = SettingsHelpers::getSetting('RENDOM_REPLACEMENT_SUBJECT') == 1 ? StringHelpers::encodeString($subject) : $subject;

        if (SettingsHelpers::getSetting('CHARSET') != 'utf-8'){
            $subject = iconv('utf-8', SettingsHelpers::getSetting('CHARSET'), $subject);
        }

        $m->Subject = $subject;

        if (SettingsHelpers::getSetting('SLEEP') > 0) sleep(SettingsHelpers::getSetting('SLEEP'));
        if (SettingsHelpers::getSetting('ORGANIZATION') != '') $m->addCustomHeader("Organization: " . SettingsHelpers::getSetting('ORGANIZATION'));
        if (SettingsHelpers::getSetting('URL') != '') $IMG = '<img alt="" border="0" src="' . (StringHelpers::getScheme(SettingsHelpers::getSetting('URL'))) . '://' . StringHelpers::getDomain(SettingsHelpers::getSetting('URL')) . '/pic/' . $subscriberId . '_' . $templateId . '" width="1" height="1">';

        $m->AddAddress($email);

        if (SettingsHelpers::getSetting('REQUEST_REPLY') == 1 && SettingsHelpers::getSetting('EMAIL') != ''){
            $m->addCustomHeader("Disposition-Notification-To: " . SettingsHelpers::getSetting('EMAIL'));
            $m->ConfirmReadingTo = SettingsHelpers::getSetting('EMAIL');
        }

        if (SettingsHelpers::getSetting('PRECEDENCE') == 'bulk')
            $m->addCustomHeader("Precedence: bulk");
        elseif (SettingsHelpers::getSetting('PRECEDENCE') == 'junk')
            $m->addCustomHeader("Precedence: junk");
        elseif (SettingsHelpers::getSetting('PRECEDENCE') == 'list')
            $m->addCustomHeader("Precedence: list");

        if (SettingsHelpers::getSetting('URL') != '') $UNSUB = SettingsHelpers::getSetting('URL') . "unsubscribe/" . $subscriberId . "/" . $token;
        $unsublink = str_replace('%UNSUB%', $UNSUB, SettingsHelpers::getSetting('UNSUBLINK'));

        $customheaders  = Customheaders::get();

        if ($customheaders) {
            foreach ($customheaders as $customheader) {
                $m->addCustomHeader($customheader->name . ": " . $customheader->value);
            }
        }

        if (SettingsHelpers::getSetting('SHOW_UNSUBSCRIBE_LINK') == 1 && SettingsHelpers::getSetting('UNSUBLINK') != '') {
            $msg = $body . "<br><br>" . $unsublink;
            $m->addCustomHeader("List-Unsubscribe: " . $UNSUB);
        } else
            $msg = $body;

        $url_info = parse_url(SettingsHelpers::getSetting('URL'));

        $msg = preg_replace_callback("/%REFERRAL\:(.+)%/isU", function($matches) { return "%URL_PATH%referral/" . base64_encode($matches[1]) . "/%USERID%"; }, $msg);
        $msg = str_replace('%NAME%', $name, $msg);
        $msg = str_replace('%UNSUB%', $UNSUB, $msg);
        $msg = str_replace('%SERVER_NAME%', $url_info['host'], $msg);
        $msg = str_replace('%USERID%', $subscriberId, $msg);
        $msg = str_replace('%URL_PATH%', SettingsHelpers::getSetting('URL'), $msg);
        $msg = SettingsHelpers::getSetting('RANDOM_REPLACEMENT_BODY') == 1 ? StringHelpers::encodeString($msg) : $msg;

        if ($attach) {
            foreach (Attach::where('templateId',$attach)->get() as $f) {

                $path = Attach::DIRECTORY . '/' . $f->file_name;

                if (Storage::exists($path)) {

                    $storagePath  = Storage::disk('local')->path($path);

                    if (SettingsHelpers::getSetting('CHARSET') != 'utf-8') $f->name = iconv('utf-8', SettingsHelpers::getSetting('CHARSET'), $f->name);

                    $ext = pathinfo($f->file_name, PATHINFO_EXTENSION);;
                    $mime_type = StringHelpers::getMimeType($ext);

                    $m->AddAttachment($storagePath, $f->name, 'base64', $mime_type);
                }
            }
        }

        if (SettingsHelpers::getSetting('CHARSET') != 'utf-8') $msg = iconv('utf-8', SettingsHelpers::getSetting('CHARSET'), $msg);
        if (SettingsHelpers::getSetting('CONTENT_TYPE') == 'html'){
            $msg .= $IMG;
        } else {
            $msg = preg_replace('/<br(\s\/)?>/i', "\n", $msg);
            $msg = StringHelpers::removeHtmlTags($msg);
        }

        $m->Body = $msg;

        if (!$m->Send()){
            $result =  ['result' => false, 'error' => $m->ErrorInfo];

        } else {
            $result = ['result' => true, 'error' => null];
        }

        $m->ClearCustomHeaders();
        $m->ClearAllRecipients();
        $m->ClearAttachments();

        if (SettingsHelpers::getSetting('HOW_TO_SEND') == 'smtp') $m->SmtpClose();

        return $result;
    }
}
