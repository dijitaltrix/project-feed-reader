#DB
	`feeds`: permissions select, insert, update, delete
		name string 255, 
		url string 255 / text for the 1024 char url limits

#UI
##Feeds
* list rss feed records
	* handle pagination
	* handle search
	* allow multiple deletes
	* publish/unpublish (pause/unpause)

* add rss feed record, 
	* require name,
		* accept anything
	* require url, 
		* test url is a url
		* test url exists - handle temporary errors by accepting feed url

* edit rss feed record
	* show delete button
	* show save button

* delete rss feed record 
	* prompt user (js alert yes/no)
		 * js alert yes/no to submit form
		 * nojs show delete confirmation page

* view rss feed
	* fetch feed
		* handle errors gracefully, eg nice error msg
		* caching? Show cached version first
	* display feed
		* display feed nicely
			* channel info, fetched date

#Issues
	* support differing rss formats - check libraries on packagist

#Improvements
	* Store feed meta data in db e.g last fetched date
	* save feed items in db
		* expire after x days (cron)
		* lot more effort for now