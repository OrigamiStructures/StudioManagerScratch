RolodexCardsTable
	Include layers for Artists and Managers
StackSet
	Write tests
StackSet, StackEntity, Layers
	refactor to create new Summary and Access Interface/Trait classes
StackEntity
	StackEntity::set() is a property constructor pass-through to Entity. 
	It is coupled to handle only a Layer type column. This seems limited.
	Also, it's frozen into StackEntity, implying no other entity would ever 
	have a Layer type column.
	Lastly, is this overrider really the way to go? isn't there a 
	built-in capture/process technique for column types? (see JSON type example)
