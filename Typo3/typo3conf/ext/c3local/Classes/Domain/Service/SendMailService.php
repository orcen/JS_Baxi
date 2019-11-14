<?php

namespace C3\Local\Domain\Service;

use In2code\Powermail\Domain\Service\Mail\SendMailService as SendMailServicePowermail;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class SendMailService
 */
class SendMailService extends SendMailServicePowermail
{

    /**
     * Manipulate message object short before powermail send the mail
     *
     * @param MailMessage $message
     * @param array $email
     * @param SendMailServicePowermail $originalService
     */
    public function manipulateMailTo($message, &$email, SendMailServicePowermail $originalService)
    {
        /**
         * if field is available in form
         * Example: ["field-marker-you-are-looking-for"]
         * GeneralUtility::_POST("tx_powermail_pi1")["field"]["name-of-marker-you-are-looking-for"]
         */
        #$marker = GeneralUtility::_POST("tx_powermail_pi1")["field"]["name-of-marker-you-are-looking-for"];
        #DebuggerUtility::var_dump($marker);

        #if (!$marker && $marker == '')
        #    return;

        #if ($originalService->getType() === "receiver") {
        #    $email['receiverEmail'] = 'schuessler@myc3.com';
        #	$email['receiverName'] = 'Dominik Schüßler';
        #    $message->setTo([$email['receiverEmail'] => $email['receiverName']]);
        #}
    }
}