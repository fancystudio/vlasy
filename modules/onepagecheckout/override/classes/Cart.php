<?php

class Cart extends CartCore
{

    public function resetCartDiscountCache()
    {
        // verification keys: VK##2
        // reset discount cache so that discount can be added and new results retrieved in single HTTP request
        self::$_discounts     = NULL;
        self::$_discountsLite = NULL;
    }
}
