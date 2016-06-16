<?php

namespace ew;

class DBUtility {

  /**
   * 
   * @param \PDO $pdo
   * @param String $table_name
   * @param int $id
   */
  public static function row_exist($pdo, $table_name, $id, $row_name = 'id') {
    $stmt = $pdo->prepare("SELECT id FROM $table_name WHERE $row_name = ?");
    //$stmt->bindValue(1, $pdo->quote($table_name));
    $stmt->bindValue(1, $id, \PDO::PARAM_INT);
    $stmt->execute();
    //$row = $stmt->fetch(\PDO::FETCH_ASSOC);

    return ($stmt->rowCount() > 0) ? $stmt->fetch(\PDO::FETCH_ASSOC) : false;
  }

  public static function filter(\Illuminate\Database\Eloquent\Builder $query, $filter) {
    if (is_array($filter['where'])) {
      foreach ($filter['where'] as $key => $value) {
        if (is_array($value)) {
          $type = array_keys($value)[0];
          switch ($type) {
            case 'in':
              $query->whereIn($key, $value[$type]);
              break;
            case 'nin':
              $query->whereNotIn($key, $value[$type]);
              break;
            default :
              $query->where($key, $type, $value[$type]);
          }
        }
        else {
          $query->where($key, $value);
        }
      }
    }
    
    return $query;
  }

}
