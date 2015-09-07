<?php

class Form {

    protected $fields = array();

    public function __construct($method = 'post') {
        $method = (string) strtolower($method);
        if($method !== 'get' || $method !== 'post') {
            throw new Exception("Form Method Invalid");
        }
    }

    public function addField(Field $field) {
        $this->fields[] = $field;
    }

    public function render() {
        foreach ($this->fields as $field) {
            $field->render();
        }
    }

    public function display() {
        echo '<form method="'.$this->method.'">';
        foreach ($this->fields as $field) {
            $field->display();
        }
        echo '</form>'
    }
}