<?php

namespace Core\Database;

class Compiler
{
    /**
     * The keywords to select clause
     * 
     * @var array
     */
    protected $keywords = [
        'select',
        'from',
        'insert',
        'update'
    ];

    /**
     * Select 
     * 
     * 
     */
    protected $select;

    public function __construct(array $select, array $from, array $queries = [])
    {
        
    }
}