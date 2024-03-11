<?php 

/*
Plugin Name: Report Contact List
Description: Display Report contact list in the admin area.
Version: 1.0
Author: leaselogiq
*/

// Include the WP_List_Table class
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

// Custom class that extends WP_List_Table
class Contact_List_Table extends WP_List_Table {
    // Define columns
    function get_columns() {
        $columns = array(
            'serial_number' => 'S.No.',
            'email'         => 'Email',
            'created_at'    => 'Created At',
        );

        return $columns;
    }

    // Prepare items for the table
    function prepare_items() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'sample_reports';

        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
        ));

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $data = $wpdb->get_results($wpdb->prepare("SELECT id, email, created_at FROM $table_name ORDER BY id DESC LIMIT %d OFFSET %d", $per_page, ($current_page - 1) * $per_page), ARRAY_A);

        $serial_number = ($current_page - 1) * $per_page + 1;

        foreach ($data as &$item) {
            $item['serial_number'] = $serial_number++;
        }

        $this->items = $data;
    }

    // Define sortable columns
    function get_sortable_columns() {
        return array(
            'serial_number' => array('serial_number', false),
            'email'         => array('email', false),
            'created_at'    => array('created_at', false),
        );
    }

    // Display each column
    function column_default($item, $column_name) {
        return esc_html($item[$column_name]);
    }
}

// Function to display the contact list page
function display_contact_list_page() {
    $contact_list_table = new Contact_List_Table();
    $contact_list_table->prepare_items();

    echo '<div class="wrap">';
    echo '<h2>Report List</h2>';
    echo '<form method="post">';
    $contact_list_table->display();
    echo '</form>';
    echo '</div>';
}

// Hook the function to an admin menu page
add_action('admin_menu', 'add_contact_list_menu');

function add_contact_list_menu() {
        add_menu_page('Report List',
                    'Report List',
                    'manage_options',
                    'report-list', 
                    'display_contact_list_page',
                    'dashicons-list-view'
                    ); }


?>