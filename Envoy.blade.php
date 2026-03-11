@servers(['web' => $server])

@setup
    $repository = 'git@github.com:marcelo-lipienski/snitch.catz.dev.br.git';
    $releases_dir = $path . '/releases';
    $app_dir = $path . '/current'; // The symlink path
    $release = date('YmdHis');
    $new_release_dir = $releases_dir .'/'. $release;
@endsetup

@story('deploy')
    clone_repository
    run_composer
    link_shared_files
    run_migrations
    optimize_laravel
    activate_release
    restart_queues
    clean_old_releases
@endstory

@task('clone_repository')
    echo 'Cloning repository ({{ $tag }})...'
    [ -d {{ $releases_dir }} ] || mkdir -p {{ $releases_dir }}
    # Clone and immediately checkout the specific tag
    git clone --depth 1 --branch {{ $tag }} {{ $repository }} {{ $new_release_dir }}
@endtask

@task('run_composer')
    echo "Installing Composer dependencies..."
    cd {{ $new_release_dir }}
    composer install --prefer-dist --no-scripts --no-dev -q -o
@endtask

@task('run_migrations')
    echo "Clearing config cache and running migrations..."
    cd {{ $new_release_dir }}
    # Force Laravel to stop looking at old cached configs
    php artisan config:clear
    php artisan migrate --force
@endtask

@task('optimize_laravel')
    echo "Optimizing Laravel..."
    cd {{ $new_release_dir }}
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
@endtask

@task('link_shared_files')
    echo "Linking shared .env and storage..."
    ln -nfs {{ $path }}/.env {{ $new_release_dir }}/.env
    
    # Remove the release's storage and link to shared
    rm -rf {{ $new_release_dir }}/storage
    ln -nfs {{ $path }}/storage {{ $new_release_dir }}/storage
    
    echo "Injecting pre-built assets..."
    mkdir -p {{ $new_release_dir }}/public/build
    # Check if files exist before copying to avoid errors
    if [ "$(ls -A {{ $path }}/shared_build 2>/dev/null)" ]; then
        cp -R {{ $path }}/shared_build/* {{ $new_release_dir }}/public/build/
    fi
@endtask

@task('activate_release')
    echo 'Flipping symlink to {{ $release }}...'
    ln -nfs {{ $new_release_dir }} {{ $app_dir }}
@endtask

@task('restart_queues')
    echo "Restarting Horizon..."
    cd {{ $app_dir }}
    php artisan horizon:terminate
@endtask

@task('clean_old_releases')
    echo "Cleaning up old releases..."
    cd {{ $releases_dir }}
    # Deletes all but the 5 most recent releases
    ls -dt */ | tail -n +6 | xargs -r rm -rf
@endtask

@task('rollback')
    echo "Rolling back to previous release..."
    cd {{ $releases_dir }}
    # Finds the second-to-last directory and points 'current' to it
    PREV=$(ls -1t {{ $releases_dir }} | head -n 2 | tail -n 1)
    ln -nfs {{ $releases_dir }}/$PREV {{ $app_dir }}
    echo "Rolled back to $PREV"
@endtask