<?php

class AdminBeststatsController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
    }
    
    public function initContent()
    {
        parent::initContent();
        
        $configure_url = $this->context->link->getAdminLink(
            'AdminModules',
            true,
            [],
            ['configure' => $this->module->name]
        );
        
        Tools::redirectAdmin($configure_url);
    }
}