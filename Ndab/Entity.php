<?php

/**
 * This file is part of the Ndab
 *
 * Copyright (c) 2012 Jan Skrasek (http://jan.skrasek.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Ndab;

use Nette,
    Nette\Database\Table;

/**
 * Ndab base model entity
 *
 * @author  Jan Skrasek
 */
class Entity extends Table\ActiveRow {
	
	/** @var string */
	protected $lang;

    public function & __get($key) {
        $key = $this->getRightKey($key);

        $method = "get$key";
        $method[3] = $method[3] & "\xDF";

        if (!$this->__isset($key) && method_exists($this, $method)) {
            $return = $this->$method();
            return $return;
        }

        return parent::__get($key);
    }

    /**
     * Returns array of subItems fetched from related() call
     * @param  string  "relatedTable:subItem"
     * @param  callable  callback for additional related call definition
     * @return array
     */
    protected function getSubRelation($selector, $relatedCallback = NULL) {
        list($relatedSelector, $subItemSelector) = explode(':', $selector);

        $related = $this->related($relatedSelector);
        if ($relatedCallback) {
            callback($relatedCallback)->invokeArgs(array($related));
        }

        $subItems = array();
        foreach ($related as $subItem) {
            $subItems[] = $subItem->$subItemSelector;
        }

        return $subItems;
    }

     /**
     * Set lang
     * @param string $lang
     */
    public function setLang($lang) {
        $this->lang = $lang;
        return $this;
    }
    
    public function getLang() {
        return $this->lang;
    }
    
    /**
     * Return right language column name
     * @param string $key
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function getRightKey($key) {
        if (substr($key, -1) == "_") {
            if (!$this->lang)
                throw new \InvalidArgumentException("If you want use \"$key\" for language variant, you must setup \$lang first");
            $prefix = $this->lang . "_";
            $key = $prefix . preg_replace('~_$~', '', $key);
        }
        return $key;
    }

}
