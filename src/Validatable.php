<?php

namespace ModelValidator;

trait Validatable
{
    public function validate($validator)
    {
        (new $validator($this))->validate();
    }
}