<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of template
 *
 * @author drkwolf
 */
class template extends Unittest_TestCase {
    //put your code here
    
    
    
    /**
     * cases :
     *  1 formated : true, asJson: false,
     * @dataProvider provider //TODO
     */
    public function testSet_contents()
    {
        // test1 : formated : true, asJson false
        $defaults = array(
          'formated' => null,
          'asJson'  => false,
          'view'     => null, //need when formated:true
         );
    }
}
