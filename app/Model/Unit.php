<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Unit
 *
 * @author dungtq
 */
class Unit extends AppModel
{

    /**
     * get Unit by id
     * 
     * @param int $id Unit id
     * @return array|null
     */
    public function getById($id)
    {
        return $this->findById($id);
    }

    /**
     * get all unit records
     * 
     * @return array | null
     */
    public function getAllUnit()
    {
        return $this->find('all');
    }

}
