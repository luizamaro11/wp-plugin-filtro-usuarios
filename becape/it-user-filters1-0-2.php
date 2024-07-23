<?php
/**
 * Plugin Name: Integração Trade - Filtros Extras para Lista de Usuários
 * Version: 1.0.2
 * Author: danilocarlos.eth
 * License: Proprietary
 * Description: Adiciona filtros extras na lista de usuários
 * Text Domain: it-user-filters
 */

function add_state_filter( $which ) {
    $selected = isset($_GET['state_'.$which]) ? $_GET['state_'.$which] : '';
    $options = [
        'AC' => 'Acre',
        'AL' => 'Alagoas',
        'AP' => 'Amapá',
        'AM' => 'Amazonas',
        'BA' => 'Bahia',
        'CE' => 'Ceará',
        'DF' => 'Distrito Federal',
        'ES' => 'Espírito Santo',
        'GO' => 'Goiás',
        'MA' => 'Maranhão',
        'MT' => 'Mato Grosso',
        'MS' => 'Mato Grosso do Sul',
        'MG' => 'Minas Gerais',
        'PA' => 'Pará',
        'PB' => 'Paraíba',
        'PR' => 'Paraná',
        'PE' => 'Pernambuco',
        'PI' => 'Piauí',
        'RJ' => 'Rio de Janeiro',
        'RN' => 'Rio Grande do Norte',
        'RS' => 'Rio Grande do Sul',
        'RO' => 'Rondônia',
        'RR' => 'Roraima',
        'SC' => 'Santa Catarina',
        'SP' => 'São Paulo',
        'SE' => 'Sergipe',
        'TO' => 'Tocantins',
    ];

    echo '<select name="state_'.$which.'" style="float:none;"><option value="">-- Estados --</option>';
    foreach ( $options as $value => $title ) {
        $selected_attr = $selected === $value ? 'selected' : '';
        echo '<option value="' . esc_attr($value) . '" ' . $selected_attr . '>' . esc_html($title) . '</option>';
    }
    echo '</select>';
    submit_button(__('Filter'), null, $which, false);
}
add_action('restrict_manage_users', 'add_state_filter');

function add_whatsapp_section_filter( $which ) {
    $selected = isset($_GET['whatsapp_section_'.$which]) ? $_GET['whatsapp_section_'.$which] : '';
    $options = [
        'optin_whatsapp_chat' => 'Grupo de Bate papo Whatsapp',
        'optin_whatsapp_daily' => 'Postagem Diária (Grupo Whatsapp sem bate papo)',
        'optin_telegram' => 'Telegram (grupo sem bate papo)',
    ];

    echo '<select name="whatsapp_section_'.$which.'" style="float:none;"><option value="">-- Aceites Whatsapp --</option>';
    foreach ( $options as $value => $title ) {
        $selected_attr = $selected === $value ? 'selected' : '';
        echo '<option value="' . esc_attr($value) . '" ' . $selected_attr . '>' . esc_html($title) . '</option>';
    }
    echo '</select>';
    submit_button(__('Filter'), null, $which, false);
}
add_action('restrict_manage_users', 'add_whatsapp_section_filter');

function add_partner_section_filter( $which ) {
    $selected = isset($_GET['partner_section_'.$which]) ? $_GET['partner_section_'.$which] : '';
    $options = [
        ['value' => 'optin_partners_sim', 'title' => 'Autorização concedida'],
        ['value' => 'optin_partners_nao', 'title' => 'Não concedida']
    ];

    echo '<select name="partner_section_'.$which.'" style="float:none;"><option value="">-- Parceiros IT --</option>';
    foreach ( $options as $option ) {
        $selected_attr = $selected === $option['value'] ? 'selected' : '';
        echo '<option value="' . esc_attr($option['value']) . '" ' . $selected_attr . '>' . esc_html($option['title']) . '</option>';
    }
    echo '</select>';
    submit_button(__('Filter'), null, $which, false);
}
add_action('restrict_manage_users', 'add_partner_section_filter');

function add_registration_date_filter() {
    $selected = isset($_GET['registration_date_filter']) ? $_GET['registration_date_filter'] : '';
    $options = [
        'today' => 'Registrados hoje',
        '7days' => 'Registrados nos últimos 7 dias',
        '15days' => 'Registrados nos últimos 15 dias',
        '30days' => 'Registrados nos últimos 30 dias',
    ];

    echo '<select name="registration_date_filter" style="float:none;"><option value="">-- Data de Registro --</option>';
    foreach ( $options as $value => $title ) {
        $selected_attr = $selected === $value ? 'selected' : '';
        echo '<option value="' . esc_attr($value) . '" ' . $selected_attr . '>' . esc_html($title) . '</option>';
    }
    echo '</select>';
    submit_button(__('Filter'), null, 'registration_date_filter', false);
}
add_action('restrict_manage_users', 'add_registration_date_filter');

function filter_users_by_whatsapp_section( $query ) {
    global $pagenow;

    if ( is_admin() && $pagenow === 'users.php') {
        $top = isset($_GET['whatsapp_section_top']) ? $_GET['whatsapp_section_top'] : null;
        $bottom = isset($_GET['whatsapp_section_bottom']) ? $_GET['whatsapp_section_bottom'] : null;
        $section = !empty($top) ? $top : $bottom;

        if (!empty($section)) {
            $meta_query = [
                [
                    'key' => $section,
                    'value' => 1
                ]
            ];
            $query->set('meta_query', $meta_query);
        }
    }
}
add_filter('pre_get_users', 'filter_users_by_whatsapp_section');

function filter_users_by_partner_section( $query ) {
    global $pagenow;

    if ( is_admin() && $pagenow === 'users.php') {
        $top = isset($_GET['partner_section_top']) ? $_GET['partner_section_top'] : null;
        $bottom = isset($_GET['partner_section_bottom']) ? $_GET['partner_section_bottom'] : null;
        $section = !empty($top) ? $top : $bottom;

        if (!empty($section)) {
            $meta_query = [
                [
                    'key' => $section,
                    'value' => 1
                ]
            ];
            $query->set('meta_query', $meta_query);
        }
    }
}
add_filter('pre_get_users', 'filter_users_by_partner_section');

function filter_users_by_registration_date( $query ) {
    global $pagenow;

    if ( is_admin() && $pagenow === 'users.php' && isset($_GET['registration_date_filter']) && !empty($_GET['registration_date_filter'])) {
        $selected = $_GET['registration_date_filter'];
        $today = date('Y-m-d');

        switch ($selected) {
            case 'today':
                $date_query = [
                    [
                        'year'  => date('Y'),
                        'month' => date('m'),
                        'day'   => date('d'),
                    ]
                ];
                break;
            case '7days':
                $date_query = [
                    [
                        'after'     => date('Y-m-d', strtotime('-7 days')),
                        'inclusive' => true
                    ]
                ];
                break;
            case '15days':
                $date_query = [
                    [
                        'after'     => date('Y-m-d', strtotime('-15 days')),
                        'inclusive' => true
                    ]
                ];
                break;
            case '30days':
                $date_query = [
                    [
                        'after'     => date('Y-m-d', strtotime('-30 days')),
                        'inclusive' => true
                    ]
                ];
                break;
        }

        $query->set('date_query', $date_query);
        $query->set('orderby', 'user_registered'); // Ordenar pelo campo 'user_registered'
        $query->set('order', 'DESC'); // Ordenar de forma descendente

        // Opção para debugar a query SQL final
        add_action('pre_get_users', function($query) {
            error_log($query->request);
        });
    }
}
add_filter('pre_get_users', 'filter_users_by_registration_date');


function it_manage_user_sortable_columns ($columns) {
    $columns['user_registered'] = 'Registration date';
    return $columns;
}
add_filter( 'manage_users_sortable_columns', 'it_manage_user_sortable_columns' );
