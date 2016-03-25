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

}
