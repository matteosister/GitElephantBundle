# GitElephantBundle #

This is a simple bundle to use the [GitElephant library](https://github.com/matteosister/GitElephant) in a Symfony project.

How to install
--------------

**Method 1 - composer for Symfony 2.1 and above (recommended)**

- Add the following line to the `composer.json` file:

``` json
{
    "require": {
        "cypresslab/gitelephant-bundle": "dev-master"
    }
}
```

- Execute composer update command

``` bash
$ composer update
```

- Register the bundle in the kernel file

*app/AppKernel.php*

``` php
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

>Is recommended to register this bundle only in development environment for safety reasons.

``` php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...other bundles
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            // ...other development and testing bundles
            $bundles[] = new Cypress\GitElephantBundle\CypressGitElephantBundle();
        }

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

Web Debug Toolbar
------------------

As a bonus, thanks to the GitElephant library, you can have the branch of any repository right inside yuor Symfony2 toolbar.

Add this to your **dev** configuration file *app/config/config_dev.yml*

    cypress_git_elephant:
        enable_profiler: true
        profiler_repository_path: "%kernel.root_dir%/../"

If you use git with Symfony2, with the above configuration, you can see directly from the browser the branch you are in. Click on the icon and you get a list of the last 10 commits for the branch you are in.

Available console commands
--------------------------

**cypress:git:commit**

This command is useful to commit (default stage all) all changes in current branch and push to all remotes.

``` bash
$ php app/console cypress:git:commit [--no-push] [--no-stage-all] [--all] message
```

**cypress:git:tag**

This command is useful to tag current commit and push to all remotes.

``` bash
$ php app/console cypress:git:tag [--no-push] [--all] tag [comment]
```

**cypress:git:merge**

This command will merge (default without fast forward) from source (default devel) to destination (default master) branch and push to all remotes.

``` bash
$ php app/console cypress:git:merge [--no-push] [--fast-forward] [--all] [source] [destination]
```

**cypress:git:hit**

Combo command to merge without fast forward option from source to destination branch, tag destination branch and push to all remotes.

``` bash
$ php app/console cypress:git:hit [--no-push] [--fast-forward] [--all] tag [comment] [source] [destination]
```

Example
-------

There is also a [demo bundle](https://github.com/matteosister/GitElephantDemoBundle) to see it in action.
