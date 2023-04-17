<?php

namespace App\Traits;

use Stripe\Stripe;
use Stripe\OAuth;
use App\User;

trait StripeTrait
{
    /**
     * Constructor For Dependency Injection
     */
    public function __construct()
    {
        Stripe::setApiKey(env("STRIPE_SECRET_KEY", 'sk_test_5r1RNfSez1Pw2sEhCraH6nfk00jzwdDk5j'));
    }

    /**
     * This will store account id
     */
    public function connectAccount($authCode)
    {
        $response = OAuth::token([
            'grant_type' => 'authorization_code',
            'code' => $authCode,
        ]);

        if (empty($response->error)) {
            User::where('id', auth()->user()->id)->update([
                'stripe_acc_id' => $response->stripe_user_id
            ]);

            $res = [
                'status' => true
            ];
        } else {
            $res = [
                'res' => $response,
                'status' => false
            ];
        }

        return $res;
    }

    /**
     * This will create customer
     */
    public function createCustomer($user)
    {
        return \Stripe\Customer::create([
            'email' => $user['email'],
            'name' => $user['name'],
        ]);
    }

    /**
     * This will create card
     */
    public function createStripeCard($cardDetails)
    {
        $token = \Stripe\Token::create(array(
            'card' => [
                'number' => $cardDetails['number'],
                'exp_month' => $cardDetails['exp_month'],
                'exp_year' => $cardDetails['exp_year'],
                'cvc' => $cardDetails['cvc']
            ]
        ));

        $card = \Stripe\Customer::createSource(
            $cardDetails['stripe_cust_id'],
            [ 'source' => $token->id ]
        );

        if ($cardDetails['default']) {
            $this->updateCustomer($cardDetails['stripe_cust_id'], $card->id);
        }
    }

    /**
     * This will retrieve all the cards
     */
    public function cards($customerId)
    {
        return \Stripe\Customer::allSources(
            $customerId,
            ['object' => 'card']
        );
    }

    /**
     * This will delete the card
     */
    public function deleteStripeCard($cardDetails)
    {
        \Stripe\Customer::deleteSource(
            $cardDetails['customerId'],
            $cardDetails['src']
        );
    }

    /**
     * This will update your card
     */
    public function upadteStripeCard($cardDetails)
    {
        \Stripe\Customer::updateSource(
            $cardDetails['customerId'],
            $cardDetails['src'],
            [
                'exp_month' => $cardDetails['exp_month'],
                'exp_year' => $cardDetails['exp_year'],
                // 'cvc' => $cardDetails['cvc']
            ]
        );

        if ($cardDetails['default']) {
            $this->updateCustomer($cardDetails['customerId'], $cardDetails['src']);
        }
    }

    /**
     * This will create charge for transection
     */
    public function createCharge($chargeDetails)
    {
        return \Stripe\Charge::create([
            'amount' => $chargeDetails['amount'] * 100,
            'currency' => 'aud',
            'source' => $chargeDetails['src'],
            'description' => $chargeDetails['description'],
            'customer' => $chargeDetails['customer'],
            'on_behalf_of' => $chargeDetails['tippieAcc'],
            'transfer_data' => [
                'destination' => $chargeDetails['tippieAcc']
            ]
        ]);
    }

    /**
     * This will retrive charge
     */
    public function retriveCharge($chargeId)
    {
        return \Stripe\Charge::retrieve($chargeId);
    }

    /**
     * Balance of tippies
     */
    public function balance($accountId)
    {
        return \Stripe\Balance::retrieve([
                'stripe_account' => $accountId
            ])->pending[0]->amount/100;
    }

    /**
     * Update customer for default source
     */
    public function updateCustomer($custId, $card)
    {
        \Stripe\Customer::update(
            $custId,
            ['default_source' => $card]
        );
    }

    /**
     * This will retrieve customer details
     */
    public function retrieveCustomerDefaultCard($customerId)
    {
        return \Stripe\Customer::retrieve($customerId)->default_source;
    }
}
