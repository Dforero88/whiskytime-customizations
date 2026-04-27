<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class PasswordRenewLog extends Module
{
    public function __construct()
    {
        $this->name = 'passwordrenewlog';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Ton Nom';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Password Renew Log');
        $this->description = $this->l('Affiche la liste des utilisateurs ayant réinitialisé leur mot de passe.');
    }

    public function install()
    {
        return parent::install();
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function getContent()
    {
        $output = '<h2>Liste des clients ayant réinitialisé leur mot de passe</h2>';

        $customers = $this->getCustomersWithPasswordRenewal();

        if (empty($customers)) {
            $output .= '<p>Aucun client trouvé avec une réinitialisation de mot de passe.</p>';
            return $output;
        }

        $output .= '
        <table class="table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Date de création</th>
                    <th>Date de réinitialisation</th>
                </tr>
            </thead>
            <tbody>
        ';

        foreach ($customers as $customer) {
            $output .= '
                <tr>
                    <td>' . htmlspecialchars($customer['email']) . '</td>
                    <td>' . htmlspecialchars($customer['date_add']) . '</td>
                    <td>' . htmlspecialchars($customer['last_passwd_gen']) . '</td>
                </tr>
            ';
        }

        $output .= '
            </tbody>
        </table>';

        return $output;
    }

    private function getCustomersWithPasswordRenewal()
    {
        $sql = 'SELECT email, date_add, last_passwd_gen FROM ' . _DB_PREFIX_ . 'customer
                WHERE last_passwd_gen IS NOT NULL AND DATE(last_passwd_gen) != "2025-07-21" ORDER BY last_passwd_gen DESC';

        return Db::getInstance()->executeS($sql);
    }
}
