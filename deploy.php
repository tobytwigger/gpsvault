<?php
namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/php-fpm.php';

// Config

set('repository', 'git@github.com:tobytwigger/cycle-store');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('cycle.linkeys.app')
    ->setSshMultiplexing(true)
    ->set('remote_user', 'ubuntu')
    ->set('branch', 'develop')
    ->set('deploy_path', '/var/www/cycle.linkeys.app');

// Tasks

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'assets:compile',
    'assets:upload',
    'artisan:migrate',
    'artisan:storage:link',
    'artisan:cache:clear',
    'artisan:route:cache',
    'artisan:view:cache',
    'artisan:config:cache',
    'artisan:event:cache',
    'artisan:optimize',
    'deploy:publish'
]);

task('assets:compile', function() {
    runLocally('npm install');
    runLocally('npm run prod');
});

task('assets:upload', function() {
    upload('public/dist', '{{release_path}}/public');
    upload('public/mix-manifest.json', '{{release_path}}/public');
});

after('deploy:failed', 'deploy:unlock');

after('deploy:success', 'artisan:horizon:terminate');
