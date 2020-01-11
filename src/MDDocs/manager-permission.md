##Phased proposal

Permissions Schema

```sql
CREATE TABLE `permissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `layer_name` varchar(30) NOT NULL DEFAULT '' COMMENT 'table name',
  `layer_id` int(11) NOT NULL COMMENT 'record id in the named table',
  `user_id` varchar(36) NOT NULL DEFAULT '' COMMENT 'owning supervisor/user',
  `manifest_id` int(11) NOT NULL COMMENT 'link to manifest for specific agreement permissions',
  `c_udd` int(6) DEFAULT NULL COMMENT 'override of create/update/delete/dispose settings',
  `range` varchar(50) DEFAULT NULL COMMENT 'range of piece numbers ',
  `blacklist` int(1) NOT NULL DEFAULT '1' COMMENT 'default is blacklist, clear flag to white',
  `manager_id` varchar(36) DEFAULT NULL COMMENT 'link direct to manager for cardfile permissions',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
```

Originally I had **Manifest**s that did not identify an *artist*. These were intended to provide general **CardFile** permissions to a *manager*.

Later, I abandoned these *partial manifests* and made all of them focus on **artist management agreements**. This was a simplifying strategy that had the unrecognized side effect of making all permissions *artist specific*.

Now, to address this limitation I'm propsing the `manager_id` column as a new way of indicating general vs manifest-specific permissions.

So, I have two possible ways of making permissions general to a manager vs specific to a management agreement.

A way forward might be to treat all permissions as general in the first system release. If there is a need for finer grain control we can look at it for a later phase.

In this case there is no need for permissions to be linked to manifest although if there are no manifests, there will be no permissions.

## problems?

How will limited permissions effect the management of portfolios and 'assembled works' like that? We don't want to extend management to the individual works... But there does need to be some degree of carry-over record access.

## Thoughts on when Permission Objects take action

###An Idea

If each entity subject to possible permission restriction gets a 'permit' property set with the `cudd` setting appropriate to the case, it would be simple to use the stacks.

- if the entity is present it can be shown (filtered on marshalling)
- `cudd` checks are on hand for and logic our tool output question.

###Another idea

* HABTM Member(type=Category) to Manifest for cardfile permissions
* Permissions only records ArtStack participant ids.
   * the full id list for each layer is calculated on permission creation. So at stack-marshal time, if permission is present, the record will provide the ids directly. Not marshal-time logic needed expcept to look-and-use or proceed as normal

###Back to our regularly scheduled program
Permissions determine the visibility of foreign data for a manager, and control the actions that manager can take on the visible data.

This means Permissions objects will be called on for advice in two circumstances:

1. During the queries that gather data
   - No data will be returned that isn't allowed so there will be no risk of inappropriate data exposure
2. During page rendering
   - Create/Edit/Delete tools will only appear when allowed

To accomplish the second goal, Permissions must be included in any object who's content was controlled by it.

It looks like an Interface should be designed so for any object that is controlled by permissions too. Just to make everything act the same for us poor programmers.

### Accessor abilities

So there will be special requirements for the accessors in entities who's contents are controlled by Permission. They will need to handle situations where normally present data is missing.

### Helper abilities

The Helpers that operate on entities who's contents are controlled by Permission will need special abilities too. The should accept both the entitiy and permissions objects and control UX features.

```php
 class SomeManagerHelper extends Helper {

    public function renderTool($entity, $permission = TRUE) {
      if ($permission || $permision->allowTool($entity)) {
         echo 'tool for user';
      } else {
         echo 'simple data display';
      }
    }
 }
```

## An Interface For Shared Objects

A good name for the Interface of objects who's contents are controlled by Permission might be `SharableInterface`. Just a first thought. I don't actually have a clear idea yet of what might be in this or who would implement it.

Based on the requirements listed in [Thoughts on when Permission object take action](#thoughts-on-when-permission-object-take-action), we might expect Tables and Helpers. But with two classes of such different type, could a single interface work?

```php
 interface SharableInterface {

   protected function checkPermission($arg);

 }
```

```php
 SomeStackTable extends StackTable implements SharableInterface {

   /**
    * This might be the implementation method
    */
   protected function checkPermission($contextData) {
      // Perhaps this would add query arguments?
      return $result;
   }

   /**
    * A method that may or may not be under permissions control
    */
   public function marshalSomeData($id, $stack) {
      // prepare some query
      $query = $this->checkPermission([$query, $otherContextData);
      // additional processes
   }
 }
```
Our previous helper example class would then change to something like this:

```php
 class SomeManagerHelper extends Helper implements SharableInterface {

   /**
    * This might be the implementation method
    */
   protected function checkPermission($contextData) {
      // This would do some boolean evaluation
      return $result;
   }

   /**
    * Tool availability may be under permissions control
    */
    public function renderTool($requiredContext) {
      if ($this->checkPermission($requiredContext) {
         echo 'tool for user';
      } else {
         echo 'simple data display';
      }
    }
 }
```

I have no idea if something this generic could work as the interface's required method. But this should stimulate thinking about the issue. And it is clear that the Permission object should be generally available in the large StackEntities and Composite objects.

## Adding Permissions to Stacks

Another possible way of organizing things (again, no idea if this would play out) is to add permission layers into the stacks that represent sharable data. Those stacks could take this data into account when operating. Or the stack could communicate permission details to the outside world based on this permission info.

A strategy like this could let shared data to be stored along with owned data because all the objects would be the same entity type, whether shared or owned. And each entity could respond in an appropriate way depending on its internals.

It's not clear yet whether the system will present mixed-ownership collection of data or whether the data will always be presented in ownership-groups. But even if the data is always segregated, a built in Permission interface based on the possible existence of permission-layer data feels good.


## Rules

- All Disposition participants must come from the Supervisor's data pool?
   - This would make it so a manager would have to have Create rights on the Supervisors address book or they would not be able to generate new clients.
   - The Dispositions store and display snapshot data, so the Supervisor would still be able to see the details if we don't have this rule.
   - Disposition to a new Person by a Manager could be a special CreatePerson process where the manager generates a Supervisor-Person record regardless of their permissions for creation. And they also get a clone of the record which they own. This could be justified because:
      - they agreed to work for the Supervisor,
      - the supervisor has a claim to the data created on their behalf,
      - the manager has an expectation that the data they create, they will own (given that they have no rights to generally create for the supervisor)

- If you have the right to see a record, you have a right to see all the data that makes it meaningful.
   - I've written that as a general rule but I'm specifically thinking about Dispositions. I had originally proposed that details of address and contact data might be kept secret in some cases. The ideas driving the secrecy rule:
      - Supervisors would feel some data represented a competitive edge that their manager's could leverage against them
      - Or Supervisors would feel the manager's could benefit from access to Supervisor data without compensation to the Supervisor
      - Data should be inherently private. Supervisors should always opt-in to sharing it.
   - This more general rule is based on the ideas:
      - Recruiting Managers represents an opt-in choice for data sharing
      - The Permission system is a tool to:
         - provide data filtering to help all staff work more efficiently
         - let the Supervisor decide how much the data can change outside of their explicit approval. But this might not be absolute, as in the case of a new Collector described above.

---

Topics: Stack, Layer, Studio Manager, Artist, Permission,
