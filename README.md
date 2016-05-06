Laravel 4 package.

Create mosaic from image set or by instagram #hashtag

To install, add to your app/config/app.php

at providers array:

<code>
'providers' => array(
	...	'Mosaic\Generator\GeneratorServiceProvider'
	),
</code>

and aliases array:

<code>
	'aliases' => array(
	...	'Mosaic' => 'Mosaic\Generator\GeneratorFacade'
	),
</code>

Call methods as Mosaic::

