<?php
namespace Usama\Tap;
use Illuminate\Http\Request;

/**
 * Created by PhpStorm.
 * User: usamaahmed
 * Date: 7/17/17
 * Time: 11:00 AM
 */

interface TapContract {
    public function addProduct($id);
    public function removeProduct($id);
    public function setTotalPrice($products);
    public function getTotalPrice();
    public function setHashString();
    public function getHashString();
    public function getCustomer();
    public function setCustomer();
    public function setGateWay();
    public function getGateWay();
    public function getMerchant();
    public function makePayment();
}