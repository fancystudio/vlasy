<?php

class Hook extends HookCore {

        public static function getHookModuleExecList($hook_name = null) {
                $context = Context::getContext();
                $cache_id = 'hook_module_exec_list' . ((isset($context->customer)) ? '_' . $context->customer->id : '');
                if (!Cache::isStored($cache_id) || $hook_name == 'displayPayment') {
                        $frontend = true;
                        $groups = array();
                        if (isset($context->employee)) {
                                $shop_list = array((int) $context->shop->id);
                                $frontend = false;
                        } else {
                                // Get shops and groups list
                                $shop_list = Shop::getContextListShopID();
                                if (isset($context->customer) && $context->customer->isLogged()) $groups = $context->customer->getGroups();
                                elseif (isset($context->customer) && $context->customer->isLogged(true))
                                                $groups = array((int) Configuration::get('PS_GUEST_GROUP'));
                                else $groups = array((int) Configuration::get('PS_UNIDENTIFIED_GROUP'));
                        }

                        // SQL Request
                        $sql = new DbQuery();
                        $sql->select('h.`name` as hook, m.`id_module`, h.`id_hook`, m.`name` as module, h.`live_edit`');
                        $sql->from('module', 'm');
                        $sql->innerJoin('hook_module', 'hm', 'hm.`id_module` = m.`id_module`');
                        $sql->innerJoin('hook', 'h', 'hm.`id_hook` = h.`id_hook`');
                        $sql->where('(SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'module_shop ms WHERE ms.id_module = m.id_module AND ms.id_shop IN (' . implode(', ',
                                        $shop_list) . ')) = ' . count($shop_list));
                        if ($hook_name != 'displayPayment') $sql->where('h.name != "displayPayment"');
                        // For payment modules, we check that they are available in the contextual country
                        elseif ($frontend) {
                                if (Validate::isLoadedObject($context->country))
                                                $sql->where('(h.name = "displayPayment" AND (SELECT id_country FROM ' . _DB_PREFIX_ . 'module_country mc WHERE mc.id_module = m.id_module AND id_country = ' . (int) $context->country->id . ' LIMIT 1) = ' . (int) $context->country->id . ')');
                                if (Validate::isLoadedObject($context->currency))
                                                $sql->where('(h.name = "displayPayment" AND (SELECT id_currency FROM ' . _DB_PREFIX_ . 'module_currency mcr WHERE mcr.id_module = m.id_module AND id_currency IN (' . (int) $context->currency->id . ', -2) LIMIT 1) IN (' . (int) $context->currency->id . ', -2))');
                        }
                        if (Validate::isLoadedObject($context->shop)) $sql->where('hm.id_shop = ' . (int) $context->shop->id);

                        if ($frontend) {
                                $sql->leftJoin('module_group', 'mg', 'mg.`id_module` = m.`id_module`');
                                $sql->where('mg.`id_group` IN (' . implode(', ', $groups) . ')');
                                //$sql->where(Module::getPaypalIgnore());
                                $sql->groupBy('hm.id_hook, hm.id_module');
                        }

                        $sql->orderBy('hm.`position`');

                        // Store results per hook name
                        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        $list = array();

                        // Get all available payment module
                        $payment_modules = array();

                        // sip2pay
                        $shiptopay_active = Configuration::get('SHIPTOPAY_ACTIVE') && isset($context->cart->id_carrier) && !$context->cart->isVirtualCart() && $hook_name == 'displayPayment' ? true : false;
                        if ($shiptopay_active) {
                                $payments = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_payment FROM `' . _DB_PREFIX_ . 'shiptopay` WHERE id_carrier = ' . (int) $context->cart->id_carrier);
                                $pArr = array();
                                if (count($payments)) {
                                        foreach ($payments as $p) {
                                                $pArr[] = $p['id_payment'];
                                        }
                                }
                        }

                        if ($results) {
                                foreach ($results as $row) {

                                        // ship2pay
                                        if (!$shiptopay_active || in_array($row['id_module'], $pArr)) {
                                                $row['hook'] = strtolower($row['hook']);
                                                if (!isset($list[$row['hook']])) $list[$row['hook']] = array();

                                                $list[$row['hook']][] = array(
                                                    'id_hook' => $row['id_hook'],
                                                    'module' => $row['module'],
                                                    'id_module' => $row['id_module'],
                                                    'live_edit' => $row['live_edit'],
                                                );
                                        }
                                }
                        }

                        if ($hook_name != 'displayPayment') {
                                Cache::store($cache_id, $list);
                                // @todo remove this in 1.6, we keep it in 1.5 for retrocompatibility
                                self::$_hook_modules_cache_exec = $list;
                        }
                }
                else $list = Cache::retrieve($cache_id);

                // If hook_name is given, just get list of modules for this hook
                if ($hook_name) {
                        $retro_hook_name = Hook::getRetroHookName($hook_name);
                        $hook_name = strtolower($hook_name);

                        $return = array();
                        if (isset($list[$hook_name])) $return = $list[$hook_name];
                        if (isset($list[$retro_hook_name])) $return = array_merge($return, $list[$retro_hook_name]);

                        if (count($return) > 0) return $return;
                        return false;
                }
                else return $list;
        }

}

