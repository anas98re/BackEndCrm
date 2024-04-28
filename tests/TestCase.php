<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    public $bearerToken = '302|mffTd0Ud9YC7XMXGPwRpCI76B2wMkAXVIbQAXAEsaa16510b';
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
