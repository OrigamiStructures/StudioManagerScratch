##Assumptions

A pattern for controller index pages is starting to emerge based on some assumed index page features. I'm proceeding in the belief that these are universal index page requirements that we'll need no matter what our eventual UI looks like.

Requirements

* The ability to break the data set into manageable pages
* The ability for change the record count for the pages
* The ability to control the sort order of the records
* The ability to filter the found set.
* The ability to page through and sort filtered sets

Even assuming that we limit UI tools by hard-wiring record counts, sort order, and provide finite filtering options through links; we still need tools to accomplish those basic requirements in the back end.

##Accumulating tool set

With these requirements, writing a simple index page for a controller means composing in a growing suite of tools.

* PaginationComponent
* PaginationHelper
* PreferencesComponent
* PreferencesHelper
* a dedicated layout (`index.ctp`)
* the element `pagination.ctp`
* a suite of planned search form elements or helper calls
* a planned Search or Filter component and matching helper
* IndexFilterManagentMidddleware

##Making an index pages

It's not as grim as it might seem though. Cake's tools are flexible and, though our app is still in flux, we've already got a pretty good mix between magic and flexibility.

###The method and .ctp files

The Middleware is wired into Application.php and won't require any attention.

AppController puts the Pagination component in every controller. That might seem like overkill because some won't require it. But I have a special module that handles stack pagination that composes in. It seemed better to do this right once rather than forget it often. That component installs its own helper.

Your controller will need to include the Preferences component. But that component also installs its own helper.

Your action is responsible for identifying two `.ctp` files .

* `index.ctp` (layout)
* your actions template file
   * of course this may match your action name

Your action's template only needs to render the list of found records. The `index` layout will place the pagination tools and the filtering tools on the page. `index` extends `default` layout, so all the other normal page elements like menus and flash messages are handled there.

The layout will place the pagination tools and preferences form on the page. It requires some setup. All that is handled by PreferencesComponent::includePagination($user_id).

The search/filter forms will eventually be inserted by the layout too. But for now, the template needs to include them.

So, for now the template file is something like this:

```php
<?php
foreach ($foundSet as $item) {
    //output display for your item
}
echo $this->element('yourSearchFilterForm');
```

Your action will be something like this:

```php
//example code

/**
 * class property
 */
public $compontents = ['Preferences'];


public function index()
{
    //begin the query to find your records
    $seedIdQuery = $Table->find('someFinder');

    //add any user search filters to the query
    //the location and name of this call will
    $this->search($seedIdQuery);

    //get and paginate your stacks
    $foundSet = $this->paginate($Table->pageFor('seedType', $seedIdQuery->toArray()));

    //prepate data for the layout and set the layout
    $this->Preferences->includePagination($this->contextUser()->getId('supervisor'));
    $this->viewBuilder()->setLayout('index');

    //set values to viewVars
    $this->set('foundSet', $foundSet);

    //identify the template to render if need be
    $this->render('specialTemplate');
}
```




