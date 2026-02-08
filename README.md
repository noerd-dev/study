# noerd/study

The noerd package is required. Make sure the project is already initialized as a Git repository.
```
composer require noerd/noerd
php artisan noerd:install
```

There are two ways to install the Study module. Using composer require, the module is installed in the vendor folder.
However, it is also possible to install it as a submodule. In this case, the module is installed in a subdirectory of the project
and can be easily extended or customized.

### Install the package in vendor folder
```
composer require noerd/study

php artisan noerd:install-study
```


### Install the package as a submodule. Make sure you already initiated a git project.
```
git submodule add git@github.com:noerd-dev/study.git app-modules/study
composer require noerd/study

php artisan noerd:install-study
```

