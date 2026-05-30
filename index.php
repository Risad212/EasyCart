<?php

/*
 * Query Builder
 */

class QueryBuilder{
  
 protected $table;
 protected $where = [];

 /*
  * Set Table Name And Return
  *
  * @param string $table
  * @return self
  */
 public function table( string $table ): self{
    $this->table = $table;
    return $this;
 }

 /*
  * Add A WHERE Condition
  *
  * @param string $column
  * @param mixed $value
  * @return self
  */
  public function where(string $column, mixed $value): self {
     $this->where[] = "{$column} = '{$value}' ";
     return $this;
  }

  /**
   * Get SQL Query
   * 
   * @return string
   */
   public function toSQL(): string {
     $sql = "SELECT * FROM {$this->table}";

     if( ! empty( $this->where ) ){
         $sql .= " WHERE " . implode(' AND ', $this->where);
     }
     return $sql;
   }

}

$instance = new QueryBuilder();

echo $instance
    ->table('users')
    ->where('name', 'John Doe')
    ->toSQL();

