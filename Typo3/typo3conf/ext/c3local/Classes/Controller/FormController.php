<?php

namespace C3\Local\Controller;

use In2code\Powermail\Controller\FormController as BeforeSaveToDb;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class FormController
 */
class FormController extends BeforeSaveToDb
{
    /**
     * Manipulate message object short before powermail send the mail
     *
     * @param Mail $mail
     * @return void
     */
    public function manipulateMailObjectOnCreate($mail)
    {
        /**
         * if field is available in form
         * Example: name-of-marker-you-are-looking-for
         * GeneralUtility::_POST("tx_powermail_pi1")["field"]["name-of-marker-you-are-looking-for"]
         */
        #$marker = GeneralUtility::_POST("tx_powermail_pi1")["field"]["name-of-marker-you-are-looking-for"];
        #if (!$marker && $marker == '')
        #    return;
        #DebuggerUtility::var_dump($user);
        #DebuggerUtility::var_dump($mail);
        #die();
    }
}