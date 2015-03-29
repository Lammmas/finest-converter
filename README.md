Converter
=========

What does it do?
----------------

Converts inputted currencies into selected currencies via jQuery AJAX calls to the API endpoint.

Requirements
------------

- PHP 5.4.16+
- HTTP server, ex. Apache, with mod_rewrite enabled
- mbstring extension
- intl extension

How do I install it?
--------------------

1. Clone/Download to a directory
2. Start your web service by.. 
  - Pointing your WAMP/LAMP/IIS/etc.. to <directory>/webroot
  - Running `bin/cake server`; By default, without any arguments provided, this will serve your application at http://localhost:8765/
3. Open your browser
4. Enjoy!

What does the "API" want?
-------------------------

API has two endpoints - /api/convert and /api/currencies. 

Currencies does not want anything, and will return a JSON array of currencies with their names in Estonian.

Convert however wants either GET or POST parameters;
- /api/convert/`amount`/`from`/`to`/`time`/, ex. /api/convert/5.75/USD/EUR/12-12-12
- /api/convert with JSON array sent over POST, with the following structure:
  `amount: float, from: string, to: string, time: string`

Convert will return an array with a status (success/error) and the target (to) currency amount in Estonian and Lithuanian bank.

For example: `{status: "success", "result": {"est": 5.1859525, "lit": 5.581868}}`. If there is an error, then the "result" will contain the error message.
