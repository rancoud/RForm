<?php

abstract class Field {

    /**
     * Name for attribute name
     * @var string
     */
    protected $name;

    /**
     * Label for attribute label
     * @var string
     */
    protected $label;

    /**
     * Id for attribute id
     * @var string
     */
    protected $id;

    /**
     * Value for attribute vlaue
     * @var string
     */
    protected $value;

    /**
     * DefaultValue for attribute vlaue
     * @var string
     */
    protected $defaultValue = '';

    /**
     * Generic List of attributes
     * @var array
     */
    protected $attributes = [];

    /**
     * Text to show for help
     * @var String
     */
    protected $help;

    /**
     * Text to show for error
     * @var String
     */
    protected $error;

    /**
     * HTML calculate for the render
     * @var String
     */
    protected $html;

    /**
     * Use Charset UTF-8 by default
     * @var String
     */
    protected $charset = 'UTF-8';

    /**
     * Check invalid utf8 for attributes values
     * @param  String $string String to check
     * @return String         String is safe or empty string
     */
    private function checkInvalidUtf8($string) {
        $string = (string) $string;

        if (strlen($string) === 0) {
            return '';
        }

        static $isUtf8 = null;
        if (!isset($isUtf8)) {
            $isUtf8 = in_array($this->charset, ['utf8', 'utf-8', 'UTF8', 'UTF-8']);
        }
        if (!$isUtf8) {
            return $string;
        }

        static $utf8Pcre = null;
        if (!isset($utf8Pcre)) {
            $utf8Pcre = preg_match('/^./u', 'a');
        }

        if (!$utf8Pcre) {
            return $string;
        }

        if (preg_match('/^./us', $string) === 1) {
            return $string;
        }

        return '';
    }

    /**
     * Normalize entities
     * @param  String $string String to secure
     * @return String         String secured
     */
    private function normalizeEntities($string) {
        $string = str_replace('&', '&amp;', $string);

        $string = preg_replace_callback('/&amp;([A-Za-z]{2,8}[0-9]{0,2});/', [$this, 'namedEntities'], $string);
        $string = preg_replace_callback('/&amp;#(0*[0-9]{1,7});/', [$this, 'normalizeEntities2'], $string);
        $string = preg_replace_callback('/&amp;#[Xx](0*[0-9A-Fa-f]{1,6});/', [$this, 'normalizeEntities3'], $string);

        return $string;
    }

    /**
     * Callback #1 for normalizeEntities
     * @param  String $matches preg_replace_callback
     * @return String
     */
    private function namedEntities($matches) {
        if (empty($matches[1])) {
            return '';
        }

        $i = $matches[1];
        return "&amp;$i;";
    }

    /**
     * Callback #2 for normalizeEntities
     * @param  String $matches preg_replace_callback
     * @return String
     */
    private function normalizeEntities2($matches) {
        if (empty($matches[1])) {
            return '';
        }

        $i = $matches[1];
        if ($this->validUnicode($i)) {
            $i = str_pad(ltrim($i,'0'), 3, '0', STR_PAD_LEFT);
            $i = "&#$i;";
        }
        else {
            $i = "&amp;#$i;";
        }

        return $i;
    }

    /**
     * Callback #3 for normalizeEntities
     * @param  String $matches preg_replace_callback
     * @return String
     */
    private function normalizeEntities3($matches) {
        if (empty($matches[1])) {
            return '';
        }

        $hexchars = $matches[1];
        return (!$this->validUnicode(hexdec($hexchars))) ? "&amp;#x$hexchars;" : '&#x'.ltrim($hexchars,'0').';';
    }

    /**
     * Valid unicode with hex value
     * @param  integer $i Value in hexadecimal
     * @return boolean    value is valid or not
     */
    private function validUnicode($i) {
        return ( $i == 0x9 || $i == 0xa || $i == 0xd ||
                ($i >= 0x20 && $i <= 0xd7ff) ||
                ($i >= 0xe000 && $i <= 0xfffd) ||
                ($i >= 0x10000 && $i <= 0x10ffff) );
    }

    /**
     * Encode special chars and html chars
     * @param  String $string     String to secure
     * @param  Const $quoteStyle  Type of quotes
     * @return String             String secured
     */
    private function specialChars($string, $quoteStyle = ENT_QUOTES) {
        $string = (string) $string;

        if (strlen($string) === 0) {
            return '';
        }

        if (!preg_match('/[&<>"\']/', $string)) {
            return $string;
        }

        if (empty($quoteStyle)) {
            $quoteStyle = ENT_NOQUOTES;
        }
        elseif (!in_array($quoteStyle, [0, 2, 3, 'single', 'double'], true)) {
            $quoteStyle = ENT_QUOTES;
        }

        if (in_array($this->charset, ['utf8', 'utf-8', 'UTF8', 'UTF-8'])) {
            $charset = 'UTF-8';
        }

        $_quoteStyle = $quoteStyle;

        if ($quoteStyle === 'double') {
            $quoteStyle = ENT_COMPAT;
            $_quoteStyle = ENT_COMPAT;
        }
        elseif ($quoteStyle === 'single') {
            $quoteStyle = ENT_NOQUOTES;
        }

        $string = $this->normalizeEntities($string);

        $string = htmlspecialchars($string, $quoteStyle, $charset, false);

        if ('single' === $_quoteStyle) {
            $string = str_replace("'", '&#039;', $string);
        }

        return $string;
    }

    /**
     * Escape attribute value
     * @param  String $attr Value
     * @return String       Value secured
     */
    protected function escapeAttribute($attr) {
        $attr = $this->checkInvalidUtf8($attr);
        $attr = $this->specialChars($attr);

        return $attr;
    }

    /**
     * Render in html all attributes
     * @return String
     */
    protected function getAttributes() {
        $html = '';

        foreach ($this->attributes as $k => $v) {
            $html.= $k . '="' . $this->escapeAttribute($v) . '"';
        }

        return $html;
    }

    /**
     * Get Value to show for field
     * @return String
     */
    protected function getValue() {
        return $this->escapeAttribute($this->value);
    }

    /**
     * Get configuration
     * @param  [type] $args [description]
     * @return [type]       [description]
     */
    public function prepare($args) {
        foreach ($args as $k => $v) {
            if (property_exists($this, $k)) {
                $this->{$k} = $v;
                continue;
            }

            $this->attributes[$k] = $v;
        }

        return $this;
    }

    public function validateValue($containers, $key = null) {
        if($key === null) {
            $key = $this->name;
        }

        if(!is_array($containers)) {
            $containers = array($containers);
        }

        foreach ($containers as $container) {
            if(is_array($container)) {
                if (isset($container[$key])) {
                    return $container[$key];
                }
            }
            else if(is_object($container)) {
                if (isset($container->$key)) {
                    return $container->$key;
                }
            }
            else {
                return $container;
            }
        }

        return $this->defaultValue;
    }

    /**
     * Render the html for field (to implement)
     * @return [type] [description]
     */
    public abstract function render();

    /**
     * Display the render
     * @return void
     */
    public function display() {
        echo $this->html;
    }

    /**
     * Show directly the field without calling render and display
     * @return void
     */
    public function show() {
        $this->render()->$this->display();
    }

    /**
     * Facade / EXPERIMENTAIL
     * @param  [type] $name      [description]
     * @param  [type] $arguments [description]
     * @return [type]            [description]
     */
    public static function __callStatic($name, $arguments) {
        $query = new InputTextField(); 
        return call_user_func_array([$query, $name], $arguments);
    }

}