<?php 

// This file creates an order, then creates an invoice for that order, and then finally publishes the invoice. 

// PART 1
// package the order object

// WARNING: Square support may need to change these values. 
$squaretoken = 'OUR_LIVE_SQUARE_API_KEY';

// NOTE: no need to edit the variables below, they come from our account and will help you test. 
$squareItemVariationID = '6HURMXXP6APJOKRTHYUMTUUR';
$squareLocationID = 'DT3C4BTJZRCS3';
$squareCustomer = 'ZQX9496J3WXAB1J5V16KJW05P8'; // this is my customer account (the developer). it's ok to use this id. 
$squareTaxId = 'X5JABJKURZTJ3NQEFNZCQHSQ';
$squareServiceChargeId = 'UME2W7XPCRAIXEMEAQKKB33M';

//Date manipulation 
$today = date('Y-m-d'); // Get today's date
$due_date = date('Y-m-d', strtotime($today . ' +2 days'));


// Generate a random idempotency key
$idempotency_key = uniqid('', true);
// Remove the decimal point and any non-alphanumeric characters
$idempotency_key = str_replace('.', '', $idempotency_key);
$idempotency_key = preg_replace('/[^a-zA-Z0-9]/', '', $idempotency_key);
// Format the idempotency key as a string in the desired format
$idempotency_key_string = sprintf(
'%s-%s-%s-%s-%s',
substr($idempotency_key, 0, 8),
substr($idempotency_key, 8, 4),
substr($idempotency_key, 12, 4),
substr($idempotency_key, 16, 4),
substr($idempotency_key, 20, 12)
);



                         $neworder = array(
                              "idempotency_key" => $idempotency_key_string,
                              "order" => array(
                                   "line_items" => array(
                                        array(
                                             "item_type" => "ITEM",
                                             "quantity" => "1",
                                             "catalog_object_id" => $squareItemVariationID,
                                             // "note" => "note here"
                                        )
                                   ),
                                   "location_id" => $squareLocationID,
                                   "customer_id" => $squareCustomer,
                                   "reference_id" => "idpap,6033922530,3609017923,525", //limit 40 characters
                                   "state" => "OPEN",
                                   "taxes" => array(
                                        array(
                                             "catalog_object_id" => $squareTaxId,
                                             "scope" => "ORDER"
                                        )
                                   ),
                                   "service_charges" => array(
                                        array(
                                             "catalog_object_id" => $squareServiceChargeId,
                                             "scope" => "ORDER"
                                        )
                                   )
                              )
                         );



                        // PART 2 
                        // Create order

                         $curl2 = curl_init();
                         curl_setopt_array($curl2, array(
                              CURLOPT_URL => 'https://connect.squareup.com/v2/orders',
                              CURLOPT_RETURNTRANSFER => true,
                              CURLOPT_ENCODING => '',
                              CURLOPT_MAXREDIRS => 10,
                              CURLOPT_TIMEOUT => 0,
                              CURLOPT_FOLLOWLOCATION => true,
                              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                              CURLOPT_CUSTOMREQUEST => 'POST',
                              CURLOPT_POSTFIELDS => json_encode($neworder),
                              CURLOPT_HTTPHEADER => array(
                              'Authorization: Bearer '.$squaretoken,
                              'Square-Version: 2023-04-19',
                              'Content-Type: application/json'
                              ),
                         ));
                         
                         // Execute the cURL request and handle the response
                         $response2 = curl_exec($curl2);
                         $jsonObject2 = json_decode($response2);
                         curl_close($curl2);
                         
                         // look for errors 
                         if($jsonObject2->errors[0]->code!="" && $jsonObject2->errors[0]->code!=null ){
                              return rest_ensure_response("Error: ". $jsonObject2->errors[0]->code . " ". $jsonObject2->errors[0]->field . " ". $jsonObject2->errors[0]->detail);
                         }


                         // PART 3 
                         // Prepare invoice for newly created order 

                         // see if a new order was returned...
                         $newOrderID = $jsonObject2->order->id;
                         if($newOrderID != "" && $newOrderID != null){

                              // Generate a random idempotency key
                              $idempotency_key = uniqid('', true);
                              // Remove the decimal point and any non-alphanumeric characters
                              $idempotency_key = str_replace('.', '', $idempotency_key);
                              $idempotency_key = preg_replace('/[^a-zA-Z0-9]/', '', $idempotency_key);
                              // Format the idempotency key as a string in the desired format
                              $idempotency_key_string = sprintf(
                              '%s-%s-%s-%s-%s',
                              substr($idempotency_key, 0, 8),
                              substr($idempotency_key, 8, 4),
                              substr($idempotency_key, 12, 4),
                              substr($idempotency_key, 16, 4),
                              substr($idempotency_key, 20, 12)
                              );

                               // create invoice for the order
                               $newInvoice = array(
                                   "invoice" => array(
                                       "delivery_method" => "SHARE_MANUALLY",
                                       "order_id" => $newOrderID,
                                       "primary_recipient" => array(
                                           "customer_id" => $squareCustomer
                                       ),
                                       "title" =>"Your Photos",
                                       "description" =>"Thank you for the opportunity to capture your memories.",
                                       "payment_requests" => array(
                                           array(
                                               "automatic_payment_source" => "NONE",
                                               "request_type" => "BALANCE",
                                               "tipping_enabled" => true,
                                               "due_date" => $due_date
                                           )
                                       ),
                                       "accepted_payment_methods" => array(
                                           "card" => true,
                                           "square_gift_card" => true
                                       )
                                   ),
                                   "idempotency_key" => $idempotency_key_string
                               );
                               
                             // Set up the cURL request
                              $curlInvoice = curl_init();
                              curl_setopt_array($curlInvoice, array(
                                   CURLOPT_URL => 'https://connect.squareup.com/v2/invoices',
                                   CURLOPT_RETURNTRANSFER => true,
                                   CURLOPT_ENCODING => '',
                                   CURLOPT_MAXREDIRS => 10,
                                   CURLOPT_TIMEOUT => 0,
                                   CURLOPT_FOLLOWLOCATION => true,
                                   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                   CURLOPT_CUSTOMREQUEST => 'POST',
                                   CURLOPT_POSTFIELDS => json_encode($newInvoice),
                                   CURLOPT_HTTPHEADER => array(
                                   'Authorization: Bearer '.$squaretoken,
                                   'Square-Version: 2023-04-19',
                                   'Content-Type: application/json'
                                   ),
                              ));


                              // Execute the cURL request and handle the response
                              $curlresponseInvoice = curl_exec($curlInvoice);
                              $jsonObjectInvoice = json_decode($curlresponseInvoice);
                              curl_close($curlInvoice);
                              
                              // see if a new order was returned
                              $newInvoiceID = $jsonObjectInvoice->invoice->id;
                              $newInvoiceVersion = $jsonObjectInvoice->invoice->version;


                              // PART 4: Publish Invoice

                              // continue if we have a new invoice. 
                              if($newInvoiceID != "" && $newInvoiceID != null){

                                    // grab payment link for the invoice by publishing
                                   $publishInvoice = array(
                                        "version"         => $newInvoiceVersion,
                                        "idempotency_key" => $idempotency_key_string
                                   );

                                    // Set up the cURL request
                                   $curlPubInvoice = curl_init();
                                   curl_setopt_array($curlPubInvoice, array(
                                        CURLOPT_URL => 'https://connect.squareup.com/v2/invoices/'.urlencode($newInvoiceID).'/publish',
                                        CURLOPT_RETURNTRANSFER => true,
                                        CURLOPT_ENCODING => '',
                                        CURLOPT_MAXREDIRS => 10,
                                        CURLOPT_TIMEOUT => 0,
                                        CURLOPT_FOLLOWLOCATION => true,
                                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                        CURLOPT_CUSTOMREQUEST => 'POST',
                                        CURLOPT_POSTFIELDS => json_encode($publishInvoice),
                                        CURLOPT_HTTPHEADER => array(
                                        'Authorization: Bearer '.$squaretoken,
                                        'Square-Version: 2023-04-19',
                                        'Content-Type: application/json'
                                        ),
                                   ));

                                   // Execute the cURL request and handle the response
                                   $curlResponsePubInvoice = curl_exec($curlPubInvoice);
                                   $jsonObjectPubInvoice = json_decode($curlResponsePubInvoice);
                                   curl_close($curlPubInvoice);
                                   
                                   // see if a new invoice was returned
                                   $publishedInvoiceURL = $jsonObjectPubInvoice->invoice->public_url;
                                   $publishedInvoiceID = $jsonObjectPubInvoice->invoice->id;

                                   // continue if we have a new invoice.....

                                }
                            }
?>
