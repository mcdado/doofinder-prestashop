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
 * Upgrades the Doofinder module to version 5.1.3.
 *
 * Multistore installs configured with older versions only store DF_API_KEY and DF_REGION
 * per shop, while newer versions also keep a global value (see DoofinderConfig::saveApiConfig()).
 * The 5.1.4 and 8.1.9 upgrades read both without a shop, so in the "all shops" context they
 * fail with "Missing API KEY" and PrestaShop disables the module. This step copies the default
 * shop's values (or the first shop that has them) to the global configuration when missing.
 *
 * @param Doofinder $module the Doofinder module instance being upgraded
 *
 * @return bool always true
 */
function upgrade_module_5_1_3($module)
{
    DoofinderConfig::debug('Initiating 5.1.3 upgrade');

    $shops = Shop::getShops();
    $defaultShopId = (int) Configuration::get('PS_SHOP_DEFAULT');
    usort($shops, function ($a, $b) use ($defaultShopId) {
        return ((int) $b['id_shop'] === $defaultShopId) - ((int) $a['id_shop'] === $defaultShopId);
    });

    foreach (['DF_API_KEY', 'DF_REGION'] as $key) {
        if (Configuration::getGlobalValue($key)) {
            continue;
        }

        foreach ($shops as $shop) {
            $value = Configuration::get($key, null, $shop['id_shop_group'], $shop['id_shop']);
            if ($value) {
                Configuration::updateGlobalValue($key, $value);
                DoofinderConfig::debug("Copied {$key} of shop {$shop['id_shop']} to the global configuration");
                break;
            }
        }
    }

    return true;
}
