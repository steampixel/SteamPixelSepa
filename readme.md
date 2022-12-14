# SEPA Plugin for Shopware 6
This plugin provides a simple way to collect SEPA payment information. It will inject SEPA form fields into the chekout process and into the customer backend section. It will store the SEPA information to each order and will also store the information directly to the customer account. The user can change the information inside the user section. This plugin comes with the AGPLv3 license.

## Requirements and installation

1. Upload the plugin files to your Webspace and place it inside the custom/plugins/SteamPixelSepa folder
2. Enable the plugin in the shop backend

3. Create the following custom fileds for the order entity:
* custom_order_sepa_owner       Text
* custom_order_sepa_iban        Text
* custom_order_sepa_bic         Text

4. Create the following custom fileds for the customer entity:
* custom_customer_sepa_owner    Text
* custom_customer_sepa_iban     Text
* custom_customer_sepa_bic      Text

5. Alter the variable payment.id in line 66 in the file custom/plugins/SteamPixelSepa/src/Resources/views/storefront/component/payment/payment-fields.html.twig. This will enable the custom fields for this payment method in the frontend.

6. Clear your shop caches

## Development / Todo
* Add backend configuration for the payment.id
* Auto create / remove the custom fields

## Warning and Disclaimer
SEPA information is extremely sensitive data. It is your responsibility to ensure that the software and hardware used (server, operating system, shop software, etc...) meets the latest applicable security requirements. The SEPA data is stored unencrypted inside your database. You should delete this data regularly. The manufacturer of the plugin is not responsible for damage (e.g. data theft) caused by exploiting security vulnerabilities of the shop, the server, the operating system or inside this plugin.

