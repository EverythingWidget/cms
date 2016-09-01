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
    if (is_array($filter['include'])) {
      foreach ($filter['include'] as $value) {
        $query->with($value)->has($value);
      }
    }

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

  public static function create_table($table, $fields) {

    $sql = "CREATE TABLE IF NOT EXISTS `$table` (";
    $pk = '';

    foreach ($fields as $field => $type) {
      $sql .= "`$field` $type,";

      if (preg_match('/AUTO_INCREMENT/i', $type)) {
        $pk = $field;
      }
    }

    $sql = rtrim($sql, ',') /* . ', PRIMARY KEY (`' . $pk . '`)' */;

    $sql .= ") CHARACTER SET utf8 COLLATE utf8_general_ci";
    return $sql;
  }

  private static $TYPES = [
      'BIGINT'  => 'BIGINT(20)',
      'BOOLEAN' => 'TINYINT(1)'
  ];

  public static function alter_table($table, $fields, $current_stucture) {
    $new_fields = [];

    $sql = "ALTER TABLE `$table` ";

    foreach ($fields as $field => $type) {

      $status = 'new';

      foreach ($current_stucture as $old_field) {
        $new_type = strtoupper($old_field['Type']) . ' ';
        $is_null = $old_field['Null'] === 'YES' ? 'NULL ' : 'NOT NULL ';
        $extra = $old_field['Extra'] ? strtoupper($old_field['Extra']) . ' ' : '';
        $primary_key = $old_field['Key'] === 'PRI' ? 'PRIMARY KEY ' : '';
        $default = !is_null($old_field['Default']) ? 'DEFAULT ' . $old_field['Default'] : '';
        $old_type = $new_type . $is_null . $extra . $primary_key . $default;
        $old_type = trim($old_type);

        if ($old_field['Field'] === $field) {
          if ($old_type === $type) {
            $status = 'same';
            $new_fields[] = $field;
          }
          elseif ($old_field['Field'] === $field && $old_type !== $type) {
            $sql .= "MODIFY COLUMN `$field` $type,";
            $status = 'modify';
            $new_fields[] = $field;
          }
        }
      }

      if ($status === 'new') {
        $sql .= "ADD COLUMN `$field` $type, ";
      }
    }



    $sql = rtrim($sql, ',');

    return $sql;
  }

  public static function get_table_structre($table) {
    $PDO = \EWCore::get_db_PDO();

    $statement = $PDO->prepare("DESCRIBE $table");

    if (!$statement->execute()) {
//      return $statement->errorInfo();
      return false;
    }

    $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

    return $result;
  }

}
