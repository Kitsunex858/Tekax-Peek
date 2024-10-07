<?php
/*
Plugin Name: WooCommerce Adoption Plugin
Description: A custom plugin to manage dog adoptions using WooCommerce.
Version: 1.1
Author: Kitsunex858
*/

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    // Registrar tipo de producto personalizado "dog"
    add_action( 'init', 'register_dog_product_type' );
    function register_dog_product_type() {
        class WC_Product_Dog extends WC_Product {
            public function __construct( $product ) {
                $this->product_type = 'dog';
                parent::__construct( $product );
            }
        }
    }

    // Añadir el tipo de producto "dog" al selector de tipos de producto
    add_filter( 'product_type_selector', 'add_dog_product_type' );
    function add_dog_product_type( $types ){
        $types[ 'dog' ] = __( 'Perro' );
        return $types;
    }
    
    // Ocultar otros tipos de productos con CSS
    add_action( 'admin_head', 'hide_other_product_types' );
    function hide_other_product_types() {
        echo '<style>
            #product-type option:not([value="dog"]) {
                display: none;
            }
        </style>';
    }

    // Añadir campos personalizados a la página de producto
    add_action( 'woocommerce_product_options_general_product_data', 'add_dog_custom_fields' );
    function add_dog_custom_fields() {
        echo '<div class="options_group">';

        woocommerce_wp_select(
            array(
                'id'          => '_dog_sex',
                'wrapper_class' => 'show_if_dog',
                'label'       => __('Sexo del perro', 'woocommerce' ),
                'options'     => array(
                    'male'         => __( 'Macho', 'woocommerce' ),
                    'female'       => __( 'Hembra', 'woocommerce' ),
                ),
                'desc_tip'    => 'true',
                'description' => __( 'Selecciona el sexo del perro.', 'woocommerce' )
            )
        );


        woocommerce_wp_text_input( 
            array( 
                'id'          => '_dog_age', 
                'wrapper_class' => 'show_if_dog', 
                'label'       => __( 'Edad del perro', 'woocommerce' ), 
                'placeholder' => '', 
                'desc_tip'    => 'true',
                'description' => __( 'Introduce la edad del perro.', 'woocommerce' ) 
            )
        );

        woocommerce_wp_text_input( 
            array( 
                'id'          => '_dog_breed', 
                'wrapper_class' => 'show_if_dog', 
                'label'       => __( 'Raza del perro', 'woocommerce' ), 
                'placeholder' => '', 
                'desc_tip'    => 'true',
                'description' => __( 'Introduce la raza del perro.', 'woocommerce' ) 
            )
        );

        woocommerce_wp_text_input( 
            array( 
                'id'          => '_dog_size', 
                'wrapper_class' => 'show_if_dog', 
                'label'       => __( 'Tamaño del perro', 'woocommerce' ), 
                'placeholder' => '', 
                'desc_tip'    => 'true',
                'description' => __( 'Introduce el tamaño del perro (Pequeño, Mediano, Grande).', 'woocommerce' ) 
            )
        );

        woocommerce_wp_text_input( 
            array( 
                'id'          => '_dog_color', 
                'wrapper_class' => 'show_if_dog', 
                'label'       => __( 'Color del perro', 'woocommerce' ), 
                'placeholder' => '', 
                'desc_tip'    => 'true',
                'description' => __( 'Introduce el color del perro.', 'woocommerce' ) 
            )
        );

        woocommerce_wp_textarea_input( 
            array( 
                'id'          => '_dog_behavior', 
                'wrapper_class' => 'show_if_dog', 
                'label'       => __( 'Comportamiento del perro', 'woocommerce' ), 
                'placeholder' => '', 
                'desc_tip'    => 'true',
                'description' => __( 'Describe el comportamiento del perro.', 'woocommerce' ) 
            )
        );

        woocommerce_wp_textarea_input( 
            array( 
                'id'          => '_dog_health', 
                'wrapper_class' => 'show_if_dog', 
                'label'       => __( 'Estado de salud del perro', 'woocommerce' ), 
                'placeholder' => '', 
                'desc_tip'    => 'true',
                'description' => __( 'Describe el estado de salud del perro.', 'woocommerce' ) 
            )
        );

        woocommerce_wp_checkbox( 
            array( 
                'id'            => '_dog_adopted', 
                'wrapper_class' => 'show_if_dog', 
                'label'         => __( 'Adoptado', 'woocommerce' ), 
                'description'   => __( 'Marca si el perro ya fue adoptado.', 'woocommerce' ) 
            )
        );

        echo '</div>';
    }

    // Guardar los campos personalizados al guardar el producto
    add_action( 'woocommerce_process_product_meta', 'save_dog_custom_fields' );
    function save_dog_custom_fields( $post_id ) {
        $fields = [
            '_dog_sex',
            '_dog_age', 
            '_dog_breed', 
            '_dog_size', 
            '_dog_color', 
            '_dog_behavior', 
            '_dog_health',
            '_dog_adopted'
        ];
        
        foreach ($fields as $field) {
            $value = $_POST[$field];
            if( !empty( $value ) ) {
                update_post_meta( $post_id, $field, esc_attr( $value ) );
            } else {
                delete_post_meta( $post_id, $field );
            }
        }
    }

    // Mostrar los campos personalizados en la página del producto
    add_action( 'woocommerce_single_product_summary', 'display_dog_custom_fields', 25 );
    function display_dog_custom_fields() {
        global $post;

        $fields = [
            '_dog_sex'       => __( 'Sexo', 'woocommerce' ),
            '_dog_age'       => __( 'Edad', 'woocommerce' ),
            '_dog_breed'     => __( 'Raza', 'woocommerce' ),
            '_dog_size'      => __( 'Tamaño', 'woocommerce' ),
            '_dog_color'     => __( 'Color', 'woocommerce' ),
            '_dog_behavior'  => __( 'Comportamiento', 'woocommerce' ),
            '_dog_health'    => __( 'Estado de salud', 'woocommerce' ),
        ];
        
        foreach ($fields as $field => $label) {
            $value = get_post_meta( $post->ID, $field, true );
            if( !empty( $value ) ) {
                echo '<p>' . $label . ': ' . esc_html( $value ) . '</p>';
            }
        }
    }

    // Mostrar el formulario de adopción en la página del producto
    // add_action( 'woocommerce_single_product_summary', 'display_adoption_form', 35 );
    function display_adoption_form() {
        global $post;
        $dog_name = get_the_title($post->ID);
        echo do_shortcode('[contact-form-7 id="9004309" title="Formulario de Adopción" dog-name="' . $dog_name . '"]');
    }

    // Mostrar label "Adoptado" en la imagen del producto si el perro ha sido adoptado
    add_filter( 'woocommerce_product_thumbnails', 'show_adopted_label' );
    // add_filter( 'woocommerce_before_single_product_summary', 'show_adopted_label' );
    function show_adopted_label() {
        global $product;
        $is_adopted = get_post_meta( $product->get_id(), '_dog_adopted', true );

        if ( $is_adopted ) {
            echo '<div class="adopted-label" style="position: absolute; top: 10px; left: 10px; background: #255384; color: white; padding: 5px 10px; z-index: 1000;">' . __( 'Adoptado! :D', 'woocommerce' ) . '</div>';
        }
    }
}

