<?php
// models/Todo.php
class Todo {
    public $id;
    public $task;
    public $is_completed;
    public $created_at;
    public $updated_at;

    public function __construct($id, $task, $is_completed, $created_at, $updated_at) {
        $this->id = $id;
        $this->task = $task;
        $this->is_completed = $is_completed;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }
}
?>