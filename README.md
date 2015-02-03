
Organizer
=========
It is something that can help you better organize your static files(javascript/css) in your PHP based project. 

Just organize your javascript/css files in what ever ways you want, keeping related code in seperate files and directories and later organizer can merge & minify them so that it could be fetched just by a single http request.

Features
========
- Merging js/css files
- Minifying the bundle (merged code) using JShrink (for javascript) or CssMin (for CSS)
- Caching those files 

Getting Started
========

### Installation
Installing Organizer [via Composer](https://getcomposer.org/doc/00-intro.md) is very simple . 
Just add this in your composer.json 

    "require": {
	    "gckabir/organizer": "*"
    }
And run `composer install` or `update`

### Step 1: Configuration
Configuring as simple as:

		// Organizer Config
		$config = [
			'serverUrl'	=> 'http://localhost/<your-project>/',  // Site's base url
			'javascript'	=> [
				'useStrict'=> false,
				'basePath' 	=> 'js/',  // a directory containing all js files
				'minify'	=> false,
				'cache'		=> false,
			]
		];
		
		\Gckabir\Organizer\OZR::init($config);

Just set the parameters and it's done. 

### Step 2: Creating a bundle of js files

		$js = \Gckabir\Organizer\OZR::organizeJS('homepage-js', array(
			'javascript1',
			'javascript2',
		));

		// Additional files could be added like this
		$js->add(array(
			'javascript3',			
			'javascript4'
		));
                
		// merge all files & build the bundle
		$js->build();
### Step 3: Using the bundle in html
	<?php $js->includeHere() ?>


Or

	<script type="text/javascript" src="<?php echo $js->build() ?>"></script>


The Difference
==============

Instead of doing this:

	<script type="text/javascript" src="/static/ng/core/module.js"></script>
	<script type="text/javascript" src="/static/ng/core/constants.js"></script>
	<script type="text/javascript" src="/static/ng/core/services/app.js"></script>
	<script type="text/javascript" src="/static/ng/core/services/auth.js"></script>
	<script type="text/javascript" src="/static/ng/core/services/item.js"></script>
	<script type="text/javascript" src="/static/ng/core/services/user.js"></script>
	<script type="text/javascript" src="/static/ng/core/services/logo.js"/>
	<script type="text/javascript" src="/static/ng/core/services/user_profile.js"></script>
	<script type="text/javascript" src="/static/ng/core/directives/specific-scripts.js"></script>
	<script type="text/javascript" src="/static/ng/core/run.js"></script>

	<!-- Content -->
	<script type="text/javascript" src="/static/ng/content/module.js"></script>
	<script type="text/javascript" src="/static/ng/content/config.js"></script>
	<script type="text/javascript" src="/static/ng/content/controllers/controllers.js"></script>
	<script type="text/javascript" src="/static/ng/content/controllers/home.js"></script>
	<script type="text/javascript" src="/static/ng/content/controllers/siso.js"></script>
	
You can do this; pretty simply: 

	<?php 
	$js = OZR::organizeJS('js-bundle');
	$js->add(array(
		'core/module',
		'core/constants',
		'core/services/*',
		'core/directives/*',
		'core/run',

		// Content
		'content/module',
		'content/config',
		'content/controllers/*'
	));
	?>
	<script type="text/javascript" src="<?php echo $js->build() ?>"></script>
