Here's a log of the time I spent on this project, according to my time tracker it was ~13hrs which I would imagine is a bit excessive for a simple app. However in my defence I was interrupted mercilessly during the day and it was rare to get more than an hour solid 'in the zone' time.

I also quite enjoyed the challenge and spent some time satisfying my own sanity by going back to basics with regards to the packages I used. In the end I chose Slim and a basic PDO mapper (similar concept to Doctrine I believe), but went round the houses wondering if I should leverage a full framework like Laravel or go back to basics and do everything from scratch. I decided that Laravel probably wouldn't help you evaluate any of my code as it would simply be 'textbook' Laravel and where is the fun in that? I started using Eloquent with Slim but it is overkill for this application and would add another dependency increasing technical debt.

So here is my app, warts and all, it is light on unit testing and documentation, as mentioned with Dave I have focussed on testing the more crucial parts of the real estate app I developed, mainly the bookings logic as that is where the complexity and money lies. I know this example is very weak in this area and documentation. In my defence I have completed useful unit testing (with Travis CI) in a separate project if you would like to take a look - https://github.com/dijitaltrix/Scythe-View.

There are multiple improvements that could be made, for example Error handling is simply try catch in index.php, however this should be farmed off into \App\ExceptionHandler for example. Also the Feed Model should have range checking on the ID, I'd probably move this into a separate class or let the ORM software handle this - e.g. Laravel has a fillable property that governs what can and can't be mass assigned - you'll see notes in the code where I check the ID matches/is not over-ridden on update, things like this can be added to the model 'setters' so the models data integrity is always preserved.

I left the UI pretty basic but it is function and is accessible by keyboard, using the new html5 tags should help with other assistive devices etc.. but I feel there's room for improvement.

Overall I enjoyed developing this example and I'm grateful for the experience to get back to coding again. I've never really used RSS feeds myself but with a few improvements (caching/searchable feeds articles allowing notifications, ui etc.) I'd consider using it as my main news source.



##### 27th
- 17:05 - Initial setup
- 17:35 - Got spec down
- break
- 18:00 - Browse packagist for RSS libraries
- 18:15 - Setup composer with SimplePie 
- break

##### 12th 
- 15:30 - Set additional requirements for Slim Framework v3, Twig and Eloquent
- break 
- 19:25 - Setup local dev server - not, using local php server -S
- 19:35 - Setup app using Slim 3 add folder layout and app files
- 20:05 - Adding feeds module
- break
- 21:20 - Get Feeds module routes/mvc working
- 21:30 - Add Phinx for db migration/seeding support
- 21:45 - Migrated and seeded db
- break

##### 13th 
- 11:30 - Create models, ditch Eloquent
- 11:40 - Get twig/view logic working for feeds.create
- 12:00 - Create App support, copy in PDO class get php-cs working on laptop, tm var issue
- 12:30 - Added app views
- break 
- 13:00 - Do Feed logic
- 13:15 - Added input filter, using mapper not db
- break 
- 15:15 - Get feed vide working
- 15:20 - Get and display feed contents using SimplePie
- break
- 16:40 - Pass SimplePie in as dependency
- 17:00 - Got feed view working
- 17:30 - Playing round with feed view and reading stuff!
- break

##### 14th
- 11:45 - Check updated feed, get ui ideas 
- 11:50 - Get feed create/edit working, validation
- 12:30 - break
- 13:15 - Continue validation - curl
- 13:45 - Got validation working, updated views with named routes
- break
- 15:30 - Got edit working with session alerts, errors etc..

##### 16th 
- 11:20 - Add delete feature
- 14:30 - Make frontend look pretty 
- 16:00 - break 
- 21:00 - run through, add installation guide 
- 21:30 - break
- 21:45 - made name optional - fetch remote, validating on existing url

##### 18th 
- 11:00 - finish up, send to GitHub create readme and instructions 
- 12:30 - Overlaid some js
