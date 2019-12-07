It might be useful to populate these with PersonCards to provide more detail about the state. This came to mind
because Auth->user() has username data in it (this is the basis for CurrentUser and so for ContextUser), but if
the supervisor is changed in the ContextUser object, there is no obvious way we would know the new name.

And, while we could get a username for the supervisor, we can't get equivalent data for any other person
identified in the object.

##CurrentUser

This object is a simple wrapper on the data available through Auth::user(). It provides getters for the
available array node but no other features at this point.

Sample data
```php
[
	'id' => '21b2',
	'management_token' => '21b2',
	'username' => 'don',
 	'email' => 'ddrake@dreamingmind.com',
	'first_name' => 'Don',
	'last_name' => 'Drake',
	'activation_date' => object(Cake\I18n\Time) { },
	'tos_date' => object(Cake\I18n\Time) { },
	'active' => true,
	'is_superuser' => false,
	'role' => 'user',
	'created' => object(Cake\I18n\Time) { },
	'modified' => object(Cake\I18n\Time) { },
	'artist_id' => '21b2',
	'member_id' => (int) 1
]
```

It is meant to provide a stable, valid, baseline for the system state, who is logged in, what are their
rights on the system.

##ContextUser

ContextUser is intended to be a guide to the use-state of the system. I imagine 3 pieces of information
will be key:

- who is the supervisor currently acting
- who is the manager currently in focus
- who is the artist currently in focus

it's easy to imagine this also tracking artwork stack focus. And it could encompass all the function
of the earlier pending disposition cache object.

### As A $this->allow filter

Not having an artist created (or selected) means that many actions will be unusable. I don't really know
the details here, but if ContextUser actually becomes a control structure in the system, its internal
values can't be left unknown. That would force the calling code to provide extra conditional logic.

ContextUser will have to contain all the fallback rules so it always provides meaningful return values,
even when the user has not taken actions to set the values.

##Is ContextUser another SystemState?

As this grows in scope it feels a little like the failed SystemState class. So, where did that class fail,
and am I actually walking that path again?

###A Class For All Seasons

**Past:** SystemState was supposed to work as a global class. To accomplish my goals it may have been easier
to make a static class and set values into it for later retrieval. But I didn't understand the behavior or use
of static classes at the time. Even now I'm not sure of all the details.

Rather than static, I tried to make all the base class times accept the current copy of SystemState and carry
it on a property. This was a very messy process. Using a Singleton pattern would almost certainly made to
process of getting the current copy into whatever class required it.

**Present** I'm not trying to make the class universally present. I will want it to be available from a wide
variety of locations, but have better techniques to make that happen now.

><small>**Todo Alert**
>The current TableLocator override in AppController shows that I haven't yet implemented any of these
>*better* strategies. Gotta fix that.</small>

**Past:** I thought of the class as a way to keep track of State across multiple site-calls. But I never
persisted the instantiation, so it was built up fresh on each call. So it was incapable of fulfilling the goal.

**Present:** If I want the preservation of State information, I now understand I have to persist the object.
Session data is already stored across visits and could be used.

**Past:** I used the class to duplicate the ViewVars system to make every variable `set( )` in a controller
action available in every class that had a copy of SystemState.

**Present:** I recognize this strategy was the best answer to the question "How can you maximize coupling
across all classes and every layer of the app?". This feature was compensating for my lack of understand
of proper OOP thinking.

**Past:** Once the class was generally available it became a repository for all kinds of behavior and data.

**Present:** Again, a lack of understanding of OOP thinking. But this does point to the fact that I should
have a variety of 'state' objects, each with a focused purpose. So the scope-creep considered at the
end of ContextUser should be avoided.

##Making it accessible

I suggested above that I might use the Session as a place to store the data. This is actually a good
solution because it is a completely stand-alone class `Cake\Http\Session`. It extends nothing, implements
no interface, and takes just three constructor arguments.

It shouldn't be too hard to find the values that correspond to the currently used app session and blend
into that. Or, I could make a unique session for my suite of objects or a session for each object.

##Maintaining the data

###ContextUser

Hope is that ContextUser will keep track of the current Supervisor, current focused Manager (if any),
and current focused Artist (if any). And that these values would persist across calls. In this way, I
imagine the user can make choices that reflect their desire to limit (or not) the data set they are viewing.

So, depending on the values set in ContextUser through past page interactions, a user arriving on
artwork/index might see a page of mixed artwork from all their owned and received artists, or they
might see only a page of artwork from a specific artist.

It's possible that a page, like artwork/index, would have filtering tools available. These would set
ContextUser values and re-render. But I also see page-sequence navigation choices as a mechanism for
incrementally setting values in ContextUser.

For example, supervisor/index offers delegated managers and manifest agreements as links to more
detailed editing pages. Clicking a link means the responding action could now update one or more
ContextUser value. As the user moves through subsequent pages, the earlier choices would act as filters.

This **sequencial filtering** means we would also have to have clear indications on the page of how
the data was being limited, and tools to open the data back up.

##Scope

Since ContextUser (and other proposed state objects) are focused on maintaining our application's
State for a logged in user, it should have no role in the API. If a developer needs their app to 
maintain state, they will have to implement a system for that.

This points to a rule we could make: The API must be stateless.

Topics: Studio Manager, Permissions,
