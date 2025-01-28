<?php

use Wishborn\Upgrades\Tests\TestCase;

uses(TestCase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| Define custom expectations that should be available for your tests.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
}); 