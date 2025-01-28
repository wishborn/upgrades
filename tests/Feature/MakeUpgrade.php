<?php

use Illuminate\Support\Facades\File;

beforeEach(function () {
    // Clean up any test upgrades
    if (File::exists(base_path('upgrades'))) {
        File::deleteDirectory(base_path('upgrades'));
    }
});

afterEach(function () {
    // Clean up any test upgrades
    if (File::exists(base_path('upgrades'))) {
        File::deleteDirectory(base_path('upgrades'));
    }
});

test('can create an upgrade file', function () {
    $upgradeName = 'TestUpgrade';
    
    $this->artisan('make:wish', ['name' => $upgradeName])
        ->expectsOutput('Upgrade created successfully.')
        ->assertExitCode(0);

    $fileName = date('Y_m_d_His') . '_' . strtolower($upgradeName) . '.php';
    $filePath = base_path("upgrades/{$fileName}");

    expect(File::exists($filePath))->toBeTrue();
    expect(File::get($filePath))->toContain('class TestUpgrade extends Upgrade');
});

test('fails with invalid upgrade name', function () {
    $this->artisan('make:wish', ['name' => ''])
        ->expectsOutput('The upgrade name cannot be empty.')
        ->assertExitCode(1);
}); 
