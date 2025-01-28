# Laravel Upgrade System

A system to manage data/code upgrades during deployments, similar to database migrations.

## Installation

You can install the package via composer:

```bash
composer require wishborn/upgrades
```

The package will automatically register its service provider.

After installation, you should run the migrations to create the required database table:

```bash
php artisan migrate
```

If the migration doesn't run automatically, you can force publish and run it:

```bash
# Publish the migrations
php artisan vendor:publish --tag=wishborn-upgrades-migrations

# Run migrations
php artisan migrate
```

## Usage

### Creating an Upgrade

```bash
php artisan upgrade:make "Add user settings"
```

This will create a new upgrade file in the `upgrades` directory with a timestamp prefix.

### Running Upgrades

To run all pending upgrades:

```bash
php artisan upgrade:run
```

To skip confirmations:

```bash
php artisan upgrade:run -y
```

### Checking Status

To see the status of all upgrades:

```bash
php artisan upgrade:status
```

## Writing Upgrades

Upgrades are similar to Laravel migrations but for any type of data or code changes. All upgrades are automatically wrapped in a database transaction - if any part of the upgrade fails, all changes will be rolled back.

Here's an example:

```php
<?php

namespace Wishborn\Upgrades;

use Wishborn\Upgrades\Support\Upgrade;
use App\Models\User;

class AddDefaultSettingsToUsers extends Upgrade
{
    protected function up(): void
    {
        $this->info('Adding default settings to users...');
        
        // If any part of this fails, all changes will be rolled back automatically
        User::whereNull('settings')
            ->chunk(100, function ($users) {
                foreach ($users as $user) {
                    $this->line("Processing user: {$user->email}");
                    
                    if ($this->confirm("Update user {$user->email}?")) {
                        $user->settings = ['theme' => 'light'];
                        $user->save();
                    }
                }
            });

        $this->info('Default settings added successfully!');
    }
}
```

### Available Methods

Output Methods:
- `$this->info('Message')` - Green text
- `$this->error('Message')` - Red text
- `$this->warn('Message')` - Yellow text
- `$this->comment('Message')` - Yellow text
- `$this->line('Message')` - Plain text
- `$this->newLine(1)` - Add blank lines

Confirmations (skipped with -y flag):
```php
if ($this->confirm('Continue?')) {
    // User confirmed
}
```

## Best Practices

1. Make upgrades atomic and idempotent
2. Transactions are handled automatically - all changes in an upgrade will be rolled back if any part fails
3. Add descriptive comments and logging
4. Handle errors gracefully
5. Use chunking for large data operations
6. Add confirmations for destructive operations

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information. 