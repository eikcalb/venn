<?php
namespace database;

class Paybot extends Database{
    
    /**
     *  Create a new teller entry on the database.
     *  This must be created in order to receive payment and make payment.
     *  This should not be used by front-end users, if possible, this should be automatically created.
     *  Transactions should not be made if there is no ticket available for the transaction.
     * 
     * @param array $parties This is an array containing identifiers of the parties involved in the transaction.
     *                       The array contains:
     *                          'payee' this is the name of the payee.
     *                          'drawer' this is the name of the drawee/drawer.
     *                          'title' this is an identifier pointing to the nature of the project paid for.
     *                          'amount' this is the amount paid for the project.
     *                          
     */
    public function insert($parties){
        $client = $parties['payee'];
        $service = $parties['drawer'];
        
    }
    
    public function query(){
        
    }
    
    public function update(){
        
    }
    
    public function delete(){
        
    }
}
