<?php

class InputTextField extends Field {

    public function render() {
        $this->html = '';

        $this->html = '<label>Anti</label>';
        $this->html.= '<input type="text" name="" value="" '.$this->getAttributes().'/>';

        return $this;
    }
}