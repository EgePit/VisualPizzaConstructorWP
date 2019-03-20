<?php
namespace Core\Classes;

class GoogleRating {
    private $api_key = 'AIzaSyCJ_eijK3f31p6Ccfb46FVloukSgcL14ck';
    public $address = '';
    public $place_id = '';
    public $rating = 0;

    function __construct($address) {
        $this->address = urlencode($address);
        $this->get_place_id();
        $this->get_rating();
    }

    function get_place_id() {
        $ch = curl_init();
        $url = "https://maps.google.com/maps/api/geocode/json?key={$this->api_key}&address={$this->address}";
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        $this->place_id = json_decode(curl_exec($ch))->results[0]->place_id;
        curl_close($ch);
    }

    function get_rating() {
        $ch = curl_init();
        $url = "https://maps.googleapis.com/maps/api/place/details/json?placeid={$this->place_id}&key={$this->api_key}";
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        $this->rating = json_decode(curl_exec($ch))->result->rating;
        curl_close($ch);
    }
}