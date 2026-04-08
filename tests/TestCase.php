<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Cria a aplicação Laravel para os testes.
     *
     * IMPORTANTE: força o uso do banco de testes (treinaedu_testing) ANTES
     * do Laravel bootar, garantindo que RefreshDatabase NÃO apague dados
     * do banco de desenvolvimento (treinaedu).
     */
    public function createApplication()
    {
        // Define as variáveis de ambiente ANTES do Laravel ler o .env
        putenv('APP_ENV=testing');
        putenv('DB_DATABASE=treinaedu_testing');
        $_ENV['APP_ENV'] = 'testing';
        $_ENV['DB_DATABASE'] = 'treinaedu_testing';
        $_SERVER['APP_ENV'] = 'testing';
        $_SERVER['DB_DATABASE'] = 'treinaedu_testing';

        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        // Garante override mesmo se algo no boot reverter
        $app['config']->set('database.connections.mysql.database', 'treinaedu_testing');
        $app['db']->purge('mysql');

        return $app;
    }
}
