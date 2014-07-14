<?php
// Heading
$_['heading_title']                 = 'OPAY';
$_['text_opay']                     = '<a onclick="window.open(\'http://www.opay.lt\');"><img src="view/image/payment/opay.png" alt="OPAY.LT" title="OPAY.LT" style="border: 1px solid #EEEEEE;" /></a>';

// Text
$_['text_payment']                  = 'Mokėjimas';
$_['text_success']                  = 'Sėkmingai išsaugota!';

// Entry
$_['entry_status']                  = 'Būsena:'; // nežinau kaip Opencarte verčiasi standartiškai
$_['entry_website']                 = 'Tinklalapio ID (website_id):';
$_['entry_finished_order_status']   = 'Užsakymo būsena po mokėjimo patvirtinimo:';
$_['entry_new_order_status']        = 'Užsakymo būsena po užsakymo sukūrimo:';
$_['entry_user_id']                 = 'Vartotojo ID (user_id):<br /><span class="help">Reikalingas kai įjungtas Testavimo rėžimas</span>';
$_['entry_test_mode']               = 'Testavimo rėžimas:';
$_['entry_geo_zone']                = 'Geografinė zona:';  // nežinau kaip Opencarte vadinasi standartiškai
$_['entry_password_signature']      = 'Pasirašymo slaptažodis:';
$_['entry_rsa_signature']           = 'Privatus raktas:';
$_['entry_certificate']             = 'OPAY sertifikatas:';
$_['entry_canceled_order_status']   = 'Užsakymo būsena po nepavykusio apmokėjimo:';
$_['entry_show_channels']           = 'Rodyti mokėjimo būdus:';
$_['entry_sort_order']              = 'Rikiavimo tvarka:'; // nežinau kaip Opencarte vadinasi standartiškai

// Error
$_['error_permission']              = 'Klaida: Neturite teisių keisti OPAY mokėjimo nustatymų.';
$_['error_website']                 = 'Tinklalapio ID yra privalomas!';
$_['error_empty_signing']           = 'Prašome įvesti Pasirašymo slaptažodį arba Privatų raktą ir OPAY sertifikatą kartu.';
$_['error_empty_rsa_signature']     = 'Prašome įvesti Privatų raktą';
$_['error_empty_certificate']       = 'Prašome įvesti OPAY sertifikatą.';
$_['error_empty_user_id']           = 'Kai testavimo rėžimas yra įjungtas, Vartotojo ID privalo būti įvestas.';