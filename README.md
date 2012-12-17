# Symfony2 and WordPress integration by FullSIX

This is a work in progress

This bundle tries to integrate WordPress with Symfony2, in the way that WordPress
is used to handle the views, and Symfony2 is used to handle the controller.

This bundle should be used when you want to add Symfony2 controllers to an existing
WordPress website, for example. This is really basic but it fact works quite well.

It needs Symfony >= 2.2 in order to interpret Twig tags inside WordPress content.

## Installation

### Download and install the bundle

Add the following dependencies to your projects composer.json file:

    "require": {
        # ..
        "fullsix/wordpress-bundle": "dev-master"
        # ..
    }

### Register the bundle in the kernel

``` php
<?php
// app/AppKernel.php

public function registerBundles() {
    $bundles = array(
        // ...
        new FullSIX\Bundle\FullSIXWordPressBundle(),
        // ...
    );
}
```

### Install WordPress

Install WordPress in your _web_ directory and launch its configuration.

### Install the WordPress plugin

A simple WordPress plugin is needed to interpret the Twig content inside WordPress.
Install it from [here](https://github.com/fullsixspain/fullsix_wordpress_plugin).

### Modify your app_dev.php

Edit your app_dev.php file and add this line at the beginning:

``` php
use FullSIX\Bundle\WordPressBundle\WordPressResponse;
```

And at the end, replace:

``` php
$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
```

With:

``` php
$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
global $container, $response;
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$container = $kernel->getContainer();
if ($response instanceof WordPressResponse) {
    $targetUrl = $response->getTargetUrl();
    if (!empty($targetUrl)) {
        $_SERVER['REQUEST_URI'] = $targetUrl;
    }
    define('WP_USE_THEMES', true);
    require('./wp-blog-header.php');
} else {
    $response->send();
    $kernel->terminate($request, $response);
}
```

Once modified, replicate the changes to your app.php file and delete WordPress's index.php file.
If you activate WordPress's url rewriting capabilities, you may need to comment modifications
that WordPress automatically made to your .htaccess file.

## Example of Symfony2 controller

``` php
/**
 * @Route("/test-page/")
 */
public function pageAction()
{
    return WordPressResponse::currentPage(array("var1" => "value1", "var2" => 2));
}

/**
 * @Route("/test-form/")
 */
public function formAction(Request $request)
{
    // Create form
    $builder = $this->createFormBuilder();
    $form = $builder
        ->add('var1', 'text', array("label" => "Variable 1", "required" => false, "constraints" => new NotBlank()))
        ->add('var2', 'text', array("label" => "Variable 2", "required" => false, "constraints" => new NotBlank()))
        ->getForm();
    $result = null;
    if ($request->getMethod() == 'POST') {
        $form->bind($request);
        $data = $form->getData();
        $result = $data["var1"]." ".$data["var2"];
    }
    return WordPressResponse::currentPage(array('form' => $form->createView(), "result" => $result));
}
```

This will create two routes, which will delegate their view to the respective WordPress page. For example, in the
/test-page/ WordPress content, you can have:

    This is my test page, with some really interesting content.
    <ul>
	<li>var1 = {{ var1 }}</li>
	<li>var2 = {{ var2 }}</li>
    </ul>

Which will display as:

    This is my test page, with some really interesting content.
        * var1 = value1
        * var2 = 2

The /test-form/ page can have the following content:

    Hi,

    This is my test form.

    <form method="post">
        {{ form_widget(form) }}
        <input type="submit" value="Ok" />
    </form>
    {% if result is not null %}
    Result: {{ result }}
    {% endif %}

This will display a basic form which will concatenate the two variables when submitted.
