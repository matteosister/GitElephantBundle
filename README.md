# GitElephantBundle #

This is a simple bundle to use the [GitElephant library](https://github.com/matteosister/GitElephant) in a Symfony2 project

Watch a [simple live example](http://gitelephant.cypresslab.net/) of what you can do with [GitElephant](https://github.com/matteosister/GitElephant), GitElephantBundle, Symfony2 and a git repository...

[Download the demo bundle code](https://github.com/matteosister/GitElephantDemoBundle) used in the live example

![GitElephantBundle wdt](https://github.com/matteosister/GitElephantBundle/raw/master/preview.png)


How to install
--------------

**Method 1 - deps file**

- Add the GitElephant library and the bundle itself in the deps file

*deps*

    [GitElephant]
        git=git://github.com/matteosister/GitElephant.git
        target=git-elephant

    [GitElephantBundle]
        git=git://github.com/matteosister/GitElephantBundle.git
        target=/bundles/Cypress/GitElephantBundle

- register the two namespaces in the autoload.php file

*app/autoload.php*

``` php
<?php
$loader->registerNamespaces(array(
    // ...other namespaces
    'GitElephant'      => __DIR__.'/../vendor/git-elephant/src',
    'Cypress'          => __DIR__.'/../vendor/bundles',
));
```

- register the bundle in the kernel file

*app/AppKernel.php*

``` php
<?php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...other bundles
            new Cypress\GitElephantBundle\CypressGitElephantBundle(),
        );
        // ...other bundles
        return $bundles;
    }
}
```

**Method 2 - submodules**

You can also manage the two git repositories with git and submodules. It could be a mess if you don't know what you do, but I personally prefer this way

``` bash
$ git submodule add git://github.com/matteosister/GitElephant.git src/git-elephant
$ git submodule add git://github.com/matteosister/GitElephantBundle.git src/Cypress/GitElephantBundle
```

This two commands will clone the two repositories inside your "src" folder. You can use any folder you want in your symfony project. Just remember to update the *app/autoload.php* file and to activate the bundle in *app/AppKernel.php*

The namespace for the bundle is "Cypress". You must clone the bundle in a Cypress folder, or it will not work. Your autoload file should point to the folder that **contains** the Cypress folder

The GitElephant namespace in autoload should point to the "src" folder inside the GitElephant repository

To actually clone the submodules give the command

``` bash
$ git submodule update --init
```

For more info about git submodules read the [dedicated section](http://progit.org/book/ch6-6.html) inside the awesome **Pro Git** book by Scott Chacon.

How to use
----------

To use the bundle you have to define two parameters in you *app/config/config.yml* file under *cypress_git_elephant* section

    cypress_git_elephant:
        binary_path: /usr/local/bin/git
        repositories:
            "GitElephant": "/home/matteo/libraries/GitElephant"
            "Bootstrap": "/home/matteo/libraries/Bootstrap"
            # ... other repositories

**binary_path**: (optional) is the path to your git executable. If you don't provide this GItElephant try to argue the right executable with "which git". Remember that this lib only works on *nix filesystems.

Now, inside your controllers, you can easily access the GitElephant library with dependency injection:

**repositories**: (at least one is required) is an hash with *key*: a repository name, *value*: the repository path

The repository path could also be a bare repository (useful for web servers). But without a checked out copy you won't be able to modify the repository state. You will be able to show the repository, but not, for example, create a new commit

``` php
<?php
class AwesomeController extends Controller
{
    /**
     * @Route("/", name="repository_root")
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function rootAction(Request $request)
    {
        // Repository instance
        $repositories = $this->get('cypress_git_elephant.repository_collection');
        // There is also an handy alias
        $repositories = $this->get('git_repositories');
        // $repositories is an instance of GitElephant\Cypress\GitElephantBundle\Collection\GitElephantRepositoryCollection
        // it has the Countable, ArrayAccess and Iterator interfaces. So you can do:
        $num_repos = count($repositories); //number of repositories
        $git_elephant = $repositories->get('GitElephant'); // retrieve a Repository instance by its name (defined in config.yml)
        // iterate
        foreach ($repositories as $repo) {
            $repo->getLog();
        }
    }
}
```

Read the documentation of [GitElephant](https://github.com/matteosister/GitElephant) to know what you can do with the *Repository* class, or [watch the demo site](http://gitelephant.cypresslab.net/) build with this bundle, and [the relative code](https://github.com/matteosister/GitElephantDemoBundle).

Wedb Debug Toolbar
------------------

As a bonus, thanks to the GitElephant library, you can have the branch of any repository right inside yuor Symfony2 toolbar.

Add this to your **dev** configuration file *app/config/config_dev.yml*

    cypress_git_elephant:
        profiler_repository_path: "%kernel.root_dir%/../"

If you use git with Symfony2, with the above configuration, you can see directly from the browser the branch you are in.

Example
-------

There is also a [demo bundle](https://github.com/matteosister/GitElephantDemoBundle) to see it in action