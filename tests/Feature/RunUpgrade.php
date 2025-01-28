<?php

use Illuminate\Support\Facades\File;

beforeEach(function () {
    // Clean up and create test upgrade
    if (File::exists(base_path('upgrades'))) {
        File::deleteDirectory(base_path('upgrades'));
    }
    createTestUpgrade();
});

afterEach(function () {
    // Clean up test upgrades
    if (File::exists(base_path('upgrades'))) {
        File::deleteDirectory(base_path('upgrades'));
    }
});

test('can run pending upgrades', function () {
    $this->artisan('upgrade:run')
        ->expectsOutput('Running upgrades...')
        ->assertExitCode(0);

    expect(db()->table('wishborn_upgrades')->where('name', 'TestUpgrade')->first())
        ->status->toBe('completed');
});

test('skips already run upgrades', function () {
    // First run
    $this->artisan('upgrade:run');

    // Second run should skip
    $this->artisan('upgrade:run')
        ->expectsOutput('No pending upgrades.')
        ->assertExitCode(0);

    expect(db()->table('wishborn_upgrades')->count())->toBe(1);
});

function createTestUpgrade()
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