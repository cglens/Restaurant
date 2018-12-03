<?php

//but : lancer le programme

interface InterceptingFilter
{
    public function run(Http $http, array $queryFields, array $formFields);
}