RolodexCardsTable
	Include layers for Artists and Managers
StackSet
	Write tests
StackSet, StackEntity, Layers
	Refactor to create new Summary and Access Interface/Trait classes
	What should 'all' and 'first' do from different grouping levels?
		All or First from a StackSet wouldn't require a property/layer name, 
			it would pull all or the first StackEntity?
		All or First from a Layer wouldn't require a property/layer name, 
			it would pull all or the first layer member's
		All or First from a StackEntity would not make sensed
	What is the future of the WRAPPED/BARE argument? Will it be sensible? 
		And more generally, what return type can the user rely on?
StackEntity
	StackEntity::set() is a property constructor pass-through to Entity. 
	It is coupled to handle only a Layer type column. This seems limited.
	Also, it's frozen into StackEntity, implying no other entity would ever 
	have a Layer type column.
	Lastly, is this overrider really the way to go? isn't there a 
	built-in capture/process technique for column types? (see JSON type example)
