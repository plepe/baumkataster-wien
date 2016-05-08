<?php
class Dataset {
  function __construct($id) {
    global $db;
    $this->id = $id;

    $res = $db->query("select count(*) c from data");
    $elem = $res->fetch();
    $res->closeCursor();

    $date = Date("c", filemtime("data/baum.db"));

    include "datasets/{$id}.php";
    $this->data = array(
      'source' => $source,
      'db_columns' => $db_columns,
      'form_search_def' => $form_search_def,
      'table_def' => $table_def,
      'count' => $elem['c'],
      'date' => $date,
    );
  }

  function view() {
    return $this->data;
  }
}
