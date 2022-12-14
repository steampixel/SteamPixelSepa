# SEPA Plugin for Shopware 6
This plugin provides a simple way to collect SEPA payment information. It will inject SEPA form fields into the chekout process and into the customer backend section. It will store the SEPA information to each order and will also store the information directly to the customer account. The user can change the information inside the user section. Please note the license conditions in the license.md

## Requirements and installation

2. Upload the plugin files to your Webspace and place it inside the custom/plugins/SteamPixelSepa folder
3. Enable the plugin in the shop backend

4. Create the following custom fileds for the order entity:
* custom_order_sepa_owner       Text
* custom_order_sepa_iban        Text
* custom_order_sepa_bic         Text

5. Create the following custom fileds for the customer entity:
* custom_customer_sepa_owner    Text
* custom_customer_sepa_iban     Text
* custom_customer_sepa_bic      Text

6. Alter the variable payment.id in line 66 in the file custom/plugins/SteamPixelSepa/src/Resources/views/storefront/component/payment/payment-fields.html.twig. This will enable the custom fields for this payment method in the frontend.

7. Clear your shop caches

## Development / Todo
* Add backend configuration for the payment.id
* Auto create / remove the custom fields
