<?php
/**
 * Personalizzazione delle pagine base di WordPress.
 *
 * @package Design_Laboratori_Italia
 */

/**
 * The manager that setups Page content.
 */
class Page_Manager {
	/**
	 * Constructor of the Manager.
	 */
	public function __construct() {}

	/**
	 * Configure the Page.
	 *
	 * @return void
	 */
	public function setup() {
		add_action( 'init', array( $this, 'add_post_fields' ) );
	}

	/**
	 * Add fields to the page.
	 *
	 * @return void
	 */
	public function add_post_fields() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group(
			array(
				'key'                   => 'group_670e6f9be1c67',
				'title'                 => 'Campi Pagina Base',
				'fields'                => array(
					array(
						'key'                  => 'field_670e717b3bedb',
						'label'                => 'Pagine collegate',
						'name'                 => 'pagine_collegate',
						'aria-label'           => '',
						'type'                 => 'relationship',
						'instructions'         => '',
						'required'             => 0,
						'conditional_logic'    => 0,
						'wrapper'              => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'post_type'            => array(
							0 => 'notizia',
							1 => 'evento',
						),
						'post_status'          => array(
							0 => 'publish',
						),
						'taxonomy'             => '',
						'filters'              => array(
							0 => 'search',
						),
						'return_format'        => 'object',
						'min'                  => '',
						'max'                  => '',
						'allow_in_bindings'    => 1,
						'elements'             => '',
						'bidirectional'        => 0,
						'bidirectional_target' => array(),
					),
					array(
						'key'               => 'field_670e717b3bedc',
						'label'             => 'Promuovi in Home',
						'name'              => 'promuovi_in_home',
						'aria-label'        => '',
						'type'              => 'true_false',
						'instructions'      => 'Mostra questa pagina nella sezione "Cosa puoi fare" della Home Page.',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'message'           => '',
						'default_value'     => 0,
						'ui'                => 0,
						'ui_on_text'        => '',
						'ui_off_text'       => '',
					),
					array(
						'key'               => 'field_670e717b3bedd',
						'label'             => 'Mostra le sottopagine come card',
						'name'              => 'mostra_sottopagine_a_card',
						'aria-label'        => '',
						'type'              => 'true_false',
						'instructions'      => 'Mostra, in testa al corpo di questa pagina, le sue sottopagine dirette come card di navigazione (titolo, estratto e link "Vai al servizio").',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'message'           => '',
						'default_value'     => 0,
						'ui'                => 0,
						'ui_on_text'        => '',
						'ui_off_text'       => '',
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'page',
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
				'show_in_rest'          => 0,
			)
		);
	}
}
