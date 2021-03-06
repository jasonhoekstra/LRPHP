<?php
// @package LR-PHP
// @copyright 2011 Jeffrey Hill
// @license Apache 2.0 License http://www.apache.org/licenses/LICENSE-2.0.html
defined('LREXEC') or die();

class LRUtility {
    
    public static function _($t) // Identity
    {
        return $t;
    }
    
    public static function _boolean($t)
    {
        return $t ? 'true' : 'false';
    }
    
    public static function _date($t)
    {
        return date('Y-m-d',strtotime($t));
    }
    
    public static function _datetime($t)
    {
        return date('c',strtotime($t));
    }
    
    public static function _int($t)
    {
        return intval($t);
    }
    
    public static function _string($t)
    {
        return (string) $t;
    }
    
}

/**
* TidyJSON
*
* A simple class for cleaning up poorly formatted JSON strings. No validation
* is performed; if you pass in bogus data, you will get bogus output.
* 
* Credits:
* Mike Matz https://github.com/pix0r
* Kevin Burns https://github.com/KevBurnsJr
*/

class TidyJSON {
    
    protected static $default_config = array(
    'indent' => ' ',
    'space' => ' ',
    );
    
    protected static $string_chars = array('"', "'");
    protected static $esc_string_chars = array("\\\"", "\\'");
    protected static $white_chars = array(" ", "\t", "\n", "\r");
    
    /**
    * tidy
    * @param string $json JSON-formatted string you'd like to tidy
    * @param array $config Optional configuration values
    */
    public static function tidy($json, $config = null) {
        $config = self::get_config($config);
        $out = '';
        $level = 0;
        $strchr = null;
        $c = null;
        for ($x = 0; $x < strlen($json); $x++) {
        $lc = $c;
        $c = substr($json, $x, 1);
        if ($strchr === null) {
        if (in_array($c, self::$white_chars))
        continue;
        if (in_array($c, self::$string_chars)) {
        $strchr = $c;
        } else {
        if ($c == '{' || $c == '[') {
        $eol = true;
        $level++;
        } elseif ($c == '}' || $c == ']') {
        $level--;
        $out .= "\n" . self::indent($level, $config);
        } elseif ($c == ',') {
        $eol = true;
        } elseif ($c == ':') {
            $c .= $config['space'];
        }
        }
        } else {
            if ($c === $strchr && !in_array($lc.$c, self::$esc_string_chars)) {
                $strchr = null;
            }
        }
        $out .= $c;
            if ($eol) {
                $eol = false;
                $out .= "\n" . self::indent($level, $config);
            }
        }
        
        // Remove trailing whitespace
        while (in_array(substr($out, -1), self::$white_chars)) {
            $out = substr($out, 0, -1);
        }
        
        return $out;
    }
    
    protected static function indent($level, $config) {
        $out = '';
        for ($x = 0; $x < $level; $x++) $out .= $config['indent'];
        return $out;
    }
    
    protected static function get_config($config = null) {
    return is_array($config) ? array_merge(self::$default_config, $config) : self::$default_config;
    }
}