<?php

namespace Tests;
use PHPUnit\Framework\Attributes\Test;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use \Illuminate\Foundation\Testing\DatabaseTransactions;
}
