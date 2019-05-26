<?php
namespace Tests\Framework\Modules;

use GuzzleHttp\Psr7\Response;

class CallStringActions {

    function __invoke() {
        return new Response(200, [], 'Yep, ca marche !');
    }

}