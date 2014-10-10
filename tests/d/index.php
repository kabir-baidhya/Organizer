<?php 
require_once __DIR__ . '/../../vendor/autoload.php';

use Gckabir\Organizer\Javascript;

Javascript::init([
	'basePath'	=> 'scripts/',
	'cache'		=> false,
	'minify'	=> true
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
<script src="<?php echo $js->build() ?>" type="text/javascript"></script>
