<?php

class InputSelectField extends Field {

    protected $options;
    protected $defaultSelectedValue;
    protected $blankOption;

    protected function renderOptions() {
        $html = '';

        foreach ($this->options as $options) {
            if(!empty($options[0]) && $options[0] === 'OPTGRP') {
                $html.='<optgroup label="'.$this->escapeAttribute($options[1]).'" '.((!empty($options[2]) && $options[2] === 'disabled') ? 'disabled="disabled"' : '').'>';

                foreach ($options[3] as $option) {
                    $htmlAttr = '';
                    if(!empty($option['attrs'])) {
                        $attrs = $option['attrs'];
                        foreach ($attrs as $k => $v) {
                            $htmlAttr.= $k . '="' . $this->escapeAttribute($v) . '"';
                        }
                        unset($option['attrs']);
                    }
                    $html.='<option value="'.key($option).'" '.$htmlAttr.'>'.current($option).'</option>';
                }

                $html.='</optgroup>';
            }
            else {
                $htmlAttr = '';
                if(!empty($options['attrs'])) {
                    $attrs = $options['attrs'];
                    foreach ($attrs as $k => $v) {
                        $htmlAttr.= $k . '="' . $this->escapeAttribute($v) . '"';
                    }
                    unset($options['attrs']);
                }
                $html.='<option value="'.key($options).'" '.$htmlAttr.'>'.current($options).'</option>';
            }
        }

        return $html;
    }

    public function render() {
        $this->html = '';

        $this->html.= '<label for="'.$this->id.'">'.$this->label.'</label>';
        $this->html.= '<select name="'.$this->name.'" id="'.$this->id.'" '.$this->getAttributes().'>';
        $this->html.= $this->renderOptions();
        $this->html.= '</select>';

        return $this;
    }
}