// Добавляем поле сортировки в форму категории
add_action('product_cat_add_form_fields', 'add_sorting_option_to_category', 10, 2);
add_action('product_cat_edit_form_fields', 'edit_sorting_option_in_category', 10, 2);

function add_sorting_option_to_category() {
    ?>
    <div class="form-field">
        <label for="category_sort_order"><?php _e('Сортировка товаров по умолчанию', 'woocommerce'); ?></label>
        <select name="category_sort_order" id="category_sort_order">
            <option value="menu_order"><?php _e('По умолчанию', 'woocommerce'); ?></option>
            <option value="popularity"><?php _e('По популярности', 'woocommerce'); ?></option>
            <option value="rating"><?php _e('По рейтингу', 'woocommerce'); ?></option>
            <option value="date"><?php _e('По новизне', 'woocommerce'); ?></option>
            <option value="price"><?php _e('По цене: по возрастанию', 'woocommerce'); ?></option>
            <option value="price-desc"><?php _e('По цене: по убыванию', 'woocommerce'); ?></option>
        </select>
    </div>
    <?php
}

function edit_sorting_option_in_category($term, $taxonomy) {
    $category_sort_order = get_term_meta($term->term_id, 'category_sort_order', true);
    ?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="category_sort_order"><?php _e('Сортировка товаров по умолчанию', 'woocommerce'); ?></label>
        </th>
        <td>
            <select name="category_sort_order" id="category_sort_order">
                <option value="menu_order" <?php selected($category_sort_order, 'menu_order'); ?>><?php _e('По умолчанию', 'woocommerce'); ?></option>
                <option value="popularity" <?php selected($category_sort_order, 'popularity'); ?>><?php _e('По популярности', 'woocommerce'); ?></option>
                <option value="rating" <?php selected($category_sort_order, 'rating'); ?>><?php _e('По рейтингу', 'woocommerce'); ?></option>
                <option value="date" <?php selected($category_sort_order, 'date'); ?>><?php _e('По новизне', 'woocommerce'); ?></option>
                <option value="price" <?php selected($category_sort_order, 'price'); ?>><?php _e('По цене: по возрастанию', 'woocommerce'); ?></option>
                <option value="price-desc" <?php selected($category_sort_order, 'price-desc'); ?>><?php _e('По цене: по убыванию', 'woocommerce'); ?></option>
            </select>
        </td>
    </tr>
    <?php
}

// Сохраняем выбранную сортировку для категории
add_action('created_product_cat', 'save_sorting_option_for_category', 10, 2);
add_action('edited_product_cat', 'save_sorting_option_for_category', 10, 2);

function save_sorting_option_for_category($term_id) {
    if (isset($_POST['category_sort_order'])) {
        update_term_meta($term_id, 'category_sort_order', sanitize_text_field($_POST['category_sort_order']));
    }
}

// Изменяем сортировку товаров в зависимости от категории
add_filter('woocommerce_get_catalog_ordering_args', 'apply_category_sorting', 10, 2);

function apply_category_sorting($args, $query) {
    if (is_product_category()) {
        // Получаем текущую категорию
        $current_term = get_queried_object();

        // Получаем выбранную сортировку для категории
        $category_sort_order = get_term_meta($current_term->term_id, 'category_sort_order', true);

        if (!empty($category_sort_order)) {
            switch ($category_sort_order) {
                case 'popularity':
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'desc';
                    $args['meta_key'] = 'total_sales';
                    break;
                case 'rating':
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'desc';
                    $args['meta_key'] = '_wc_average_rating';
                    break;
                case 'date':
                    $args['orderby'] = 'date';
                    $args['order'] = 'desc';
                    break;
                case 'price':
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'asc';
                    $args['meta_key'] = '_price';
                    break;
                case 'price-desc':
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'desc';
                    $args['meta_key'] = '_price';
                    break;
                default:
                    $args['orderby'] = 'menu_order';
                    $args['order'] = 'asc';
                    break;
            }
        }
    }

    return $args;
}
