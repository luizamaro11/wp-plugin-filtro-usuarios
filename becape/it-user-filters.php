<?php
/**
 * Plugin Name: Integração Trade - Filtros Extras para Lista de Usuários
 * Version: 1.0.1
 * Author: Glauber Portella <glauberportella@gmail.com>
 * License: Proprietary
 * Description: Adiciona filtros extras na lista de usuários
 * Text Domain: it-user-filters
 */

function add_state_filter( $which ) {
    $selected = $_GET['state_'.$which] ?? '';
    $options = [
        'AC' => 'Acre',
        'AL' => 'Alagoas',
        'AP' => 'Amapá',
        'AM' => 'Amazonas',
        'BA' => 'Bahia',
        'CE' => 'Ceará',
        'DF' => 'Distrito Federal',
        'ES' => 'Espirito Santo',
        'GO' => 'Goiás',
        'MA' => 'Maranhão',
        'MS' => 'Mato Grosso do Sul',
        'MT' => 'Mato Grosso',
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

    echo ' <select name="state_'.$which.'" style="float:none;"><option value="">-- Estados --</option>';
    foreach ( $options as $value => $title ) {
        $selected_attr = $selected == $value ? 'selected' : '';
        echo '<option value="' . $value . '" '.$selected_attr.'>' . $title . '</option>';
    }
    echo '</select>';
    submit_button(__( 'Filter' ), null, $which, false);
}
add_action( 'restrict_manage_users', 'add_state_filter' );

function add_whatsapp_section_filter( $which ) {
    $selected = $_GET['whatsapp_section_'.$which] ?? '';
    $options = [
        'optin_whatsapp_chat' => 'Grupo de Bate papo Whatsapp',
        'optin_whatsapp_daily' => 'Postagem Diária (Grupo Whatsapp sem bate papo)',
        'optin_telegram' => 'Telegram (grupo sem bate papo)',
    ];

    echo ' <select name="whatsapp_section_'.$which.'" style="float:none;"><option value="">-- Aceites Whatsapp --</option>';
    foreach ( $options as $value => $title ) {
        $selected_attr = $selected == $value ? 'selected' : '';
        echo '<option value="' . $value . '" '.$selected_attr.'>' . $title . '</option>';
    }
    echo '</select>';
    submit_button(__( 'Filter' ), null, $which, false);
}
add_action( 'restrict_manage_users', 'add_whatsapp_section_filter' );

function add_partner_section_filter( $which ) {
    $selected = $_GET['partner_section_'.$which] ?? '';
    $options = [
        ['value' => 'optin_partners_sim', 'title' => 'Autorização concedida'],
        ['value' => 'optin_partners_nao', 'title' => 'Não concedida']
    ];

    echo ' <select name="partner_section_'.$which.'" style="float:none;"><option value="">-- Parceiros IT --</option>';
    foreach ( $options as $index => $option ) {
        $selected_attr = $selected == $option['value'] ? 'selected' : '';
        echo '<option value="' . $option['value'] . '" '.$selected_attr.'>' . $option['title'] . '</option>';
    }
    echo '</select>';
    submit_button(__( 'Filter' ), null, $which, false);
}
add_action( 'restrict_manage_users', 'add_partner_section_filter' );

function filter_users_by_whatsapp_section( $query ) {
    global $pagenow;

    if ( is_admin() && 'users.php' == $pagenow) {
        // figure out which button was clicked. The $which in filter action functions
        $top = $_GET['whatsapp_section_top'] ? $_GET['whatsapp_section_top'] : null;
        $bottom = $_GET['whatsapp_section_bottom'] ? $_GET['whatsapp_section_bottom'] : null;
        if (!empty($top) OR !empty($bottom)) {
            $section = !empty($top) ? $top : $bottom;
            $meta_query = array(
                array(
                    'key' => $section,
                    'value' => 1
                )
            );
            $query->set( 'meta_query', $meta_query );
        }
    }
}
add_filter( 'pre_get_users', 'filter_users_by_whatsapp_section' );

function filter_users_by_partner_section( $query ) {
    global $pagenow;

    if ( is_admin() && 'users.php' == $pagenow) {
        // figure out which button was clicked. The $which in filter action functions
        $top = $_GET['partner_section_top'] ? $_GET['partner_section_top'] : null;
        $bottom = $_GET['partner_section_bottom'] ? $_GET['partner_section_bottom'] : null;
        if (!empty($top) OR !empty($bottom)) {
            $section = !empty($top) ? $top : $bottom;
            $meta_query = array(
                array(
                    'key' => 'optin_partners',
                    'value' => $section == 'optin_partners_sim' ? 1 : 0,
                )
            );
            $query->set( 'meta_query', $meta_query );
        }
    }
}
add_filter( 'pre_get_users', 'filter_users_by_partner_section' );

function filter_users_by_state( $query ) {
    global $pagenow;

    $meta_values = [
        'AC' => ['AC', 'Acre'],
        'AL' => ['AL', 'Alagoas'],
        'AP' => ['AP', 'Amapá'],
        'AM' => ['AM', 'Amazonas'],
        'BA' => ['BA', 'Bahia'],
        'CE' => ['CE', 'Ceará'],
        'DF' => ['DF', 'Distrito Federal'],
        'ES' => ['ES', 'Espirito Santo'],
        'GO' => ['GO', 'Goiás'],
        'MA' => ['MA', 'Maranhão'],
        'MS' => ['MS', 'Mato Grosso do Sul'],
        'MT' => ['MT', 'Mato Grosso'],
        'MG' => ['MG', 'Minas Gerais'],
        'PA' => ['PA', 'Pará'],
        'PB' => ['PB', 'Paraíba'],
        'PR' => ['PR', 'Paraná'],
        'PE' => ['PE', 'Pernambuco'],
        'PI' => ['PI', 'Piauí'],
        'RJ' => ['RJ', 'Rio de Janeiro'],
        'RN' => ['RN', 'Rio Grande do Norte'],
        'RS' => ['RS', 'Rio Grande do Sul'],
        'RO' => ['RO', 'Rondônia'],
        'RR' => ['RR', 'Roraima'],
        'SC' => ['SC', 'Santa Catarina'],
        'SP' => ['SP', 'São Paulo'],
        'SE' => ['SE', 'Sergipe'],
        'TO' => ['TO', 'Tocantins'],
    ];

    if ( is_admin() && 'users.php' == $pagenow) {
        // figure out which button was clicked. The $which in filter action functions
        $top = $_GET['state_top'] ? $_GET['state_top'] : null;
        $bottom = $_GET['state_bottom'] ? $_GET['state_bottom'] : null;
        if (!empty($top) OR !empty($bottom)) {
            $state = !empty($top) ? $top : $bottom;
            $values = $meta_values[$state] ?? null;
            if ($values) {
                $meta_query = array(
                    array(
                        'compare' => 'IN',
                        'key' => 'billing_state',
                        'value' => $values
                    )
                );
                $query->set( 'meta_query', $meta_query );
            }
        }
    }
}
add_filter( 'pre_get_users', 'filter_users_by_state' );

function it_manage_user_sortable_columns ($columns) {
    $columns['user_registered'] = 'Registration date';
    return $columns;
}
add_filter( 'manage_users_sortable_columns', 'it_manage_user_sortable_columns' );