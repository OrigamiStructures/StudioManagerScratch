##The purpose of Manifests

Manifests are issued by registered users acting in the role of supervisor.

A manifest record identifies one of the supervisor's member records as an artist. It also identifies a member record that represents a manager for that artist.

Through the manifest, the owner of the manager record gains permission to view and operate on the aritst's data.

![An object diagram that illustrates that fact that three quarters of the records involved in a Manifest must belong to one registered user](/img/images/image/e42891d0-2d79-4040-b846-85316fd624a5/most-basic-manifest-objdia.png "Understanding required record ownership in among a manifest and its members")

In the simplest use of the system—an artist managing their own artwork—the three member records linked to the manifest will be the same record. In this case the interface will smooth away all the complexity that allows for collaborative management of artist data.

![An object diagram that shows the one Manifest that will exist in the simple case where an artist uses the system to manage their own artwork](/img/images/image/781be57b-b699-449d-8e10-d6f19413e244/single-user-single-ortist-manifest-objdia.png "A Manifest in a single user, single artist scenario")

Manifests have proved very difficult to reason about. So this document will attempt to catalog the ways that
they can appear and operate in the system. And, at least as important, I'll try to establish and define
terminology to use when discussing manifests. Thes terms will also guide the naming functions and variables.

##Manifests/Member fields and associations

A basic manifest/member linked-entity package is not too useful. The stack system will provide objects with sufficient context for useful features. But it's important to understand the basics for when need to query outside the Stack system.

![A class diagram showing a Manifest and the Member records which are always associated with it](/img/images/image/21cdf85a-262a-40a7-805c-0521d50aaa9b/member-manifest-fields.png "Linking fields in Manifests and Members")

- `Member::id` associations
   - `Manifest::supervisor_member`
   - `Manifest::manager_member`
   - `Manifest::member_id` (identifies the artist)
- `User::id` associations
   - `Member::user_id`
   - `Manifest::user_id`
   - `Manifest::supervisor_id`
   - `Manifest::manager_id`

```php
 class ManifestsTable extends AppTable
{
    public function initialize(array $config)
    {
        $this->belongsTo('Supervisor', [
            'className' => 'Members',
            'foreignKey' => 'supervisor_member',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Manager', [
            'className' => 'Members',
            'foreignKey' => 'manager_member',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Artist', [
            'className' => 'Members',
            'foreignKey' => 'member_id',
            'joinType' => 'INNER'
        ]);
    }
}
```

```php
class MembersTable extends AppTable
{
    public function initialize(array $config)     {
        $this->hasMany('ArtistManifests', [
            'className' => 'Manifests',
            'foreignKey' => 'member_id',
            'dependent' => TRUE,
        ]);
        $this->hasMany('ManagerManifests', [
            'className' => 'Manifests',
            'foreignKey' => 'manager_member',
            'dependent' => TRUE,
        ]);
        $this->hasMany('SupervisorManifests', [
            'className' => 'Manifests',
            'foreignKey' => 'supervisor_member',
            'dependent' => TRUE,
        ]);
    }
}
```

##Changing perspective and the need for clear terminology

Manifests have proven very difficult to reason about because each one is descriptive relative to three different actors in the system. And since the member records may link to one, two, or three nodes of the manifest, each one has the potential to be describe as one of those three different actors.

It will be important to define some clear terms to use when discussing what the manifest means in regards to actors. These terms will also guide the naming functions and variables.

###Manifests can describe themselves.

A simple record contains a lot of information and so the base entity can answer a lot of questions without the need for larger composite objects. **Manifest** shares its `getter` strategy with **ContextUser** which also records supervisor, manager, and artist links and data. Rather than using **get*ExplicitPropery*()** these classes use **get*DataType*($actor)** to reduce the number of methods in the class.

- data return methods
    - getMemberId($actor)
    - getOwnerId($actor)
    - getName($actor) returns null in some situations
- boolean checks
    - selfAssigned()
    - hasArtist($id)
    - hasManager($id)
    - hasSupervisor($id)

These methods also underly methods of the higher level composite objects. Member-primary classes will be **PersonCard** or any class that extends or collects it. Manifest-primary classes are any (future) extenders or collectors of **ManifestStack**.

###Terminology in composite objects

Typically you encounter these elements in a Manifest-primary or Member-primary way. Member-primary objects—**PersonCard** or **ArtistCard**—contain manifests to provide context to the Card. In Manifest-primary mode you'll have a **ManifestStack** which will contain full **PersonCards** providing objects capable of creating richly detailed pages and deep-context processes.

Managers can exist in 3 different flavors

- A Manifest which names the user as both Supervisor and Manager
   - DefaultResponsibilities
   - DefaultManagement
   - **OwnedManagement** (preferred?)

- A Manifest which names the user as Supervisor but not as Manager
   - **DelegatedManagement** (preferred?)
   - AssignedManagement
   - ManagementStaff

- A Manifest which names the user as Manager, but not as Supervisor
   - ManagementResponsibilities
   - AssignedManagement
   - AssignedResponsibilities
   - **ReceivedManagement** (preferred?)

### Manfests when used as Artist identifiers
Artists can exist in 3 different flavors

- Manifests that identify the artist a Supervisor has defined
   - DefaultArtists
   - **OwnedArtists** (preferred?)
   - Artists
- Manifests that identify **OwnedArtists** that have **DelegatedManagement**
   - **DelegatedArtists** (preferred?)
   - ManagedArtists
- Manifest that identifies the artist in **ReceivedManagement**
   - AssignedArtist
   - **ReceivedArtist**

### A Generic, focused ManifestStack
It is possible to think of the Stack as a generic object, regardless of its content

- The stack or page that explains the permissions and details of one Manager regarding one Artist
   - **ManagementAgreement**

I don't think there would need to be a manifest to define self-as-manager because the record would not define anything that couldn't be assumed about the user. Manifests to define self-owned-artists on the other hand are required to define the artist (?)

This may not actually be true. I think the self-owned-artists need manifests so handling of artists becomes normalized no matter which variety arrives in code. I wonder if manager manifests need to be generated for the same reason.

## Organizing the pages

Using these terms, here are possible pages, the kind of manifests they focus on, and what tasks they facilitate.



### Supervisor/index

It's important to remember that this sequence of pages focuses on Artist data owned by this Supervisor, on Managers this Supervisor has granted permission to (including selfManagedAgreements), and on the ManagementAgreements for those Managers.

These pages do not expose agreements extended to this user by other Supervisors (where this user is the Manager in the agreement).

- **DelegatedManagement**
   - Choosing one of the listed managers take this supervisor [to a page](#supervise-manager) where they can view and adjust the details of the assignments made to that manager
   - A tool to recruit/acknowledge an new manager
      - From existing person
      - From person defined on the fly
      - Respond to pending requests-to-manage that have been received (or are these emailed links that never appear in the system?)
      - A list of pending recruitment offers for reminder/deletion?
- **DelegatedArtists**
   - Choosing one of the listed artists takes this supervisor  [to a page](#supervise-artist)  where they can view and adjust details of this artist's managers
   - A tool to identify a new artist
      - From existing person
      - From person defined on the fly

### Supervise/Manager/:id

- **DelegatedArtists** for the single **DelegatedManagement** (a set of **ManagementAgreements**)
	- Adjustments to the **ManagerManifest** can be made on this page (CRUD and Contact Permission changes)
    - Each **ManagementAgreement** can be revoked from this page
	- Allow navigation to a single **ManagementAgreement** [see Supervise/Manager/:id/Artist:id](#supervise-manager-id-artist-id)

><small>**Pagination Note**
>Including the address book on this page would create a double-pagination condition. Typically, the primary page data is paginated (in this case the **DelegatedArtists** for this staff member. Page requests get embedded in the URL by Cake. Address data would also need to be paginated and some extension of the PaginationComponents parsing of URL parameters would have to be designed to handle this. Creating a separate page for contact permission supervision would solve the problem.</small>

### Supervise/Artist/:id

- **DelegatedManagement** for the single ManagedArtist (a set of **ManagementAgreements**)
	- Adjustments to the **ManagerManifest** can be made on this page (CRUD and Contact Permission changes)
    - Each **ManagementAgreement** can be revoked from this page
	- Allow navigation to a single **ManagementAgreement** [see Supervise/Manager/:id/Artist:id](#supervise-manager-id-artist-id)

### Supervise/Manager/:id/:id

Also callable as `Supervise/Artist/:id/:id`. This page is accessible from multiple contexts so it should be named in a source-agnostic way but it should probably retain proper 'back' button context.

- Details of a single **ManagementAgreement**
- Allow changes in CRUD settings for the permitted Artworks
- Allow changes to the Permission set
	- This requires access to the full set of Artworks for this artist. Since the Supervisor may have limited their own Permission set to simplify their own Management duties, this case must ignore Permissions when pulling the ArtStack

## Alternate organizing strategy

Here is another way to try and think about this problem. These notes try to describe the display elements needed in different situations. What about functional elements?

![Written notes atempting to describe the necessary display modules for manifests when view from different primary focuses in different contexts](/img/images/image/6e72f88f-cb0a-4ab2-9a4e-b19a10c33381/FullSizeRender.jpeg "Manifest Terminology")


Topics: Studio Manager, Artist, Manager,
