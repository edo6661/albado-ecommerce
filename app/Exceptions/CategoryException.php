<?php

namespace App\Exceptions;

use Exception;

class CategoryException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param string $message
     */
    public function __construct($message = 'An error occurred with the category.')
    {
        parent::__construct($message);
    }

    /**
     * Render the exception to the HTTP response.
     *
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        return response()->json(['error' => $this->getMessage()], 500);
    }
}

class CategoryNotFoundException extends CategoryException
{
    /**
     * Create a new exception instance.
     *
     * @param string $message
     */
    public function __construct($message = 'Category not found.')
    {
        parent::__construct($message);
    }

    /**
     * Render the exception to the HTTP response.
     *
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        return response()->json(['error' => $this->getMessage()], 404);
    }
}