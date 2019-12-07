*CurrentUser* and *ContextUser* are meant to isolate logic related to the registered user and the role
that user wants to play in the system, respectivley.

##CurrentUser

This object is a simple wrapper on the data available through Auth::user(). It provides getters for the
available array node but no other features at this point.

Sample data
```php
$UserData = [
	'id' => '21b2',
	'management_token' => '21b2',
	'username' => 'don',
 	'email' => 'ddrake@dreamingmind.com',
	'first_name' => 'Don',
	'last_name' => 'Drake',
	'activation_date' => object(Cake\I18n\Time),
	'tos_date' => object(Cake\I18n\Time),
	'active' => true,
	'is_superuser' => false,
	'role' => 'user',
	'created' => object(Cake\I18n\Time),
	'modified' => object(Cake\I18n\Time),
	'artist_id' => '21b2',
	'member_id' => (int) 1
];
```

It is meant to provide a stable, valid, baseline for the system state, who is logged in, what are their
rights on the system.

I'm thinking it may be more useful when composed into Context user. For now it is an external, isolated
object available through `Controller::currentUser()`, on Table classes as `Table::currentUser` or
possibly on the Session (implementation has not been confirmed).

##ContextUser

###The situation in December 2019

Details of the *ContextUser* interface are not finalized. Integration into the system is tentative.

*ContextUser* identifies up to 3 identies to guide app behavior.

- Supervisor
- Manager
- Artist

These are the same identities identified in *Manifest* records and contextualized in *ManifestStack* entities.
I imagine *ContextUser* will be consulted on pages that deal with *ManifestStack*s.

####Interface

These calls are already used far and wide in the system and must be supported. However, their current
use exists in a legacy state. At some point, we need to establish proper rules for these methods
and review the code to properly apply those rules.

- `userId()`
   - Get the identity of the current user
   - How do we reconcile this with the **supervisor** identity? Do we always return `supervisor_id`
   and use **CurrentUser** when we truly require the registred user's identity?
- `artistId()`
   - The `artist_id` used to be equal to the `user_id` but has been redefined as a `member_id`.
   But the creation of **ArtStack** entities has not change to reflect this. Nor has any other
   corner of the system. And the way this method is used in light of the planned purpose of
   this classes **Artist Identity** makes no sense. So...

####The Supervisor Identity

This identity is provided so that ***System Developers*** can impersonate different registered users
as they operate on the the code. It is also imagined that ***System Administrators*** will set `Supervisor`
to the identity of a registered user to diagnose and correct problems the user is experiencing.

No other registered user will have access to this property. This property will be managed to match
the identity of the currently logged in user in all cases except those identified above.

####The Manager Identity

**Speculative**

The general idea here was meant to follow the pattern of the *Supervisor Identity*. It's not clear
if there is meaningful functionality here though.

The identity and permissions for a user—their ability to see and manage an artist's data—is established
by a **ManifestStack**.

The **Artist Identity** is meant as a *focusing filter* and possibly that idea may be valuable. In this
case the **Manager Identity** would actually be a way of identifying what foreign supervisor this
user wanted to work for. If multiple supervisors had delegated artist management to this user, the system
could let the user focus on one supervisors stable of artists through such a filter.

####The Artist Identity

This is intended as a focus/filter value that can be used as a user moves through the app. It would have
the effect of making a multi-artist data set appear to be a one-artist set, thus simplifying the users screens.


Topics: Studio Manager, Permissions,
