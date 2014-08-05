<?php

class Category extends CategoryCore
{
    public $heureka_category;

  public function __construct($id_category = null, $id_lang = null, $id_shop = null){
  self::$definition['fields']['heureka_category'] = array('type' => self::TYPE_STRING,  'validate' => 'isString', 'required' => false, 'size' => 255);
  parent::__construct($id_category,$id_lang,$id_shop );
  }

}