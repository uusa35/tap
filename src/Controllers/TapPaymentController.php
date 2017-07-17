<?php
namespace Usama\Tap;

use App\Http\Controllers\Controller;
use App\Services\TapInvoice;

/**
 * Created by PhpStorm.
 * User: usamaahmed
 * Date: 7/15/17
 * Time: 6:04 PM
 */
class TapPaymentController extends Controller implements TapContract
{
    public function addProduct($id)
    {
        $products = session()->has('cart') ? session()->get('cart') : collect([]);
        $products = $products->reject(function ($value, $key) use ($id) {
            return $value['UnitID'] == $id;
        });
        $products->push([
            "CurrencyCode" => "KWD",
            "ImgUrl" => "http://2e0e4e551211ba98fa70-d81ddca05536e7c590811927217ea7a4.r4.cf3.rackcdn.com/catalog/product/cache/1/image/700x700/17f82f742ffe127f42dca9de82fb58b1/g/r/green_apple_fragrance.jpg",
            "Quantity" => 2,
            "TotalPrice" => 2,
            "UnitDesc" => "Astonishing green apple!",
            "UnitID" => $id,
            "UnitName" => "Green Apple",
            "UnitPrice" => 1,
            "VndID" => ""
        ]);
        session()->put('cart', $products);
        $this->setTotalPrice($products);
        return redirect()->back();
    }

    public function removeProduct($id)
    {
        abort_if(!session()->has('cart'), 403, 'no products');
        $products = session()->get('cart');
        $products = $products->reject(function ($value, $key) use ($id) {
            return $value['UnitID'] == $id;
        });
        session()->put('cart', $products);
        return redirect()->back();
    }

    public function clearProducts()
    {
        session()->forget('cart');
        return redirect()->home();
    }

    public function setTotalPrice($products)
    {
        session()->put('totalPrice', $products->pluck("TotalPrice")->sum());
    }

    public function getTotalPrice()
    {
        return session()->get('totalPrice');
    }

    public function getProducts()
    {
        return session()->get('cart');
    }

    public function setCustomer()
    {
        session()->put('customer', [
            "Email" => "sara@tap.com.kw",
            "Floor" => "4",
            "Gender" => "F",
            "ID" => "",
            "Mobile" => "965998868811",
            "Name" => "Sara Khaled",
            "Nationality" => "KW",
            "Street" => "Ahmed AL Jaber",
            "Area" => "Shaeq",
            "CivilID" => "",
            "Building" => "Yousef Al Matrouk",
            "Apartment" => "1",
            "DOB" => "1990-01-01"
        ]);
        return session()->get('customer');
    }

    public function getCustomer()
    {
        return session()->has('customer') ? session()->get('customer') : $this->setCustomer();
    }

    public function setGateWay()
    {
        session()->put('gateway', "ALL");
    }

    public function getGateWay()
    {
        return session()->has('gateway') ? [["Name" => session()->get('gateway')]] : [["Name" => config('tap.gatewayDefault')]];
    }

    public function getMerchant()
    {
        return [
            "AutoReturn" => config('tap.autoReturn'),
            "ErrorURL" => config('tap.errorUrl'),
            "HashString" => $this->getHashString(),
            "LangCode" => config('tap.langCode'),
            "MerchantID" => config('tap.merchantId'),
            "Password" => config('tap.password'),
            "PostURL" => config('tap.postUrl'),
            "ReferenceID" => '45870225000',
            "ReturnURL" => config('tap.returnUrl'),
            "UserName" => config('tap.userName')
        ];
    }

    public function setHashString()
    {
        return $toBeHashedString = 'X_MerchantID' . config('tap.merchantId') .
            'X_UserName' . config('tap.userName') .
            'X_ReferenceID' . '45870225000' .
            'X_Mobile' . '1234567' .
            'X_CurrencyCode' . config('tap.currencyCode') .
            'X_Total' . $this->getTotalPrice() . '';
    }

    public function getHashString() {
        return  hash_hmac('sha256', $this->setHashString(), config('tap.apiKey'));
    }

    public function makePayment()
    {
        $finalArray = [
            'CustomerDC' => $this->getCustomer(),
            'lstProductDC' => $this->getProducts()->values(),
            'lstGateWayDC' => $this->getGateWay(),
            'MerMastDC' => $this->getMerchant(),
        ];

        $client = new \GuzzleHttp\Client();
        $response = $client->post(config('tap.paymentUrl'), [
            'body' => json_encode($finalArray, JSON_UNESCAPED_SLASHES),
            'headers' => [
                'content-type' => 'application/json'
            ]
        ]);
        $invoice = new TapInvoice($response);
        return $invoice->storePayment();
    }
}

