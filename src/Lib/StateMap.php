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
    'review' => 2,
    'create' => 1,
    'createUnique' => ARTWORK_CREATE_UNIQUE,
    'refine' => 4,
    'initialize' => NULL,
    'mapStates' => NULL,
	'testMe' => NULL,
	 'validateQuantities' => 2
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
  ),
  'Editions' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
    'review' => 2,
    'create' => 1,
    'refine' => 4,
  ),
  'Formats' => 
  array (
    'index' => NULL,
    'view' => NULL,
    'add' => NULL,
    'edit' => NULL,
    'delete' => NULL,
    'review' => 2,
    'create' => 1,
    'refine' => 4,
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
    'review' => 2,
    'create' => 1,
    'refine' => 4,
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