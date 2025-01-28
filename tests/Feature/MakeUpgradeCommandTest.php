<?php

namespace Wishborn\Upgrades\Tests\Feature;

use Wishborn\Upgrades\Tests\TestCase;
use Illuminate\Support\Facades\File;

class MakeUpgradeCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Clean up any test upgrades
        if (File::exists(base_path('upgrades'))) {
            File::deleteDirectory(base_path('upgrades'));
        }
    }

    protected function tearDown(): void
    {
        // Clean up any test upgrades
        if (File::exists(base_path('upgrades'))) {
            File::deleteDirectory(base_path('upgrades'));
        }

        parent::tearDown();
    }

    /** @test */
    public function it_can_create_an_upgrade_file()
    {
        $upgradeName = 'TestUpgrade';
        
        $this->artisan('make:wish', ['name' => $upgradeName])
            ->expectsOutput('Upgrade created successfully.')
            ->assertExitCode(0);

        $fileName = date('Y_m_d_His') . '_' . strtolower($upgradeName) . '.php';
        $filePath = base_path("upgrades/{$fileName}");

        $this->assertTrue(File::exists($filePath));
        $this->assertStringContainsString('class TestUpgrade extends Upgrade', File::get($filePath));
    }

    /** @test */
    public function it_fails_with_invalid_upgrade_name()
    {
        $this->artisan('make:wish', ['name' => ''])
            ->expectsOutput('The upgrade name cannot be empty.')
            ->assertExitCode(1);
    }
} 