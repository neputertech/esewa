<?php

namespace Neputer;

use Illuminate\Support\Facades\Config;


class Esewa {
    protected $endpoint;
    protected $formMethod;


    public function __construct()
    {
        $this->endpoint = Config::get('esewa.debug') ? "https://uat.esewa.com.np/epay" : "https://esewa.com.np/epay";
        $this->formMethod = "POST";
    }

    public function setFormMethod(string $method)
    {
        $this->formMethod = $method;
        return $this;
    }

    /**
     * tAmt = total amount
     * amt = amount
     * scd = Merchant/service code provided by esewa.
     * pid = A unique ID representing product/item.
     * su = Success URI: a URI to redirect after successful transaction in eSewa.
     * fu = Failure URI: a URI to redirect after failed transaction in eSewa
     *epay_payment
     * @param array $paymentDetail
     */
    public function pay(array $paymentDetail)
    {
        $url = $this->endpoint . '/main';

        $paymentDetail = array_merge([
            'txAmt' => 0,
            'psc' => 0,
            'pdc' => 0,
            'scd' => Config::get('esewa.scd'),
            'su' => Config::get('esewa.website_url'),
            'fu' => Config::get('esewa.website_url'),
        ], $paymentDetail);

        return self::send($url, $paymentDetail);
    }

    /**
     * @param array $paymentDetail
     * Prompts user with WalletID, PIN & OTP
     */
    public function send(string $url, array $paymentDetail)
    {
        $html = [];
        foreach ($paymentDetail as $key => $value) :
            $html[] = "<input type=\"hidden\" name=\"$key\" value=\"$value\"/>";
        endforeach;

        $html = implode("", $html);

        $form =  <<< EOT
            <html><head><style></style></head><body>
                <form id="form" action="$url" method="$this->formMethod">
                $html
                </form>

                <script>document.getElementById("form").submit();</script>
            </body></html>
        EOT;

        return $form;
    }
    


    public function verify(array $successDetail)
    {
        $successDetail = array_merge([
            'amt' => null,
            'rid' => null,
            'pid' => null,
            'scd' => Config::get('esewa.scd'),
        ], $successDetail);

        $curl = curl_init($this->endpoint . '/transrec');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $successDetail);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        return str_replace(PHP_EOL, '', strip_tags($response)) == 'Success';
    }
}

