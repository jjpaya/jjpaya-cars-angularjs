<?php
	require_once 'libs/utils/includes.php';
	assert_add_to_include_path($_SERVER['DOCUMENT_ROOT']);
	
	require_once 'libs/db/inc.php';
	require_once 'libs/mvc/inc.php';

	Database::set_default_details('jjpaya', 'jjpaya', 'forum');

	$r = new MvcRouter;
	$r->set_page_brand('JJPaya Cars');
	$r->set_page_mvc_content(array(
		mvc_load_mod('common'),
		mvc_load_mod('header'),
		mvc_load_mod_from_url_path_or_exception('page_main', 'err404', 'err503'),
		mvc_load_mod('footer')
	));

	$r->handle_request();
?>