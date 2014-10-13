<?php 
require_once __DIR__ . '/../../vendor/autoload.php';

use Gckabir\Organizer\Javascript;

Javascript::init([
	'basePath'	=> 'scripts/',
	'cache'		=> false,
	'minify'	=> true,
    'wrap'      => true, 
    'dependencies'  => ['window', 'HottieHunter']
]);

Javascript::serve();//server on the same page


$js = Javascript::organize('homepage', [
	'foobar',
	'hello',
], '2.9');


$js->vars([
	'message'	=> 'sdfsdf' .date('Y-m-d H:i:s'),
	'success'	=> 'yes',
	'foo'		=> 'bar'
]);
$js->addBefore('bar/joo');
$js->setVar('foo', 'Foo Bar modified');
$js->addScript('document.write(JSON.stringify($vars));');

?>
<script type="text/javascript">
	var HottieHunter = {
        baseUrl: function(segment) {
            return 'sdfsdf' + (segment ? segment : '');
        },
        originUrl: function(segment) {
            return 'sdfsdf' + (segment ? segment : '');
        },
        userId: function() {
            return 'dfgdf'
        },
        authenticated: 'ggsdf',
        
        redirect: function(url) {
            window.location = this.baseUrl(url);
        },
        slugify: function(text) {
            return text
            .toLowerCase()
            .replace(/[^\w ]+/g, '')
            .replace(/ +/g, '-');
        },
        unseenNotifications: 0,
        userHasNoticed: true //user has just a glimse
    };
</script>
<script src="<?php echo $js->build() ?>" type="text/javascript"></script>
