<?php
/**
 * Created by Renna Reemet on 28.3.15 13:21
 */

namespace App\Controller\Component;

use Cake\Cache\Cache;
use Cake\Controller\Component;
use Cake\Utility\Xml;
use Cake\Network\Http\Client;

class CurrencyComponent extends Component {
	private $_currencies = [];

	/**
	 * @param array $config
	 */
	public function initialize(array $config) {
		$this->_currencies = Cache::remember("currency_names", function () {
			// We are only using Eesti Pank because Lithuanian Bank doesn't return currency names
			return $this->fetchEestiPank(true);
		}, 'yearly');
	}

	/**
	 * General getter for the currency names
	 *
	 * @return array Associated array in the form of ['EUR' => 'Euro']
	 */
	public function getCurrencies() {
		return $this->_currencies;
	}

	/**
	 * Calculates the amount of resulting currency when converting from base currency
	 * Returns results bank-specifically
	 *
	 * @param float  $amount    The amount to be converted
	 * @param string $from      Code of the origin currency
	 * @param string $to        Code of the target currency
	 * @param string $time      Exchange time, defaults to '2010-12-30'
	 *
	 * @return array  Resulting target currency amount in each bank, ex. ['est' => '0.05', 'lit' => '8']
	 *                If there's a value missing or incorrect time then can return ['est => error, 'lit' => '8]
	 *                or the likes
	 */
	public function calculate($amount, $from = "EUR", $to = "USD", $time = "2010-12-30") {
		$rates = Cache::remember("currency_rates_on_" . $time, function () use ($time) {
			return $this->fetchRates($time);
		}, 'yearly');

		if ($rates['est'] !== false) {
			$relation = $rates['est'][ strtoupper($from) ] / $rates['est'][ strtoupper($to) ];
			$est = $amount * $relation;
		} else $est = "Info puudub";

		if ($rates['lit'] !== false) {
			$relation = $rates['lit'][ strtoupper($from) ] / $rates['lit'][ strtoupper($to) ];
			$lit = $amount * $relation;
		} else $lit = "Info puudub";

		return ['est' => $est, 'lit' => $lit];
	}

	/**
	 * @param string $time  Exchange time, defaults to '2010-12-30'
	 *
	 * @return array    Associated array of banks
	 *                  Example: ['est' => ['EUR' => '15.6466'], 'lit' => ['EUR' => '3.4528']]
	 */
	private function fetchRates($time = "2010-12-30") {
		if (strtotime($time) < strtotime("2011-01-01")) $est = $this->fetchEestiPank(false, $time);
		else $est = false;

		if (strtotime($time) < strtotime("2014-12-31")) $lit = $this->fetchLitBank($time);
		else $lit = false;

		return ['est' => $est, 'lit' => $lit];
	}

	/**
	 * Currency conversion rates against Estonian Kroon, usable until 30th of December 2010
	 *
	 * @param bool   $onlyNames Set to TRUE to only return the currency names in Estonian
	 * @param string $time      Exchange time, defaults to '2010-12-30'
	 *
	 * @return array|boolean    Associated array in the form of ['EUR' => '15.6466'] or ['EUR' => 'Euro'],
	 *                          depending on $onlyNames; can return false if there was an error
	 */
	private function fetchEestiPank($onlyNames = false, $time = "2010-12-30") {
		$url = 'http://statistika.eestipank.ee/Reports?type=curd&format=xml&date1=' . $time . '&print=off&lng=';
		// Switch to English language because in Estonian the numbers are separated by commas not points
		$url .= $onlyNames ? "est" : "eng";
		$http = new Client();
		$response = $http->get($url);

		// Easier to play around with an array than a rigid object that requires jumping through hoops
		$parsed = Xml::toArray($response->xml->Body->Currencies);

		if (empty($parsed['Currencies'])) return false;

		$result = [];

		// Time to parse
		foreach ($parsed['Currencies']['Currency'] as $currency) {
			if ($onlyNames == true)	$result[$currency['@name']] = $currency['@text'];
			else $result[$currency['@name']] = $currency['@rate'];
		}

		return $result;
	}

	/**
	 * Currency conversion rates against Lithuanian Litas
	 *
	 * @param string $time  Exchange time, defaults to '2010-12-30'
	 *
	 * @return array Associated array in the form of ['EUR' => '3.4528']
	 */
	private function fetchLitBank($time = '2010-12-30') {
		$url = 'http://webservices.lb.lt/ExchangeRates/ExchangeRates.asmx/getExchangeRatesByDate?Date=' . $time;
		$http = new Client();
		$response = $http->get($url);
		$parsed = Xml::toArray($response->xml);
		$result = [];

		foreach ($parsed['ExchangeRates']['item'] as $currency) {
			$result[$currency['currency']] = $currency['rate'];
		}

		return $result;
	}
}
