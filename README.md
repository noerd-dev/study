# noerd/study

The noerd package is required. Make sure the project is already initialized as a Git repository.
```
composer require noerd/noerd
php artisan noerd:install
```

Install the package. Make sure you already initiated a git project.
```
git submodule add git@github.com:noerd-dev/study.git app-modules/study
composer require noerd/study

php artisan noerd:install-study
```
