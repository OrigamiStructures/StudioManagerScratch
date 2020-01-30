#Migration
The system has been upgraded to 3.8. Quasi-core files like those in bin,
index.php and the like have been fixed.

Deprecations have been handled.

CakeDC/Users has been upgraded. The user system
function was restored after the upgrade.

A Command (tester) has been written to run test suites because the phpunit
xml runner was not handling fixtures properly but the tests would otherwise
run properly.

#Cleanup

SystemState was removed. Features that were still of value have moved out to new classes.

Two new smaller classes have taken up its 'idenity tracking' uses. CurrentUser and ContextUser. These may collapse to one classe. They are responsible for tracking the user's focus in the system, eg working on a single artists works.

Many classes and files have been pruned form the system

EditionStackComponent::stackQuery(), an original nested stack concept has been
eliminated and ArtStack can now emit the equivalent data structures. These
structures should be rethought, but all functionality was preserved.

With a lot of cleanup done expansion of features has begun again.

#Rebuild

##Core data structures

The new Stack system has been systematized and a layer access system (LAS) has been built, then gone through a major refactor. The LAS provides standard tools to pull stack data out from any of the 3 levels of orgainization.

A suite of REPL pages was created as a backup to the unit test.

##Pagination
The Pagination system was tested against stacks. I had to write a small plugin module to make them work together, but Cake pagination had a hook in place for just that. Now stack sets can be paginated with all the normal tools.

##User Preferences
The foundation of a user preferences system has been written. Data is stored in a single json object. This means the options can easily be extended as needed. Tools have been written that can modify the josn object from post data. Only values that are not defaults will be stored and only nodes identified in the post data will be considered.

On the controller and view sides, the preferences entity can answer all prefs-value questions with a value; the user-variant or default.

##Card File, the `members` wrapper system

With Stacks operational and the LAS in place to work with them, work started on the the new RolodexCard stacks (people, organizations, and categories). Since Rolodex is still in copyright, in all the public facing work we are working through CardFileController. This had the unexpected advantage of freeing us from the stumbling into use of the limited RolodexCard superclass. Instead, seed queries start from the Members table then go directly to one of the three specific card types, or to a FatGenericCard.

CardFile can show what I believe to be the 4 major index lists

* mixed cards
* People
* Organizations
* Categories
* Supervisors (a superuser page)

Individual cards of any type are displayed on cardfile/view/:id. Each card shows it a page with full features for that type of card; manager, person, artist, organization...

All the current index and view pages in CardFile are linked together to create a primitive prototype UX and to facilitate development. Menus exist to access all the index pages.

1/29/2020
- Preferences have been encapsulated and disentangled from pagination.
- PagePrefs and Pagination both render on an `index` layout
- The prototype index page filtering now works with Middleware so that filtered contents will work with pagination (and pref-changes to pagination).
- Middleware keeps index filters alive within a specified page scope. The specification system for the scopes is primitive. Need will force evolution.
- Category card new/add is working. It links categories to managers as the basis for address book sharing by supervisors.

1/30/2020
- New Shares layer is working on all Rolodex cards. This is the basis for address book sharing.
- Shares layer data outputs on all cardfile prototype pages

##In Dev

###Index page filtering

There is a test interface installed on all cardfile index pages. It works fine but is written in a procedural way, just for cardfiles.

###Pagination

There is still a question about contained data pagination. The component looks like it has a feature for that but I haven't figured out how to make it work with stacks yet.

###CardFile
Create the view page tools for each kind of card.

Build crud for card files

- Category
  -[x] create
  -[ ] update
  -[ ] delete
- Organization
  -[ ] create
  -[ ] update
  -[ ] delete
- Person
  -[ ] create
  -[ ] update
  -[ ] delete
- Artist
  -[ ] create
  -[ ] update
  -[ ] delete
- Supervisor
  -[ ] create
  -[ ] update
  -[ ] delete
- Manager
  -[ ] create
  -[ ] update
  -[ ] delete




