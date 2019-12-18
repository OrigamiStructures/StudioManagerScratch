##Persons

Once a person has been create (Member record), certain ones cannot be allowed to change `member_type`.

For example, once an Artist has artworks, changing them to a Category would f' the system.

This is a bit like the freeze that comes on pieces once they have disposed in certain ways.

Topics: Studio Manager, Policy, Business Rule

##Address and Contact

If an address or contact is involved in a Supervisor/Manager connection, deletion of either record
must force replacement by some other data

##Artists

Artist need new lines to artwork to support the new Aritst-is-Member, Supervisor-is-User,
User-data-is-private arrangements.

We need to decide what tables actually get liked to the Artist. Probably no Addresses or Contacts.
Possibly Dispositions?

There is a lot to think about here

###Day 2

It may be possible to only link the Artwork to the artist. Formats may be generic descriptions that a manager
would want to use for many artists. Though, linking editions_formats to an artist could be used to create a
secondary, filtered list of formats that have applied to an artist's work.

Editions are another candidate for Artist linking. In fact I have explored having editions be the basis for
having collaborative works show up in two artist's listings without creating new records.

So at this point we have Artwork and Edition links required. EditionsFormats possible.

EditionFormats probably suffer from the same multi-artist issues as:

Pieces could ride along without an artist link. This could make them hard to work with as isolated entities.
But if they were always in some stack context, that could guarantee the upstream perspective.

The prospect of collaborative works suggests Pieces should not get an artist link because there would be no
clear ownership of the piece is such a case.

Dispositions would also need to rely on context provided by a stack to identify the artist or artists involved.

I had played with the idea having snapshot artist data in the dispositions, similar to the collector/address
data. There are some confounding arguments to consider.

Dispositions can still have meaning if a Member record involved in the transaction gets deleted. But what is
the meaning if the artist of the work gets deleted? Don't their artworks get deleted?

And what if the artwork DOES get deleted. Doesn't the Disposition become meaningless?

But what if the work is collaborative and only one artist is deleted? If the artists were not snap-shotted in,
they would disappear from the disposition.

This requires some policy thoughts about what 'deletion' means in the system. This relates back to privacy issues.

Also, some thoughts about collaboration and two artists registered as separate users must be sorted out.

##Collaborations

The current concept of privacy and owned data means there will be a large degree of trust required in
the case of two collaboration artists who are also registered users.

One will own an edition, the other will benefit from access to the data. As long as the relationship exists,
no problem. But if there is some change in system use or a conflict between the participants, the non-owner
will stand to loose a lot of meaningful historical data.

It would be nice to have some friendly policy that lays out the fate of data at the beginning of the arrangement,
when the relationship is at its best, and can only be changed by mutual consent.

Roughly, I think the policy should be spell out the ownership as we plan, but state that either participant can
end the arrangement but a useful and relevant minimum of data will be copied to the non-owner member at the end
of the relationship.

So we identify shared ownership with one member being the data custodian. Logging can protect the members form
malicious data destruction. That means we need special logging and restoration tools for these collaboration arrangements

###Collaborations and Manifests

Is there a way these collaborations can work from the planned Manifest system or is this a new system?

I don't see an overlap. It does raise the question though; what happens if only one of the collaborating artists
is visible to a manager? Do we need to prevent that or does the `many` relationship smooth evertything out?

###Collaborations and Marketing

Collaborations represent a way to get more artists on the system.

##Permissions

A chain of ideas:

- A Supervisor who's Artist has not been delegated cannot be *permission-filtered* for that Supervisor.
    This prevents *invisible* artwork. But what about sold-out, inactive, or archived works? Is there
    any feature to allow this? Or even a *recent* or *most-active* sorting scheme?
- Permission assignment must insure all artworks remain visible (managed by someone)
- Self set permissions only show delegated works as available choices
- Un-delegating a work automatically un-self-filters it if no other delegation exists
- FEATURE? Delegating automatically self-filters a work

Though, superficially, these options sound reasonable, we need to consider the site from a use-orientation
not just this code-permutation context. What would be the meaning of a Supervisor that wanted only to
delegate work to managers but never see that work except to review the managers permissions? That doesn't
really seem to make sense.

But the idea that a Supervisor with delegates would want to reduce their own screen load to simplify
their work does make sense.

- A Supervisor view should be able to show all delegated work/artists and their managers

##User Roles

CurrentUser has started using the User table `role` column for decision making. But we currently have
no plan for what the roles are or what they allow.

Nor do we have an knowledge of whether this is part of the DCUsers RBAC system or whether
there are tools in that system we should be using.

Topics: Studio Manager, Policy, Data Structure
