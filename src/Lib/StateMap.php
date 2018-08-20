<?php
namespace App\Lib;
class StateMap {
	public $map = array (
  'App' => 
  array (
    'initialize' => NULL,
    'beforeRender' => NULL,
    'beforeFilter' => NULL,
    'mapStates' => NULL,
    'afterFilter' => NULL,
    'set' => NULL,
    '__construct' => NULL,
  ),
  'Artworks' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
    'sample' => NULL,
    'review' => ARTWORK_REVIEW,
    'create' => ARTWORK_CREATE,
    'createUnique' => ARTWORK_CREATE_UNIQUE,
    'refine' => ARTWORK_REFINE,
    'initialize' => NULL,
    'mapStates' => NULL,
	'testMe' => NULL,
	'validateQuantities' => ARTWORK_REVIEW // never checked/used?
  ),
  'Designs' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
  ),
  'Dispositions' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
	  'create' => DISPOSITION_CREATE,
	  'refine' => DISPOSITION_REFINE,
  ),
  'Editions' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
    'review' => ARTWORK_REVIEW,
    'create' => ARTWORK_CREATE,
    'refine' => ARTWORK_REFINE,
	'assign' => NULL, // not sure what this should be. Can't recall how these are used
  ),
  'Formats' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
    'review' => ARTWORK_REVIEW,
    'create' => ARTWORK_CREATE,
    'refine' => ARTWORK_REFINE,
  ),
  'Groups' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
  ),
  'GroupsMembers' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
  ),
  'Images' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
  ),
  'Locations' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
  ),
  'Members' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
    'review' => MEMBER_REVIEW,
    'create' => MEMBER_CREATE,
    'refine' => MEMBER_REFINE,
    'addNode' => MEMBER_REFINE,
  ),
  'Menus' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
  ),
  'Pages' => 
  array (
    'display' => NULL,
  ),
  'Pieces' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
	'review' => NULL,
	'renumber' => PIECE_RENUMBER,
  ),
  'Series' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
  ),
  'Subscriptions' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
  ),
  'Users' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
    'register' => NULL,
    'login' => NULL,
    'logout' => NULL,
    'profile' => NULL,
    'validateEmail' => NULL,
  ),
);
}
?>