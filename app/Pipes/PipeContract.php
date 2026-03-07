<?php

namespace Fishinglog\Pipes;

use Closure;

interface PipeContract
{
    public function handle(mixed $input, Closure $next);

}
