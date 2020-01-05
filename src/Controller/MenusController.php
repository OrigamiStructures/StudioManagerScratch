<?php

/*
 * Copyright 2015 Origami Structures
 */

namespace App\Controller;

/**
 * CakePHP MenusController
 * @author jasont
 */
class MenusController extends AppController {

    public function index() {
        $menus = $this->Menus->assemble();
        $this->set(compact('menus'));
    }

}
