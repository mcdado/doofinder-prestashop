<?php
/**
 * @author    Doofinder
 * @copyright Doofinder
 * @license   MIT
 * @see       https://opensource.org/licenses/MIT
 */

use PrestaShop\Module\Doofinder\Configuration\DoofinderConfig;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Upgrades the Doofinder module to version 8.6.0.
 *
 * The actionFrontControllerSetMedia hook (page type, product id and category name for the
 * single script) was added to HookManager::registerHooks() for new installs only, so modules
 * upgraded from older versions never registered it.
 *
 * @param Doofinder $module the Doofinder module instance being upgraded
 *
 * @return bool true if the hook is registered
 */
function upgrade_module_8_6_0($module)
{
    DoofinderConfig::debug('Initiating 8.6.0 upgrade');

    // Without a shop list only the shops of the current context would get the hook
    return $module->registerHook('actionFrontControllerSetMedia', Shop::getCompleteListOfShopsID());
}
