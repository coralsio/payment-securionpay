<?php

/**
 * SecurionPay Authorize Request.
 */

namespace Corals\Modules\Payment\SecurionPay\Message;

/**
 * SecurionPay Authorize Request.
 *
 * An Authorize request is similar to a purchase request but the
 * charge issues an authorization (or pre-authorization), and no money
 * is transferred.  The transaction will need to be captured later
 * in order to effect payment. Uncaptured charges expire in 7 days.
 *
 * Either a customerReference or a card is required.  If a customerReference
 * is passed in then the cardReference must be the reference of a card
 * assigned to the customer.  Otherwise, if you do not pass a customer ID,
 * the card you provide must either be a token, like the ones returned by
 * SecurionPay.js, or a dictionary containing a user's credit card details.
 *
 * IN OTHER WORDS: You cannot just pass a card reference into this request,
 * you must also provide a customer reference if you want to use a stored
 * card.
 *
 * Example:
 *
 * <code>
 *   // Create a gateway for the SecurionPay Gateway
 *   // (routes to GatewayFactory::create)
 *   $gateway = Payment::create('SecurionPay');
 *
 *   // Initialise the gateway
 *   $gateway->initialize(array(
 *       'apiKey' => 'MyApiKey',
 *   ));
 *
 *   // Create a credit card object
 *   // This card can be used for testing.
 *   $card = new CreditCard(array(
 *               'firstName'    => 'Example',
 *               'lastName'     => 'Customer',
 *               'number'       => '4242424242424242',
 *               'expiryMonth'  => '01',
 *               'expiryYear'   => '2020',
 *               'cvv'          => '123',
 *               'email'                 => 'customer@example.com',
 *               'billingAddress1'       => '1 Scrubby Creek Road',
 *               'billingCountry'        => 'AU',
 *               'billingCity'           => 'Scrubby Creek',
 *               'billingPostcode'       => '4999',
 *               'billingState'          => 'QLD',
 *   ));
 *
 *   // Do an authorize transaction on the gateway
 *   $transaction = $gateway->authorize(array(
 *       'amount'                   => '10.00',
 *       'currency'                 => 'USD',
 *       'description'              => 'This is a test authorize transaction.',
 *       'card'                     => $card,
 *   ));
 *   $response = $transaction->send();
 *   if ($response->isSuccessful()) {
 *       echo "Authorize transaction was successful!\n";
 *       $sale_id = $response->getTransactionReference();
 *       echo "Transaction reference = " . $sale_id . "\n";
 *   }
 * </code>
 *
 * @see \Corals\Modules\Payment\SecurionPay\Gateway
 * @link https://securionpay.com/docs/api#charges
 */
class CreateChargeRequest extends AbstractRequest
{

    public function getData()
    {
        $this->validate('amount', 'currency');

        $data = array();

        $data['amount'] = $this->getAmountInteger();
        $data['currency'] = strtolower($this->getCurrency());
        $data['description'] = $this->getDescription();
        $data['metadata'] = $this->getMetadata();
        $data['captured'] = 'true';

        if ($this->getSource()) {
            $data['card'] = $this->getSource();
        } elseif ($this->getCustomerReference()) {
            $data['customerId'] = $this->getCustomerReference();
        } else {
            // one of cardReference, token, or card is required
            $this->validate('source');
        }

        return $data;
    }

    public function getEndpoint()
    {
        return $this->endpoint . '/charges';
    }
}
