<?php
/**
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Fixcwe79cwe89 extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'fixcwe79cwe89';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'DNK';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('CWE-79_CWE-89');
        $this->description = $this->l('CWE-79 CVE-2023-30838 CWE-89 CVE-2023-30839');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install() && $this->applayPatch();
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->removePatch();
    }

    public function applayPatch(){
        $file = _PS_CORE_DIR_ . '/classes/Validate.php';
        $bfile = _PS_CORE_DIR_ . '/classes/Validate.php.backup';
        $content = file_get_contents($file);
        if ($content !== false && file_put_contents($bfile, $content) !== false) {
            $search = '$events .= \'|onselectstart|onstart|onstop\';';
            $replace = '$events .= \'|onselectstart|onstart|onstop|onanimationcancel|onanimationend|onanimationiteration|onanimationstart\';';
            $content = str_replace($search, $replace, $content);
            if (file_put_contents($file,$content) === false) {
                return false;
            }
        }

        $file = _PS_CORE_DIR_ . '/classes/db/Db.php';
        $bfile = _PS_CORE_DIR_ . '/classes/db/Db.php.backup';
        $content = file_get_contents($file);
        if ($content !== false && file_put_contents($bfile, $content) !== false) {
            $search = '/(if \N+?select\|show\|explain\|describe\|desc\N+?{)[\W\w]+?(\s+?throw new PrestaShopDatabaseException\N+?\);)[\W\w]+?(\$use_cache\);)/';
            $replace = '$1$2';
            $content = preg_replace($search, $replace, $content);
            if (file_put_contents($file,$content) === false) {
                return false;
            }
        }

        return true;
    }

    public function removePatch(){
        $file = _PS_CORE_DIR_ . '/classes/Validate.php';
        $bfile = _PS_CORE_DIR_ . '/classes/Validate.php.backup';
        $content = file_get_contents($bfile);
        if ($content !== false) {
            if (file_put_contents($file, $content) === false) {
                return false;
            } else {
                unlink($bfile);
            }
        }

        $file = _PS_CORE_DIR_ . '/classes/db/Db.php';
        $bfile = _PS_CORE_DIR_ . '/classes/db/Db.php.backup';
        $content = file_get_contents($bfile);
        if ($content !== false) {
            if (file_put_contents($file, $content) === false) {
                return false;
            } else {
                unlink($bfile);
            }
        }
    }
}
