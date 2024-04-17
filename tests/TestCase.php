<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    public $bearerToken = '212|M5WRlcYoPrOPANDZlIZPYXe2EE03PtUs5qDglpAe6ae0aba0';
    public $bearerTokenAnas = '45|mgbZD2ztD3rvxh4nPbPbkpB2gtPL2juk0bEGfzJWe4c3f7a5';

    protected function setUp(): void
    {
        parent::setUp();

        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        DB::rollBack();

        parent::tearDown();
    }
}
