<?php
/* (c) Juan Alonso <juan.jalogut@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

require 'recipe/common.php';
require __DIR__ . '/magento_2_1/files.php';
require __DIR__ . '/magento_2_1/maintenance.php';
require __DIR__ . '/magento_2_1/database.php';
require __DIR__ . '/magento_2_1/cache.php';
require __DIR__ . '/magento_2_1/rollback.php';

# ----- Deployment properties ---
set('default_timeout', 900);
// [Optional] git repository
set('repository', '');
// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

# ----- Magento properties -------
set('magento_dir', 'magento');
set('magento_bin', '{{magento_dir}}/bin/magento');

set('shared_files', [
    '{{magento_dir}}/app/etc/env.php',
]);

set('shared_dirs', [
    '{{magento_dir}}/var',
    '{{magento_dir}}/pub/media',
    '{{magento_dir}}/pub/static/_cache',
]);

set('writable_dirs', [
    '{{magento_dir}}/var',
    '{{magento_dir}}/pub/static',
    '{{magento_dir}}/pub/media',
    '{{magento_dir}}/pub/static/_cache',
]);

set('clear_paths', [
    '{{magento_dir}}/pub/static/_cache',
]);

# ---- Deployment Flow
desc('Deploy project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:shared',
    'deploy:writable',
    'files:generate',
    'maintenance:set',
    'cache:clear:if-maintenance',
    'database:upgrade',
    'deploy:symlink',
    'maintenance:unset',
    'cache:clear',
    'deploy:unlock',
    'cleanup',
    'success',
]);

after('deploy:failed', 'deploy:unlock');

before('rollback', 'rollback:validate');
after('rollback', 'cache:clear');
