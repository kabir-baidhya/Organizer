
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



