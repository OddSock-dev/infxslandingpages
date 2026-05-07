<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Providers\AppServiceProvider;
use Illuminate\Database\Schema\Builder;
use Tests\TestCase;

class AppServiceProviderTest extends TestCase
{
    protected function tearDown(): void
    {
        Builder::$defaultStringLength = 255;

        parent::tearDown();
    }

    public function test_it_uses_a_mysql_safe_default_string_length_for_mysql_connections(): void
    {
        config()->set('database.default', 'mysql');
        Builder::$defaultStringLength = 255;

        $provider = new AppServiceProvider($this->app);
        $provider->boot();

        $this->assertSame(191, Builder::$defaultStringLength);
    }

    public function test_it_leaves_the_default_string_length_unchanged_for_non_mysql_connections(): void
    {
        config()->set('database.default', 'sqlite');
        Builder::$defaultStringLength = 255;

        $provider = new AppServiceProvider($this->app);
        $provider->boot();

        $this->assertSame(255, Builder::$defaultStringLength);
    }
}
