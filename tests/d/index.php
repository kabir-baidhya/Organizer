<?php 
require_once __DIR__ . '/../../vendor/autoload.php';

use Gckabir\Organizer\Javascript;

Javascript::init([
	'basePath'	=> 'scripts/',
	'cache'		=> false,
	'minify'	=> false,
    'wrap'      => true, 
    
]);

Javascript::serve();//server on the same page


$js = Javascript::organize('homepage', [
	'foobar',
	'hello',
	'bar/joo'
], '2.9');


$js->vars([
	'message'	=> 'sdfsdf' .date('Y-m-d H:i:s'),
	'success'	=> 'yes',
	'foo'		=> 'bar'
]);

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
