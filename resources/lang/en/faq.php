<?php

return [
    'str' => '
<strong>Q:How can I personalize my emails?</strong>
<p>A: Yes, you can. To do this, put %NAME% in the letter template. The next mailing letters, each time it will be replaced by the name of the subscriber.</p>
<strong>Q: Why don\'t I get emails to Gmail\'s Box?</strong>
<p>A: Probably IP of your SMTP server is in the blacklist of Gmail or emails are filtered by antispam filter. Contact support of Gmail.</p>
<strong>Q: Mailing log reported that 300 emails were sent out, but had received half of them. Why?</strong>
<p>A: Try to identify the reason why no letters were sent. You can seen it delivery report in the statement of mail delivery,
    which usually is delivering at the e-mail address that specified in the settings for value "E-mail".
    You or the sending server should receive a delivery report with the reasons for non-delivery.</p>
<strong>Q: I can not send any newsletter via SMTP server. The log mailing writes the following error:
    "The following From address failed: vasya-pupkin@my-domain.com : Called Mail() without being connected". What\'s wrong?</strong>
<p>A: There are a several reason. Perhaps you may have set the wrong address or port in SMTP server settings.
    Another reason may be that access to the SMTP server is closed by a firewall or SMTP server is temporarily unavailable.</p>
<strong>Q: No picture is shown in the HTML e-mail format.</strong>
<p>A: For security purposes most e-mail clients and free public email services block images downloaded from external sources.</p>
<strong>Q: What is SMTP server?</strong>
<p>A: SMTP (Simple Mail Transfer Protocol) - is the server in a network, global or
    local which accepts e-mail on further transfer, and also accepts e-mail from other servers for his local users.</p>
<strong>Q: Attachments are not displayed in sent emails. How to fix it?</strong>
<p>A: Change file permissions (CHMOD) for the file index.php, and also for the directories "attach" to 755.</p>
<strong>Q: The mailing log is not considered sending the number of read emails. Why?</strong>
<p>A: Check the mail format in the settings. This feature only works if you selected HTML format.</p>
'
];
