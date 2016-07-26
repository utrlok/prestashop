<?php
if (!defined('_PS_VERSION_'))
    exit;
class vexon_admin_menu extends Module
{
    protected $_html = '';
    public function __construct()
    {
        $this->name          = 'vexon_admin_menu';
        $this->tab           = 'vexon';
        $this->version       = '1.0';
        $this->author        = 'Maciej Kara';
        $this->need_instance = 1;
        $this->is_configurable = 1;
        $this->bootstrap     = true;
        $this->currencies    = false;
        parent::__construct();
        $this->displayName = $this->l('Menu w panelu administracyjnym');
        $this->description = $this->l('Pozwala dodać własne odnośniki w bocznym menu w panelu administracyjnym');
    }
    public function install()
    {
        parent::install(); //instalacji bazy danych        
        Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'vexon_admin_menu` (`id` int(11) NOT NULL,`position` int(11) NOT NULL,`id_parent` int(11) NOT NULL,`description` varchar(64) NOT NULL,`link` varchar(256) NOT NULL,`icon` varchar(64) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
        Db::getInstance()->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'vexon_admin_menu` ADD PRIMARY KEY (`id`);');
        Db::getInstance()->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'vexon_admin_menu` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
        if (!$this->registerHook('adminMenu'))
            return false;
        return true;
    }
    public function uninstall()
    {
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'vexon_koszty_wysylki`;');
        return parent::uninstall();
    }
    public function getContent()
    {
        return $this->_html;
    }
    public function generateMenu()
    {
        //pobieranie głównych        
        $men = "SELECT * FROM " . _DB_PREFIX_ . "vexon_admin_menu ORDER BY position ASC";
        $zap = Db::getInstance()->ExecuteS($men);
        foreach ($zap as $row) {
            $menu[] = array(
                "description" => $row['description'],
                "id_parent" => $row['id_parent'],
                "id" => $row['id'],
                "link" => $row['link'],
                "position" => $row['position'],
                "icon" => $row['icon']
            );
        }
		
		return $menu;
		
    }
    public function hookAdminMenu()
    {
        $this->smarty->assign(array(
            'menu' => $this->generateMenu(),
            'token' => Tools::getAdminTokenLite('AdminModules'),
        ));
        return $this->display(__FILE__, 'menu_left.tpl');
    }
}
?>
