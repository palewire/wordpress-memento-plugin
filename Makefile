.PHONY: test timegate

test:
	clear
	phpunit --configuration phpunit.xml --coverage-text

timegate:
	clear
	curl -X GET -I http://localhost:8888/timegate/http:/localhost:8888/2015/04/26/hello-world/ --header "Accept-Datetime: Mon, 27 July 2015 01:00:00 GMT"