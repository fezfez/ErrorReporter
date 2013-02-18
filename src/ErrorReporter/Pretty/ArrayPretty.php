<?php
namespace ErrorReporter\Pretty;


class ArrayPretty
{
    /**
     * Permet faire un var_dump propre (désérialize les object serialize)
     * @param string $var
     * @param boolean $html
     * @param integer $level
     * @return string
     */
    public static function format($var, $html = false, $level = 0)
    {
        $spaces = "";
        $space = $html ? "&nbsp;" : " ";
        $newline = $html ? "<br />" : "\n";
        for ($i = 1; $i <= 8; $i++) {
            $spaces .= $space;
        }
        $tabs = $spaces;
        for ($i = 1; $i <= $level; $i++) {
            $tabs .= $spaces;
        }
        if (is_array($var)) {
            $title = "Array";
        } elseif (is_object($var)) {
            $title = get_class($var)." Object";
        }
        $output = $title . $newline . substr($tabs, 0, -4) . '(' . $newline;
        foreach($var as $key => $value) {
            $data = @unserialize($value);
            if (is_array($value) || is_object($value)) {
                $level++;
                $value = self::format($value, $html, $level);
                $level--;
            } elseif($data !== false) {
                $level++;
                $value = print_r($data, true);
                //$value = self::format($data, $html, $level);
                //var_dump($value);exit;
                $level--;
            }
            $output .= $tabs . "[" . $key . "] => " . $value . $newline;
        }
        $output .= substr($tabs, 0, -4) . ')' . $newline;

        return $output;
    }
}