@servers(['web' => 'geren@mroczek.org'])

@setup
  $repository = '~/git/fishinglog.git';
  $releses_dir = '~/fishinglog/releases';
  $app_dir = '~/fishinglog';
  $release = date('YmdHis');
  $new_release_dir = $releses_dir .'/'. $release;
@endsetup

@story('deploy')
  clone_repository
  run_composer
  update_symlinks
@endstory

@task('clone_repository')
  whoami
  echo 'Cloning repository';
  git clone {{ $repository }} {{ $new_release_dir }}
@endtask

@task('run_composer')
  echo 'Starting development ({{ $release }})'
  cd {{ $new_release_dir }}
  php ~/composer.phar install --prefer-dist --no-scripts -q -o
@endtask

@task('update_symlinks')
  echo 'Changing group'
  chgrp -R www {{ $new_release_dir }}
  
  echo 'Linking storage directory'
  rm -rf {{ $new_release_dir }}/storage
  ln -s {{ $app_dir }}/storage {{ $new_release_dir }}/storage

  echo 'Linking .env file'
  ln -s {{ $app_dir }}/.env {{ $new_release_dir }}/.env

  echo 'Linking current release'
  ln -s {{ $new_release_dir }} {{ $app_dir }}/current

  ln -sF {{ $app_dir }}/current/public /usr/local/www/vhosts/fishinglog.mroczek.org
@endtask