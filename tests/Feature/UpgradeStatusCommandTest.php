<?php

namespace Wishborn\Upgrades\Tests\Feature;

use Wishborn\Upgrades\Tests\TestCase;
use Wishborn\Upgrades\Models\Upgrade;
use Illuminate\Support\Facades\File;

class UpgradeStatusCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test upgrade file
        $this->createTestUpgrade();
    }

    protected function tearDown(): void
    {
        // Clean up test upgrades
        if (File::exists(base_path('upgrades'))) {
            File::deleteDirectory(base_path('upgrades'));
        }

        parent::tearDown();
    }

    /** @test */
    public function it_shows_pending_upgrades()
    {
        $this->artisan('upgrade:status')
            ->expectsOutput('Pending Upgrades:')
            ->expectsOutput('TestUpgrade')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_shows_completed_upgrades()
    {
        // Run the upgrade first
        $this->artisan('upgrade:run');

        $this->artisan('upgrade:status')
            ->expectsOutput('Completed Upgrades:')
            ->expectsOutput('TestUpgrade')
            ->assertExitCode(0);
    }

    private function createTestUpgrade()
    {
        $content = <<<PHP
<?php

namespace Wishborn\Upgrades\Upgrades;

use Wishborn\Upgrades\Support\Upgrade;

class TestUpgrade extends Upgrade
{
    public function up(): void
    {
        // Test upgrade
    }

    public function down(): void
    {
        // Test downgrade
    }
}
PHP;

        if (!File::exists(base_path('upgrades'))) {
            File::makeDirectory(base_path('upgrades'), 0755, true);
        }

        $fileName = date('Y_m_d_His') . '_test_upgrade.php';
        File::put(base_path("upgrades/{$fileName}"), $content);
    }
} 