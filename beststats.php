<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Beststats extends Module
{
    public function __construct()
    {
        $this->name = 'beststats';
        $this->tab = 'analytics_stats';
        $this->version = '1.1.0'; // Podbijamy wersję
        $this->author = 'BESTLAB Ernest';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Best Stats');
        $this->description = $this->l('Moduł do wyświetlania zaawansowanych statystyk sprzedaży.');

        $this->confirmUninstall = $this->l('Czy na pewno chcesz odinstalować ten moduł?');
    }

    public function install()
    {
        if (!parent::install() || !$this->installTab()) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() || !$this->uninstallTab()) {
            return false;
        }
        return true;
    }

    public function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminBeststats';
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Statystyka sprzedaży';
        }
        $tab->id_parent = 492; 
        $tab->module = $this->name;
        return $tab->add();
    }

    public function uninstallTab()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminBeststats');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        return true;
    }
    
    public function getContent()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/admin.css');

        $today = new DateTime();
        $yesterday = (new DateTime())->modify('-1 day');
        $month_start = new DateTime('first day of this month');

        $stats_today = $this->getStatsForDateRange($today, $today);
        $stats_yesterday = $this->getStatsForDateRange($yesterday, $yesterday);
        $stats_month = $this->getStatsForDateRange($month_start, $today);
        
        $top_customers = $this->getTopCustomersOfMonth();
        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

        foreach ($top_customers as &$customer) {
            $customer['total_spent_formatted'] = Tools::displayPrice($customer['total_spent'], $currency);
        }

        $this->context->smarty->assign([
            'today_date' => $today->format('Y-m-d'),
            'yesterday_date' => $yesterday->format('Y-m-d'),
            'stats_today' => $this->formatStats($stats_today, $currency),
            'stats_yesterday' => $this->formatStats($stats_yesterday, $currency),
            'stats_month' => $this->formatStats($stats_month, $currency),
            'top_customers' => $top_customers,
            'bootstrap_version' => 5
        ]);

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }

    private function getStatsForDateRange(DateTime $dateFrom, DateTime $dateTo)
    {
        $cancelled_status_id = 6; // Status "Anulowane"

        $query = new DbQuery();
        // Używamy warunkowego sumowania (IF), aby obliczyć wszystko w jednym zapytaniu
        $query->select('SUM(IF(o.current_state != '.(int)$cancelled_status_id.', o.total_paid_tax_incl - o.total_shipping_tax_incl, 0)) as total_revenue');
        $query->select('SUM(IF(o.current_state = '.(int)$cancelled_status_id.', o.total_paid_tax_incl - o.total_shipping_tax_incl, 0)) as cancelled_revenue');
        $query->select('COUNT(IF(o.current_state != '.(int)$cancelled_status_id.', o.id_order, NULL)) as order_count');
        $query->select('SUM(IF(o.current_state != '.(int)$cancelled_status_id.', o.total_discounts_tax_incl, 0)) as total_discounts');

        $query->from('orders', 'o');
        $query->where('o.date_add BETWEEN "' . $dateFrom->format('Y-m-d 00:00:00') . '" AND "' . $dateTo->format('Y-m-d 23:59:59') . '"');
        // Usunęliśmy filtr `current_state` z WHERE, aby objąć zapytaniem wszystkie zamówienia z danego okresu
        
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
        
        $stats = [
            'revenue' => (float) $result['total_revenue'],
            'order_count' => (int) $result['order_count'],
            'discounts' => (float) $result['total_discounts'],
            'cancelled_revenue' => (float) $result['cancelled_revenue'],
            'average' => 0
        ];

        if ($stats['order_count'] > 0) {
            $stats['average'] = $stats['revenue'] / $stats['order_count'];
        }

        return $stats;
    }
    
    private function formatStats(array $stats, Currency $currency)
    {
        return [
            'revenue' => Tools::displayPrice($stats['revenue'], $currency),
            'order_count' => $stats['order_count'],
            'discounts' => Tools::displayPrice($stats['discounts'], $currency),
            'average' => Tools::displayPrice($stats['average'], $currency),
            'cancelled_revenue' => Tools::displayPrice($stats['cancelled_revenue'], $currency), // NOWA WARTOŚĆ
        ];
    }
    
    private function getTopCustomersOfMonth()
    {
        $month_start = new DateTime('first day of this month');
        $today = new DateTime();
        $delivered_status_id = 4;

        $query = new DbQuery();
        $query->select('c.firstname, c.lastname, SUM(o.total_paid_tax_incl - o.total_shipping_tax_incl) as total_spent');
        $query->from('orders', 'o');
        $query->leftJoin('customer', 'c', 'c.id_customer = o.id_customer');
        $query->where('o.date_add BETWEEN "' . $month_start->format('Y-m-d 00:00:00') . '" AND "' . $today->format('Y-m-d 23:59:59') . '"');
        $query->where('o.current_state = ' . (int)$delivered_status_id);
        $query->where('o.id_customer != 0');
        $query->groupBy('o.id_customer');
        $query->orderBy('total_spent DESC');
        $query->limit(10);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }
}