# RForm
Create Form with simple PHP classes

## How to use it?
### For single input
Classic
```
$args = array('name' => '', 'label' => '');
$input = new InputTextField();
$input->prepare($args)->render()->display();
```

## TODO
Add simple input textarea, select, checkbox, radio, submit, button  
Add validator and filter in the main class