<?php

// popup Add Customer
add_action('wp_ajax_my_get_countries', function () {
    if (!current_user_can('manage_woocommerce')) {
        wp_send_json_error('No permission');
    }
    $countries_obj = new WC_Countries();
    $countries = $countries_obj->get_countries();
    wp_send_json_success($countries);
});

function my_add_customer_popup_button()
{
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'shop_order')
        return;
    ?>
    <style>
        #my-add-customer-popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        #my-add-customer-popup .popup-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 350px;
            max-width: 90%;
        }

        #my-add-customer-popup input,
        #my-add-customer-popup select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
    </style>

    <div id="my-add-customer-popup">
        <div class="popup-content">
            <h2>Add New Customer</h2>
            <input type="text" id="new_customer_firstname" placeholder="First Name">
            <input type="text" id="new_customer_lastname" placeholder="Last Name">
            <input type="email" id="new_customer_email" placeholder="Email">
            <!-- Country Select -->
            <select id="new_customer_country">
                <option value="">Loading countries...</option>
            </select>
            <div style="text-align:right;">
                <button class="button" id="close_customer_popup">Cancel</button>
                <button class="button button-primary" id="save_customer_btn">Save</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function insertAddCustomerBtn() {
                const label = document.querySelector('label[for="customer_user"]');
                if (!window.location.pathname.endsWith('/wp-admin/admin.php') ||
                    window.location.search.indexOf('page=wc-orders') === -1 ||
                    window.location.search.indexOf('action=new') === -1) {
                    return false;
                }
                if (label && !document.querySelector('#my-add-customer-btn')) {
                    const btn = document.createElement("button");
                    label.style.position = "relative";
                    btn.id = "my-add-customer-btn";
                    btn.type = "button";
                    btn.className = "button-link";
                    btn.style.position = "absolute";
                    btn.style.right = "0";
                    btn.textContent = "Add Customer";
                    btn.addEventListener("click", function () {
                        document.getElementById('my-add-customer-popup').style.display = 'flex';
                        loadCountries();
                    });
                    label.appendChild(btn);
                    return true;
                }
                return false;
            }

            if (!insertAddCustomerBtn()) {
                const wait = setInterval(function () {
                    if (insertAddCustomerBtn()) clearInterval(wait);
                }, 300);
            }

            // Close popup
            document.getElementById('close_customer_popup').onclick = function () {
                document.getElementById('my-add-customer-popup').style.display = 'none';
            };

            // Load countries from API (example using restcountries.com)
            function loadCountries() {
                const select = document.getElementById('new_customer_country');
                select.innerHTML = '<option value="">Loading...</option>';

                fetch(ajaxurl + '?action=my_get_countries')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            select.innerHTML = '<option value="">Select Country</option>';
                            Object.entries(data.data)
                                .sort((a, b) => a[1].localeCompare(b[1]))
                                .forEach(([code, name]) => {
                                    const opt = document.createElement('option');
                                    opt.value = code; // Mã quốc gia theo chuẩn WC
                                    opt.textContent = name;
                                    select.appendChild(opt);
                                });
                        } else {
                            select.innerHTML = '<option value="">Error loading countries</option>';
                            console.error(data.data || 'Unknown error');
                        }
                    })
                    .catch(err => {
                        select.innerHTML = '<option value="">Error loading countries</option>';
                        console.error(err);
                    });
            }


            // Save customer
            document.getElementById('save_customer_btn').onclick = function () {
                const email = document.getElementById('new_customer_email').value.trim();
                const firstname = document.getElementById('new_customer_firstname').value.trim();
                const lastname = document.getElementById('new_customer_lastname').value.trim();
                const country = document.getElementById('new_customer_country').value.trim();
                if (!firstname) {
                    alert('Please enter First Name');
                    return;
                }
                // Validate: Last Name
                if (!lastname) {
                    alert('Please enter Last Name');
                    return;
                }
                // Validate: Email
                if (!email) {
                    alert('Please enter Email');
                    return;
                }
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    alert('Invalid email format');
                    return;
                }
                // Validate: Country
                if (!country) {
                    alert('Please select a country');
                    return;
                }

                fetch(ajaxurl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'my_create_customer',
                        email: email,
                        firstname: firstname,
                        lastname: lastname,
                        country: country,
                        _wpnonce: '<?php echo wp_create_nonce("my_add_customer_nonce"); ?>'
                    })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert('Customer created!');
                            document.getElementById('my-add-customer-popup').style.display = 'none';
                            const select = jQuery('#customer_user');
                            if (select.find("option[value='" + data.data.id + "']").length === 0) {
                                select.append(new Option(data.data.name, data.data.id, true, true));
                            }
                            select.val(data.data.id).trigger('change');
                        } else {
                            alert('Error: ' + data.data);
                        }
                    })
                    .catch(err => alert('Error: ' + err));
            };
        });
    </script>
    <?php
}
add_action('admin_footer', 'my_add_customer_popup_button');

function my_ajax_create_customer()
{
    check_ajax_referer('my_add_customer_nonce');
    $email = sanitize_email($_POST['email']);
    // $username = sanitize_user($_POST['username']);
    $firstname = sanitize_text_field($_POST['firstname']);
    $lastname = sanitize_text_field($_POST['lastname']);
    $country = sanitize_text_field($_POST['country']);

    if (email_exists($email)) {
        wp_send_json_error('Username or email already exists');
    }

    $username = sanitize_user(current(explode('@', $email)));
    $password = wp_generate_password(12, false);
    $user_id = wp_create_user($username, $password, $email);


    if (is_wp_error($user_id)) {
        wp_send_json_error($user_id->get_error_message());
    }

    wp_update_user([
        'ID' => $user_id,
        'display_name' => $firstname . ' ' . $lastname,
        'first_name' => $firstname,
        'last_name' => $lastname,
        'email' => $email,
        'role' => 'customer'
    ]);

    update_user_meta($user_id, 'billing_country', $country);
    update_user_meta($user_id, 'shipping_country', $country);

    wp_send_json_success([
        'id' => $user_id,
        'name' => $firstname . ' ' . $lastname,
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'country' => $country
    ]);
}
add_action('wp_ajax_my_create_customer', 'my_ajax_create_customer');
