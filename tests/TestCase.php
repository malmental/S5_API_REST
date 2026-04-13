<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // PassportSeeder is called individually in tests that need it
    // to avoid running it for tests that don't need database access
}
