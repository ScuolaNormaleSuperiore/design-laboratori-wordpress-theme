UPDATE wp_options  SET option_value='http://localhost' WHERE option_name='siteurl';
UPDATE wp_options  SET option_value='http://localhost' WHERE option_name='home';
UPDATE wp_options  SET option_value='/%postname%/' WHERE option_name='permalink_structure';