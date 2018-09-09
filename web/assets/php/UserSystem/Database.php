<?php
/**
* Class full of methods for dealing with databases effectively adn securely.
*
* @package    UserSystem
* @author     Ethan Henderson <ethan@zbee.me>
* @copyright  Copyright 2014-2015 Ethan Henderson
* @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
* @link       https://github.com/zbee/usersystem
* @since      Class available since Release 0.59
*/
/*
  This file is part of Zbee/UserSystem.

  Zbee/UserSystem is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  Zbee/UserSystem is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with Zbee/UserSystem.  If not, see <http://www.gnu.org/licenses/>.
*/

class Database extends Utils {

  /**
  * A shortcut for easily escaping a table/column name for PDO
  * Example: $UserSystem->dbIns(["users",["u"=>"Bob","e"=>"bob@ex.com"]])
  *
  * @access private
  * @param string $field
  * @return string
  */
  private function quoteIdent ($field) {
    return "`".str_replace("`", "``", $field)."`";
  }

  /**
  * A shortcut for eaily inserting a new item into a database.
  * Example: $UserSystem->dbIns(["users",["u"=>"Bob","e"=>"bob@ex.com"]])
  *
  * @access public
  * @param array $data
  * @return mixed
  */
  public function dbIns ($data) {
    $data[0] = $this->quoteIdent(DB_PREFACE.$data[0]);
    $dataArr = $enArr = [];
    foreach ($data[1] as $col => $item) array_push($dataArr, [$col, $item]);
    $cols = $entries = "";
    foreach ($dataArr as $item) {
      $cols .= $this->quoteIdent($item[0]).", ";
      $entries .= "?, ";
      array_push($enArr, $item[1]);
    }
    $cols = substr($cols, 0, -2);
    $entries = substr($entries, 0, -2);
    $stmt = $this->DATABASE->prepare("
      INSERT INTO $data[0] ($cols) VALUES ($entries)
    ");
    $stmt = $stmt->execute($enArr);

    if ($stmt) return $this->DATABASE->lastInsertId();
    return false;
  }


  /**
  * A shortcut for eaily updating an item into a database.
  * Example: $UserSystem->dbUpd(["users",[e"=>"bob@ex.com"],["u"=>"Bob"]])
  *
  * @access public
  * @param array $data
  * @return boolean
  */
  public function dbUpd ($data) {
    $data[0] = $this->quoteIdent(DB_PREFACE.$data[0]);
    $dataArr = $qArr = [];
    foreach ($data[1] as $col => $item) array_push($dataArr, [$col, $item]);
    $update = $equals = "";
    foreach ($dataArr as $item) {
      $update .= $this->quoteIdent($item[0])."=?, ";
      array_push($qArr, $item[1]);
    }
    $equalsArr = [];
    foreach ($data[2] as $col => $item) {
      array_push(
        $equalsArr,
        [
          $this->sanitize($col, "q"),
          $this->sanitize($item, "q")
        ]
      );
    }
    foreach ($equalsArr as $item) {
      $equals .= $this->quoteIdent($item[0])."=? AND ";
      array_push($qArr, $item[1]);
    }
    $equals = substr($equals, 0, -5);
    $update = substr($update, 0, -2);
    $stmt = $this->DATABASE->prepare("
      UPDATE $data[0] SET $update WHERE $equals
    ");
    return $stmt->execute($qArr);
  }


  /**
  * A shortcut for eaily deleting an item in a database.
  * Example: $UserSystem->dbDel(["users",["u"=>"Bob"],1])
  *
  * @access public
  * @param array $data
  * @return boolean
  */
  public function dbDel ($data) {
    $data[0] = $this->quoteIdent(DB_PREFACE.$data[0]);
    $limit = isset($data[2]) ? "limit " . $this->santize($data[2], "n") : "";
    $dataArr = $eqArr = [];
    foreach ($data[1] as $col => $item) array_push($dataArr, [$col, $item]);
    $equals = "";
    foreach ($dataArr as $item) {
      $equals .= $this->quoteIdent($item[0])."=? AND ";
      array_push($eqArr, $item[1]);
    }
    $equals = substr($equals, 0, -5);
    $stmt = $this->DATABASE->prepare("
      DELETE FROM ".$data[0]." WHERE $equals $limit
    ");
    return $stmt->execute($eqArr);
  }

  /**
  * Returns an array for the database search performed, again, just a shortcut
  * for hitting required functions
  * Example:
  * $UserSystem->dbSel(["users",["username"=>Bob","id"=>0],["id","desc"]])
  *
  * @access public
  * @param array $data
  * @return array
  */
  public function dbSel ($data) {
    $data[0] = $this->quoteIdent(DB_PREFACE.$data[0]);
    $dataArr = $qmark = [];
    foreach ($data[1] as $col => $item) {
      array_push(
        $dataArr,
        [
          $col,
          is_array($item) ? "@~#~@".$item[0]."~=exarg@@".$item[1] : $item
        ]
      );
    }
    $equals = "";
    foreach ($dataArr as $item) {
      $diff = '=';
      if (substr($item[1], 0, 5) === "@~#~@") {
        $diff = explode("~=exarg@@", substr($item[1], 5))[0];
        $item[1] = explode("~=exarg@@", $item[1])[1];
      }
      $equals .= " AND ".$this->quoteIdent($item[0]).$diff."?";
      array_push($qmark, $item[1]);
    }
    $equals = substr($equals, 5);
    $sort = "";
    if (array_key_exists(2, $data))
      $sort = "sort by " . $this->quoteIdent($data[2][0])
        . ($data[2][1] == "desc" ? "desc" : "asc");
    $stmt = $this->DATABASE->prepare("
      select * from ".$data[0]." where $equals $sort
    ");
    $stmt->execute($qmark);
    $arr = [(is_object($stmt) ? $stmt->rowCount() : 0)];
    if ($arr[0] > 0)
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)) array_push($arr, $row);
    return $arr;
  }
}
