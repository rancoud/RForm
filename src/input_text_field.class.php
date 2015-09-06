<?php

class InputTextField extends Field {

    public function render() {
        $this->html = '';

        $this->html.= '<label>'.$this->label.'</label>';
        $this->html.= '<input type="text" name="'.$this->name.'" value="'.$this->getValue().'" '.$this->getAttributes().'/>';

        return $this;
    }
}