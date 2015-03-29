<?php
/**
 * Created by Renna Reemet on 28.3.15 11:54
 */

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use DateTime;

class ApiController extends AppController {
	public function initialize() {
		parent::initialize();

		$this->loadComponent('RequestHandler');
		$this->loadComponent('Currency');
		$this->loadComponent('Cookie', ['expiry' => '1 month']);

		$this->Cookie->configKey('History', 'encryption', false);
	}

	public function beforeFilter(Event $event) {
		// We're only sending back JSON here, so no point in using full-blown views
		$this->view = "json";
		$this->layout = "ajax";

		parent::beforeFilter($event);
	}

	/**
	 * @return array    Associated array of available currencies, ex. ["USD" => "United Stated Dollar", "EUR" => "Euro"]
	 */
	public function currencies() {
		// Either read or fetch, write and read from cache the currency names in an associated array
		$this->set("content", json_encode($this->Currency->getCurrencies()));
	}

	/**
	 * @param float  $amount    Amount to be converted between $from and $to
	 * @param string $from      Currency code, defaults to "USD"
	 * @param string $to        Currency code, defaults to "EUR"
	 * @param string $time      Optional, used for conversion in the past, ex. "2010-12-30"
	 *                          Supported formats: m/d/y | y-m-d | d.m.Y
	 */
	public function convert($amount = 0.00, $from = "USD", $to = "EUR", $time = "2010-12-30") {
		$result = ['status' => 'error'];
		$error = false;

		if ($this->request->is(["ajax", "post", "put"])) {
			list($amount, $from, $to, $time) = $this->parsePost();
		}

		$from = strtoupper($from);
		$to = strtoupper($to);

		if (!is_numeric($amount) || $amount <= 0) {
			$error = true;
			$result['result'] = $amount == null ? "No amount sent" : $amount . " is not a valid number";
		}

		// Make sure date is in a supported format
		if (strpos($time, "/") !== false) {
			$date = DateTime::createFromFormat('m/d/Y', $time);
			$time = $date->format("Y-m-d");
		} else if (strpos($time, ".") !== false) {
			$date = DateTime::createFromFormat('d.m.Y', $time);
			$time = $date->format("Y-m-d");
		}

		// Estonian bank doesn't have records before the given date, so we error
		if (strpos($time, "-") === false || strtotime($time) < strtotime("1992-06-21")) {
			$error = true;
			$result['result'] = "Incorrect date";
		}

		$currencies = $this->Currency->getCurrencies();
		if (!array_key_exists($from, $currencies) || !array_key_exists($to, $currencies)) {
			$error = true;
			$result['result'] = "Invalid currency code";
		}

		if (!$error) {
			$calc = $this->Currency->calculate($amount, $from, $to, $time);

			if (!is_string($calc)) {
				$result['status'] = 'success';
			}

			$result['result'] = $calc;

			if ($this->Cookie->check("History")) {
				$history = $this->Cookie->read("History");
				if (is_null($history) || $history == "") $history = []; // Just to make sure that history is initialized
			} else $history = [];


			$history[] = array_merge(
				[
					'amount' => $amount,
					'from'   => $from,
					'to'     => $to,
					'time'   => $time,
				],
				$calc
			);

			$this->Cookie->write("History", $history);
		}

		$this->set("content", json_encode($result));
	}

	/**
	 * Helper functin for if the request is not made with GET parameters but with JSON parameters
	 *
	 * @return array    ex. ["amount" => 1.59, "from" => "USD", "to" => "EUR", "time" => "2010-12-30"]
	 */
	private function parsePost() {
		$data = $this->request->input('json_decode', true);

		// Setting the default values, in case something is missing
		$data = array_merge(
			["amount" => 0.00, "from" => "USD", "to" => "EUR", "time" => "2010-12-30"],
			$data
		);

		return [$amount = $data['amount'], $from = $data['from'], $to = $data['to'], $time = $data['time']];
	}
}
