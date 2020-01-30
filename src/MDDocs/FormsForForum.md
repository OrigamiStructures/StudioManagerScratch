CakePHP 3.8.5

I have a form that is created by two cooperating `.ctp`s and view blocks.

The Form context is being lost. I'm wondering if there is something about view blocks that looses the context object.

```php
//the element I call, target_variation.ctp
$this->start('special_form_control');
   echo $this->Form->control('special_control');
$this->end();

echo $this->element('common_form_wrapper');
```

```php
//the element common_form_wrapper.ctp

echo $this->Form->create($Context);
//echo common inputs here
echo $this->fetch('special_form_control');
//echo more common stuff
echo $this->Form->end();
```

I had trouble with the results, and when I checked the return from `FormHelper::context()` I see that `common_wrapper_form` has a valid context object in the helper, `target_variation` has lost that object and has a null object instead.

The order of events looked like trouble so I switched things around:

```php
//the element call, target_variation.ctp

$this->element('common_form_wrapper');

$this->start('special_form_control');
   echo $this->Form->control('special_control');
$this->end();

echo fetch('begin_form');
echo fetch('special_form_control');
echo fetch('end_form');
```

```php
//the element common_form_wrapper.ctp

$this->start('begin_form');
    echo $this->Form->create($Context);
    //echo common inputs here
$this->end();
$this->start('end_form');
    //echo more common stuff
    echo $this->Form->end();
$this->end();
```

But I'm still getting a null context object in `target_variation`.

I tried using FormHelper::context($contextObject) to set or reset the context in `taget_variation` but still got a null object. ??


