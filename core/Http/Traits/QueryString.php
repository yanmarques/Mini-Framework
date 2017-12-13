<?php

namespace Core\Http\Traits;

trait QueryString
{
    public function normalizeQueryString(string $queryString)
    {
        $query = explode('=', explode('&', $queryString));
        echo "<pre/>";
        var_dump($query);die;
    }

    public function uriWithoutQueryString(string $uri, $queryString = '')
    {
        return preg_replace("/\?{$queryString}/", '', $uri);
    }
}