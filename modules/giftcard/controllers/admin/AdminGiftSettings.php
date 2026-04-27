<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FMM Modules
 *  @copyright FME Modules 2024
 *  @license   Single domain
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class AdminGiftSettingsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->lang = false;
        $this->deleted = false;
        $this->bootstrap = true;
        parent::__construct();
        $this->context = Context::getContext();
    }

    public function initProcess()
    {
        parent::initProcess();
        $url = Context::getContext()->link->getAdminLink('AdminModules') . '&configure=' . $this->module->name . '&token=' . Tools::getAdminTokenLite('AdminModules');
        Tools::redirectAdmin($url);
    }
}
