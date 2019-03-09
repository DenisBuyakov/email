QUICK START
-----------

	require_once '../vendor/autoload.php';
	$a= new \DenisBuyakov\DBCache\Email();
	$check=$a->check('denis.da.by@gmail.com');
	$a->delete('denis.da.by@gmail.com');
	$check=$a->check('denis.da.by@gmail.com');
	$a->add('5184189@gmail.com');
	$check=$a->check('5184189@gmail.com');
	$a->saveDataToDB();