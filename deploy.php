<?php

namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/php-fpm.php';

// Config

set('keep_releases', 3);
set('repository', 'git@github.com:tobytwigger/gpsvault');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('gpsvault.co.uk')
    ->setSshMultiplexing(true)
    ->set('remote_user', 'ubuntu')
    ->set('branch', 'develop')
    ->set('deploy_path', '/var/www/gpsvault');

// Tasks

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'horizon:publish',
    'assets:compile',
    'assets:upload',
    'artisan:migrate',
    'permission:install',
    'artisan:storage:link',
    'artisan:cache:clear',
    'artisan:route:cache',
    'artisan:view:cache',
    'artisan:config:cache',
    'artisan:event:cache',
    'artisan:optimize',
    'deploy:publish',
]);

task('horizon:publish', artisan('horizon:publish'));

task('permission:install', artisan('permission:install'));

task('assets:compile', function () {
    runLocally('npm install');
    runLocally('npm run prod');
});

task('assets:upload', function () {
    upload('public/dist', '{{release_path}}/public');
    upload('public/mix-manifest.json', '{{release_path}}/public');
});

after('deploy:failed', 'deploy:unlock');

// Not currently working, as they don't change their release reference
//after('deploy:success', 'artisan:horizon:terminate');
//after('deploy:success', function() {
//    run('php {{release_path}}/artisan websockets:restart');
//});
after('deploy:success', artisan('horizon:terminate'));
