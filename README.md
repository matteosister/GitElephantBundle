# GitElephantBundle #

This is a simple bundle to use the [GitElephant library](https://github.com/matteosister/GitElephant) in a Symfony2 project

Watch a [simple live example](http://gitelephant.cypresslab.net.158.69-195-222.groveurl.com/) of what you can do with [GitElephant](https://github.com/matteosister/GitElephant), GitElephantBundle, Symfony2 and a git repository...

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
        repository_path: /home/matteo/repo-test
        binary_path: /usr/local/bin/git

**repository_path**: (required) is the path on your filesystem where you have your git repository (also a bare one)

**binary_path**: (optional) is the path to your git executable. If you don't provide this GItElephant try to argue the right executable with "which git". Remember that this lib only works on *nix filesystems.

Now, inside your controllers, you can easily access the GitElephant library with dependency injection:

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
        $repo = $this->get('cypress_git_elephant.repository');
        // There is also an handy alias
        $repo = $this->get('git_repository');
    }
}
```

Example
-------

There is also a [demo bundle](https://github.com/matteosister/GitElephantDemoBundle) to see it in action