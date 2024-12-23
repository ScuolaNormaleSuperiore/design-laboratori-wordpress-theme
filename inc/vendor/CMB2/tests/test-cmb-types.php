<?php
/**
 * CMB2_Types tests
 *
 * @package   Tests_CMB2
 * @author    CMB2 team
 * @license   GPL-2.0+
 * @link      https://cmb2.io
 */

require_once( 'test-cmb-types-base.php' );

class Test_CMB2_Types extends Test_CMB2_Types_Base {

	/**
	 * Set up the test fixture
	 */
	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
	}

	public function test_repeatable_field() {
		$this->field_test['fields'][0]['repeatable'] = true;
		$this->field_test['fields'][0]['options'] = array(
			'add_row_text' => 'ADD NEW ROW',
		);
		$cmb   = new CMB2( $this->field_test );
		$field = cmb2_get_field( $this->field_test['id'], 'field_test_field', $this->post_id );
		$this->assertInstanceOf( 'CMB2_Field', $field );

		$expected_field = '
		<div class="cmb-row cmb-type-text cmb2-id-field-test-field cmb-repeat table-layout" data-fieldtype="text">
			<div class="cmb-th"><label for="field_test_field">Name</label></div>
			<div class="cmb-td">
				<p class="cmb2-metabox-description">This is a description</p>
				<div id="field_test_field_repeat" class="cmb-repeat-table cmb-nested">
					<div class="cmb-tbody cmb-field-list">
						<div class="cmb-row cmb-repeat-row">
							<div class="cmb-td">
								<input type="text" class="regular-text" name="field_test_field[0]" id="field_test_field_0" data-iterator="0" value="" data-hash=\'7pbortq7uk50\'/>
							</div>
							<div class="cmb-td cmb-remove-row">
								<button type="button" class="button-secondary cmb-remove-row-button" title="' . esc_attr__( 'Remove Row', 'cmb2' ) . '">' . esc_html__( 'Remove', 'cmb2' ) . '</button>
							</div>
						</div>
						<div class="cmb-row empty-row hidden">
							<div class="cmb-td">
								<input type="text" class="regular-text" name="field_test_field[1]" id="field_test_field_1" data-iterator="1" value="" data-hash=\'7pbortq7uk50\'/>
							</div>
							<div class="cmb-td cmb-remove-row">
								<button type="button" class="button-secondary cmb-remove-row-button" title="' . esc_attr__( 'Remove Row', 'cmb2' ) . '">' . esc_html__( 'Remove', 'cmb2' ) . '</button>
							</div>
						</div>
					</div>
				</div>
				<p class="cmb-add-row">
					<button type="button" data-selector="field_test_field_repeat" class="cmb-add-row-button button-secondary">ADD NEW ROW</button>
				</p>
			</div>
		</div>
		';

		$this->assertHTMLstringsAreEqual( $expected_field, $this->render_field( $field ) );
	}

	public function test_field_options_cb() {
		$cmb   = new CMB2( $this->options_cb_test );
		$field = cmb2_get_field( $this->options_cb_test['id'], 'options_cb_test_field', $this->post_id );
		$this->assertInstanceOf( 'CMB2_Field', $field );

		$expected_field = '
		<div class="cmb-row cmb-type-select cmb2-id-options-cb-test-field" data-fieldtype="select">
			<div class="cmb-th"><label for="options_cb_test_field">Name</label></div>
			<div class="cmb-td">
				<select class="cmb2_select" name="options_cb_test_field" id="options_cb_test_field" data-hash=\'3r53biqipcgg\'>
					<option value="one" >One</option>
					<option value="two" >Two</option>
					<option value="true" >1</option>
					<option value="false" ></option>
					<option value="post_id" >' . $this->post_id . '</option>
					<option value="object_type" >post</option>
					<option value="type" >select</option>
				</select>
				<p class="cmb2-metabox-description">This is a description</p>
			</div>
		</div>
		';

		$this->assertHTMLstringsAreEqual( $expected_field, $this->render_field( $field ) );
	}

	public function test_field_options() {
		$cmb   = new CMB2( $this->options_test );
		$field = cmb2_get_field( $this->options_test['id'], 'options_test_field', $this->post_id );
		$this->assertInstanceOf( 'CMB2_Field', $field );

		$expected_field = '
		<div class="cmb-row cmb-type-select cmb2-id-options-test-field" data-fieldtype="select">
			<div class="cmb-th"><label for="options_test_field">Name</label></div>
			<div class="cmb-td">
				<select class="cmb2_select" name="options_test_field" id="options_test_field" data-hash=\'29ds75gri04g\'>
					<option value="one" >One</option>
					<option value="two" >Two</option>
					<option value="true" >1</option>
					<option value="false" >
					</option>
				</select>
				<p class="cmb2-metabox-description">This is a description</p>
			</div>
		</div>
		';

		$this->assertHTMLstringsAreEqual( $expected_field, $this->render_field( $field ) );
	}

	public function test_field_options_bools() {
		$cmb   = new CMB2( $this->options_test );
		$field = cmb2_get_field( $this->options_test['id'], 'options_test_field', $this->post_id );
		$this->assertInstanceOf( 'CMB2_Field', $field );

		$this->assertEquals( $field->options( 'one' ), 'One' );
		$this->assertEquals( $field->options( 'two' ), 'Two' );
		$this->assertTrue( $field->options( 'true' ) );
		$this->assertFalse( $field->options( 'false' ) );
		$this->assertFalse( $field->options( 'random_string' ) );
	}

	public function test_field_attributes() {
		$cmb   = new CMB2( $this->attributes_test );
		$field = cmb2_get_field( $this->attributes_test['id'], 'attributes_test_field', $this->post_id );
		$this->assertInstanceOf( 'CMB2_Field', $field );

		$expected_field = '
		<div class="cmb-row cmb-type-text cmb2-id-attributes-test-field table-layout" data-fieldtype="text">
			<div class="cmb-th"><label for="attributes_test_field">Name</label></div>
			<div class="cmb-td">
				<input type="number" class="regular-text" name="attributes_test_field" id="arbitrary-id" value="" data-hash=\'1gc4nlm50org\' disabled="disabled" data-test=\'{"one":"One","two":"Two","true":true,"false":false,"array":{"nested_data":true}}\'/>
				<p class="cmb2-metabox-description">This is a description</p>
			</div>
		</div>
		';

		$this->assertHTMLstringsAreEqual( $expected_field, $this->render_field( $field ) );
	}

	public function test_get_file_ext() {
		$type = $this->get_field_type_object( 'file' );
		$ext = $type->get_file_ext( site_url( '/wp-content/uploads/2014/12/test-file.pdf' ) );
		$this->assertEquals( 'pdf', $ext );
	}

	public function test_get_file_name_from_path() {
		$type = $this->get_field_type_object( 'file' );
		$name = $type->get_file_name_from_path( site_url( '/wp-content/uploads/2014/12/test-file.pdf' ) );
		$this->assertEquals( 'test-file.pdf', $name );
	}

	public function test_is_valid_img_ext() {
		$type = $this->get_field_type_object( 'file' );
		$type->get_new_render_type( $type->field->type(), 'CMB2_Type_File' )->render();

		$ext = $type->get_file_ext( site_url( '/wp-content/uploads/2014/12/test-file.pdf' ) );
		$this->assertFalse( $type->is_valid_img_ext( $ext ) );
		$this->assertFalse( $type->is_valid_img_ext( '.pdf' ) );
		$this->assertFalse( $type->is_valid_img_ext( 'jpg' ) );
		$this->assertFalse( $type->is_valid_img_ext( '.test' ) );

		$valid_types = apply_filters( 'cmb2_valid_img_types', array( 'jpg', 'jpeg', 'png', 'gif', 'ico', 'icon' ) );

		foreach ( $valid_types as $ext ) {
			$is_valid = $type->is_valid_img_ext( '/test.' . $ext, true );
			$this->assertEquals( $is_valid, $type->type->is_valid_img_ext( '/test.' . $ext, true ) );
			$this->assertTrue( $is_valid );
		}

		// Add .test as a valid image type
		add_filter( 'cmb2_valid_img_types', array( __CLASS__, 'add_type_cb' ) );
		$this->assertTrue( $type->is_valid_img_ext( '/test.test' ) );
	}

	public function test_text_field() {
		$this->assertHTMLstringsAreEqual(
			'<input type="text" class="regular-text" name="field_test_field" id="field_test_field" value="" data-hash=\'4lavrjdps2t0\'/><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array( $this->get_field_type_object(), 'render' ) )
		);
	}

	public function test_text_field_after_value_update() {

		update_post_meta( $this->post_id, $this->text_type_field['id'], 'test value' );

		$this->assertHTMLstringsAreEqual(
			'<input type="text" class="regular-text" name="field_test_field" id="field_test_field" value="test value" data-hash=\'4lavrjdps2t0\'/><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array( $this->get_field_type_object(), 'render' ) )
		);

		delete_post_meta( $this->post_id, $this->text_type_field['id'] );
	}

	public function test_hidden_field() {
		$this->assertHTMLstringsAreEqual(
			'<input type="hidden" class="cmb2-hidden" name="field_test_field" id="field_test_field" value="" data-hash=\'4lavrjdps2t0\'/>',
			$this->capture_render( array( $this->get_field_type_object( 'hidden' ), 'render' ) )
		);
	}

	public function test_text_medium_field() {

		$this->assertHTMLstringsAreEqual(
			'<input type="text" class="cmb2-text-medium" name="field_test_field" id="field_test_field" value="" data-hash=\'4lavrjdps2t0\'/><span class="cmb2-metabox-description">This is a description</span>',
			$this->capture_render( array( $this->get_field_type_object( 'text_medium' ), 'render' ) )
		);
	}

	public function test_text_email_field() {

		$this->assertHTMLstringsAreEqual(
			'<input type="email" class="cmb2-text-email cmb2-text-medium" name="field_test_field" id="field_test_field" value="" data-hash=\'4lavrjdps2t0\'/><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array( $this->get_field_type_object( 'text_email' ), 'render' ) )
		);
	}

	public function test_text_url_field() {

		$this->assertHTMLstringsAreEqual(
			'<input type="text" class="cmb2-text-url cmb2-text-medium regular-text" name="field_test_field" id="field_test_field" value="" data-hash=\'4lavrjdps2t0\'/><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array( $this->get_field_type_object( 'text_url' ), 'render' ) )
		);
	}

	public function test_text_url_after_value_update() {

		$value = 'test value';
		update_post_meta( $this->post_id, $this->text_type_field['id'], $value );

		$this->assertHTMLstringsAreEqual(
			'<input type="text" class="cmb2-text-url cmb2-text-medium regular-text" name="field_test_field" id="field_test_field" value="' . esc_url_raw( $value ) . '" data-hash=\'4lavrjdps2t0\'/><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array( $this->get_field_type_object( 'text_url' ), 'render' ) )
		);

		delete_post_meta( $this->post_id, $this->text_type_field['id'] );
	}

	public function test_text_date_field_after_value_update() {

		update_post_meta( $this->post_id, $this->text_type_field['id'], 'today' );

		$field = $this->get_field_object( 'text_date' );
		$type = $this->get_field_type_object( $field );

		// Check that date format is set to the default (since we didn't set it)
		$this->assertEquals( 'm\/d\/Y', $field->args( 'date_format' ) );

		$value = $field->format_timestamp( strtotime( 'today' ) );

		$this->assertHTMLstringsAreEqual(
			sprintf( '<input type="text" class="cmb2-text-small cmb2-datepicker" name="field_test_field" id="field_test_field" value="%s" data-hash=\'4lavrjdps2t0\' data-datepicker=\'{"dateFormat":"mm&#39;\/&#39;dd&#39;\/&#39;yy"}\'/><span class="cmb2-metabox-description">This is a description</span>', $value ),
			$this->capture_render( array( $type, 'render' ) )
		);

		delete_post_meta( $this->post_id, $this->text_type_field['id'] );
	}

	public function test_text_time_field_after_value_update() {

		update_post_meta( $this->post_id, $this->text_type_field['id'], 'today' );

		$field = $this->get_field_object( 'text_time' );
		$type = $this->get_field_type_object( $field );

		// Check that time format is set to the default (since we didn't set it)
		$this->assertEquals( 'h:i A', $field->args( 'time_format' ) );

		$value = $field->format_timestamp( strtotime( 'today' ), 'time_format' );

		$this->assertHTMLstringsAreEqual(
			sprintf( '<input type="text" class="cmb2-timepicker text-time" name="field_test_field" id="field_test_field" value="%s" data-timepicker=\'{"timeFormat":"hh:mm TT"}\' data-hash=\'4lavrjdps2t0\'/><span class="cmb2-metabox-description">This is a description</span>', $value ),
			$this->capture_render( array( $type, 'render' ) )
		);

		delete_post_meta( $this->post_id, $this->text_type_field['id'] );
	}

	public function test_text_money_field() {

		$expected_field = '$ <input type="text" class="cmb2-text-money" name="field_test_field" id="field_test_field" value="" data-hash=\'4lavrjdps2t0\'/><span class="cmb2-metabox-description">This is a description</span>';

		$this->assertHTMLstringsAreEqual(
			$expected_field,
			$this->capture_render( array( $this->get_field_type_object( 'text_money' ), 'render' ) )
		);

		/**
		 * Create a new field type object,
		 * but use a British pound symbol for the prefix
		 */

		// replace $ w/ £
		$expected_field = substr_replace( $expected_field, '£', 0, 1 );

		$type = $this->get_field_type_object( array(
			'type'         => 'text_money',
			'before_field' => '£',
		) );

		$this->assertHTMLstringsAreEqual(
			$expected_field,
			$this->capture_render( array( $type, 'render' ) )
		);

		/**
		 * Create a new field type object,
		 * but use a callback to produce the British pound symbol
		 */

		// update expected
		$expected_field = str_replace( '£', '£ text_money', $expected_field );

		$type = $this->get_field_type_object( array(
			'type'         => 'text_money',
			'before_field' => array( __CLASS__, 'change_money_cb' ),
		) );

		$this->assertHTMLstringsAreEqual(
			$expected_field,
			$this->capture_render( array( $type, 'render' ) )
		);

		$this->assertEquals( '£ text_money', $type->field->get_param_callback_result( 'before_field' ) );
	}

	public function test_text_money_field_value_update() {
		$field = $this->get_field_object( 'text_money' );
		$field->save_field( '8.2' );
		$this->assertEquals( '8.20', get_post_meta( $this->post_id, $this->text_type_field['id'], 1 ) );

		delete_post_meta( $this->post_id, $this->text_type_field['id'] );
		$field = $this->get_field_object( 'text_money' );
		$field->save_field( '0.00' );
		$this->assertEquals( '0.00', get_post_meta( $this->post_id, $this->text_type_field['id'], 1 ) );

		delete_post_meta( $this->post_id, $this->text_type_field['id'] );
		$field->save_field( '0' );
		$this->assertEquals( '', get_post_meta( $this->post_id, $this->text_type_field['id'], 1 ) );

	}

	public function test_textarea_small_field() {
		$this->assertHTMLstringsAreEqual(
			'<textarea class="cmb2-textarea-small" name="field_test_field" id="field_test_field" cols="60" rows="4" data-hash=\'4lavrjdps2t0\'></textarea><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array( $this->get_field_type_object( 'textarea_small' ), 'render' ) )
		);
	}

	public function test_textarea_code_field() {
		$classes = 'cmb2-textarea-code';
		if ( ! CMB2_Utils::wp_at_least( '4.9.0' ) ) {
			$classes .= ' disable-codemirror';
		}

		$this->assertHTMLstringsAreEqual(
			'<pre><textarea class="' . $classes . '" name="field_test_field" id="field_test_field" cols="60" rows="10" data-hash=\'4lavrjdps2t0\'></textarea></pre><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array( $this->get_field_type_object( 'textarea_code' ), 'render' ) )
		);
	}

	public function test_wysiwyg_field() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$version = 'ver=' . get_bloginfo( 'version' );

		$field = $this->get_field_object( array(
			'type' => 'wysiwyg',
			'options' => array(
				'quicktags' => false,
			),
		) );
		$type = $this->get_field_type_object( $field );

		$this->assertHTMLstringsAreEqual(
			'
			<div id="wp-field_test_field-wrap" class="wp-core-ui wp-editor-wrap html-active">
				<link rel=\'stylesheet\' id=\'dashicons-css\' href=\'' . includes_url( "css/dashicons$suffix.css?$version" ) . '\' type=\'text/css\' media=\'all\' />
				<link rel=\'stylesheet\' id=\'editor-buttons-css\' href=\'' . includes_url( "css/editor$suffix.css?$version" ) . '\' type=\'text/css\' media=\'all\' />
				<div id="wp-field_test_field-editor-container" class="wp-editor-container">
					<textarea class="wp-editor-area" rows="20" cols="40" name="field_test_field" id="field_test_field">
					</textarea>
				</div>
			</div>
			<p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array( $type, 'render' ) )
		);
	}

	public function test_text_date_timestamp_field_after_value_update() {

		$val_to_update = strtotime( 'today' );

		update_post_meta( $this->post_id, $this->text_type_field['id'], $val_to_update );

		$get_val = get_post_meta( $this->post_id, $this->text_type_field['id'], 1 );

		$field = $this->get_field_object( 'text_date_timestamp' );

		$this->assertEquals( $val_to_update, $get_val );
		$this->assertEquals( $val_to_update, $field->escaped_value() );

		$formatted_val_to_update = $field->format_timestamp( $val_to_update );

		$this->assertHTMLstringsAreEqual(
			sprintf( '<input type="text" class="cmb2-text-small cmb2-datepicker" name="field_test_field" id="field_test_field" value="%s" data-hash=\'4lavrjdps2t0\' data-datepicker=\'{"dateFormat":"mm&#39;\/&#39;dd&#39;\/&#39;yy"}\'/><span class="cmb2-metabox-description">This is a description</span>', $formatted_val_to_update ),
			$this->capture_render( array( $this->get_field_type_object( $field ), 'render' ) )
		);

		delete_post_meta( $this->post_id, $this->text_type_field['id'] );
	}

	public function test_text_datetime_timestamp_field_after_value_update() {

		update_post_meta( $this->post_id, $this->text_type_field['id'], strtotime( 'today' ) );
		$today_stamp = strtotime( 'today' );

		$field = $this->get_field_object( 'text_datetime_timestamp' );

		// Check that date format is set to the default (since we didn't set it)
		$this->assertEquals( 'm\/d\/Y', $field->args( 'date_format' ) );
		$this->assertEquals( 'h:i A', $field->args( 'time_format' ) );

		$date_val = $field->format_timestamp( $today_stamp );
		$time_val = $field->format_timestamp( $today_stamp, 'time_format' );

		$this->assertHTMLstringsAreEqual(
			sprintf( '<input type="text" class="cmb2-text-small cmb2-datepicker" name="field_test_field[date]" id="field_test_field_date" value="%s" data-datepicker=\'{"dateFormat":"mm&#39;\/&#39;dd&#39;\/&#39;yy"}\' data-hash=\'4lavrjdps2t0\'/><input type="text" class="cmb2-timepicker text-time" name="field_test_field[time]" id="field_test_field_time" value="%s" data-timepicker=\'{"timeFormat":"hh:mm TT"}\' data-hash=\'4lavrjdps2t0\'/><span class="cmb2-metabox-description">This is a description</span>', $date_val, $time_val ),
			$this->capture_render( array( $this->get_field_type_object( $field ), 'render' ) )
		);

		delete_post_meta( $this->post_id, $this->text_type_field['id'] );
	}

	public function test_text_datetime_timestamp_timezone_field_after_value_update() {

		if ( version_compare( PHP_VERSION, '5.3' ) >= 0 ) {

			// date_default_timezone_set( 'America/New_York' );
			// $tzstring = CMB2_Utils::timezone_string();
			$tzstring = 'America/New_York';
			$test_stamp = strtotime( '2pm April 12 2016' );

			$field = $this->get_field_object( 'text_datetime_timestamp_timezone' );
			$date_val = $field->format_timestamp( $test_stamp );
			$time_val = $field->format_timestamp( $test_stamp, 'time_format' );

			$value_to_save = new DateTime( $date_val . ' ' . $time_val, new DateTimeZone( $tzstring ) );
			$value_to_save = serialize( $value_to_save );

			update_post_meta( $this->post_id, $this->text_type_field['id'], $value_to_save );

			$get_val = get_post_meta( $this->post_id, $this->text_type_field['id'], 1 );
			$this->assertEquals( $value_to_save, $get_val );

			$zones = wp_timezone_choice( $tzstring );

			$this->assertHTMLstringsAreEqual(
				sprintf( '<input type="text" class="cmb2-text-small cmb2-datepicker" name="field_test_field[date]" id="field_test_field_date" value="04/12/2016" data-datepicker=\'{"dateFormat":"mm&#39;\/&#39;dd&#39;\/&#39;yy"}\' data-hash=\'4lavrjdps2t0\'/><input type="text" class="cmb2-timepicker text-time" name="field_test_field[time]" id="field_test_field_time" value="06:00 PM" data-timepicker=\'{"timeFormat":"hh:mm TT"}\' data-hash=\'4lavrjdps2t0\'/><select class="cmb2_select cmb2-select-timezone" name="field_test_field[timezone]" id="field_test_field_timezone" data-hash=\'4lavrjdps2t0\'>%s</select><p class="cmb2-metabox-description">This is a description</p>', $zones ),
				$this->capture_render( array( $this->get_field_type_object( 'text_datetime_timestamp_timezone' ), 'render' ) )
			);

			delete_post_meta( $this->post_id, $this->text_type_field['id'] );
		}
	}

	public function test_select_timezone_field_after_value_update() {
		$value_to_save = CMB2_Utils::timezone_string();
		update_post_meta( $this->post_id, $this->text_type_field['id'], $value_to_save );
		$zones = wp_timezone_choice( $value_to_save );

		$this->assertHTMLstringsAreEqual(
			sprintf( '<select class="cmb2_select cmb2-select-timezone" name="field_test_field" id="field_test_field" data-hash=\'4lavrjdps2t0\'>%s</select><span class="cmb2-metabox-description">This is a description</span>', $zones ),
			$this->capture_render( array( $this->get_field_type_object( 'select_timezone' ), 'render' ) )
		);

		delete_post_meta( $this->post_id, $this->text_type_field['id'] );
	}

	public function test_colorpicker_field() {
		$this->assertHTMLstringsAreEqual(
			'<input type="text" class="cmb2-text-small cmb2-colorpicker" name="field_test_field" id="field_test_field" value="#" data-hash=\'4lavrjdps2t0\'/><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array( $this->get_field_type_object( 'colorpicker' ), 'render' ) )
		);
	}

	public function test_colorpicker_field_default() {
		$this->assertHTMLstringsAreEqual(
			'<input type="text" class="cmb2-text-small cmb2-colorpicker" name="field_test_field" id="field_test_field" value="#bada55" data-hash=\'4lavrjdps2t0\'/><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array(
				$this->get_field_type_object( array(
					'type' => 'colorpicker',
					'default' => '#bada55',
				) ),
				'render',
			) )
		);
	}

	public function test_title_field() {
		$this->assertHTMLstringsAreEqual(
			'<h5 class="cmb2-metabox-title" id="field-test-field" data-hash=\'4lavrjdps2t0\'>Name</h5><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array( $this->get_field_type_object( 'title' ), 'render' ) )
		);
	}

	public function test_select_field() {
		$field = $this->get_field_object( $this->options_test['fields'][0] );
		$this->assertHTMLstringsAreEqual(
			'<select class="cmb2_select" name="options_test_field" id="options_test_field" data-hash=\'4m4sm12idu40\'><option value="one" >One</option><option value="two" >Two</option><option value="true" >1</option><option value="false" ></option></select><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array( $this->get_field_type_object( $field ), 'render' ) )
		);
	}

	public function test_select_field_after_value_update() {
		update_post_meta( $this->post_id, $this->options_test['fields'][0]['id'], 'one' );

		$field = $this->get_field_object( $this->options_test['fields'][0] );
		$this->assertHTMLstringsAreEqual(
			'<select class="cmb2_select" name="options_test_field" id="options_test_field" data-hash=\'4m4sm12idu40\'><option value="one" selected=\'selected\'>One</option><option value="two" >Two</option><option value="true" >1</option><option value="false" ></option></select><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array( $this->get_field_type_object( $field ), 'render' ) )
		);
	}

	public function test_select_field_after_value_update_with_floats() {
		$args = array(
			'name' => 'Name',
			'desc' => '',
			'id'   => 'options_test_value_update_with_floats',
			'type' => 'select',
			'options' => array(
				'1.3' => '1.3',
				'0.8' => 0.8,
				'0.1' => '0.1',
				1     => '1',
				'0.0' => '0.0',
				0     => '0',
				''     => 'nothing',
				'one' => 'one',
			),
		);

		$tests = array(
			array(
				'0.1',
				'<option value="1.3" >1.3</option><option value="0.8" >0.8</option><option value="0.1" selected=\'selected\'>0.1</option><option value="1" >1</option><option value="0.0" >0.0</option><option value="0" >0</option><option value="" >nothing</option><option value="one" >one</option>',
			),
			array(
				0.1,
				'<option value="1.3" >1.3</option><option value="0.8" >0.8</option><option value="0.1" selected=\'selected\'>0.1</option><option value="1" >1</option><option value="0.0" >0.0</option><option value="0" >0</option><option value="" >nothing</option><option value="one" >one</option>',
			),
			array(
				.1,
				'<option value="1.3" >1.3</option><option value="0.8" >0.8</option><option value="0.1" selected=\'selected\'>0.1</option><option value="1" >1</option><option value="0.0" >0.0</option><option value="0" >0</option><option value="" >nothing</option><option value="one" >one</option>',
			),
			array(
				1,
				'<option value="1.3" >1.3</option><option value="0.8" >0.8</option><option value="0.1" >0.1</option><option value="1" selected=\'selected\'>1</option><option value="0.0" >0.0</option><option value="0" >0</option><option value="" >nothing</option><option value="one" >one</option>',
			),
			array(
				'1',
				'<option value="1.3" >1.3</option><option value="0.8" >0.8</option><option value="0.1" >0.1</option><option value="1" selected=\'selected\'>1</option><option value="0.0" >0.0</option><option value="0" >0</option><option value="" >nothing</option><option value="one" >one</option>',
			),
			array(
				'one',
				'<option value="1.3" >1.3</option><option value="0.8" >0.8</option><option value="0.1" >0.1</option><option value="1" >1</option><option value="0.0" >0.0</option><option value="0" >0</option><option value="" >nothing</option><option value="one" selected=\'selected\'>one</option>',
			),
			array(
				'0.0',
				'<option value="1.3" >1.3</option><option value="0.8" >0.8</option><option value="0.1" >0.1</option><option value="1" >1</option><option value="0.0" selected=\'selected\'>0.0</option><option value="0" >0</option><option value="" >nothing</option><option value="one" >one</option>',
			),
			array(
				0,
				'<option value="1.3" >1.3</option><option value="0.8" >0.8</option><option value="0.1" >0.1</option><option value="1" >1</option><option value="0.0" >0.0</option><option value="0" selected=\'selected\'>0</option><option value="" >nothing</option><option value="one" >one</option>',
			),
			array(
				'',
				'<option value="1.3" >1.3</option><option value="0.8" >0.8</option><option value="0.1" >0.1</option><option value="1" >1</option><option value="0.0" >0.0</option><option value="0" >0</option><option value="" selected=\'selected\'>nothing</option><option value="one" >one</option>',
			),
		);

		foreach ( $tests as $index => $test ) {

			update_post_meta( $this->post_id, $args['id'], $test[0] );
			$field = $this->get_field_object( $args );

			$this->assertHTMLstringsAreEqual(
				'<select class="cmb2_select" name="' . $args['id'] .'" id="' . $args['id'] .'" data-hash=\'2o3ljnauqs10\'>'. $test[1] .'</select>',
				$this->capture_render( array( $this->get_field_type_object( $field ), 'render' ) ),
				"Test index: $index"
			);

		}
		delete_post_meta( $this->post_id, $this->text_type_field['id'] );
	}

	public function test_select_field_after_value_update_with_leading_zeroes() {
		$args = array(
			'name' => 'Name',
			'desc' => '',
			'id'   => 'options_test_field2',
			'type' => 'select',
			// 'options' => $month_options,
			'options' => array(
				'00' => '[No month]',
				'01' => 'Jan',
				'02' => 'Feb',
				'03' => 'Mar',
				'04' => 'Apr',
				'05' => 'May',
				'06' => 'Jun',
				'07' => 'Jul',
				'08' => 'Aug',
				'09' => 'Sep',
				10   => 'Oct',
				11   => 'Nov',
				12   => 'Dec',
			),
		);

		$tests = array(
			0 => array(
				'00',
				'<option value="00" selected=\'selected\'>[No month]</option><option value="01" >Jan</option><option value="02" >Feb</option><option value="03" >Mar</option><option value="04" >Apr</option><option value="05" >May</option><option value="06" >Jun</option><option value="07" >Jul</option><option value="08" >Aug</option><option value="09" >Sep</option><option value="10" >Oct</option><option value="11" >Nov</option><option value="12" >Dec</option>',
			),
			1 => array(
				'01',
				'<option value="00" >[No month]</option><option value="01" selected=\'selected\'>Jan</option><option value="02" >Feb</option><option value="03" >Mar</option><option value="04" >Apr</option><option value="05" >May</option><option value="06" >Jun</option><option value="07" >Jul</option><option value="08" >Aug</option><option value="09" >Sep</option><option value="10" >Oct</option><option value="11" >Nov</option><option value="12" >Dec</option>',
			),
			2 => array(
				'09',
				'<option value="00" >[No month]</option><option value="01" >Jan</option><option value="02" >Feb</option><option value="03" >Mar</option><option value="04" >Apr</option><option value="05" >May</option><option value="06" >Jun</option><option value="07" >Jul</option><option value="08" >Aug</option><option value="09" selected=\'selected\'>Sep</option><option value="10" >Oct</option><option value="11" >Nov</option><option value="12" >Dec</option>',
			),
			3 => array(
				10,
				'<option value="00" >[No month]</option><option value="01" >Jan</option><option value="02" >Feb</option><option value="03" >Mar</option><option value="04" >Apr</option><option value="05" >May</option><option value="06" >Jun</option><option value="07" >Jul</option><option value="08" >Aug</option><option value="09" >Sep</option><option value="10" selected=\'selected\'>Oct</option><option value="11" >Nov</option><option value="12" >Dec</option>',
			),
			4 => array(
				12,
				'<option value="00" >[No month]</option><option value="01" >Jan</option><option value="02" >Feb</option><option value="03" >Mar</option><option value="04" >Apr</option><option value="05" >May</option><option value="06" >Jun</option><option value="07" >Jul</option><option value="08" >Aug</option><option value="09" >Sep</option><option value="10" >Oct</option><option value="11" >Nov</option><option value="12" selected=\'selected\'>Dec</option>',
			),
			5 => array(
				0,
				'<option value="00" selected=\'selected\'>[No month]</option><option value="01" >Jan</option><option value="02" >Feb</option><option value="03" >Mar</option><option value="04" >Apr</option><option value="05" >May</option><option value="06" >Jun</option><option value="07" >Jul</option><option value="08" >Aug</option><option value="09" >Sep</option><option value="10" >Oct</option><option value="11" >Nov</option><option value="12" >Dec</option>',
			),
			6 => array(
				1,
				'<option value="00" >[No month]</option><option value="01" selected=\'selected\'>Jan</option><option value="02" >Feb</option><option value="03" >Mar</option><option value="04" >Apr</option><option value="05" >May</option><option value="06" >Jun</option><option value="07" >Jul</option><option value="08" >Aug</option><option value="09" >Sep</option><option value="10" >Oct</option><option value="11" >Nov</option><option value="12" >Dec</option>',
			),
			7 => array(
				0,
				'<option value="00" selected=\'selected\'>[No month]</option><option value="01" >Jan</option><option value="02" >Feb</option><option value="03" >Mar</option><option value="04" >Apr</option><option value="05" >May</option><option value="06" >Jun</option><option value="07" >Jul</option><option value="08" >Aug</option><option value="09" >Sep</option><option value="10" >Oct</option><option value="11" >Nov</option><option value="12" >Dec</option>',
			),
			8 => array(
				'10',
				'<option value="00" >[No month]</option><option value="01" >Jan</option><option value="02" >Feb</option><option value="03" >Mar</option><option value="04" >Apr</option><option value="05" >May</option><option value="06" >Jun</option><option value="07" >Jul</option><option value="08" >Aug</option><option value="09" >Sep</option><option value="10" selected=\'selected\'>Oct</option><option value="11" >Nov</option><option value="12" >Dec</option>',
			),
			9 => array(
				'12',
				'<option value="00" >[No month]</option><option value="01" >Jan</option><option value="02" >Feb</option><option value="03" >Mar</option><option value="04" >Apr</option><option value="05" >May</option><option value="06" >Jun</option><option value="07" >Jul</option><option value="08" >Aug</option><option value="09" >Sep</option><option value="10" >Oct</option><option value="11" >Nov</option><option value="12" selected=\'selected\'>Dec</option>',
			),
			10 => array(
				'0.1',
				'<option value="00" >[No month]</option><option value="01" >Jan</option><option value="02" >Feb</option><option value="03" >Mar</option><option value="04" >Apr</option><option value="05" >May</option><option value="06" >Jun</option><option value="07" >Jul</option><option value="08" >Aug</option><option value="09" >Sep</option><option value="10" >Oct</option><option value="11" >Nov</option><option value="12" >Dec</option>',
			),
		);

		foreach ( $tests as $index => $test ) {

			update_post_meta( $this->post_id, $args['id'], $test[0] );
			$field = $this->get_field_object( $args );

			$this->assertHTMLstringsAreEqual(
				'<select class="cmb2_select" name="' . $args['id'] .'" id="' . $args['id'] .'" data-hash=\'h5f4ba7nvs40\'>'. $test[1] .'</select>',
				$this->capture_render( array( $this->get_field_type_object( $field ), 'render' ) ),
				"Test index: $index"
			);

		}

		delete_post_meta( $this->post_id, $this->text_type_field['id'] );
	}

	public function test_taxonomy_select_field() {

		$args = $this->options_test['fields'][0];
		$args['type'] = 'taxonomy_select';
		$args['taxonomy'] = 'category';
		$field = $this->get_field_object( $args );

		$this->assertHTMLstringsAreEqual(
			'<select class="cmb2_select" name="options_test_field" id="options_test_field" data-hash=\'4m4sm12idu40\'><option value="" >None</option><option value="number_2" >number_2</option><option value="test_category" selected=\'selected\'>test_category</option><option value="uncategorized" >Uncategorized</option></select><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array( $this->get_field_type_object( $field ), 'render' ) )
		);
	}

	public function test_radio_field() {
		$args = $this->options_test['fields'][0];
		$args['type'] = 'radio';
		$field = $this->get_field_object( $args );
		$this->assertHTMLstringsAreEqual(
			'<ul class="cmb2-radio-list cmb2-list"><li><input type="radio" class="cmb2-option" name="options_test_field" id="options_test_field1" value="one" data-hash=\'4m4sm12idu40\'/><label for="options_test_field1">One</label></li><li><input type="radio" class="cmb2-option" name="options_test_field" id="options_test_field2" value="two" data-hash=\'4m4sm12idu40\'/><label for="options_test_field2">Two</label></li><li><input type="radio" class="cmb2-option" name="options_test_field" id="options_test_field3" value="true" data-hash=\'4m4sm12idu40\'/><label for="options_test_field3">1</label></li><li><input type="radio" class="cmb2-option" name="options_test_field" id="options_test_field4" value="false" data-hash=\'4m4sm12idu40\'/><label for="options_test_field4"></label></li></ul><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array( $this->get_field_type_object( $field ), 'render' ) )
		);
	}

	public function test_multicheck_field() {
		$args = $this->options_test['fields'][0];
		$args['type'] = 'multicheck';
		$field = $this->get_field_object( $args );
		$this->assertHTMLstringsAreEqual(
			'<ul class="cmb2-checkbox-list cmb2-list"><li><input type="checkbox" class="cmb2-option" name="options_test_field[]" id="options_test_field1" value="one" data-hash=\'4m4sm12idu40\'/><label for="options_test_field1">One</label></li><li><input type="checkbox" class="cmb2-option" name="options_test_field[]" id="options_test_field2" value="two" data-hash=\'4m4sm12idu40\'/><label for="options_test_field2">Two</label></li><li><input type="checkbox" class="cmb2-option" name="options_test_field[]" id="options_test_field3" value="true" data-hash=\'4m4sm12idu40\'/><label for="options_test_field3">1</label></li><li><input type="checkbox" class="cmb2-option" name="options_test_field[]" id="options_test_field4" value="false" data-hash=\'4m4sm12idu40\'/><label for="options_test_field4"></label></li></ul><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array( $this->get_field_type_object( $field ), 'render' ) )
		);
	}

	public function test_multicheck_field_after_value_update() {
		update_post_meta( $this->post_id, $this->options_test['fields'][0]['id'], array( 'false', 'one' ) );

		$args = $this->options_test['fields'][0];
		$args['type'] = 'multicheck';
		$field = $this->get_field_object( $args );
		$this->assertHTMLstringsAreEqual(
			'<ul class="cmb2-checkbox-list cmb2-list"><li><input type="checkbox" class="cmb2-option" name="options_test_field[]" id="options_test_field1" value="one" checked="checked" data-hash=\'4m4sm12idu40\'/><label for="options_test_field1">One</label></li><li><input type="checkbox" class="cmb2-option" name="options_test_field[]" id="options_test_field2" value="two" data-hash=\'4m4sm12idu40\'/><label for="options_test_field2">Two</label></li><li><input type="checkbox" class="cmb2-option" name="options_test_field[]" id="options_test_field3" value="true" data-hash=\'4m4sm12idu40\'/><label for="options_test_field3">1</label></li><li><input type="checkbox" class="cmb2-option" name="options_test_field[]" id="options_test_field4" value="false" checked="checked" data-hash=\'4m4sm12idu40\'/><label for="options_test_field4"></label></li></ul><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array( $this->get_field_type_object( $field ), 'render' ) )
		);

		delete_post_meta( $this->post_id, $this->text_type_field['id'] );
	}

	public function test_checkbox_field() {
		$type_object = $this->get_field_type_object( 'checkbox' );
		$this->check_box_assertion( array( $type_object, 'render' ) );

		update_post_meta( $type_object->field->object_id, 'field_test_field', 'true' );

		// Test when value exists
		$this->check_box_assertion( array( $this->get_field_type_object( 'checkbox' ), 'render' ), true );

		$type_object = $this->get_field_type_object( 'checkbox' );

		// Test when value exists again
		$this->check_box_assertion( $type_object->checkbox(), true );

		// Test when value exists but we tell checkbox it's not checked
		$this->check_box_assertion( $type_object->checkbox( array(), false ) );

		delete_post_meta( $type_object->field->object_id, 'field_test_field' );

		// Test when value doesn't exist but we tell checkbox it is checked
		$this->check_box_assertion( $type_object->checkbox( array(), true ), true );

	}

	public function test_taxonomy_radio_field() {
		$args = $this->options_test['fields'][0];
		$args['type'] = 'taxonomy_radio';
		$args['taxonomy'] = 'category';
		$field = $this->get_field_object( $args );

		$this->assertHTMLstringsAreEqual(
			'<ul class="cmb2-radio-list cmb2-list"><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field1" value="" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field1">None</label></li><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field2" value="number_2" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field2">number_2</label></li><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field3" value="test_category" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field3">test_category</label></li><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field4" value="uncategorized" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field4">Uncategorized</label></li></ul><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array(
				$this->get_field_type_object( array(
					'type' => 'taxonomy_radio',
					'taxonomy' => 'category',
				) ), 'render',
			) )
		);
	}

	public function test_taxonomy_radio_field_after_value_update() {

		$set = wp_set_post_categories( $this->post_id, array( $this->term, 1 ) );
		$terms = wp_get_post_categories( $this->post_id );
		$this->assertTrue( in_array( $this->term, $terms ) );
		$this->assertTrue( ! ! $set );
		// $this->assertEquals( 0, $this->term );
		$type = $this->get_field_type_object( array(
			'type' => 'taxonomy_radio',
			'taxonomy' => 'category',
		) );
		$this->assertHTMLstringsAreEqual(
			'<ul class="cmb2-radio-list cmb2-list"><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field1" value="" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field1">None</label></li><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field2" value="number_2" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field2">number_2</label></li><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field3" value="test_category" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field3">test_category</label></li><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field4" value="uncategorized" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field4">Uncategorized</label></li></ul><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array( $type, 'render' ) )
		);

		wp_set_object_terms( $this->post_id, 'test_category', 'category' );
	}

	public function test_taxonomy_radio_hierarchical_field_after_value_update() {
		$set = wp_set_post_categories( $this->post_id, $this->get_test_hierarchical_terms() );
		$terms = wp_get_post_categories( $this->post_id );
		$this->assertTrue( in_array( $this->term, $terms ) );
		$this->assertTrue( ! ! $set );
		// $this->assertEquals( 0, $this->term );
		$type = $this->get_field_type_object( array(
			'type' => 'taxonomy_radio_hierarchical',
			'taxonomy' => 'category',
		) );
		$this->assertHTMLstringsAreEqual(
			'<ul class="cmb2-radio-list cmb2-list"><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field1" value="" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field1">None</label></li><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field2" value="number_2" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field2">number_2</label></li><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field3" value="test_category" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field3">test_category</label></li><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field4" value="test_category0" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field4">test_category0</label></li><li class="cmb2-indented-hierarchy"><ul><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field5" value="test_category00" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field5">test_category00</label></li><li class="cmb2-indented-hierarchy"><ul><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field6" value="test_category000" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field6">test_category000</label></li></ul></li><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field7" value="test_category01" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field7">test_category01</label></li><li class="cmb2-indented-hierarchy"><ul><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field8" value="test_category010" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field8">test_category010</label></li></ul></li></ul></li><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field9" value="test_category1" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field9">test_category1</label></li><li class="cmb2-indented-hierarchy"><ul><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field10" value="test_category10" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field10">test_category10</label></li><li class="cmb2-indented-hierarchy"><ul><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field11" value="test_category100" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field11">test_category100</label></li></ul></li><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field12" value="test_category11" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field12">test_category11</label></li><li class="cmb2-indented-hierarchy"><ul><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field13" value="test_category110" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field13">test_category110</label></li></ul></li></ul></li><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field14" value="test_category2" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field14">test_category2</label></li><li class="cmb2-indented-hierarchy"><ul><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field15" value="test_category20" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field15">test_category20</label></li><li class="cmb2-indented-hierarchy"><ul><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field16" value="test_category200" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field16">test_category200</label></li></ul></li><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field17" value="test_category21" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field17">test_category21</label></li><li class="cmb2-indented-hierarchy"><ul><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field18" value="test_category210" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field18">test_category210</label></li></ul></li></ul></li><li><input type="radio" class="cmb2-option" name="field_test_field" id="field_test_field19" value="uncategorized" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field19">Uncategorized</label></li></ul><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array( $type, 'render' ) )
		);

		wp_set_object_terms( $this->post_id, 'test_category', 'category' );
	}

	public function test_taxonomy_multicheck_field() {
		$this->assertHTMLstringsAreEqual(
			'<ul class="cmb2-checkbox-list cmb2-list"><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field1" value="number_2" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field1">number_2</label></li><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field2" value="test_category" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field2">test_category</label></li><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field3" value="uncategorized" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field3">Uncategorized</label></li></ul><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array(
				$this->get_field_type_object( array(
					'type' => 'taxonomy_multicheck',
					'taxonomy' => 'category',
				) ), 'render',
			) )
		);
	}

	public function test_taxonomy_multicheck_field_after_value_update() {

		$set = wp_set_post_categories( $this->post_id, array( $this->term, 1 ) );
		$terms = wp_get_post_categories( $this->post_id );
		$this->assertTrue( in_array( $this->term, $terms ) );
		$this->assertTrue( ! ! $set );
		// $this->assertEquals( 0, $this->term );
		$type = $this->get_field_type_object( array(
			'type' => 'taxonomy_multicheck',
			'taxonomy' => 'category',
		) );
		$this->assertHTMLstringsAreEqual(
			'<ul class="cmb2-checkbox-list cmb2-list"><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field1" value="number_2" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field1">number_2</label></li><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field2" value="test_category" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field2">test_category</label></li><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field3" value="uncategorized" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field3">Uncategorized</label></li></ul><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array( $type, 'render' ) )
		);

		wp_set_object_terms( $this->post_id, 'test_category', 'category' );
	}

	public function test_taxonomy_multicheck_hierarchical_field() {
		$this->assertHTMLstringsAreEqual(
			'<ul class="cmb2-checkbox-list cmb2-list"><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field1" value="number_2" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field1">number_2</label></li><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field2" value="test_category" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field2">test_category</label></li><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field3" value="uncategorized" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field3">Uncategorized</label></li></ul><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array(
				$this->get_field_type_object( array(
					'type' => 'taxonomy_multicheck_hierarchical',
					'taxonomy' => 'category',
				) ), 'render',
			) )
		);
	}

	public function test_taxonomy_multicheck_hierarchical_field_after_value_update() {
		$set = wp_set_post_categories( $this->post_id, $this->get_test_hierarchical_terms() );
		$terms = wp_get_post_categories( $this->post_id );
		$this->assertTrue( in_array( $this->term, $terms ) );
		$this->assertTrue( ! ! $set );
		// $this->assertEquals( 0, $this->term );
		$type = $this->get_field_type_object( array(
			'type' => 'taxonomy_multicheck_hierarchical',
			'taxonomy' => 'category',
		) );
		$this->assertHTMLstringsAreEqual(
			'<ul class="cmb2-checkbox-list cmb2-list"><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field1" value="number_2" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field1">number_2</label></li><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field2" value="test_category" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field2">test_category</label></li><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field3" value="test_category0" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field3">test_category0</label></li><li class="cmb2-indented-hierarchy"><ul><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field4" value="test_category00" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field4">test_category00</label></li><li class="cmb2-indented-hierarchy"><ul><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field5" value="test_category000" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field5">test_category000</label></li></ul></li><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field6" value="test_category01" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field6">test_category01</label></li><li class="cmb2-indented-hierarchy"><ul><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field7" value="test_category010" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field7">test_category010</label></li></ul></li></ul></li><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field8" value="test_category1" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field8">test_category1</label></li><li class="cmb2-indented-hierarchy"><ul><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field9" value="test_category10" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field9">test_category10</label></li><li class="cmb2-indented-hierarchy"><ul><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field10" value="test_category100" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field10">test_category100</label></li></ul></li><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field11" value="test_category11" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field11">test_category11</label></li><li class="cmb2-indented-hierarchy"><ul><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field12" value="test_category110" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field12">test_category110</label></li></ul></li></ul></li><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field13" value="test_category2" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field13">test_category2</label></li><li class="cmb2-indented-hierarchy"><ul><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field14" value="test_category20" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field14">test_category20</label></li><li class="cmb2-indented-hierarchy"><ul><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field15" value="test_category200" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field15">test_category200</label></li></ul></li><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field16" value="test_category21" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field16">test_category21</label></li><li class="cmb2-indented-hierarchy"><ul><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field17" value="test_category210" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field17">test_category210</label></li></ul></li></ul></li><li><input type="checkbox" class="cmb2-option" name="field_test_field[]" id="field_test_field18" value="uncategorized" checked="checked" data-hash=\'4lavrjdps2t0\'/><label for="field_test_field18">Uncategorized</label></li></ul><p class="cmb2-metabox-description">This is a description</p>',
			$this->capture_render( array( $type, 'render' ) )
		);

		wp_set_object_terms( $this->post_id, 'test_category', 'category' );
	}

	public function test_file_list_field() {
		$this->assertHTMLstringsAreEqual(
			'<input type="hidden" class="cmb2-upload-file cmb2-upload-list" name="field_test_field" id="field_test_field" value="" size="45" data-previewsize=\'[120,120]\' data-sizename=\'thumbnail\' data-queryargs=\'\' data-hash=\'4lavrjdps2t0\'/><input type="button" class="cmb2-upload-button button-secondary cmb2-upload-list" name="" id="" value="' . esc_attr__( 'Add or Upload Files', 'cmb2' ) . '" data-hash=\'4lavrjdps2t0\'/><p class="cmb2-metabox-description">This is a description</p><ul id="field_test_field-status" class="cmb2-media-status cmb-attach-list"></ul>',
			$this->capture_render( array(
				$this->get_field_type_object( array(
					'type' => 'file_list',
					'preview_size' => array( 120, 120 ),
				) ), 'render',
			) )
		);
	}

	public function test_file_list_field_after_value_update() {

		$images = get_attached_media( 'image', $this->post_id );
		$attach_1_url = get_permalink( $this->attachment_id );
		$attach_2_url = get_permalink( $this->attachment_id2 );

		$this->assertEquals( $images, array(
			$this->attachment_id => get_post( $this->attachment_id ),
			$this->attachment_id2 => get_post( $this->attachment_id2 ),
		) );

		update_post_meta( $this->post_id, $this->text_type_field['id'], array(
			$this->attachment_id => $attach_1_url,
			$this->attachment_id2 => $attach_2_url,
		) );

		$field_type = $this->get_field_type_object( 'file_list' );

		$sizename = CMB2_Utils::wp_at_least( '4.7' ) && version_compare( get_bloginfo( 'version' ), '5.0', '<' )
			? 'twentyseventeen-thumbnail-avatar'
			: 'thumbnail';

		$this->assertHTMLstringsAreEqual(
			sprintf( '<input type="hidden" class="cmb2-upload-file cmb2-upload-list" name="field_test_field" id="field_test_field" value="" size="45" data-previewsize=\'[50,50]\' data-sizename=\'' . $sizename . '\' data-queryargs=\'\' data-hash=\'4lavrjdps2t0\'/><input type="button" class="cmb2-upload-button button-secondary cmb2-upload-list" name="" id="" value="' . esc_attr__( 'Add or Upload Files', 'cmb2' ) . '" data-hash=\'4lavrjdps2t0\'/><p class="cmb2-metabox-description">This is a description</p><ul id="field_test_field-status" class="cmb2-media-status cmb-attach-list">%1$s%2$s</ul>',
				$this->file_sprintf( array(
					'file_name'     => $field_type->get_file_name_from_path( $attach_1_url ),
					'attachment_id' => $this->attachment_id,
					'url'           => $attach_1_url,
				) ),
				$this->file_sprintf( array(
					'file_name'     => $field_type->get_file_name_from_path( $attach_2_url ),
					'attachment_id' => $this->attachment_id2,
					'url'           => $attach_2_url,
				) )
			),
			$this->capture_render( array( $field_type, 'render' ) )
		);

		delete_post_meta( $this->post_id, $this->text_type_field['id'] );
	}

	public function test_file_field() {
		$this->assertHTMLstringsAreEqual(
			'<input type="text" class="cmb2-upload-file regular-text" name="field_test_field" id="field_test_field" value="" size="45" data-previewsize=\'[199,199]\' data-sizename=\'medium\' data-queryargs=\'\' data-hash=\'4lavrjdps2t0\'/><input class="cmb2-upload-button button-secondary" type="button" value="' . esc_attr__( 'Add or Upload File', 'cmb2' ) . '" /><p class="cmb2-metabox-description">This is a description</p><input type="hidden" class="cmb2-upload-file-id" name="field_test_field_id" id="field_test_field_id" value=""/><div id="field_test_field-status" class="cmb2-media-status"></div>',
			$this->capture_render( array(
				$this->get_field_type_object( array(
					'type' => 'file',
					'preview_size' => array( 199, 199 ),
				) ),
				'render',
			) )
		);
	}

	public function test_file_field_after_value_update() {
		update_post_meta( $this->post_id, $this->text_type_field['id'], get_permalink( $this->attachment_id ) );
		update_post_meta( $this->post_id, $this->text_type_field['id'] . '_id', $this->attachment_id );

		$field_type = $this->get_field_type_object( array(
			'type'         => 'file',
			'preview_size' => array( 199, 199 ),
		) );

		$file_url = get_permalink( $this->attachment_id );
		$file_name = $field_type->get_file_name_from_path( $file_url );

		$this->assertHTMLstringsAreEqual(
			sprintf( '<input type="text" class="cmb2-upload-file regular-text" name="field_test_field" id="field_test_field" value="%2$s" size="45" data-previewsize=\'[199,199]\' data-sizename=\'medium\' data-queryargs=\'\' data-hash=\'4lavrjdps2t0\'/><input class="cmb2-upload-button button-secondary" type="button" value="' . esc_attr__( 'Add or Upload File', 'cmb2' ) . '" /><p class="cmb2-metabox-description">This is a description</p><input type="hidden" class="cmb2-upload-file-id" name="field_test_field_id" id="field_test_field_id" value="%1$d"/><div id="field_test_field-status" class="cmb2-media-status"><div class="file-status cmb2-media-item"><span>' . esc_html__( 'File:', 'cmb2' ) . ' <strong>%3$s</strong></span>&nbsp;&nbsp; (<a href="%2$s" target="_blank" rel="external">' . esc_html__( 'Download','cmb2' ) . '</a> / <a href="#" class="cmb2-remove-file-button" rel="field_test_field">' . esc_html__( 'Remove', 'cmb2' ) . '</a>)</div></div>',
				$this->attachment_id,
				$file_url,
				$file_name
			),
			$this->capture_render( array( $field_type, 'render' ) )
		);

		delete_post_meta( $this->post_id, $this->text_type_field['id'] );
		delete_post_meta( $this->post_id, $this->text_type_field['id'] . '_id' );
	}

	public function test_file_field_id_value_empty_after_input_value_empty() {
		$field = $this->get_field_object( array(
			'id' => 'test_value_input_empty',
			'type' => 'file',
		) );

		$file_url = get_permalink( $this->attachment_id );
		$field->save_field_from_data( array(
			'test_value_input_empty' => $file_url,
			'test_value_input_empty_id' => $this->attachment_id,
		) );

		$this->assertSame( get_post_meta( $this->post_id, 'test_value_input_empty', true ), $file_url );
		$this->assertSame( get_post_meta( $this->post_id, 'test_value_input_empty_id', true ), (string) $this->attachment_id );

		$field->save_field_from_data( array(
			'test_value_input_empty' => '',
			'test_value_input_empty_id' => $this->attachment_id,
		) );

		$this->assertSame( get_post_meta( $this->post_id, 'test_value_input_empty', true ), '' );
		$this->assertSame( get_post_meta( $this->post_id, 'test_value_input_empty_id', true ), '' );
	}

	public function test_oembed_field() {
		$this->assertHTMLstringsAreEqual(
			sprintf( '<input type="text" class="cmb2-oembed regular-text" name="field_test_field" id="field_test_field" value="" data-objectid=\'%1$d\' data-objecttype=\'post\' data-hash=\'4lavrjdps2t0\'/><p class="cmb2-metabox-description">This is a description</p><p class="cmb-spinner spinner"></p><div id="field_test_field-status" class="cmb2-media-status ui-helper-clearfix embed_wrap"></div>', $this->post_id ),
			$this->capture_render( array( $this->get_field_type_object( 'oembed' ), 'render' ) )
		);
	}

	/**
	 * @group cmb2-ajax-embed
	 */
	public function test_oembed_field_after_value_update() {
		$args = array(
			'src'       => 'http://www.youtube.com/embed/EOfy5LDpEHo?feature=oembed',
			'url'       => 'https://www.youtube.com/watch?v=EOfy5LDpEHo',
			'field_id'  => 'field_test_field',
			'object_id' => $this->post_id,
		);

		update_post_meta( $this->post_id, $this->text_type_field['id'], $args['url'] );

		$args['oembed_result'] = array(
			'<iframe ',
			sprintf( 'src="%s"', $args['src'] ),
			'</iframe>',
		);

		$this->assertOEmbedResult( $args );

		delete_post_meta( $this->post_id, $this->text_type_field['id'] );
	}

	public function test_js_dependencies() {
		$expected = array(
			'jquery'                   => 'jquery',
			'jquery-ui-core'           => 'jquery-ui-core',
			'jquery-ui-datepicker'     => 'jquery-ui-datepicker',
			'jquery-ui-datetimepicker' => 'jquery-ui-datetimepicker',
			'media-editor'             => 'media-editor',
			'wp-color-picker'          => 'wp-color-picker',
			'jquery-ui-sortable'       => 'jquery-ui-sortable'
		);

		if ( CMB2_Utils::wp_at_least( '4.9.0' ) ) {
			$expected['code-editor'] = 'code-editor';
		}

		$this->assertEquals( $expected, Test_CMB2_JS::dependencies() );
	}

	public function test_save_group() {
		$is_53 = version_compare( PHP_VERSION, '5.3' ) >= 0;

		$cmb_group = new_cmb2_box( array(
			'id'           => 'group_metabox',
			'title'        => 'title',
			'object_types' => array( 'page' ),
		) );
		$group_field_id = $cmb_group->add_field( array(
			'id'   => 'group',
			'type' => 'group',
		) );
		foreach ( array( 'text', 'textarea_small', 'file' ) as $type ) {
			$cmb_group->add_group_field( $group_field_id, array(
				'id'   => $type,
				'type' => $type,
			) );
		}
		if ( $is_53 ) {
			$date_args = array(
				'id' => 'text_datetime_timestamp_timezone',
				'type' => 'text_datetime_timestamp_timezone',
				'time_format' => 'H:i',
				'date_format' => 'Y-m-d',
				'repeatable' => true,
			);
			$cmb_group->add_group_field( $group_field_id, $date_args );
		}

		$to_save = array(
			'group' => array(
				array(
					'text' => 'Entry Title',
					'textarea_small' => 'Nullam id dolor id nibh ultricies vehicula ut id elit. ',
					'file' => 'http://example.com/files/2015/07/IMG.jpg',
					'file_id' => 518,
					'text_datetime_timestamp_timezone' => array(
						array(
							'date' => '2015-11-20',
							'time' => '17:00',
							'timezone' => 'America/New_York',
						),
						array(
							'date' => '2015-11-20',
							'time' => '17:00',
							'timezone' => 'America/Chicago',
						),
						array(
							'date' => null,
							'time' => null,
							'timezone' => null,
						),
					),
				),
			),
		);

		if ( ! $is_53 ) {
			unset( $to_save['group'][0]['text_datetime_timestamp_timezone'] );
		} else {
			$date_values = array();
			foreach ( $to_save['group'][0]['text_datetime_timestamp_timezone'] as $key => $value ) {
				if ( null === $value['date'] ) {
					continue;
				}

				$tzstring = $value['timezone'];
				$offset = CMB2_Utils::timezone_offset( $tzstring );

				if ( 'UTC' === substr( $tzstring, 0, 3 ) ) {
					$tzstring = timezone_name_from_abbr( '', $offset, 0 );
					$tzstring = false !== $tzstring ? $tzstring : timezone_name_from_abbr( '', 0, 0 );
				}

				$full_format = $date_args['date_format'] . ' ' . $date_args['time_format'];
				$full_date   = $value['date'] . ' ' . $value['time'];

				$datetime = date_create_from_format( $full_format, $full_date );

				if ( ! is_object( $datetime ) ) {
					$date_values[] = '';
				} else {
					$timestamp = $datetime->setTimezone( new DateTimeZone( $tzstring ) )->getTimestamp();
					$date_values[] = serialize( $datetime );
				}
			}
		}

		$values = cmb2_get_metabox( $cmb_group->cmb_id, $this->post_id, 'post' )->get_sanitized_values( $to_save );

		$expected = array(
			'group' => array(
				array(
					'text' => 'Entry Title',
					'textarea_small' => 'Nullam id dolor id nibh ultricies vehicula ut id elit. ',
					'file_id' => 518,
					'file' => 'http://example.com/files/2015/07/IMG.jpg',
				),
			),
		);

		if ( $is_53 ) {

			date_default_timezone_set( 'America/New_York' );

			$expected['group'][0]['text_datetime_timestamp_timezone_utc'] = array( 1448056800, 1448060400 );

			// If DST, remove an hour.
			if ( date( 'I' ) ) {
				foreach ( $expected['group'][0]['text_datetime_timestamp_timezone_utc'] as $key => $value ) {
					$expected['group'][0]['text_datetime_timestamp_timezone_utc'][ $key ] = $value - 3600;
				}
			}

			$expected['group'][0]['text_datetime_timestamp_timezone'] = $date_values;
		}

		$this->assertEquals( $expected, $values );
	}

	public function test_save_group_with_file_field() {
		$cmb = new CMB2( array(
			'id' => 'test-save-file-in-group',
			'object_types' => array(
				'post',
			),
			'fields' => array(
				'group_field' => array(
					'name' => 'Group',
					'desc' => 'Group description',
					'id' => 'group_field',
					'type' => 'group',
					'fields' => array(
						'first_field' => array(
							'name' => 'Field 1',
							'id' => 'first_field',
							'type' => 'text',
						),
						'test_file' => array(
							'name' => 'Name',
							'id' => 'test_file',
							'type' => 'file',
						),
					),
				),
			),
		) );

		$test_values = array(
			'group_field' => array(
				array(
					'first_field'  => '',
					'test_file'    => '',
					'test_file_id' => '0',
				),
			),
		);
		$expected = $test_values;
		$expected['group_field'] = array();

		$this->assertEquals( $expected, $cmb->get_sanitized_values( $test_values ) );

		$test_values['group_field'][0]['first_field'] = 'one';
		$test_values['group_field'][0]['test_file'] = 'http://two';

		$expected = $test_values;
		unset( $expected['group_field'][0]['test_file_id'] );

		$this->assertEquals( $expected, $cmb->get_sanitized_values( $test_values ) );

		$test_values['group_field'][0]['test_file_id'] = '3';
		$expected['group_field'][0]['test_file_id'] = 3;

		$this->assertEquals( $expected, $cmb->get_sanitized_values( $test_values ) );
	}

	public static function options_cb( $field ) {
		return array(
			'one'         => 'One',
			'two'         => 'Two',
			'true'        => true,
			'false'       => false,
			'post_id'     => $field->object_id,
			'object_type' => $field->object_type,
			'type'        => $field->args( 'type' ),
		);
	}

	public static function add_type_cb( $types ) {
		$types[] = 'test';
		return $types;
	}

	public static function change_money_cb( $field_args ) {
		return '£ ' . $field_args['type'];
	}

	public function test_maybe_custom_field_object() {
		$cmb = new CMB2( array(
			'id' => 'field_test',
			'fields' => array(
				array(
					'name' => 'Name',
					'desc' => 'This is a description',
					'id'   => 'field_test_field_custom',
					'type' => 'test_custom',
				),
			),
		) );

		add_action( 'cmb2_render_test_custom', array( __CLASS__, 'hey_macarena' ) );

		$field = cmb2_get_field( 'field_test', 'field_test_field_custom', $this->post_id );

		$types = new CMB2_Types( $field );

		$this->assertSame( false, $types->maybe_custom_field_object( 'test_custom' ) );

		$this->assertSame( 'hey macarena!', $this->capture_render( array( $types, 'render' ) ) );

		add_filter( 'cmb2_render_class_test_custom', array( __CLASS__, 'return_cmb2_type_title' ) );

		$this->assertInstanceOf( 'CMB2_Type_Title', $types->maybe_custom_field_object( 'test_custom' ) );

		$expected = '<h5 class="cmb2-metabox-title" id="field-test-field-custom" data-hash=\'17d97i7k89uo\'>Name</h5><p class="cmb2-metabox-description">This is a description</p>';

		$this->assertHTMLstringsAreEqual( $expected, $this->capture_render( array( $types, 'render' ) ) );
	}

	public static function hey_macarena() {
		echo 'hey macarena!';
	}

	public static function return_cmb2_type_title() {
		return 'CMB2_Type_Title';
	}

	protected function get_test_hierarchical_terms() {
		$terms = array( $this->term, 1 );

		for ( $i=0; $i < 3; $i++ ) {
			$terms[] = $term = $this->factory->term->create( array(
				'taxonomy' => 'category',
				'name' => 'test_category' . $i,
			) );
			for ( $j=0; $j < 2; $j++ ) {
				$terms[] = $subterm = $this->factory->term->create( array(
					'taxonomy' => 'category',
					'name' => 'test_category' . $i . $j,
					'parent' => $term,
				) );
				for ( $y=0; $y < 1; $y++ ) {
					$terms[] = $this->factory->term->create( array(
						'taxonomy' => 'category',
						'name' => 'test_category' . $i . $j . $y,
						'parent' => $subterm,
					) );
				}
			}

		}

		return $terms;
	}
}
