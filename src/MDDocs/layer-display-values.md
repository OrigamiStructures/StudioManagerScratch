Some Layers in StackEntities, like Identity, contain convenient data for page display.

Other Layers, like Manifests, are just collections of link values.

In these later cases, we need to pack the layer with some additional data so the entity can fulfill its role as a full context logic and display object.

##Alternatives

There are two strategies:

- Contain the linked descriptor entity in the layer entities
- Make a synthetic property on the layer entities that contain the output data and add accessor methods

The first strategy would make the stacks deeper and add a new level of access complexity. That's not recommended.

The second strategy means either adding another sub-class of entity for the effected stack participants or, having entities that may or may not have certain properties set.

It may also be possible to modify the Table classes to ALWAY make the modifications to the entity, even when they are used outside a stack.

##The goal

Come up with a pattern that we can apply system-wide

- MembershipsTable::findHook() (early take)
- ManifestsTable::findNameOfParticipants() (modern take)

Topics: Studio Manager, Layer,
