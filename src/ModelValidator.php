<?php

namespace ModelValidator;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class ModelValidator
{
    use ValidatesRequests;

    abstract protected function getData();

    abstract protected function getRules();

    protected function getMessages()
    {
        return [];
    }

    protected function getAttributes()
    {
        return [];
    }

    public function validate()
    {
        $validator = app(Factory::class)->make($this->getData(), $this->getRules(), $this->getMessages(),
            $this->getAttributes());

        if ($validator->fails()) {
            $this->throwValidationException(request(), $validator);
        }
    }
}
