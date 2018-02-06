<?php

namespace Crevasse;

use Exception;

class ParseException extends Exception
{
    /**
     * ParseException constructor.
     * ParseException thrown when client returns error.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        try {
            throw new Exception();
        } catch (Exception $e) {
            !getenv('BURPSUITE_DEBUG') ?:
                error_log("[DEBUG] [Exception] Class:[{$data['class']}] Function:[{$data['function']}] ".
                    "Message:[{$data['message']}] Status:[{$data['status']}]");
            exit(json_encode([
                'error'  => $data['status'],
                'message'=> $data['message'],
                'stack'  => !getenv('BURPSUITE_DEBUG') ?: (array) $e,
            ]));
        }
    }
}