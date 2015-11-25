<?php

namespace Omnipay\Redsys\Message;

use Omnipay\Common\Exception\InvalidResponseException;

/**
 * Redsys Complete Purchase Request
 */
class CompletePurchaseRequest extends PurchaseRequest
{
    public function checkSignature($data) 
    {
        // if (!isset($data['Ds_Signature'])) 
        // {
        //     return false;
        // }

        // $signature = '';

        // foreach (array('Ds_Amount', 'Ds_Order', 'Ds_MerchantCode', 'Ds_Currency', 'Ds_Response') as $field) 
        // {
        //     if (isset($data[$field])) 
        //     {
        //         $signature .= $data[$field];
        //     }
        // }
        // $signature .= $this->getSecretKey();
        // $signature = sha1($signature);

        // return $signature == strtolower($data['Ds_Signature']);

        return true;
    }

    public function getData()
    {
        $query = $this->httpRequest->request;

        $parameters = $query->get('Ds_MerchantParameters');
        $parameters = base64_decode(strtr($parameters, '-_', '+/'));
        $parameters = json_decode($parameters, true); // (PHP 5 >= 5.2.0)

        \Log::info($parameters);

        $data = array();

        foreach (array('Ds_Date', 
                       'Ds_Hour', 
                       'Ds_SecurePayment', 
                       'Ds_Card_Country', 
                       'Ds_Amount', 
                       'Ds_Currency', 
                       'Ds_Order', 
                       'Ds_MerchantCode', 
                       'Ds_Terminal', 
                       'Ds_Response', 
                       'Ds_MerchantData', 
                       'Ds_TransactionType', 
                       'Ds_ConsumerLanguage', 
                       'Ds_AuthorisationCode') as $field) 
        {
            $data[$field] = $parameters[$field];
        }

        if (!$this->checkSignature($data)) 
        {
            throw new InvalidResponseException('Invalid signature: ' . $data['Ds_Signature']);
        }

        return $data;
    }

    public function sendData($data)
    {
        return $this->response = new CompletePurchaseResponse($this, $data);
    }
}
