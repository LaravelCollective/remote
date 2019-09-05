<?php

namespace Collective\Remote;

use Collective\Remote\Console\TailCommand;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class RemoteServiceProvider extends ServiceProvider
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
      'Tail' => 'command.tail',
    ];

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Boot the Service Provider.
     */
    public function boot()
    {
        if (!$this->isLumen()) {
            $this->publishes([
              __DIR__.'/../config/remote.php' => config_path('remote.php'),
            ]);
        }

        $this->registerCommands();
    }

    /**
     * Check if package is running under Lumen app.
     *
     * @return bool
     */
    protected function isLumen()
    {
        return Str::contains($this->app->version(), 'Lumen') === true;
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('remote', function ($app) {
            return new RemoteManager($app);
        });
    }

    /**
     * Register the commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        foreach (array_keys($this->commands) as $command) {
            $method = "register{$command}Command";
            call_user_func_array([$this, $method], []);
        }
        $this->commands(array_values($this->commands));
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerTailCommand()
    {
        $this->app->singleton('command.tail', function ($app) {
            return new TailCommand();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['remote'];
    }
}
